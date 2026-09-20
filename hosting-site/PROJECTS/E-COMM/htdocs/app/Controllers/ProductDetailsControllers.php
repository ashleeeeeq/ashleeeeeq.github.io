<?php

namespace App\Controllers;

use App\Models\ProductModel;

class ProductDetailsControllers extends BaseController
{
    public function index($id)
    {
        $productModel = new ProductModel();
        $data['product'] = $productModel->getProductDetails($id);

        if (!$data['product']) {
            return redirect()->to('/storefront')->with('error', 'Product not found');
        }

        // Get variations
        $data['variations'] = $productModel->getVariations($id);

        // Get related products
        $data['relatedProducts'] = $productModel
            ->where('category', $data['product']['category'])
            ->where('id !=', $id)
            ->where('stock >', 0)
            ->limit(4)
            ->findAll();

        return view('product_details', $data);
    }
}