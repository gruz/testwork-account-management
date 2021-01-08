<?php

namespace App\Service;

use App\Models\Transaction;
use Illuminate\Database\QueryException;

class TransactionService
{
    public function createTransaction(string $transaction_id, string $account_id, int $amount)
    {
        try {
            $model = Transaction::create([
                'id' => $transaction_id,
                'account_id' => $account_id,
                'amount' => $amount
            ])
                ->account()
                ->firstOrNew(
                    ['id' => $account_id,],
                );

            $model->amount = $model->amount + $amount;
            $model->transaction += 1;
            $model->save();

            return ['message' => 'Transaction created', 'code' => 200];
        } catch (QueryException $e) {
            if ($e->getCode() === "23000") {
                return ['message' => 'Transaction already exists', 'code' => 200];
            } else {
                throw $e;
            }
        }
    }

    public function getById(string $transaction_id)
    {
        $transaction = Transaction::where('id', $transaction_id)->first();
        if (empty($transaction)) {
            $response = [
                'message' => 'Transaction not found',
                'code' => '404'
            ];
        } else {
            $transaction->makeHidden('id');
            $response = [
                'message' => $transaction,
                'Description' => 'Transaction details.',
            ];
        }

        return $response;
    }
}
