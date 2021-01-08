<?php

namespace App\Service;

use App\Models\Account;
use Illuminate\Support\Facades\DB;

class AccountService
{
    public function getBalance($account_id)
    {
        $account = Account::where('id', $account_id)->first();
        if (empty($account)) {
            return ['message' => 'Account not found', 'code' => 404];
        }

        $sum = $account->amount;

        return [
            'message' => ['balance' => (int) $sum],
            200,
            'Description' => 'Account balance'
        ];
    }

    public function getMaxTransactionVolume()
    {
        // \DB::enableQueryLog();
        $model = new Account();
        $maxTransactionNumberSubQuery = $model::selectRaw('max("transaction")');
        $items = $model::whereRaw('"transaction" IN (' . DB::raw($maxTransactionNumberSubQuery->toSql()) . ')')->get();
        // dd(\DB::getQueryLog());

        $response = [];
        if (!$items->count()) {
            $response = [
                'message' => [
                    'maxVolume' => 0,
                    'accounts' => [],
                ],
                'code' => 204,
                'Description' => 'No transactions found',
            ];
        } else {
            $maxVolume = $items->first()->transaction;
            $account_ids = $items->pluck('id');

            $response = [
                'message' => [
                    'maxVolume' => (int) $maxVolume,
                    'accounts' => $account_ids,
                ],
                200,
                'Description' => 'Accounts with the max number of transactions.'
            ];
        }

        return $response;
    }
}
