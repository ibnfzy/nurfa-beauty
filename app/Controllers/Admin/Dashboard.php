<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TransactionModel;
use App\Models\CustomerModel;
use App\Models\ProductModel;
use App\Models\LoyaltyPointsLogModel;

class Dashboard extends BaseController
{
    protected TransactionModel $transactionModel;
    protected CustomerModel $customerModel;
    protected ProductModel $productModel;
    protected LoyaltyPointsLogModel $loyaltyPointsLogModel;

    public function __construct()
    {
        $this->transactionModel    = new TransactionModel();
        $this->customerModel       = new CustomerModel();
        $this->productModel        = new ProductModel();
        $this->loyaltyPointsLogModel = new LoyaltyPointsLogModel();
    }

    public function index()
    {
        $now      = new \DateTime();
        $month    = (int) $now->format('m');
        $year     = (int) $now->format('Y');

        // a. Total Penjualan Bulan Ini
        $totalPenjualanBulanIni = $this->transactionModel
            ->selectSum('final_amount')
            ->where('status', 'completed')
            ->where('MONTH(transaction_date)', $month)
            ->where('YEAR(transaction_date)', $year)
            ->first()['final_amount'] ?? 0;

        // b. Pelanggan Baru Bulan Ini
        $pelangganBaruBulanIni = $this->customerModel
            ->where('MONTH(created_at)', $month)
            ->where('YEAR(created_at)', $year)
            ->countAllResults();

        // c. Stok Menipis
        $stokMenipis = $this->productModel
            ->where('stock <', 10)
            ->where('is_active', 1)
            ->countAllResults();

        // d. Transaksi 7 Hari Terakhir
        $sevenDaysAgo = (new \DateTime())->modify('-7 days')->format('Y-m-d');
        $transaksi7Hari = $this->transactionModel
            ->where('transaction_date >=', $sevenDaysAgo)
            ->countAllResults();

        // e. Chart Penjualan Harian (30 hari terakhir)
        $thirtyDaysAgo = (new \DateTime())->modify('-30 days')->format('Y-m-d');
        $dailySales = $this->transactionModel
            ->select('DATE(transaction_date) as sale_date, SUM(final_amount) as daily_total')
            ->where('status', 'completed')
            ->where('transaction_date >=', $thirtyDaysAgo)
            ->groupBy('DATE(transaction_date)')
            ->orderBy('sale_date', 'ASC')
            ->findAll();

        $chartLabels = [];
        $chartData   = [];
        foreach ($dailySales as $row) {
            $chartLabels[] = date('d M', strtotime($row['sale_date']));
            $chartData[]   = (float) $row['daily_total'];
        }

        // f. 5 Transaksi Terakhir
        $transaksiTerakhir = $this->transactionModel
            ->withCustomer()
            ->orderBy('transactions.id', 'DESC')
            ->findAll(5);

        // g. Produk Terlaris (Top 5)
        $produkTerlaris = db_connect()->table('transaction_items')
            ->select('products.name as product_name, SUM(transaction_items.quantity) as total_qty')
            ->join('transactions', 'transactions.id = transaction_items.transaction_id')
            ->join('products', 'products.id = transaction_items.product_id')
            ->where('transactions.status', 'completed')
            ->groupBy('transaction_items.product_id')
            ->orderBy('total_qty', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        // h. Distribusi Membership
        $distribusiMembership = $this->customerModel
            ->select('membership_level, COUNT(*) as total')
            ->groupBy('membership_level')
            ->findAll();

        $data = [
            'pageTitle'               => 'Dashboard',
            'totalPenjualanBulanIni'  => $totalPenjualanBulanIni,
            'pelangganBaruBulanIni'   => $pelangganBaruBulanIni,
            'stokMenipis'             => $stokMenipis,
            'transaksi7Hari'          => $transaksi7Hari,
            'chartLabels'             => $chartLabels,
            'chartData'               => $chartData,
            'transaksiTerakhir'       => $transaksiTerakhir,
            'produkTerlaris'          => $produkTerlaris,
            'distribusiMembership'    => $distribusiMembership,
        ];

        return view('admin/dashboard/index', $data);
    }
}
