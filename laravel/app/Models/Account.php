<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'amount' => 0,
        'transaction' => 0,
    ];

    /**
     * The data type of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'uuid';

    protected $fillable = [
        'id',
        'amount',
        'transaction'
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
