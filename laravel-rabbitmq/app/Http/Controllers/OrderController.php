<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessOrder;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function order(Request $request)
    {

        $data = [
            'name' => 'Yan',
            'email' => 'yan.pantoja@liberfly.com.br',
            'cc' => '400454322322367',
            'exp' => '10/19',
            'valor' => 1000
        ];

        ProcessOrder::dispatch($data)->onConnection('rabbitmq');
    }
}
