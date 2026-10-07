<?php

namespace App\Controllers;

class LandingControllers extends BaseController
{	
    // Display the landing page
    public function index()
    {
        helper('url');
        
        // If user is already logged in, redirect to appropriate dashboard
        if (session()->get('isLoggedIn')) {
            if (session()->get('role') === 'admin') {
                return redirect()->to('/admin/dashboard');
            } else {
                return redirect()->to('/storefront');
            }
        }

        // Get featured products from database
        $productModel = new \App\Models\ProductModel();
        
        // Get products that are featured (new, trending, best_seller)
        $data['featuredProducts'] = $productModel
            ->where('featured !=', 'none')
            ->orderBy('created_at', 'DESC')
            ->limit(4)
            ->findAll();

        return view('landing_page', $data);
    }
}