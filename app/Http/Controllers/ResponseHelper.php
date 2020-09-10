<?php


namespace App\Http\Controllers;


use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

trait ResponseHelper
{
    public function ret($code, $msg, $data = null)
    {
        return is_null($data) ? compact("code", "msg") : compact("code", "msg", "data");
    }

    public function respond($code, $msg, $data = null)
    {
        return response()->json($this->ret($code, $msg, $data));
    }

    public function success_respond($data = null, $msg = 'success')
    {
        return $this->respond(0, $msg, $data);
    }

    public function success_msg($msg = 'success')
    {
        return $this->respond(0, $msg);
    }

    public function validation_respond($errors)
    {
        return $this->respond(422, '参数校验失败', $errors);
    }

    public function validate(Request $request, array $rules, array $messages = [], array $customAttributes = [])
    {
        $validator = Validator::make($request->all(), $rules, $messages, $customAttributes);
        if ($validator->fails()) {
            throw new HttpResponseException($this->respond(422, '参数校验失败', $validator->errors()));
        }
    }
}
