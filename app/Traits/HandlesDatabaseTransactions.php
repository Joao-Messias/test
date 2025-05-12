<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait HandlesDatabaseTransactions
{
    protected function executeInTransaction(callable $callback)
    {
        return DB::transaction(function () use ($callback) {
            return $callback();
        });
    }
} 