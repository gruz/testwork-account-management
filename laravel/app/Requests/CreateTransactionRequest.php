<?php

namespace App\Requests;

use App\Http\ApiRequest;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\UnprocessableEntityHttpException;

class CreateTransactionRequest extends ApiRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $transactionId = request()->header('Transaction-Id');
        $validator = Validator::make(['Transaction-Id' => $transactionId], [
            'Transaction-Id' => 'uuid'
        ]);

        if ($validator->fails()) {
            throw new UnprocessableEntityHttpException($validator->errors()->toJson());
        }

        $rules = [
            'account_id' => 'required|uuid',
            'amount' => 'required|integer',
        ];

        return $rules;
    }
}
