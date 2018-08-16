<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\PaypalController;
use App\Product;
use Paypal;

class ProductController extends Controller
{
    public function payNow($id){
        $product = Product::find($id);
        if(!empty($product)){
            $paypal_class = new PaypalController();
            return $paypal_class->getCheckout('USD',$product->title,$product->content,$product->price);
        }
        return back();
    }

    public function getDone(Request $request){

         $id = $request->get('paymentId');
        $token = $request->get('token');
        $payer_id = $request->get('PayerID');
        // return $request;

       $paypal_class = new PaypalController();
 //    return dd( $paypal_class->checkPayment($id,$token,$payer_id));
        $method =  $paypal_class->checkPayment($id,$token,$payer_id);
        if(!empty($method->state)){
            return view('paypal_checkout',[
            'method' =>$method
            ]);
        }
        return redirect('/');

   
        
    }

    public function getCancel(){
        
    }

}
