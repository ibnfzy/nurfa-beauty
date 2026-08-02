<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PromotionModel;
use App\Models\VoucherModel;
use App\Models\CustomerModel;
use App\Models\NotificationModel;

class Promotion extends BaseController
{
    /** @var PromotionModel */
    protected $promotionModel;

    /** @var VoucherModel */
    protected $voucherModel;

    /** @var CustomerModel */
    protected $customerModel;

    /** @var NotificationModel */
    protected $notificationModel;

    public function __construct()
    {
        $this->promotionModel    = new PromotionModel();
        $this->voucherModel      = new VoucherModel();
        $this->customerModel     = new CustomerModel();
        $this->notificationModel = new NotificationModel();
    }

    /**
     * @return string
     */
    public function index()
    {
        /** @var string $search */
        $search         = $this->request->getGet('q');
        /** @var string $typeFilter */
        $typeFilter     = $this->request->getGet('type');
        /** @var string $segmentFilter */
        $segmentFilter  = $this->request->getGet('target_segment');

        $builder = $this->promotionModel
            ->select('promotions.*, COUNT(vouchers.id) as voucher_count')
            ->join('vouchers', 'vouchers.promotion_id = promotions.id', 'left')
            ->groupBy('promotions.id');

        if ($search) {
            $builder->like('promotions.name', $search);
        }

        if ($typeFilter) {
            $builder->where('promotions.type', $typeFilter);
        }

        if ($segmentFilter) {
            $builder->where('promotions.target_segment', $segmentFilter);
        }

        /** @var array $data */
        $data = [
            'pageTitle'      => 'Promosi',
            'promotions'     => $builder->orderBy('promotions.id', 'DESC')->paginate(15),
            'pager'          => $this->promotionModel->pager ?? null,
            'search'         => $search ?? '',
            'typeFilter'     => $typeFilter ?? '',
            'segmentFilter'  => $segmentFilter ?? '',
        ];

        return view('admin/promotion/index', $data);
    }

    /**
     * @return string
     */
    public function create()
    {
        /** @var array $data */
        $data = [
            'pageTitle' => 'Tambah Promosi',
            'customers' => $this->customerModel
                ->select('customers.*, users.name, users.email')
                ->join('users', 'users.id = customers.user_id')
                ->orderBy('users.name', 'ASC')
                ->findAll(),
        ];

        return view('admin/promotion/create', $data);
    }

    /**
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function store()
    {
        $type = $this->request->getPost('type');

        $rules = [
            'name'           => 'required|max_length[100]',
            'type'           => 'required|in_list[discount,voucher,flash_sale,birthday]',
            'discount_type'  => 'required|in_list[percentage,fixed]',
            'discount_value' => 'required|decimal|greater_than[0]',
            'min_purchase'   => 'permit_empty|decimal',
            'start_date'     => 'required|valid_date',
            'end_date'       => 'required|valid_date',
            'target_segment' => 'required|in_list[all,new,loyal,vip]',
            'is_active'      => 'permit_empty',
        ];

        $messages = [
            'name' => [
                'required'   => 'Nama promosi wajib diisi.',
                'max_length' => 'Nama promosi maksimal 100 karakter.',
            ],
            'type' => [
                'required' => 'Tipe promosi wajib dipilih.',
                'in_list'  => 'Tipe promosi tidak valid.',
            ],
            'discount_type' => [
                'required' => 'Tipe diskon wajib dipilih.',
                'in_list'  => 'Tipe diskon tidak valid.',
            ],
            'discount_value' => [
                'required'      => 'Nilai diskon wajib diisi.',
                'decimal'       => 'Nilai diskon harus berupa angka.',
                'greater_than'  => 'Nilai diskon harus lebih besar dari 0.',
            ],
            'min_purchase' => [
                'decimal' => 'Minimal pembelian harus berupa angka.',
            ],
            'start_date' => [
                'required'   => 'Tanggal mulai wajib diisi.',
                'valid_date' => 'Tanggal mulai tidak valid.',
            ],
            'end_date' => [
                'required'   => 'Tanggal berakhir wajib diisi.',
                'valid_date' => 'Tanggal berakhir tidak valid.',
            ],
            'target_segment' => [
                'required' => 'Target segmen wajib dipilih.',
                'in_list'  => 'Target segmen tidak valid.',
            ],
        ];

        // Tambahkan validasi voucher jika tipe voucher
        if ($type === 'voucher') {
            $rules['voucher_count']        = 'required|integer|greater_than[0]|less_than[101]';
            $rules['voucher_duration_days'] = 'required|integer|greater_than[0]';

            $messages['voucher_count'] = [
                'required'     => 'Jumlah voucher wajib diisi.',
                'integer'      => 'Jumlah voucher harus berupa angka bulat.',
                'greater_than' => 'Jumlah voucher harus lebih besar dari 0.',
                'less_than'    => 'Jumlah voucher maksimal 100.',
            ];
            $messages['voucher_duration_days'] = [
                'required'     => 'Masa berlaku voucher wajib diisi.',
                'integer'      => 'Masa berlaku voucher harus berupa angka bulat.',
                'greater_than' => 'Masa berlaku voucher harus lebih besar dari 0.',
            ];
        }

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('toast', ['type' => 'error', 'message' => 'Gagal menambahkan promosi. Periksa kembali data Anda.']);
        }

        // Simpan promosi
        /** @var int $promotionId */
        $promotionId = $this->promotionModel->insert([
            'name'            => $this->request->getPost('name'),
            'type'            => $this->request->getPost('type'),
            'discount_type'   => $this->request->getPost('discount_type'),
            'discount_value'  => $this->request->getPost('discount_value'),
            'min_purchase'    => $this->request->getPost('min_purchase') ?: 0,
            'start_date'      => $this->request->getPost('start_date'),
            'end_date'        => $this->request->getPost('end_date'),
            'target_segment'  => $this->request->getPost('target_segment'),
            'is_active'       => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        // Generate voucher jika tipe voucher
        if ($type === 'voucher') {
            /** @var int $voucherCount */
            $voucherCount = (int) $this->request->getPost('voucher_count');
            /** @var int $durationDays */
            $durationDays = (int) $this->request->getPost('voucher_duration_days');
            /** @var string $expiresAt */
            $expiresAt = date('Y-m-d H:i:s', strtotime('+' . $durationDays . ' days'));
            /** @var string $segment */
            $segment = $this->request->getPost('target_segment');

            // Ambik pelanggan target jika segmen spesifik
            /** @var array $targetCustomers */
            $targetCustomers = [];
            if ($segment !== 'all') {
                $targetCustomers = $this->customerModel
                    ->where('membership_level', $segment)
                    ->findAll();
            }

            /** @var array $codes */
            $codes = [];
            for ($i = 0; $i < $voucherCount; $i++) {
                // Generate kode unik
                /** @var string $code */
                $code = $this->generateUniqueVoucherCode($codes);
                $codes[] = $code;

                /** @var int|null $customerId */
                $customerId = null;
                if ($segment !== 'all' && isset($targetCustomers[$i])) {
                    $customerId = $targetCustomers[$i]['id'];
                }

                $this->voucherModel->insert([
                    'promotion_id' => $promotionId,
                    'customer_id'  => $customerId,
                    'code'         => $code,
                    'is_used'      => 0,
                    'expires_at'   => $expiresAt,
                ]);
            }
        }

        // Kirim notifikasi jika diaktifkan
        if ($this->request->getPost('send_notification')) {
            $this->sendPromotionNotification($promotionId);
        }

        return redirect()->to('/admin/promotions')
            ->with('toast', ['type' => 'success', 'message' => 'Promosi berhasil ditambahkan!']);
    }

    /**
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function edit($id)
    {
        /** @var array|null $promotion */
        $promotion = $this->promotionModel->find($id);

        if (!$promotion) {
            return redirect()->to('/admin/promotions')
                ->with('toast', ['type' => 'error', 'message' => 'Promosi tidak ditemukan.']);
        }

        /** @var array $data */
        $data = [
            'pageTitle'  => 'Edit Promosi',
            'promotion'  => $promotion,
            'vouchers'   => $this->voucherModel
                ->where('promotion_id', $id)
                ->withCustomer()
                ->findAll(),
            'customers'  => $this->customerModel
                ->select('customers.*, users.name, users.email')
                ->join('users', 'users.id = customers.user_id')
                ->orderBy('users.name', 'ASC')
                ->findAll(),
        ];

        return view('admin/promotion/edit', $data);
    }

    /**
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function update($id)
    {
        /** @var array|null $promotion */
        $promotion = $this->promotionModel->find($id);

        if (!$promotion) {
            return redirect()->to('/admin/promotions')
                ->with('toast', ['type' => 'error', 'message' => 'Promosi tidak ditemukan.']);
        }

        $rules = [
            'name'           => 'required|max_length[100]',
            'type'           => 'required|in_list[discount,voucher,flash_sale,birthday]',
            'discount_type'  => 'required|in_list[percentage,fixed]',
            'discount_value' => 'required|decimal|greater_than[0]',
            'min_purchase'   => 'permit_empty|decimal',
            'start_date'     => 'required|valid_date',
            'end_date'       => 'required|valid_date',
            'target_segment' => 'required|in_list[all,new,loyal,vip]',
            'is_active'      => 'permit_empty',
        ];

        $messages = [
            'name' => [
                'required'   => 'Nama promosi wajib diisi.',
                'max_length' => 'Nama promosi maksimal 100 karakter.',
            ],
            'type' => [
                'required' => 'Tipe promosi wajib dipilih.',
                'in_list'  => 'Tipe promosi tidak valid.',
            ],
            'discount_type' => [
                'required' => 'Tipe diskon wajib dipilih.',
                'in_list'  => 'Tipe diskon tidak valid.',
            ],
            'discount_value' => [
                'required'      => 'Nilai diskon wajib diisi.',
                'decimal'       => 'Nilai diskon harus berupa angka.',
                'greater_than'  => 'Nilai diskon harus lebih besar dari 0.',
            ],
            'min_purchase' => [
                'decimal' => 'Minimal pembelian harus berupa angka.',
            ],
            'start_date' => [
                'required'   => 'Tanggal mulai wajib diisi.',
                'valid_date' => 'Tanggal mulai tidak valid.',
            ],
            'end_date' => [
                'required'   => 'Tanggal berakhir wajib diisi.',
                'valid_date' => 'Tanggal berakhir tidak valid.',
            ],
            'target_segment' => [
                'required' => 'Target segmen wajib dipilih.',
                'in_list'  => 'Target segmen tidak valid.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('toast', ['type' => 'error', 'message' => 'Gagal memperbarui promosi. Periksa kembali data Anda.']);
        }

        $this->promotionModel->update($id, [
            'name'            => $this->request->getPost('name'),
            'type'            => $this->request->getPost('type'),
            'discount_type'   => $this->request->getPost('discount_type'),
            'discount_value'  => $this->request->getPost('discount_value'),
            'min_purchase'    => $this->request->getPost('min_purchase') ?: 0,
            'start_date'      => $this->request->getPost('start_date'),
            'end_date'        => $this->request->getPost('end_date'),
            'target_segment'  => $this->request->getPost('target_segment'),
            'is_active'       => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        return redirect()->to('/admin/promotions')
            ->with('toast', ['type' => 'success', 'message' => 'Promosi berhasil diperbarui!']);
    }

    /**
     * Generate kode voucher unik format NF-XXXXXX.
     *
     * @param array $existingCodes
     * @return string
     */
    protected function generateUniqueVoucherCode(array $existingCodes = []): string
    {
        do {
            $code = 'NF-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
        } while (
            in_array($code, $existingCodes) ||
            $this->voucherModel->where('code', $code)->first()
        );

        return $code;
    }

    /**
     * Kirim notifikasi promosi ke pelanggan target.
     *
     * @param int $promotionId
     * @return void
     */
    protected function sendPromotionNotification(int $promotionId): void
    {
        /** @var array|null $promotion */
        $promotion = $this->promotionModel->find($promotionId);

        if (!$promotion) {
            return;
        }

        /** @var string $title */
        $title = 'Promo Baru: ' . ($promotion['name'] ?? '');

        /** @var string $discountText */
        if (($promotion['discount_type'] ?? '') === 'percentage') {
            $discountText = 'Diskon ' . ($promotion['discount_value'] ?? 0) . '%';
        } else {
            $discountText = 'Diskon Rp ' . number_format((float) ($promotion['discount_value'] ?? 0), 0, ',', '.');
        }

        /** @var string $message */
        $message = $discountText . ' berlaku dari ' . date('d/m/Y', strtotime($promotion['start_date'] ?? 'now'))
            . ' hingga ' . date('d/m/Y', strtotime($promotion['end_date'] ?? 'now')) . '.';

        if (($promotion['min_purchase'] ?? 0) > 0) {
            $message .= ' Min. pembelian Rp ' . number_format((float) $promotion['min_purchase'], 0, ',', '.') . '.';
        }

        /** @var string $segment */
        $segment = $promotion['target_segment'] ?? 'all';

        /** @var array $customers */
        if ($segment === 'all') {
            $customers = $this->customerModel->findAll();
        } else {
            $customers = $this->customerModel
                ->where('membership_level', $segment)
                ->findAll();
        }

        foreach ($customers as $customer) {
            $this->notificationModel->insert([
                'customer_id' => $customer['id'],
                'title'       => $title,
                'message'     => $message,
                'type'        => 'promotion',
                'is_read'     => 0,
            ]);
        }
    }
}
