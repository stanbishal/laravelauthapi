<?php 

namespace App\Http\Traits;

use Illuminate\Http\Response;


trait ApiResponse 
{

    public function success(bool $status = true,string $message = '',array $data = [], int $statusCode = Response::HTTP_OK)
    {
        $response = [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];

        return response()->json($response,$statusCode);
    }

    public function error(bool $status = false,string $message = '', array $data = [],array $errors = [],int $statusCode = Response::HTTP_BAD_REQUEST)
    {
        $response = [
            'status' => $status,
            'message' => $message,
            'data' => $data,
            'errors' => $errors
        ];

        return response()->json($response,$statusCode);
    }



}
