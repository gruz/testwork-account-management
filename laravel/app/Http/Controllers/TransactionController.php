<?php

namespace App\Http\Controllers;

use App\Service\TransactionService;
use App\Requests\GetTransactionRequest;
use App\Requests\CreateTransactionRequest;

class TransactionController extends Controller
{
    public function create(CreateTransactionRequest $request, TransactionService $transactionService)
    {
        $transaction_id = $request->header('Transaction-Id');
        $account_id = $request->get('account_id');
        $amount = $request->get('amount');

        $response = $transactionService->createTransaction($transaction_id, $account_id, $amount);

        return $this->responsePrepare($response);
    }

    public function getByID($transaction_id, GetTransactionRequest $request, TransactionService $transactionService)
    {
        $response = $transactionService->getById($transaction_id);

        return $this->responsePrepare($response);
    }
}
