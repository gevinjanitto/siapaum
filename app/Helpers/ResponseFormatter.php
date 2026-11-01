<?php

namespace App\Helpers;

use Illuminate\Http\Exceptions\HttpResponseException;

class ResponseFormatter
{
    protected static $response = [
        'meta' => [
            'code' => 200,
            'status' => 'Success',
            'message' => null
        ],
    ];

    public static function success($data = null, $message = null, $key = 'data')
    {
        self::$response['meta']['message'] = $message;
        self::$response[$key] = $data;

        return response()->json(self::$response, self::$response['meta']['code']);
    }

    public static function error($data = null, $message = null, $code = 400, $key = 'data')
    {
        self::$response['meta']['status'] = 'Error';
        self::$response['meta']['code'] = $code;
        self::$response['meta']['message'] = $message;
        self::$response[$key] = $data;

        return response()->json(self::$response, self::$response['meta']['code']);
    }

    public static function failedValidation($data = null, $key = 'data')
    {
        self::$response['meta']['status'] = 'Unprocessable Content';
        self::$response['meta']['code'] = 422;
        self::$response['meta']['message'] = 'Data request tidak valid';
        self::$response[$key] = $data;

        throw new HttpResponseException(
            response()->json(self::$response, self::$response['meta']['code'])
        );
    }

    public static function successPaginate($data = null, $message = null, $key = 'data')
    {
        self::$response['meta']['message'] = $message;
        self::$response[$key] = $data->items();
        self::$response['pagination'] = [
            'total_data' => $data->total(),
            'per_page' => $data->perPage(),
            'current_page' => $data->currentPage(),
            'total_page' => $data->lastPage(),
            'next_page' => $data->nextPageUrl(),
            'prev_page' => $data->previousPageUrl(),
        ];

        return response()->json(self::$response, self::$response['meta']['code']);
    }
}
