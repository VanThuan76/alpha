<?php

namespace App\Admin\Controllers;

use App\Models\Financial\ReceiverAccount;
use Crc16\Crc16;
use App\Models\Facility\Room;
use App\Models\Facility\Branch;
use App\Models\Facility\Zone;
use App\Models\Facility\Bed;
use Encore\Admin\Facades\Admin;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

abstract class Utils
{
    public static function getBranchIdFromBed($bedId){
        $room = Room::find(Bed::find($bedId)->room_id);
        $branch = Branch::find(Zone::find($room->zone_id)->branch_id);
        return $branch->id;
    }

    public static function getBranchs(){
        return Branch::where('id', Admin::user()->active_branch_id);
    }

    public static function getZones(){
        return Zone::whereIn('branch_id', Branch::where('id', Admin::user()->active_branch_id)->get('id'));
    }

    public static function getRooms(){
        return Room::whereIn('zone_id', Zone::whereIn('branch_id', Branch::where('id', Admin::user()->active_branch_id)->get('id'))->get('id'));
    }

    public static function getFullDescription($value){
        if (strlen($value) >= 10){
            return strlen($value).$value;
        }
        return '0'.strlen($value).$value;
    }

    public static function generateQr($amount, $comment){
        $account = ReceiverAccount::where('branch_id', Admin::user()->active_branch_id)->first();
        $accountInfor = '0006'.$account->bank_name.'01'.Utils::getFullDescription($account->account_number);
        $accountInfor = '0010A00000072701'.Utils::getFullDescription($accountInfor).'0208QRIBFTTA';
        $fullCommment = '08'.Utils::getFullDescription($comment);
        $rawData = '00020101021238'.Utils::getFullDescription($accountInfor).'5303704'.'54'.
        Utils::getFullDescription($amount).'5802VN62'.Utils::getFullDescription($fullCommment).'6304'; 
        $result = Crc16::CCITT_FALSE($rawData); 
        return QrCode::generate($rawData.dechex($result));
    }

}