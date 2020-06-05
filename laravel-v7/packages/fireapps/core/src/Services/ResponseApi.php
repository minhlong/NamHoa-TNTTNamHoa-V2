<?php

namespace Fireapps\Core\Services;

use Illuminate\Http\Response as ResponseCore;

class Response
{
    public function send($data, $code = ResponseCore::HTTP_OK, $message = null)
    {
        $result = [
            'status' => false,
            'message' => null,
            'data' => [],
            'errors' => [],
        ];

        if ($code == ResponseCore::HTTP_OK) {
            $result['status'] = true;
        }

        if ($code == ResponseCore::HTTP_OK) {
            if (is_string($data)) {
                $result['message'] = $data;
            } else {
                $result['data'] = $data;
                $result['message'] = $message;
            }
            unset($result['errors']);
        } else {
            $code = empty($code) ? ResponseCore::HTTP_INTERNAL_SERVER_ERROR : $code;
            if ($data instanceof \Exception) {
                $result['message'] = $data->getMessage();
            } else {
                if (is_string($data)) {
                    $result['message'] = $data;
                } else {
                    $result['errors'] = $data;
                    $result['message'] = $message;
                }
            }
            unset($result['data']);
        }
        return response()->json($result, $code);
    }
}
