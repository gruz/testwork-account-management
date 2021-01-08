<?php

namespace App\Requests;

use App\Http\ApiRequest;

class GetAccountRequest extends ApiRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $this->validateQueryUUID('account_id');

        return [];
    }
}
