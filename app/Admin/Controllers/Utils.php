<?php

namespace App\Admin\Controllers;

use App\Models\Room;
use App\Models\Branch;
use Encore\Admin\Facades\Admin;
use Illuminate\Support\Facades\DB;

abstract class Utils
{
    public static function getBranchs(){
        return Branch::where('unit_id', Admin::user()->active_unit_id);
    }

    public static function getZones(){
        return Zone::whereIn('branch_id', Branch::where('unit_id', Admin::user()->active_unit_id)->get('id'));
    }

    public static function getRooms(){
        return Room::whereIn('zone_id', Zone::whereIn('branch_id', Branch::where('unit_id', Admin::user()->active_unit_id)->get('id'))->get('id'));
    }
}