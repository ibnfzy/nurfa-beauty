<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Models\ReviewModel;
use App\Models\TransactionModel;
use App\Models\TransactionItemModel;

class Review extends BaseController
{
    protected CustomerModel $customerModel;
    protected ReviewModel $reviewModel;
    protected TransactionModel $transactionModel;
    protected TransactionItemModel $transactionItemModel;

    public function __construct()
    {
        $this->customerModel        = new CustomerModel();
        $this->reviewModel          = new ReviewModel();
        $this->transactionModel     = new TransactionModel();
        $this->transactionItemModel = new TransactionItemModel();
    }

    protected function getCustomerId()
    {
        $userId   = session()->get('user_id');
        $customer = $this->customerModel->where('user_id', $userId)->first();
        return $customer ? $customer['id'] : null;
    }

    public function add()
    {
        $customerId = $this->getCustomerId();

        $rules = [
            'product_id' => 'required|integer',
            'rating'     => 'required|integer|greater_than[0]|less_than[6]',
            'comment'    => 'permit_empty|max_length[1000]',
        ];

        $messages = [
            'product_id' => [
                'required' => 'Produk tidak valid.',
                'integer'  => 'Produk tidak valid.',
            ],
            'rating' => [
                'required'      => 'Rating wajib dipilih.',
                'integer'       => 'Rating tidak valid.',
                'greater_than'  => 'Rating minimal 1.',
                'less_than'     => 'Rating maksimal 5.',
            ],
            'comment' => [
                'max_length' => 'Ulasan maksimal 1000 karakter.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()
                ->withInput()
                ->with('toast', ['type' => 'error', 'message' => 'Gagal mengirim ulasan. Periksa kembali data Anda.']);
        }

        $productId = $this->request->getPost('product_id');

        // Cek apakah customer sudah pernah review produk ini
        $existingReview = $this->reviewModel
            ->where('customer_id', $customerId)
            ->where('product_id', $productId)
            ->first();

        if ($existingReview) {
            // Update review yang sudah ada
            $this->reviewModel->update($existingReview['id'], [
                'rating'  => $this->request->getPost('rating'),
                'comment' => $this->request->getPost('comment'),
            ]);

            return redirect()->back()
                ->with('toast', ['type' => 'success', 'message' => 'Ulasan berhasil diperbarui!']);
        }

        $this->reviewModel->insert([
            'customer_id' => $customerId,
            'product_id'  => $productId,
            'rating'      => $this->request->getPost('rating'),
            'comment'     => $this->request->getPost('comment'),
        ]);

        return redirect()->back()
            ->with('toast', ['type' => 'success', 'message' => 'Ulasan berhasil dikirim! Terima kasih atas feedback Anda.']);
    }
}
