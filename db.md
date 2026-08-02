# Database Design - Nurfa Beauty Shop

---

## Tabel Utama

```sql
-- Tabel Users (Admin & Pelanggan)
users
- id (PK)
- name
- email
- password
- role (admin/customer)
- phone
- address
- birth_date
- created_at, updated_at

-- Tabel Pelanggan (Extended Profile)
customers
- id (PK)
- user_id (FK)
- loyalty_points
- membership_level (bronze/silver/gold/platinum)
- total_spending
- first_purchase_date
- last_purchase_date
- created_at, updated_at

-- Tabel Kategori Produk
categories
- id (PK)
- name (perawatan rambut/wajah/badan)
- description
- created_at, updated_at

-- Tabel Produk
products
- id (PK)
- category_id (FK)
- name
- description
- price
- stock
- image
- is_active
- is_bundle (0=produk biasa, 1=bundle)
- bundle_products (JSON: [{product_id, quantity}, ...])
- bundle_discount (persentase diskon bundle)
- created_at, updated_at

-- Tabel Transaksi
transactions
- id (PK)
- customer_id (FK)
- shipping_address_id (FK)
- transaction_code
- transaction_date
- total_amount
- shipping_cost
- discount_amount
- final_amount
- payment_method (transfer)
- payment_proof (file path bukti transfer)
- payment_status (pending/verified/rejected)
- payment_verified_at
- payment_verified_by (admin user_id)
- payment_rejection_reason
- status (pending_payment/paid/processing/shipped/completed/cancelled)
- notes
- created_at, updated_at

-- Tabel Alamat Pengiriman
shipping_addresses
- id (PK)
- customer_id (FK)
- label (Rumah/Kantor/etc)
- recipient_name
- phone
- address (alamat lengkap)
- province
- city
- district
- postal_code
- is_default
- created_at, updated_at

-- Tabel Rekening Bank (untuk transfer)
bank_accounts
- id (PK)
- bank_name
- account_number
- account_name
- is_active
- created_at, updated_at

-- Tabel Detail Transaksi
transaction_items
- id (PK)
- transaction_id (FK)
- product_id (FK)
- quantity
- price
- subtotal
- created_at, updated_at

-- Tabel Promosi
promotions
- id (PK)
- name
- type (discount/voucher/flash_sale/birthday)
- discount_type (percentage/fixed)
- discount_value
- min_purchase
- start_date
- end_date
- target_segment (all/new/loyal/vip)
- is_active
- created_at, updated_at

-- Tabel Voucher
vouchers
- id (PK)
- promotion_id (FK)
- customer_id (FK, nullable)
- code
- is_used
- used_at
- expires_at
- created_at, updated_at

-- Tabel Poin Loyalitas
loyalty_points_log
- id (PK)
- customer_id (FK)
- points (positive=earn, negative=redeem)
- type (earn/redeem/expired)
- description
- transaction_id (FK, nullable)
- created_at

-- Tabel Ulasan Produk
reviews
- id (PK)
- customer_id (FK)
- product_id (FK)
- rating (1-5)
- comment
- created_at, updated_at

-- Tabel Notifikasi
notifications
- id (PK)
- customer_id (FK)
- title
- message
- type (promotion/birthday/loyalty/general)
- is_read
- created_at

-- Tabel Wishlist
wishlists
- id (PK)
- customer_id (FK)
- product_id (FK)
- created_at
```

---

## Struktur Migrations

```
app/Database/Migrations/
├── 001_create_users_table.php
├── 002_create_customers_table.php
├── 003_create_categories_table.php
├── 004_create_products_table.php
├── 005_create_transactions_table.php
├── 006_create_transaction_items_table.php
├── 007_create_promotions_table.php
├── 008_create_vouchers_table.php
├── 009_create_loyalty_points_log_table.php
├── 010_create_reviews_table.php
├── 011_create_notifications_table.php
└── 012_create_wishlists_table.php
```
