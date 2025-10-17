<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StripeController extends Controller
{
    public function index()
    {
        $data_array = []; // Define and pass this variable
        return view('service_bookings.index', compact('data_array'));
    }
 
    public function checkout()
    {

            \Stripe\Stripe::setApiKey(config('stripe.sk'));
            $session = \Stripe\Checkout\Session::create([
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => 'Test Product',
                        ],
                        'unit_amount' => 1000, // Amount in cents (10 USD)
                    ],
                    'quantity' => 1, // Required outside of price_data
                ]],
                'mode' => 'payment',
                'success_url' => route('success'),
                'cancel_url' => route('service_bookings.index'),
            ]);
            
            
            
            return redirect()->away($session->url);
       
    }
 
    public function success()
    {
       
        return view('service_bookings.index'); 
    }
}
