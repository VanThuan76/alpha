<?php

namespace App\Admin\Controllers;

use App\Models\CommonCode;
use App\Models\Financial\ReceiverAccount;
use Crc16\Crc16;
use App\Models\Facility\Room;
use App\Models\Facility\Branch;
use App\Models\Facility\Zone;
use App\Models\Facility\Bed;
use Encore\Admin\Facades\Admin;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

abstract class Utils
{
    public static function commonCodeFormat($type, $description, $value)
    {
        $commonCode = CommonCode::where('type', $type)
            ->where('value', $value)
            ->first();
        return $commonCode ? $commonCode->$description : '';
    }
    public static function commonCodeOptionsForSelect($type, $description, $value)
    {
        $commonCode = CommonCode::where('type', $type)
            ->pluck($description, $value);
        return $commonCode ? $commonCode : [];
    }
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
    public static function generateEmployeeCode($type)
    {
        $today = date("ymd");
        $currentTime = Carbon::now('Asia/Bangkok');
        $time = $currentTime->format('His');
        $userId = Str::padLeft(Admin::user()->id, 6, '0');
        $code = $type . $today . $userId . $time;
        return $code;
    }
    public static function generatePromoCode($type)
    {
        $today = date("ymd");
        $currentTime = Carbon::now('Asia/Bangkok');
        $time = $currentTime->format('His');
        $userId = Str::padLeft(Admin::user()->id, 6, '0');
        $code = $type . $today . $userId . $time;
        return $code;
    }

    public static function renderStringToGrid($strings, $model){
        if (is_array($strings) && count($strings) > 0) {
            $stringName = "";
            foreach ($strings as $i => $branch) {
                $stringModel = $model::find($branch);
                $stringName .= $stringModel ? $stringModel->name . " , " : "";
            }
            return "<span style='color:#3C8DBD'>$stringName</span>";
        } else {
            return "";
        }
    }
    public static function formatDate($dateTimeString)
    {
        $carbonDate = Carbon::createFromFormat('Y-m-d H:i:s', $dateTimeString);
        return $carbonDate->format('d/m/Y');
    }
}