<?php

namespace App\Http\Middleware;

use Closure;
use Session;

class roeCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
            $category_array = array(1,1); 
            $category_value = implode(',', $category_array);
        $category_add_array = array(1,2);
        $category_add = implode(',', $category_add_array);
        $service_array = array(2,1); 
        $service_value = implode(',', $service_array);
        $service_add_array = array(2,2); 
        $service_add = implode(',', $service_add_array); 
        $customer_array = array(3,1); 
        $customer_value = implode(',', $customer_array);
        $customer_add_array = array(3,2);
        $customer_add = implode(',', $customer_add_array);
        $boking_array = array(4,1);
        $boking_value = implode(',', $boking_array);
        $boking_add_array = array(4,2);
        $boking_add = implode(',', $boking_add_array);
        $graph_report_array = array(5,7);
        $graph_report = implode(',', $graph_report_array);
        $customer_report_array = array(5,3);
        $customer_report = implode(',', $customer_report_array);
        $expense_report_array = array(5,5);
        $expense_report = implode(',', $expense_report_array);
        $booking_report_array = array(5,1);
        $booking_report = implode(',', $booking_report_array);

        $user_array = array(6,1); 
        $user_value = implode(',', $user_array);
        
        $role_array = array(7,1); 
        $role_value = implode(',', $role_array);

        $role_add_array = array(7,2);
        $role_add = implode(',', $role_add_array);

        $quotation_array = array(10,1);
        $quotation_value = implode(',', $quotation_array);
        $quotation_send_array = array(10,2);
        $quotation_send = implode(',', $quotation_send_array);

        $mailing_array = array(11,1);
        $mailing_value = implode(',', $mailing_array);
        
        $expense_types_array = array(8,1); 
        $expense_types_value = implode(',', $expense_types_array); 
        $expense_types_add_array = array(8,2); 
        $expense_types_add = implode(',', $expense_types_add_array);

        $expenses_array = array(9,1);
        $expenses_value = implode(',', $expenses_array);
        $expenses_add_array = array(9,2);
        $expenses_add = implode(',', $expenses_add_array);

        $template_array = array(15,1);
        $template_value = implode(',', $template_array);
        
        $marketing_array = array(17,1);
        $marketing_value = implode(',', $marketing_array);

        if(in_array($category_value, session()->get('all_rp')['0'])) {
            return $next($request); 
        }
        if(in_array($category_add_array, session()->get('all_rp')['0'])) {
            return $next($request);
        }
        if(in_array($service_array, session()->get('all_rp')['0'])) {
            return $next($request);
            exit();
        }
        if(in_array($service_add, session()->get('all_rp')['0'])) {
            return $next($request);
            exit();
        }
        if(in_array($customer_value, session()->get('all_rp')['0'])) {
            return $next($request);
            exit();
        }
        if(in_array($customer_add, session()->get('all_rp')['0'])) {
            return $next($request);
            exit();
        }
        if(in_array($boking_value, session()->get('all_rp')['0'])) {
            return $next($request);
            exit();
        }
        if(in_array($boking_add, session()->get('all_rp')['0'])) {
            return $next($request);
            exit();
        }
        if(in_array($graph_report, session()->get('all_rp')['0'])) {
            return $next($request);
            exit();
        }
        if(in_array($customer_report, session()->get('all_rp')['0'])) {
            return $next($request);
        }
        if(in_array($expense_report, session()->get('all_rp')['0'])) {
            return $next($request);
            exit();
        }
        if(in_array($booking_report, session()->get('all_rp')['0'])) {
            return $next($request);
        }
        if(in_array($user_value, session()->get('all_rp')['0'])) {
            return $next($request);
        }
        if(in_array($role_value, session()->get('all_rp')['0'])) {
            return $next($request);
        }
        if(in_array($role_add, session()->get('all_rp')['0'])) {
            return $next($request);
        }
        if(in_array($quotation_value, session()->get('all_rp')['0'])) {
            return $next($request);
        }
        if(in_array($mailing_value, session()->get('all_rp')['0'])) {
            return $next($request);
        }
        if(in_array($mailing_value, session()->get('all_rp')['0'])) {
            return $next($request);
        }
        if(in_array($expense_types_value, session()->get('all_rp')['0'])) {
            return $next($request);
        }
        if(in_array($expense_types_add, session()->get('all_rp')['0'])) {
            return $next($request);
        }
        if(in_array($expenses_value, session()->get('all_rp')['0'])) {
            return $next($request);
        }
        if(in_array($expenses_add, session()->get('all_rp')['0'])) {
            return $next($request);
            exit();
        }
        if(in_array($template_value, session()->get('all_rp')['0'])) {
            return $next($request);
        }
        if(in_array($template_value, session()->get('all_rp')['0'])) {
            return $next($request);
        }
        
        else{
            return redirect('404');
        }
       
    }
   
}
