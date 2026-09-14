<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TransactionModel;
use App\Models\TransactionItemModel;
use App\Models\CustomerModel;
use App\Models\LoyaltyPointsLogModel;
use App\Models\VoucherModel;
use App\Models\ProductModel;
use CodeIgniter\Database\ConnectionInterface;

class Report extends BaseController
{
    /** @var TransactionModel */
    protected $transactionModel;

    /** @var TransactionItemModel */
    protected $transactionItemModel;

    /** @var CustomerModel */
    protected $customerModel;

    /** @var LoyaltyPointsLogModel */
    protected $loyaltyPointsLogModel;

    /** @var VoucherModel */
    protected $voucherModel;

    /** @var ProductModel */
    protected $productModel;

    /** @var ConnectionInterface */
    protected $db;

    public function __construct()
    {
        $this->transactionModel      = new TransactionModel();
        $this->transactionItemModel  = new TransactionItemModel();
        $this->customerModel         = new CustomerModel();
        $this->loyaltyPointsLogModel = new LoyaltyPointsLogModel();
        $this->voucherModel          = new VoucherModel();
        $this->productModel          = new ProductModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Return logo as base64 data URI for Dompdf (reliable without isRemoteEnabled).
     */
    private function getLogoDataUri(): string
    {
        $path = FCPATH . 'logo.png';
        if (! is_file($path)) {
            return '';
        }
        $mime = function_exists('mime_content_type') ? (mime_content_type($path) ?: 'image/png') : 'image/png';
        $data = @file_get_contents($path);
        if ($data === false || $data === '') {
            return '';
        }

        return 'data:' . $mime . ';base64,' . base64_encode($data);
    }

    /**
     * Halaman overview laporan
     */
    public function index()
    {
        $data = [
            'pageTitle' => 'Laporan',
        ];

        return view('admin/report/index', $data);
    }

    /**
     * Laporan penjualan dengan filter tanggal
     */
    public function sales()
    {
        /** @var string $startDate */
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        /** @var string $endDate */
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-d');
        /** @var string $period */
        $period = $this->request->getGet('period') ?? 'daily';

        // Summary stats (Total, Online vs Offline)
        $summary = $this->db->table('transactions')
            ->select('COUNT(*) as total_transactions, COALESCE(SUM(final_amount), 0) as total_revenue, COALESCE(AVG(final_amount), 0) as avg_transaction')
            ->select("COUNT(CASE WHEN payment_method != 'offline' THEN 1 END) as online_transactions")
            ->select("COALESCE(SUM(CASE WHEN payment_method != 'offline' THEN final_amount ELSE 0 END), 0) as online_revenue")
            ->select("COUNT(CASE WHEN payment_method = 'offline' THEN 1 END) as offline_transactions")
            ->select("COALESCE(SUM(CASE WHEN payment_method = 'offline' THEN final_amount ELSE 0 END), 0) as offline_revenue")
            ->whereIn('status', ['completed', 'paid'])
            ->where('transaction_date >=', $startDate . ' 00:00:00')
            ->where('transaction_date <=', $endDate . ' 23:59:59')
            ->get()
            ->getRowArray();

        $totalRevenue        = (int) ($summary['total_revenue'] ?? 0);
        $totalTransactions   = (int) ($summary['total_transactions'] ?? 0);
        $avgTransaction      = (int) ($summary['avg_transaction'] ?? 0);
        $onlineRevenue       = (int) ($summary['online_revenue'] ?? 0);
        $onlineTransactions  = (int) ($summary['online_transactions'] ?? 0);
        $offlineRevenue      = (int) ($summary['offline_revenue'] ?? 0);
        $offlineTransactions = (int) ($summary['offline_transactions'] ?? 0);

        // Breakdown by period
        $selectExpr = '';
        $labelExpr  = '';
        switch ($period) {
            case 'weekly':
                $selectExpr = "YEARWEEK(transaction_date, 1) as period_key";
                $labelExpr  = "CONCAT('Minggu ', YEARWEEK(transaction_date, 1)) as period_label";
                break;
            case 'monthly':
                $selectExpr = "DATE_FORMAT(transaction_date, '%Y-%m') as period_key";
                $labelExpr  = "DATE_FORMAT(transaction_date, '%M %Y') as period_label";
                break;
            default:
                $selectExpr = "DATE(transaction_date) as period_key";
                $labelExpr  = "DATE(transaction_date) as period_label";
                break;
        }

        $breakdown = $this->db->table('transactions')
            ->select($selectExpr)
            ->select($labelExpr)
            ->select('COUNT(*) as transaction_count')
            ->select('COALESCE(SUM(final_amount), 0) as period_revenue')
            ->select('COALESCE(AVG(final_amount), 0) as period_avg')
            ->select("COUNT(CASE WHEN payment_method != 'offline' THEN 1 END) as online_count")
            ->select("COALESCE(SUM(CASE WHEN payment_method != 'offline' THEN final_amount ELSE 0 END), 0) as online_revenue")
            ->select("COUNT(CASE WHEN payment_method = 'offline' THEN 1 END) as offline_count")
            ->select("COALESCE(SUM(CASE WHEN payment_method = 'offline' THEN final_amount ELSE 0 END), 0) as offline_revenue")
            ->whereIn('status', ['completed', 'paid'])
            ->where('transaction_date >=', $startDate . ' 00:00:00')
            ->where('transaction_date <=', $endDate . ' 23:59:59')
            ->groupBy('period_key')
            ->orderBy('period_key', 'ASC')
            ->get()
            ->getResultArray();

        // Ambil daftar transaksi detail dengan penanda channel (Beli Online vs Beli Offline)
        $recentTransactions = $this->db->table('transactions')
            ->select('transactions.*, customers.user_id, users.name as customer_name')
            ->join('customers', 'customers.id = transactions.customer_id', 'left')
            ->join('users', 'users.id = customers.user_id', 'left')
            ->whereIn('transactions.status', ['completed', 'paid'])
            ->where('transactions.transaction_date >=', $startDate . ' 00:00:00')
            ->where('transactions.transaction_date <=', $endDate . ' 23:59:59')
            ->orderBy('transactions.transaction_date', 'DESC')
            ->limit(100)
            ->get()
            ->getResultArray();

        // Chart data
        /** @var string[] $chartLabels */
        $chartLabels = [];
        /** @var int[] $chartRevenue */
        $chartRevenue = [];
        /** @var int[] $chartCounts */
        $chartCounts = [];

        foreach ($breakdown as $row) {
            $chartLabels[] = $period === 'daily' ? date('d M', strtotime($row['period_label'] ?? '')) : ($row['period_label'] ?? '');
            $chartRevenue[] = (int) ($row['period_revenue'] ?? 0);
            $chartCounts[]  = (int) ($row['transaction_count'] ?? 0);
        }

        // Top selling products
        $topProducts = $this->db->table('transaction_items')
            ->select('products.name as product_name')
            ->select('GROUP_CONCAT(DISTINCT transaction_items.variant_selection SEPARATOR "||") as variant_selections')
            ->select('SUM(transaction_items.quantity) as total_qty')
            ->select('SUM(transaction_items.subtotal) as total_revenue')
            ->join('transactions', 'transactions.id = transaction_items.transaction_id')
            ->join('products', 'products.id = transaction_items.product_id')
            ->whereIn('transactions.status', ['completed', 'paid'])
            ->where('transactions.transaction_date >=', $startDate . ' 00:00:00')
            ->where('transactions.transaction_date <=', $endDate . ' 23:59:59')
            ->groupBy('transaction_items.product_id')
            ->orderBy('total_qty', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        $data = [
            'pageTitle'           => 'Laporan Penjualan',
            'startDate'           => $startDate,
            'endDate'             => $endDate,
            'period'              => $period,
            'totalRevenue'        => $totalRevenue,
            'totalTransactions'   => $totalTransactions,
            'avgTransaction'      => $avgTransaction,
            'onlineRevenue'       => $onlineRevenue,
            'onlineTransactions'  => $onlineTransactions,
            'offlineRevenue'      => $offlineRevenue,
            'offlineTransactions' => $offlineTransactions,
            'recentTransactions'  => $recentTransactions ?? [],
            'breakdown'           => $breakdown ?? [],
            'chartLabels'         => $chartLabels,
            'chartRevenue'        => $chartRevenue,
            'chartCounts'         => $chartCounts,
            'topProducts'         => $topProducts ?? [],
        ];

        return view('admin/report/sales', $data);
    }

    /**
     * Laporan pelanggan
     */
    public function customer()
    {
        // Total customers
        $totalCustomers = $this->customerModel->countAllResults(false);

        // New customers this month
        $newThisMonth = $this->customerModel
            ->where('customers.created_at >=', date('Y-m-01'))
            ->countAllResults(false);

        // Active customers (transacted in last 30 days)
        $activeCustomers = $this->db->table('transactions')
            ->select('COUNT(DISTINCT customer_id) as count')
            ->whereIn('status', ['completed', 'paid'])
            ->where('transaction_date >=', date('Y-m-d', strtotime('-30 days')))
            ->get()
            ->getRowArray();
        $activeCount = (int) ($activeCustomers['count'] ?? 0);

        // Churn: customers with no purchase in last 90 days but had purchases before
        $churnQuery = $this->db->query(
            "SELECT COUNT(*) as count FROM customers c
             WHERE c.last_purchase_date IS NOT NULL
             AND c.last_purchase_date < DATE_SUB(CURDATE(), INTERVAL 90 DAY)"
        );
        $churnResult = $churnQuery->getRowArray();
        $churnCount  = (int) ($churnResult['count'] ?? 0);

        // New customers trend (last 6 months)
        $newCustomersTrend = $this->db->query(
            "SELECT DATE_FORMAT(created_at, '%Y-%m') as month_key,
                    DATE_FORMAT(created_at, '%M %Y') as month_label,
                    COUNT(*) as count
             FROM customers
             WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
             GROUP BY month_key
             ORDER BY month_key ASC"
        )->getResultArray();

        /** @var string[] $trendLabels */
        $trendLabels = [];
        /** @var int[] $trendData */
        $trendData = [];
        foreach ($newCustomersTrend as $row) {
            $trendLabels[] = $row['month_label'] ?? '';
            $trendData[]   = (int) ($row['count'] ?? 0);
        }

        // Customers by membership level
        $membershipDistribution = $this->db->table('customers')
            ->select('membership_level')
            ->select('COUNT(*) as count')
            ->groupBy('membership_level')
            ->get()
            ->getResultArray();

        // Top 10 customers by spending
        $topCustomers = $this->customerModel
            ->select('customers.*, users.name, users.email')
            ->join('users', 'users.id = customers.user_id')
            ->orderBy('customers.total_spending', 'DESC')
            ->limit(10)
            ->findAll();

        // Count transactions per top customer
        foreach ($topCustomers as &$cust) {
            $trxCount = $this->db->table('transactions')
                ->where('customer_id', $cust['id'])
                ->whereIn('status', ['completed', 'paid'])
                ->countAllResults(false);
            $cust['transaction_count'] = $trxCount;
        }
        unset($cust);

        // Customer retention: customers with more than 1 completed purchase
        $retentionResult = $this->db->query(
            "SELECT COUNT(*) as count FROM (
                SELECT customer_id
                FROM transactions
                WHERE status IN ('completed', 'paid')
                GROUP BY customer_id
                HAVING COUNT(*) > 1
            ) as repeat_customers"
        )->getRowArray();
        $retentionCount = (int) ($retentionResult['count'] ?? 0);

        $data = [
            'pageTitle'            => 'Laporan Pelanggan',
            'totalCustomers'       => $totalCustomers,
            'newThisMonth'         => $newThisMonth,
            'activeCount'          => $activeCount,
            'churnCount'           => $churnCount,
            'trendLabels'          => $trendLabels,
            'trendData'            => $trendData,
            'membershipDistribution' => $membershipDistribution ?? [],
            'topCustomers'         => $topCustomers ?? [],
            'retentionCount'       => $retentionCount,
        ];

        return view('admin/report/customer', $data);
    }

    /**
     * Laporan loyalitas
     */
    public function loyalty()
    {
        // Total points issued (earn + bonus)
        $issuedResult = $this->db->table('loyalty_points_log')
            ->select('COALESCE(SUM(points), 0) as total')
            ->whereIn('type', ['earn', 'bonus'])
            ->get()
            ->getRowArray();
        $totalIssued = (int) ($issuedResult['total'] ?? 0);

        // Total points redeemed
        $redeemedResult = $this->db->table('loyalty_points_log')
            ->select('COALESCE(SUM(points), 0) as total')
            ->where('type', 'redeem')
            ->get()
            ->getRowArray();
        $totalRedeemed = (int) ($redeemedResult['total'] ?? 0);

        // Active points (issued - redeemed)
        $activePoints = $totalIssued - $totalRedeemed;

        // Distribution by membership level
        $membershipStats = $this->db->query(
            "SELECT c.membership_level,
                    COUNT(*) as customer_count,
                    COALESCE(AVG(c.loyalty_points), 0) as avg_points,
                    COALESCE(SUM(c.total_spending), 0) as total_spending
             FROM customers c
             GROUP BY c.membership_level"
        )->getResultArray();

        // Points earning trend (last 6 months)
        $pointsTrend = $this->db->query(
            "SELECT DATE_FORMAT(created_at, '%Y-%m') as month_key,
                    DATE_FORMAT(created_at, '%M %Y') as month_label,
                    SUM(CASE WHEN type IN ('earn', 'bonus') THEN points ELSE 0 END) as earned,
                    SUM(CASE WHEN type = 'redeem' THEN ABS(points) ELSE 0 END) as redeemed
             FROM loyalty_points_log
             WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
             GROUP BY month_key
             ORDER BY month_key ASC"
        )->getResultArray();

        /** @var string[] $trendLabels */
        $trendLabels = [];
        /** @var int[] $trendEarned */
        $trendEarned = [];
        /** @var int[] $trendRedeemed */
        $trendRedeemed = [];
        foreach ($pointsTrend as $row) {
            $trendLabels[]   = $row['month_label'] ?? '';
            $trendEarned[]   = (int) ($row['earned'] ?? 0);
            $trendRedeemed[] = (int) ($row['redeemed'] ?? 0);
        }

        // Recent loyalty transactions (last 20)
        $recentTransactions = $this->loyaltyPointsLogModel
            ->withCustomer()
            ->orderBy('loyalty_points_log.id', 'DESC')
            ->findAll(20);

        // Voucher usage stats
        $totalVouchers = $this->voucherModel->countAllResults(false);
        $usedVouchers  = $this->voucherModel->where('is_used', 1)->countAllResults(false);
        $usageRate     = $totalVouchers > 0 ? round(($usedVouchers / $totalVouchers) * 100, 1) : 0;

        $data = [
            'pageTitle'          => 'Laporan Loyalitas',
            'totalIssued'        => $totalIssued,
            'totalRedeemed'      => $totalRedeemed,
            'activePoints'       => $activePoints,
            'membershipStats'    => $membershipStats ?? [],
            'trendLabels'        => $trendLabels,
            'trendEarned'        => $trendEarned,
            'trendRedeemed'      => $trendRedeemed,
            'recentTransactions' => $recentTransactions ?? [],
            'totalVouchers'      => $totalVouchers,
            'usedVouchers'       => $usedVouchers,
            'usageRate'          => $usageRate,
        ];

        return view('admin/report/loyalty', $data);
    }

    /**
     * Export laporan (PDF / Excel)
     *
     * @param string $type sales-pdf|sales-excel|customer-pdf|customer-excel|loyalty-pdf|loyalty-excel
     */
    public function export(string $type)
    {
        switch ($type) {
            case 'sales-pdf':
                return $this->exportSalesPdf();
            case 'sales-excel':
                return $this->exportSalesExcel();
            case 'customer-pdf':
                return $this->exportCustomerPdf();
            case 'customer-excel':
                return $this->exportCustomerExcel();
            case 'loyalty-pdf':
                return $this->exportLoyaltyPdf();
            case 'loyalty-excel':
                return $this->exportLoyaltyExcel();
            default:
                return redirect()->to('/admin/report')
                    ->with('toast', ['type' => 'error', 'message' => 'Jenis export tidak valid.']);
        }
    }

    /**
     * Export sales report as PDF
     */
    protected function exportSalesPdf()
    {
        /** @var string $startDate */
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        /** @var string $endDate */
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-d');

        $summary = $this->db->table('transactions')
            ->select('COUNT(*) as total_transactions, COALESCE(SUM(final_amount), 0) as total_revenue, COALESCE(AVG(final_amount), 0) as avg_transaction')
            ->select("COUNT(CASE WHEN payment_method != 'offline' THEN 1 END) as online_transactions")
            ->select("COALESCE(SUM(CASE WHEN payment_method != 'offline' THEN final_amount ELSE 0 END), 0) as online_revenue")
            ->select("COUNT(CASE WHEN payment_method = 'offline' THEN 1 END) as offline_transactions")
            ->select("COALESCE(SUM(CASE WHEN payment_method = 'offline' THEN final_amount ELSE 0 END), 0) as offline_revenue")
            ->whereIn('status', ['completed', 'paid'])
            ->where('transaction_date >=', $startDate . ' 00:00:00')
            ->where('transaction_date <=', $endDate . ' 23:59:59')
            ->get()
            ->getRowArray();

        $breakdown = $this->db->table('transactions')
            ->select("DATE(transaction_date) as period_label")
            ->select('COUNT(*) as transaction_count')
            ->select('COALESCE(SUM(final_amount), 0) as period_revenue')
            ->select("COUNT(CASE WHEN payment_method != 'offline' THEN 1 END) as online_count")
            ->select("COALESCE(SUM(CASE WHEN payment_method != 'offline' THEN final_amount ELSE 0 END), 0) as online_revenue")
            ->select("COUNT(CASE WHEN payment_method = 'offline' THEN 1 END) as offline_count")
            ->select("COALESCE(SUM(CASE WHEN payment_method = 'offline' THEN final_amount ELSE 0 END), 0) as offline_revenue")
            ->whereIn('status', ['completed', 'paid'])
            ->where('transaction_date >=', $startDate . ' 00:00:00')
            ->where('transaction_date <=', $endDate . ' 23:59:59')
            ->groupBy('period_label')
            ->orderBy('period_label', 'ASC')
            ->get()
            ->getResultArray();

        $topProducts = $this->db->table('transaction_items')
            ->select('products.name as product_name')
            ->select('GROUP_CONCAT(DISTINCT transaction_items.variant_selection SEPARATOR "||") as variant_selections')
            ->select('SUM(transaction_items.quantity) as total_qty')
            ->select('SUM(transaction_items.subtotal) as total_revenue')
            ->join('transactions', 'transactions.id = transaction_items.transaction_id')
            ->join('products', 'products.id = transaction_items.product_id')
            ->whereIn('transactions.status', ['completed', 'paid'])
            ->where('transactions.transaction_date >=', $startDate . ' 00:00:00')
            ->where('transactions.transaction_date <=', $endDate . ' 23:59:59')
            ->groupBy('transaction_items.product_id')
            ->orderBy('total_qty', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        $logoDataUri = $this->getLogoDataUri();
        $html = '<!DOCTYPE html><html><head><meta charset="utf-8">';
        $html .= '<style>body{font-family:sans-serif;font-size:11px}table{width:100%;border-collapse:collapse;margin-bottom:18px}th,td{border:1px solid #ddd;padding:5px 7px;text-align:left}th{background:#f3f4f6;font-weight:600}.summary td{font-size:12px;padding:6px 10px}h2{color:#E8A0BF;margin-bottom:4px}h3{margin-top:14px;margin-bottom:6px}.badge-online{color:#15803d;font-weight:bold}.badge-offline{color:#c2410c;font-weight:bold}.logo{margin-bottom:12px}.logo img{max-width:150px;height:auto}.sig-block{margin-top:40px;text-align:right}.sig-line{border-top:1px solid #000;width:200px;margin:0 auto 4px}.sig-name{font-weight:bold;font-size:11px}.sig-title{font-size:10px;color:#666}</style>';
        $html .= '</head><body>';
        $html .= $logoDataUri !== '' ? '<div class="logo"><img src="' . $logoDataUri . '" alt="Nurfa Beauty"></div>' : '';
        $html .= '<h2>Laporan Penjualan</h2>';
        $html .= '<p>Periode: ' . date('d M Y', strtotime($startDate)) . ' - ' . date('d M Y', strtotime($endDate)) . '</p>';

        $html .= '<h3>Ringkasan Penjualan</h3><table class="summary">';
        $html .= '<tr><td>Total Pendapatan</td><td><strong>Rp ' . number_format((int) ($summary['total_revenue'] ?? 0), 0, ',', '.') . '</strong></td></tr>';
        $html .= '<tr><td>Total Transaksi</td><td><strong>' . (int) ($summary['total_transactions'] ?? 0) . ' transaksi</strong></td></tr>';
        $html .= '<tr><td>Beli Online</td><td><span class="badge-online">' . (int) ($summary['online_transactions'] ?? 0) . ' transaksi (Rp ' . number_format((int) ($summary['online_revenue'] ?? 0), 0, ',', '.') . ')</span></td></tr>';
        $html .= '<tr><td>Beli Offline (Toko)</td><td><span class="badge-offline">' . (int) ($summary['offline_transactions'] ?? 0) . ' transaksi (Rp ' . number_format((int) ($summary['offline_revenue'] ?? 0), 0, ',', '.') . ')</span></td></tr>';
        $html .= '<tr><td>Rata-rata per Transaksi</td><td><strong>Rp ' . number_format((int) ($summary['avg_transaction'] ?? 0), 0, ',', '.') . '</strong></td></tr>';
        $html .= '</table>';

        $html .= '<h3>Breakdown Harian (Beli Online vs Offline)</h3><table>';
        $html .= '<tr><th>Tanggal</th><th>Total Transaksi</th><th>Beli Online</th><th>Beli Offline</th><th>Total Pendapatan</th></tr>';
        foreach ($breakdown as $row) {
            $html .= '<tr>';
            $html .= '<td>' . date('d M Y', strtotime($row['period_label'] ?? '')) . '</td>';
            $html .= '<td>' . (int) ($row['transaction_count'] ?? 0) . '</td>';
            $html .= '<td><span class="badge-online">' . (int) ($row['online_count'] ?? 0) . ' transaksi</span> (Rp ' . number_format((int) ($row['online_revenue'] ?? 0), 0, ',', '.') . ')</td>';
            $html .= '<td><span class="badge-offline">' . (int) ($row['offline_count'] ?? 0) . ' transaksi</span> (Rp ' . number_format((int) ($row['offline_revenue'] ?? 0), 0, ',', '.') . ')</td>';
            $html .= '<td><strong>Rp ' . number_format((int) ($row['period_revenue'] ?? 0), 0, ',', '.') . '</strong></td>';
            $html .= '</tr>';
        }
        $html .= '</table>';

        if (!empty($topProducts)) {
            $html .= '<h3>Produk Terlaris</h3><table>';
            $html .= '<tr><th>No</th><th>Produk</th><th>Qty Terjual</th><th>Total Pendapatan</th></tr>';
            foreach ($topProducts as $i => $prod) {
                $html .= '<tr>';
                $html .= '<td>' . ($i + 1) . '</td>';
                $variantLabels = [];
                foreach (explode('||', (string) ($prod['variant_selections'] ?? '')) as $variantJson) {
                    $variant = json_decode($variantJson, true);
                    if (is_array($variant) && !empty($variant)) {
                        $variantLabels[] = implode(', ', array_map(static fn ($key, $value) => $key . ': ' . $value, array_keys($variant), $variant));
                    }
                }
                $productLabel = esc($prod['product_name'] ?? '');
                if (!empty($variantLabels)) {
                    $productLabel .= '<br><small>Varian: ' . esc(implode(' | ', $variantLabels)) . '</small>';
                }
                $html .= '<td>' . $productLabel . '</td>';
                $html .= '<td>' . (int) ($prod['total_qty'] ?? 0) . '</td>';
                $html .= '<td>Rp ' . number_format((int) ($prod['total_revenue'] ?? 0), 0, ',', '.') . '</td>';
                $html .= '</tr>';
            }
            $html .= '</table>';
        }

        $html .= '<div class="sig-block">';
        $html .= '<div style="width:200px;margin-left:auto">';
        $html .= '<div class="sig-line"></div>';
        $html .= '<div class="sig-name">(........................)</div>';
        $html .= '<div class="sig-title">Penanggung Jawab</div>';
        $html .= '</div></div>';

        $html .= '</body></html>';

        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', false);
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="laporan-penjualan-' . $startDate . '-' . $endDate . '.pdf"')
            ->setBody($dompdf->output());
    }

    /**
     * Export sales report as Excel
     */
    protected function exportSalesExcel()
    {
        /** @var string $startDate */
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        /** @var string $endDate */
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-d');

        $summary = $this->db->table('transactions')
            ->select('COUNT(*) as total_transactions, COALESCE(SUM(final_amount), 0) as total_revenue, COALESCE(AVG(final_amount), 0) as avg_transaction')
            ->select("COUNT(CASE WHEN payment_method != 'offline' THEN 1 END) as online_transactions")
            ->select("COALESCE(SUM(CASE WHEN payment_method != 'offline' THEN final_amount ELSE 0 END), 0) as online_revenue")
            ->select("COUNT(CASE WHEN payment_method = 'offline' THEN 1 END) as offline_transactions")
            ->select("COALESCE(SUM(CASE WHEN payment_method = 'offline' THEN final_amount ELSE 0 END), 0) as offline_revenue")
            ->whereIn('status', ['completed', 'paid'])
            ->where('transaction_date >=', $startDate . ' 00:00:00')
            ->where('transaction_date <=', $endDate . ' 23:59:59')
            ->get()
            ->getRowArray();

        $breakdown = $this->db->table('transactions')
            ->select("DATE(transaction_date) as period_label")
            ->select('COUNT(*) as transaction_count')
            ->select('COALESCE(SUM(final_amount), 0) as period_revenue')
            ->select("COUNT(CASE WHEN payment_method != 'offline' THEN 1 END) as online_count")
            ->select("COALESCE(SUM(CASE WHEN payment_method != 'offline' THEN final_amount ELSE 0 END), 0) as online_revenue")
            ->select("COUNT(CASE WHEN payment_method = 'offline' THEN 1 END) as offline_count")
            ->select("COALESCE(SUM(CASE WHEN payment_method = 'offline' THEN final_amount ELSE 0 END), 0) as offline_revenue")
            ->whereIn('status', ['completed', 'paid'])
            ->where('transaction_date >=', $startDate . ' 00:00:00')
            ->where('transaction_date <=', $endDate . ' 23:59:59')
            ->groupBy('period_label')
            ->orderBy('period_label', 'ASC')
            ->get()
            ->getResultArray();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Title
        $sheet->setCellValue('A1', 'Laporan Penjualan Nurfa Beauty');
        $sheet->setCellValue('A2', 'Periode: ' . date('d M Y', strtotime($startDate)) . ' - ' . date('d M Y', strtotime($endDate)));
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        // Summary
        $sheet->setCellValue('A4', 'Total Pendapatan');
        $sheet->setCellValue('B4', (int) ($summary['total_revenue'] ?? 0));
        $sheet->setCellValue('A5', 'Total Transaksi');
        $sheet->setCellValue('B5', (int) ($summary['total_transactions'] ?? 0));
        $sheet->setCellValue('A6', 'Transaksi Online');
        $sheet->setCellValue('B6', (int) ($summary['online_transactions'] ?? 0));
        $sheet->setCellValue('C6', 'Rp ' . number_format((int) ($summary['online_revenue'] ?? 0)));
        $sheet->setCellValue('A7', 'Transaksi Offline');
        $sheet->setCellValue('B7', (int) ($summary['offline_transactions'] ?? 0));
        $sheet->setCellValue('C7', 'Rp ' . number_format((int) ($summary['offline_revenue'] ?? 0)));
        $sheet->setCellValue('A8', 'Rata-rata per Transaksi');
        $sheet->setCellValue('B8', (int) ($summary['avg_transaction'] ?? 0));
        $sheet->getStyle('B4:B8')->getNumberFormat()->setFormatCode('#,##0');

        // Breakdown header
        $sheet->setCellValue('A10', 'Tanggal');
        $sheet->setCellValue('B10', 'Total Transaksi');
        $sheet->setCellValue('C10', 'Transaksi Online');
        $sheet->setCellValue('D10', 'Omset Online');
        $sheet->setCellValue('E10', 'Transaksi Offline');
        $sheet->setCellValue('F10', 'Omset Offline');
        $sheet->setCellValue('G10', 'Total Pendapatan');
        $sheet->getStyle('A10:G10')->getFont()->setBold(true);

        $row = 11;
        foreach ($breakdown as $data) {
            $sheet->setCellValue('A' . $row, $data['period_label'] ?? '');
            $sheet->setCellValue('B' . $row, (int) ($data['transaction_count'] ?? 0));
            $sheet->setCellValue('C' . $row, (int) ($data['online_count'] ?? 0));
            $sheet->setCellValue('D' . $row, (int) ($data['online_revenue'] ?? 0));
            $sheet->setCellValue('E' . $row, (int) ($data['offline_count'] ?? 0));
            $sheet->setCellValue('F' . $row, (int) ($data['offline_revenue'] ?? 0));
            $sheet->setCellValue('G' . $row, (int) ($data['period_revenue'] ?? 0));
            $row++;
        }
        if (!empty($breakdown)) {
            $sheet->getStyle('D11:D' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('F11:F' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('G11:G' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0');
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        $fileName = 'laporan-penjualan-' . $startDate . '-' . $endDate . '.xlsx';
        $tempFile = WRITEPATH . 'cache/' . $fileName;
        $writer->save($tempFile);

        return $this->response
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->setBody(file_get_contents($tempFile));
    }

    /**
     * Export customer report as PDF
     */
    protected function exportCustomerPdf()
    {
        $totalCustomers = $this->customerModel->countAllResults(false);

        $newThisMonth = $this->customerModel
            ->where('customers.created_at >=', date('Y-m-01'))
            ->countAllResults(false);

        $membershipDistribution = $this->db->table('customers')
            ->select('membership_level, COUNT(*) as count')
            ->groupBy('membership_level')
            ->get()
            ->getResultArray();

        $topCustomers = $this->customerModel
            ->select('customers.*, users.name, users.email')
            ->join('users', 'users.id = customers.user_id')
            ->orderBy('customers.total_spending', 'DESC')
            ->limit(10)
            ->findAll();

        $logoDataUri = $this->getLogoDataUri();
        $html = '<!DOCTYPE html><html><head><meta charset="utf-8">';
        $html .= '<style>body{font-family:sans-serif;font-size:12px}table{width:100%;border-collapse:collapse;margin-bottom:20px}th,td{border:1px solid #ddd;padding:6px 8px;text-align:left}th{background:#f3f4f6;font-weight:600}h2{color:#E8A0BF}h3{margin-top:20px}.logo{margin-bottom:12px}.logo img{max-width:150px;height:auto}</style>';
        $html .= '</head><body>';
        $html .= $logoDataUri !== '' ? '<div class="logo"><img src="' . $logoDataUri . '" alt="Nurfa Beauty"></div>' : '';
        $html .= '<h2>Laporan Pelanggan Nurfa Beauty</h2>';
        $html .= '<p>Tanggal Cetak: ' . date('d M Y') . '</p>';

        $html .= '<h3>Ringkasan</h3><table>';
        $html .= '<tr><td>Total Pelanggan</td><td><strong>' . $totalCustomers . '</strong></td></tr>';
        $html .= '<tr><td>Pelanggan Baru (Bulan Ini)</td><td><strong>' . $newThisMonth . '</strong></td></tr>';
        $html .= '</table>';

        $html .= '<h3>Distribusi Membership</h3><table>';
        $html .= '<tr><th>Level</th><th>Jumlah</th></tr>';
        $levelLabels = ['bronze' => 'Bronze', 'silver' => 'Silver', 'gold' => 'Gold', 'platinum' => 'Platinum'];
        foreach ($membershipDistribution as $md) {
            $level = $md['membership_level'] ?? '';
            $html .= '<tr><td>' . ($levelLabels[$level] ?? ucfirst($level)) . '</td><td>' . (int) ($md['count'] ?? 0) . '</td></tr>';
        }
        $html .= '</table>';

        $html .= '<h3>Top 10 Pelanggan</h3><table>';
        $html .= '<tr><th>No</th><th>Nama</th><th>Email</th><th>Level</th><th>Total Belanja</th></tr>';
        foreach ($topCustomers as $i => $cust) {
            $level = $cust['membership_level'] ?? 'bronze';
            $html .= '<tr>';
            $html .= '<td>' . ($i + 1) . '</td>';
            $html .= '<td>' . esc($cust['name'] ?? '') . '</td>';
            $html .= '<td>' . esc($cust['email'] ?? '') . '</td>';
            $html .= '<td>' . ($levelLabels[$level] ?? ucfirst($level)) . '</td>';
            $html .= '<td>Rp ' . number_format((int) ($cust['total_spending'] ?? 0), 0, ',', '.') . '</td>';
            $html .= '</tr>';
        }
        $html .= '</table>';

        $html .= '</body></html>';

        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', false);
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="laporan-pelanggan-' . date('Y-m-d') . '.pdf"')
            ->setBody($dompdf->output());
    }

    /**
     * Export customer report as Excel
     */
    protected function exportCustomerExcel()
    {
        $totalCustomers = $this->customerModel->countAllResults(false);

        $newThisMonth = $this->customerModel
            ->where('customers.created_at >=', date('Y-m-01'))
            ->countAllResults(false);

        $topCustomers = $this->customerModel
            ->select('customers.*, users.name, users.email')
            ->join('users', 'users.id = customers.user_id')
            ->orderBy('customers.total_spending', 'DESC')
            ->limit(10)
            ->findAll();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Laporan Pelanggan Nurfa Beauty');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->setCellValue('A2', 'Tanggal: ' . date('d M Y'));

        $sheet->setCellValue('A4', 'Total Pelanggan');
        $sheet->setCellValue('B4', $totalCustomers);
        $sheet->setCellValue('A5', 'Pelanggan Baru (Bulan Ini)');
        $sheet->setCellValue('B5', $newThisMonth);

        $sheet->setCellValue('A7', 'No');
        $sheet->setCellValue('B7', 'Nama');
        $sheet->setCellValue('C7', 'Email');
        $sheet->setCellValue('D7', 'Level');
        $sheet->setCellValue('E7', 'Total Belanja');
        $sheet->getStyle('A7:E7')->getFont()->setBold(true);

        $levelLabels = ['bronze' => 'Bronze', 'silver' => 'Silver', 'gold' => 'Gold', 'platinum' => 'Platinum'];
        $row = 8;
        foreach ($topCustomers as $i => $cust) {
            $level = $cust['membership_level'] ?? 'bronze';
            $sheet->setCellValue('A' . $row, $i + 1);
            $sheet->setCellValue('B' . $row, $cust['name'] ?? '');
            $sheet->setCellValue('C' . $row, $cust['email'] ?? '');
            $sheet->setCellValue('D' . $row, $levelLabels[$level] ?? ucfirst($level));
            $sheet->setCellValue('E' . $row, (int) ($cust['total_spending'] ?? 0));
            $row++;
        }
        if (!empty($topCustomers)) {
            $sheet->getStyle('E8:E' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0');
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        $fileName = 'laporan-pelanggan-' . date('Y-m-d') . '.xlsx';
        $tempFile = WRITEPATH . 'cache/' . $fileName;
        $writer->save($tempFile);

        return $this->response
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->setBody(file_get_contents($tempFile));
    }

    /**
     * Export loyalty report as PDF
     */
    protected function exportLoyaltyPdf()
    {
        $issuedResult = $this->db->table('loyalty_points_log')
            ->select('COALESCE(SUM(points), 0) as total')
            ->whereIn('type', ['earn', 'bonus'])
            ->get()
            ->getRowArray();
        $totalIssued = (int) ($issuedResult['total'] ?? 0);

        $redeemedResult = $this->db->table('loyalty_points_log')
            ->select('COALESCE(SUM(points), 0) as total')
            ->where('type', 'redeem')
            ->get()
            ->getRowArray();
        $totalRedeemed = (int) ($redeemedResult['total'] ?? 0);

        $activePoints = $totalIssued - $totalRedeemed;

        $membershipStats = $this->db->query(
            "SELECT c.membership_level, COUNT(*) as customer_count,
                    COALESCE(AVG(c.loyalty_points), 0) as avg_points,
                    COALESCE(SUM(c.total_spending), 0) as total_spending
             FROM customers c GROUP BY c.membership_level"
        )->getResultArray();

        $totalVouchers = $this->voucherModel->countAllResults(false);
        $usedVouchers  = $this->voucherModel->where('is_used', 1)->countAllResults(false);

        $logoDataUri = $this->getLogoDataUri();
        $html = '<!DOCTYPE html><html><head><meta charset="utf-8">';
        $html .= '<style>body{font-family:sans-serif;font-size:12px}table{width:100%;border-collapse:collapse;margin-bottom:20px}th,td{border:1px solid #ddd;padding:6px 8px;text-align:left}th{background:#f3f4f6;font-weight:600}h2{color:#E8A0BF}h3{margin-top:20px}.logo{margin-bottom:12px}.logo img{max-width:150px;height:auto}</style>';
        $html .= '</head><body>';
        $html .= $logoDataUri !== '' ? '<div class="logo"><img src="' . $logoDataUri . '" alt="Nurfa Beauty"></div>' : '';
        $html .= '<h2>Laporan Loyalitas</h2>';
        $html .= '<p>Tanggal Cetak: ' . date('d M Y') . '</p>';

        $html .= '<h3>Ringkasan Poin</h3><table>';
        $html .= '<tr><td>Total Poin Dikeluarkan</td><td><strong>' . number_format($totalIssued, 0, ',', '.') . '</strong></td></tr>';
        $html .= '<tr><td>Total Poin Ditebus</td><td><strong>' . number_format($totalRedeemed, 0, ',', '.') . '</strong></td></tr>';
        $html .= '<tr><td>Poin Aktif</td><td><strong>' . number_format($activePoints, 0, ',', '.') . '</strong></td></tr>';
        $html .= '</table>';

        $html .= '<h3>Distribusi per Membership</h3><table>';
        $html .= '<tr><th>Level</th><th>Jumlah Pelanggan</th><th>Rata-rata Poin</th><th>Total Spending</th></tr>';
        $levelLabels = ['bronze' => 'Bronze', 'silver' => 'Silver', 'gold' => 'Gold', 'platinum' => 'Platinum'];
        foreach ($membershipStats as $ms) {
            $level = $ms['membership_level'] ?? '';
            $html .= '<tr>';
            $html .= '<td>' . ($levelLabels[$level] ?? ucfirst($level)) . '</td>';
            $html .= '<td>' . (int) ($ms['customer_count'] ?? 0) . '</td>';
            $html .= '<td>' . number_format((int) ($ms['avg_points'] ?? 0), 0, ',', '.') . '</td>';
            $html .= '<td>Rp ' . number_format((int) ($ms['total_spending'] ?? 0), 0, ',', '.') . '</td>';
            $html .= '</tr>';
        }
        $html .= '</table>';

        $html .= '<h3>Voucher</h3><table>';
        $html .= '<tr><td>Total Voucher</td><td><strong>' . $totalVouchers . '</strong></td></tr>';
        $html .= '<tr><td>Terpakai</td><td><strong>' . $usedVouchers . '</strong></td></tr>';
        $html .= '</table>';

        $html .= '</body></html>';

        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', false);
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="laporan-loyalitas-' . date('Y-m-d') . '.pdf"')
            ->setBody($dompdf->output());
    }

    /**
     * Export loyalty report as Excel
     */
    protected function exportLoyaltyExcel()
    {
        $issuedResult = $this->db->table('loyalty_points_log')
            ->select('COALESCE(SUM(points), 0) as total')
            ->whereIn('type', ['earn', 'bonus'])
            ->get()
            ->getRowArray();
        $totalIssued = (int) ($issuedResult['total'] ?? 0);

        $redeemedResult = $this->db->table('loyalty_points_log')
            ->select('COALESCE(SUM(points), 0) as total')
            ->where('type', 'redeem')
            ->get()
            ->getRowArray();
        $totalRedeemed = (int) ($redeemedResult['total'] ?? 0);

        $activePoints = $totalIssued - $totalRedeemed;

        $membershipStats = $this->db->query(
            "SELECT c.membership_level, COUNT(*) as customer_count,
                    COALESCE(AVG(c.loyalty_points), 0) as avg_points,
                    COALESCE(SUM(c.total_spending), 0) as total_spending
             FROM customers c GROUP BY c.membership_level"
        )->getResultArray();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Laporan Loyalitas Nurfa Beauty');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->setCellValue('A2', 'Tanggal: ' . date('d M Y'));

        $sheet->setCellValue('A4', 'Total Poin Dikeluarkan');
        $sheet->setCellValue('B4', $totalIssued);
        $sheet->setCellValue('A5', 'Total Poin Ditebus');
        $sheet->setCellValue('B5', $totalRedeemed);
        $sheet->setCellValue('A6', 'Poin Aktif');
        $sheet->setCellValue('B6', $activePoints);

        $sheet->setCellValue('A8', 'Level');
        $sheet->setCellValue('B8', 'Jumlah Pelanggan');
        $sheet->setCellValue('C8', 'Rata-rata Poin');
        $sheet->setCellValue('D8', 'Total Spending');
        $sheet->getStyle('A8:D8')->getFont()->setBold(true);

        $levelLabels = ['bronze' => 'Bronze', 'silver' => 'Silver', 'gold' => 'Gold', 'platinum' => 'Platinum'];
        $row = 9;
        foreach ($membershipStats as $ms) {
            $level = $ms['membership_level'] ?? '';
            $sheet->setCellValue('A' . $row, $levelLabels[$level] ?? ucfirst($level));
            $sheet->setCellValue('B' . $row, (int) ($ms['customer_count'] ?? 0));
            $sheet->setCellValue('C' . $row, (int) ($ms['avg_points'] ?? 0));
            $sheet->setCellValue('D' . $row, (int) ($ms['total_spending'] ?? 0));
            $row++;
        }
        if (!empty($membershipStats)) {
            $sheet->getStyle('C9:D' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0');
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        $fileName = 'laporan-loyalitas-' . date('Y-m-d') . '.xlsx';
        $tempFile = WRITEPATH . 'cache/' . $fileName;
        $writer->save($tempFile);

        return $this->response
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->setBody(file_get_contents($tempFile));
    }
}
