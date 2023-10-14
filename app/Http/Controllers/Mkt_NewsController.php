<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use Validator;
use App\Http\Resources\News as NewsResource;
use App\Models\Marketing\News;

class Mkt_NewsController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $branchId = $request->get("branch_id");
        if (is_null($branchId)){
            $news = News::all();
        } else {
            $news = News::where("branch_id", $branchId)->orderBy('view', 'DESC')->get();
        }

        return $this->sendResponse(NewsResource::collection($news), 'News retrieved successfully.');
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

        $news = News::create($input);

        return $this->sendResponse(new NewsResource($news), 'News created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function show($id)
    {
        $news = News::find($id);
        $news->view ++;
        $news->save();
        if (is_null($news)) {
            return $this->sendError('News not found.');
        }

        return $this->sendResponse(new NewsResource($news), 'News retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, News $news)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
            'detail' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $news->name = $input['name'];
        $news->save();

        return $this->sendResponse(new NewsResource($news), 'News updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(News $news)
    {
        $news->delete();
        return $this->sendResponse([], 'News deleted successfully.');
    }
}