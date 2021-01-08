<?php

namespace App\Http;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Exceptions\UnprocessableEntityHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

abstract class ApiRequest extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        throw new UnprocessableEntityHttpException($validator->errors()->toJson());
    }

    protected function failedAuthorization()
    {
        throw new HttpException(403);
    }

    protected function validateQueryUUID($parameterName)
    {
        $transactionId = request()->route()->parameter($parameterName);
        $validator = \Illuminate\Support\Facades\Validator::make([$parameterName => $transactionId], [
            $parameterName => 'required|uuid'
        ]);

        if ($validator->fails()) {
            throw new UnprocessableEntityHttpException($validator->errors()->toJson());
        }
    }
}
