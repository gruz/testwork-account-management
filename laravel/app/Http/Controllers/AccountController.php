<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Service\AccountService;
use App\Requests\GetAccountRequest;

class AccountController extends Controller
{
    public function getBalance($account_id, GetAccountRequest $request, AccountService $accountService)
    {
        $response = $accountService->getBalance($account_id);

        return $this->responsePrepare($response);
    }

    public function getMaxTransactionVolume(Request $request, AccountService $accountService)
    {
        $response = $accountService->getMaxTransactionVolume();

        return $this->responsePrepare($response);
    }
}
