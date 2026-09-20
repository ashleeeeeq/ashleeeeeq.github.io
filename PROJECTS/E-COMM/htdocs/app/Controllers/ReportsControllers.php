<?php

namespace App\Controllers;

use App\Models\OrderModel;

class ReportsControllers extends BaseController
{

    public function index()
    {

        $orderModel = new OrderModel();

        $data['dailySales'] = $orderModel
            ->where('DATE(created_at)', date('Y-m-d'))
            ->selectSum('total_price')
            ->first();


        $data['monthlySales'] = $orderModel
            ->where('MONTH(created_at)', date('m'))
            ->selectSum('total_price')
            ->first();


        return view('admin_reports', $data);

    }

}