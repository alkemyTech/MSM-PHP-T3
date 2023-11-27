<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'currency',
        'transaction_limit',
        'balance',
        'user_id',
        'cbu',
        'deleted',
    ];


    protected $hidden = ['created_at', 'updated_at', 'user_id'];


    protected $with = ['user'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
