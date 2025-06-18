<?php

namespace App\Helpers;

use Symfony\Component\HttpFoundation\Response;

class ApiResponse
{
    private static $response = [
        'status' => null,
        'message' => null,
        'data' => null,
    ];

    public static function success()
    {
        $instance = new self;
        self::$response['status'] = Response::HTTP_OK;

        return $instance;
    }

    public static function conflict()
    {
        $instance = new self;
        self::$response['status'] = Response::HTTP_CONFLICT;

        return $instance;
    }

    public static function not_found()
    {
        $instance = new self;
        self::$response['status'] = Response::HTTP_NOT_FOUND;

        return $instance;
    }

    public static function server_error()
    {
        $instance = new self;
        self::$response['status'] = Response::HTTP_INTERNAL_SERVER_ERROR;

        return $instance;
    }

    public function message(string $message)
    {
        self::$response['message'] = $message;

        return $this;
    }

    public function data($data)
    {
        self::$response['data'] = $data;

        return $this;
    }

    public function response()
    {
        $response = self::$response;
        unset($response['status']);
        if (! self::$response['message']) {
            unset($response['message']);
        }
        if (! self::$response['data']) {
            unset($response['data']);
        }

        return response()->json($response, self::$response['status']);
    }

    public function showFile($file)
    {
        return response()->file($file);
    }
}
