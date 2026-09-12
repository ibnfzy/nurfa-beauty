<?php

namespace App\Services;

use App\Models\TransactionModel;

class AdminDashboardService
{
    protected TransactionModel $transactionModel;

    public function __construct()
    {
        $this->transactionModel = new TransactionModel();
    }

    /**
     * Get count of pending payment verifications
     */
    public function getPendingPaymentCount(): int
    {
        return $this->transactionModel
            ->where('payment_proof IS NOT NULL', null, false)
            ->where('payment_status', 'pending')
            ->countAllResults();
    }
}
