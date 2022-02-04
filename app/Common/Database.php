<?php

namespace App\Common;

use Illuminate\Support\Facades\DB;

use Exception;

class Database
{
    /**
     * Wrap callback into sql-transaction.
     *
     * @throws Exception
     */
    public static function makeTransaction(callable $transactionCallback) : void
    {
        try {
            DB::beginTransaction();
            $transactionCallback();
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }
}
