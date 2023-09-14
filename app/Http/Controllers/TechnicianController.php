<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\AdminUser;
use Validator;
use App\Http\Resources\Technician as TechnicianResource;

class TechnicianController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $unitId = $request->get("unit_id");
        if (is_null($unitId)){
            $technician = AdminUser::all();
        } else {
            $technician = AdminUser::where("active_unit_id", $unitId)->get();
        }

        return $this->sendResponse(TechnicianResource::collection($technician), 'Technician retrieved successfully.');
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

        $technician = AdminUser::create($input);

        return $this->sendResponse(new TechnicianResource($technician), 'Technician created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function show($id)
    {
        $technician = AdminUser::find($id);
        if (is_null($technician)) {
            return $this->sendError('Technician not found.');
        }

        return $this->sendResponse(new TechnicianResource($technician), 'Technician retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, AdminUser $technician)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
            'detail' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $technician->name = $input['name'];
        $technician->save();

        return $this->sendResponse(new TechnicianResource($technician), 'Technician updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(AdminUser $technician)
    {
        $technician->delete();
        return $this->sendResponse([], 'Technician deleted successfully.');
    }
}