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

    protected $hidden = ['created_at', 'updated_at', 'account_id'];

    protected $with = ['account'];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
