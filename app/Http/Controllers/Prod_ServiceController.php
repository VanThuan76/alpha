<?php

namespace App\Http\Controllers;

use App\Http\Response\CommonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Resources\Service as ServiceResource;
use App\Models\Product\Service;
use Illuminate\Support\Facades\Validator;

class Prod_ServiceController extends BaseController
{
    use CommonResponse;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $branchId = $request->get("branch_id");
        if (is_null($branchId)) {
            $service = Service::all();
        } else {
            $service = Service::where("branch_id", $branchId)->get();
        }

        return $this->sendResponse(ServiceResource::collection($service), 'Service retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
            'detail' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $service = Service::create($input);

        return $this->sendResponse(new ServiceResource($service), 'Service created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function show($id)
    {
        $service = Service::find($id);
        if (is_null($service)) {
            return $this->sendError('Service not found.');
        }

        return $this->sendResponse(new ServiceResource($service), 'Service retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, Service $service)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
            'detail' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $service->name = $input['name'];
        $service->save();

        return $this->sendResponse(new ServiceResource($service), 'Service updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Service $service)
    {
        $service->delete();
        return $this->sendResponse([], 'Service deleted successfully.');
    }
    public function get(Request $request)
    {
        $user = auth('api')->user();
        $limit = $request->input('limit', 20);
        $previousLastServiceId = $request->input('previous_last_service_id', 0);

        if ($request->input('limit')) {
            $limit = 20;
        }
        
        $servicesQuery = Service::where('status', 1)->orderBy('id', 'desc')->limit($limit);

        if ($request->input('previous_last_service_id') !== null) {
            $servicesQuery->where('id', '<', $previousLastServiceId);
        }
        
        $services = $servicesQuery->get();

        $result = [
            'services' => $services->map(function ($service) {
                $tagsArray = array_map('trim', explode(',', $service->tags));
                return [
                    'id' => $service->id,
                    'image_url' => $service->image,
                    'title' => $service->name,
                    'introduction' => $service->introduction,
                    'used_count' => $service->used_count,
                    'details' => $service->details,
                    'tags' => $tagsArray,
                    'rate' => $service->rate,
                    'comment_count' => $service->comment_count,
                ];
            })
        ];
        if ($user) {
            $response = $this->_formatBaseResponse(200, $result, 'Lấy thông tin thành công', []);
            return response()->json($response);
        } else {
            $response = $this->_formatBaseResponse(401, null, 'Lấy thông tin không thành công', ['errors' => 'Unauthorised']);
            return response()->json($response, 401);
        }
    }
}
