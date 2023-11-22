<?php

namespace App\Http\DTO;

class BalanceDTO
{
    public $accounts;
    public $balance;
    public $history;
    public $fixedTermDeposits;

    public function __construct(object $accounts, array $balance, array $history, array $fixedTermDeposits)
    {
        $this->accounts = $accounts;
        $this->balance = $balance;
        $this->history = $history;
        $this->fixedTermDeposits = $fixedTermDeposits;
    }
}