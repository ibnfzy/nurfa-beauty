<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\ProductModel;

class Category extends BaseController
{
    protected $categoryModel;
    protected $productModel;

    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
        $this->productModel  = new ProductModel();
    }

    public function index()
    {
        $search = $this->request->getGet('q');

        $builder = $this->categoryModel->select('categories.*, COUNT(products.id) as product_count')
            ->join('products', 'products.category_id = categories.id', 'left')
            ->groupBy('categories.id');

        if ($search) {
            $builder->like('categories.name', $search);
        }

        $data = [
            'pageTitle'  => 'Kategori Produk',
            'categories' => $builder->orderBy('categories.id', 'DESC')->paginate(10),
            'pager'      => $this->categoryModel->pager,
            'search'     => $search,
        ];

        return view('admin/category/index', $data);
    }

    public function store()
    {
        $rules = [
            'name'        => 'required|max_length[100]',
            'description' => 'permit_empty',
        ];

        $messages = [
            'name' => [
                'required'    => 'Nama kategori wajib diisi.',
                'max_length'  => 'Nama kategori maksimal 100 karakter.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('toast', ['type' => 'error', 'message' => 'Gagal menambahkan kategori. Periksa kembali data Anda.']);
        }

        $this->categoryModel->save([
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
        ]);

        return redirect()->to('/admin/categories')
            ->with('toast', ['type' => 'success', 'message' => 'Kategori berhasil ditambahkan!']);
    }

    public function update($id)
    {
        $category = $this->categoryModel->find($id);

        if (!$category) {
            return redirect()->to('/admin/categories')
                ->with('toast', ['type' => 'error', 'message' => 'Kategori tidak ditemukan.']);
        }

        $rules = [
            'name'        => 'required|max_length[100]',
            'description' => 'permit_empty',
        ];

        $messages = [
            'name' => [
                'required'    => 'Nama kategori wajib diisi.',
                'max_length'  => 'Nama kategori maksimal 100 karakter.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('toast', ['type' => 'error', 'message' => 'Gagal memperbarui kategori. Periksa kembali data Anda.']);
        }

        $this->categoryModel->update($id, [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
        ]);

        return redirect()->to('/admin/categories')
            ->with('toast', ['type' => 'success', 'message' => 'Kategori berhasil diperbarui!']);
    }

    public function delete($id)
    {
        $category = $this->categoryModel->find($id);

        if (!$category) {
            return redirect()->to('/admin/categories')
                ->with('toast', ['type' => 'error', 'message' => 'Kategori tidak ditemukan.']);
        }

        $productCount = $this->productModel->where('category_id', $id)->countAllResults();

        if ($productCount > 0) {
            return redirect()->to('/admin/categories')
                ->with('toast', ['type' => 'error', 'message' => 'Kategori tidak dapat dihapus karena masih memiliki ' . $productCount . ' produk.']);
        }

        $this->categoryModel->delete($id);

        return redirect()->to('/admin/categories')
            ->with('toast', ['type' => 'success', 'message' => 'Kategori berhasil dihapus!']);
    }
}
