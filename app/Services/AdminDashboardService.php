<?php

namespace App\Services;

use App\Models\TransactionModel;
use App\Models\NotificationModel;

class AdminDashboardService
{
    protected TransactionModel $transactionModel;
    protected NotificationModel $notificationModel;

    public function __construct()
    {
        $this->transactionModel = new TransactionModel();
        $this->notificationModel = new NotificationModel();
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

    /**
     * Get count of unread notifications for all customers
     */
    public function getUnreadNotificationCount(): int
    {
        return $this->notificationModel
            ->where('is_read', 0)
            ->countAllResults();
    }
}
