<?php

namespace App\Http\Response;

trait CommonResponse
{
    private function _formatCountResponse($data, $perPage, $total)
    {
        return [
            'data' => $data,
            'totalElements' => $perPage,
            'totalPages' => $total,
        ];
    }

    private function _formatBaseResponse($statusCode, $data, $message, $other = null)
    {
        $response = [
            'statusCode' => $statusCode,
            'data' => $data,
            'message' => $message,
        ];
    
        if (!is_null($other)) {
            $response = array_merge($response, $other);
        }
    
        return $response;
    }
}
