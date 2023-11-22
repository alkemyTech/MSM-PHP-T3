<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FixedTerm extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'account_id',
        'interest',
        'total',
        'duration',
        'closed_at'
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
