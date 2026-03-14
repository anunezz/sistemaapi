<?php

namespace App\Traits;

use App\Models\Transaccion;

trait BinnacleTrait
{
    public function guardarMovimiento($userId, $moduleId, $transactionTypeId, $action) {
        Transaccion::create([
         "user_id" => $userId,
         "cat_transaction_type_id" => $transactionTypeId,
         "cat_module_id" => $moduleId,
         "action"=> $action,
         "created_at" => now(),
         "updated_at" => now()
        ]);
    }
}
