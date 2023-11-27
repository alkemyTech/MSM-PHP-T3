<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    
    protected $table = 'transactions';

    protected $fillable = ['amount', 'type', 'description', 'account_id', 'transaction_date'];

    protected $hidden = ['created_at', 'updated_at', 'account_id'];

    protected $with = ['account'];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
