<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    //
    protected $fillable = [
        "amount","customer_id","braintree_id"
    ];
    public function customer()
    {
        return $this->belongsTo('App\Customer');
    }
}
