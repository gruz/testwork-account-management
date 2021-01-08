<?php

namespace App\Requests;

use App\Http\ApiRequest;

class GetTransactionRequest extends ApiRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $this->validateQueryUUID('transaction_id');

        return [];
    }
}
