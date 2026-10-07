<?php

namespace App\Controllers;

use App\Models\ProductModel;

class ProductsControllers extends BaseController
{
    public function index()
    {
        $productModel = new ProductModel();
        
        // Get filter parameters
        $category = $this->request->getGet('category');
        $sort = $this->request->getGet('sort') ?: 'newest';
        $search = $this->request->getGet('search');
        
        // Build query
        $query = $productModel;
        
        if ($category) {
            $query = $query->where('category', $category);
        }
        
        if ($search) {
            $query = $query->groupStart()
                ->like('product_name', $search)
                ->orLike('description', $search)
                ->groupEnd();
        }
        
        // Apply sorting
        switch ($sort) {
            case 'price_low':
                $query = $query->orderBy('price', 'ASC');
                break;
            case 'price_high':
                $query = $query->orderBy('price', 'DESC');
                break;
            case 'name':
                $query = $query->orderBy('product_name', 'ASC');
                break;
            default:
                $query = $query->orderBy('created_at', 'DESC');
        }
        
        $data['products'] = $query->findAll();
        $data['category'] = $category;
        $data['sort'] = $sort;
        $data['search'] = $search;
        
        return view('products', $data);
    }

    public function category($category)
    {
        $productModel = new ProductModel();
        $data['products'] = $productModel->where('category', $category)->findAll();
        $data['category'] = $category;
        return view('products', $data);
    }

    public function details($id)
    {
        $productModel = new ProductModel();
        $product = $productModel->find($id);
        
        if (!$product) {
            return redirect()->to('/products')->with('error', 'Product not found');
        }
        
        // Get related products (same category, exclude current, in stock)
        $relatedProducts = $productModel
            ->where('category', $product['category'])
            ->where('id !=', $id)
            ->where('stock >', 0)
            ->limit(4)
            ->findAll();
        
        $data['product'] = $product;
        $data['relatedProducts'] = $relatedProducts;
        
        return view('product_details', $data);
    }

    public function search()
    {
        $keyword = $this->request->getGet('q');
        $productModel = new ProductModel();
        
        $data['products'] = $productModel
            ->like('product_name', $keyword)
            ->orLike('description', $keyword)
            ->findAll();
        $data['search'] = $keyword;
        
        return view('products', $data);
    }
}