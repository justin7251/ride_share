<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait HandlesTransactions
{
    /**
     * Execute a callback within a database transaction.
     *
     * @param  \Closure $callback
     * @return mixed
     * @throws \Throwable
     */
    public static function transaction(\Closure $callback)
    {
        return DB::transaction($callback);
    }
}
