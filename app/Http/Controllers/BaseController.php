<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BaseController extends Controller
{
    public function handleResponse($result, $msg)
    {

        $res = [
            'success' => true,
            'data'    => $result,
            'message' => $msg,
        ];
        $header = array(
            'Content-Type' => 'application/json; charset=UTF-8',
            'charset' => 'utf-8'
        );

        return response()->json($res, 200, $header, JSON_UNESCAPED_UNICODE);
    }

    public function handleError($error, $errorMsg = [], $code = 404)
    {

        $res = [
            'success' => false,
            'message' => $error,
        ];
        $header = array(
            'Content-Type' => 'application/json; charset=UTF-8',
            'charset' => 'utf-8'
        );


        if (!empty($errorMsg)) {
            $res['data'] = $errorMsg;
        }
        return response()->json($res, $code, $header, JSON_UNESCAPED_UNICODE);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
        ]);
    }

}
