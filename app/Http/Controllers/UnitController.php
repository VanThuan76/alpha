<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\Unit;
use Validator;
use App\Http\Resources\Unit as UnitResource;

class UnitController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $units = Unit::all();

        return $this->sendResponse(UnitResource::collection($units), 'Units retrieved successfully.');
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

        $unit = Unit::create($input);

        return $this->sendResponse(new UnitResource($unit), 'Unit created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function show($id)
    {
        $unit = Unit::find($id);
        if (is_null($unit)) {
            return $this->sendError('Unit not found.');
        }

        return $this->sendResponse(new UnitResource($unit), 'Unit retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, Unit $unit)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
            'detail' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $unit->name = $input['name'];
        $unit->save();

        return $this->sendResponse(new UnitResource($unit), 'Unit updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Unit $unit)
    {
        $unit->delete();
        return $this->sendResponse([], 'Unit deleted successfully.');
    }
}