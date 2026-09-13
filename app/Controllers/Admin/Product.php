<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\ProductModel;

class Product extends BaseController
{
    protected ProductModel $productModel;
    protected CategoryModel $categoryModel;

    public function __construct()
    {
        $this->productModel  = new ProductModel();
        $this->categoryModel = new CategoryModel();
    }

    public function index()
    {
        $search   = $this->request->getGet('q');
        $category = $this->request->getGet('category');

        $builder = $this->productModel->withCategory();

        if ($search) {
            $builder->like('products.name', $search);
        }

        if ($category) {
            $builder->where('products.category_id', $category);
        }

        $data = [
            'pageTitle'  => 'Produk',
            'products'   => $builder->orderBy('products.id', 'DESC')->paginate(10),
            'pager'      => $this->productModel->pager,
            'search'     => $search,
            'category'   => $category,
            'categories' => $this->categoryModel->orderBy('name', 'ASC')->findAll(),
        ];

        return view('admin/product/index', $data);
    }

    public function create()
    {
        $data = [
            'pageTitle'   => 'Tambah Produk',
            'categories'  => $this->categoryModel->orderBy('name', 'ASC')->findAll(),
            'allProducts' => $this->productModel->where('is_active', 1)->where('is_bundle', 0)->orderBy('name', 'ASC')->findAll(),
        ];

        return view('admin/product/create', $data);
    }

    public function store()
    {
        $rules = [
            'name'        => 'required|max_length[200]',
            'category_id' => 'required|integer',
            'price'       => 'required|decimal',
            'stock'       => 'required|integer',
            'description' => 'permit_empty',
            'image'       => 'uploaded[image]|max_size[image,2048]|is_image[image]|mime_in[image,image/jpg,image/jpeg,image/png,image/webp]',
        ];

        $messages = [
            'name' => [
                'required'   => 'Nama produk wajib diisi.',
                'max_length' => 'Nama produk maksimal 200 karakter.',
            ],
            'category_id' => [
                'required' => 'Kategori wajib dipilih.',
                'integer'  => 'Kategori tidak valid.',
            ],
            'price' => [
                'required' => 'Harga wajib diisi.',
                'decimal'  => 'Harga harus berupa angka.',
            ],
            'stock' => [
                'required' => 'Stok wajib diisi.',
                'integer'  => 'Stok harus berupa angka bulat.',
            ],
            'image' => [
                'uploaded'  => 'Gambar produk wajib diunggah.',
                'max_size'  => 'Ukuran gambar maksimal 2MB.',
                'is_image'  => 'File yang diunggah harus berupa gambar.',
                'mime_in'   => 'Format gambar harus JPG, JPEG, PNG, atau WEBP.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('toast', ['type' => 'error', 'message' => 'Gagal menambahkan produk. Periksa kembali data Anda.']);
        }

        $image = $this->request->getFile('image');
        $imageName = $image->getRandomName();
        $image->move(FCPATH . 'uploads/products', $imageName);

        $variants = $this->normalizeVariants($this->request->getPost('variants'));

        $this->productModel->save([
            'category_id'    => $this->request->getPost('category_id'),
            'name'           => $this->request->getPost('name'),
            'description'    => $this->request->getPost('description'),
            'price'          => $this->request->getPost('price'),
            'stock'          => $this->request->getPost('stock'),
            'image'          => $imageName,
            'is_active'      => $this->request->getPost('is_active') ?? 1,
            'is_bundle'      => $this->request->getPost('is_bundle') ? 1 : 0,
            'bundle_products' => $this->request->getPost('is_bundle') ? $this->request->getPost('bundle_products') : null,
            'bundle_discount' => $this->request->getPost('bundle_discount') ?: 0,
            'variants'        => $variants,
        ]);

        return redirect()->to('/admin/products')
            ->with('toast', ['type' => 'success', 'message' => 'Produk berhasil ditambahkan!']);
    }

    public function edit(int $id)
    {
        $product = $this->productModel->find($id);

        if (!$product) {
            return redirect()->to('/admin/products')
                ->with('toast', ['type' => 'error', 'message' => 'Produk tidak ditemukan.']);
        }

        $data = [
            'pageTitle'   => 'Edit Produk',
            'product'     => $product,
            'categories'  => $this->categoryModel->orderBy('name', 'ASC')->findAll(),
            'allProducts' => $this->productModel->where('is_active', 1)->where('id !=', $id)->orderBy('name', 'ASC')->findAll(),
        ];

        return view('admin/product/edit', $data);
    }

    public function update(int $id)
    {
        $product = $this->productModel->find($id);

        if (!$product) {
            return redirect()->to('/admin/products')
                ->with('toast', ['type' => 'error', 'message' => 'Produk tidak ditemukan.']);
        }

        $rules = [
            'name'        => 'required|max_length[200]',
            'category_id' => 'required|integer',
            'price'       => 'required|decimal',
            'stock'       => 'required|integer',
            'description' => 'permit_empty',
            'image'       => 'permit_empty|max_size[image,2048]|is_image[image]|mime_in[image,image/jpg,image/jpeg,image/png,image/webp]',
        ];

        $messages = [
            'name' => [
                'required'   => 'Nama produk wajib diisi.',
                'max_length' => 'Nama produk maksimal 200 karakter.',
            ],
            'category_id' => [
                'required' => 'Kategori wajib dipilih.',
                'integer'  => 'Kategori tidak valid.',
            ],
            'price' => [
                'required' => 'Harga wajib diisi.',
                'decimal'  => 'Harga harus berupa angka.',
            ],
            'stock' => [
                'required' => 'Stok wajib diisi.',
                'integer'  => 'Stok harus berupa angka bulat.',
            ],
            'image' => [
                'max_size' => 'Ukuran gambar maksimal 2MB.',
                'is_image' => 'File yang diunggah harus berupa gambar.',
                'mime_in'  => 'Format gambar harus JPG, JPEG, PNG, atau WEBP.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('toast', ['type' => 'error', 'message' => 'Gagal memperbarui produk. Periksa kembali data Anda.']);
        }

        $isBundle = $this->request->getPost('is_bundle') ? 1 : 0;
        $variants = $this->normalizeVariants($this->request->getPost('variants'));

        $data = [
            'category_id'    => $this->request->getPost('category_id'),
            'name'           => $this->request->getPost('name'),
            'description'    => $this->request->getPost('description'),
            'price'          => $this->request->getPost('price'),
            'stock'          => $this->request->getPost('stock'),
            'is_active'      => $this->request->getPost('is_active') ?? 1,
            'is_bundle'      => $isBundle,
            'bundle_products' => $isBundle ? $this->request->getPost('bundle_products') : null,
            'bundle_discount' => $this->request->getPost('bundle_discount') ?: 0,
            'variants'        => $variants,
        ];

        $image = $this->request->getFile('image');

        if ($image && $image->isValid() && !$image->hasMoved()) {
            // Delete old image
            if ($product['image']) {
                $oldPath = FCPATH . 'uploads/products/' . $product['image'];
                if (is_file($oldPath)) {
                    unlink($oldPath);
                }
            }

            $imageName = $image->getRandomName();
            $image->move(FCPATH . 'uploads/products', $imageName);
            $data['image'] = $imageName;
        }

        $this->productModel->update($id, $data);

        return redirect()->to('/admin/products')
            ->with('toast', ['type' => 'success', 'message' => 'Produk berhasil diperbarui!']);
    }

    protected function normalizeVariants(?string $variants): ?string
    {
        if (!$variants) {
            return null;
        }

        $items = json_decode($variants, true);
        if (!is_array($items)) {
            return null;
        }

        $merged = [];
        foreach ($items as $item) {
            $name  = trim((string) ($item['attribute_name'] ?? ''));
            $value = trim((string) ($item['attribute_value'] ?? ''));
            if ($name === '' || $value === '') {
                continue;
            }
            if (!isset($merged[$name])) {
                $merged[$name] = [];
            }
            if (!in_array($value, $merged[$name], true)) {
                $merged[$name][] = $value;
            }
        }

        return $merged ? json_encode($merged, JSON_UNESCAPED_UNICODE) : null;
    }

    public function delete(int $id)
    {
        $product = $this->productModel->find($id);

        if (!$product) {
            return redirect()->to('/admin/products')
                ->with('toast', ['type' => 'error', 'message' => 'Produk tidak ditemukan.']);
        }

        // Delete image file
        if ($product['image']) {
            $imagePath = FCPATH . 'uploads/products/' . $product['image'];
            if (is_file($imagePath)) {
                unlink($imagePath);
            }
        }

        $this->productModel->delete($id);

        return redirect()->to('/admin/products')
            ->with('toast', ['type' => 'success', 'message' => 'Produk berhasil dihapus!']);
    }
}
