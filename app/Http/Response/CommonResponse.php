<?php

namespace App\Traits;

trait CommonResponse
{
    private function _formatCountResponse($data, $perPage, $total)
    {
        return [
            'content' => $data,
            'totalElements' => $perPage,
            'totalPages' => $total,
        ];
    }

    private function _formatBaseResponse($statusCode, $data, $message)
    {
        return [
            'statusCode' => $statusCode,
            'data' => $data,
            'message' => $message,
        ];
    }
}
