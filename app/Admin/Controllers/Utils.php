<?php

namespace App\Admin\Controllers;

use Crc16\Crc16;
use App\Models\Room;
use App\Models\ReceiverAccount;
use App\Models\Branch;
use Encore\Admin\Facades\Admin;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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

    public static function getFullDescription($value){
        if (strlen($value) >= 10){
            return strlen($value).$value;
        }
        return '0'.strlen($value).$value;
    }

    public static function generateQr($amount, $comment){
        $account = ReceiverAccount::where('unit_id', Admin::user()->active_unit_id)->first();
        $accountInfor = '0006'.$account->bank_name.'01'.Utils::getFullDescription($account->account_number);
        $accountInfor = '0010A00000072701'.Utils::getFullDescription($accountInfor).'0208QRIBFTTA';
        $fullCommment = '08'.Utils::getFullDescription($comment);
        $rawData = '00020101021238'.Utils::getFullDescription($accountInfor).'5303704'.'54'.
        Utils::getFullDescription($amount).'5802VN62'.Utils::getFullDescription($fullCommment).'6304'; 
        $result = Crc16::CCITT_FALSE($rawData); 
        return QrCode::generate($rawData.dechex($result));
    }
}