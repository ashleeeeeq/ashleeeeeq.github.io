<?php

namespace App\Controllers;

use App\Models\ProductModel;

class AdminProductsControllers extends BaseController
{

    public function index()
    {

        $productModel = new ProductModel();

        $data['products'] = $productModel->findAll();

        return view('admin_products', $data);

    }


    public function create()
    {
        return view('admin_product_create');
    }


    public function store()
    {

        $productModel = new ProductModel();

        $productModel->save([

            'product_name' => $this->request->getPost('product_name'),
            'description' => $this->request->getPost('description'),
            'price' => $this->request->getPost('price'),
            'stock' => $this->request->getPost('stock'),
            'featured' => $this->request->getPost('featured')

        ]);

        return redirect()->to('/admin/products');

    }


    public function delete($id)
    {

        $productModel = new ProductModel();

        $productModel->delete($id);

        return redirect()->to('/admin/products');

    }

}