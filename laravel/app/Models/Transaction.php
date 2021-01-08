<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'uuid';

    protected $visible = [
        'id',
        'account_id',
        'amount',
        'transaction',
        'maxVolume'
    ];

    protected $fillable = [
        'id',
        'account_id',
        'amount',
        'transaction',
    ];

    protected $casts = [
        'amount' => 'integer',
        'transaction' => 'integer',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
