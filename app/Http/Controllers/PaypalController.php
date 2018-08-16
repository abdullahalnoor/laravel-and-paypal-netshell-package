<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Netshell\Paypal\Facades\Paypal;
use Redirect;

class PaypalController extends Controller
{
    private $_apiContext;

    public function __construct()
    {
        $this->_apiContext = PayPal::ApiContext(
            config('paypal.client_id'),
            config('paypal.secret'));
		
		$this->_apiContext->setConfig(array(
			'mode' => config('paypal.mode'),
			'service.EndPoint' => config('paypal.link_mode'),
			'http.ConnectionTimeOut' => config('timeout'),
			'log.LogEnabled' => config('paypal.enable_log'),
			'log.FileName' => storage_path('logs/paypal.log'),
			'log.LogLevel' => 'FINE'
		));

    }

    public function getCheckout($currency,$title,$description,$price)
{
	$payer = PayPal::Payer();
	$payer->setPaymentMethod('paypal');

	$amount = PayPal:: Amount();
	// $amount->setName($title);
	$amount->setCurrency($currency);
	$amount->setTotal($price); // This is the simple way,
	// you can alternatively describe everything in the order separately;
	// Reference the PayPal PHP REST SDK for details.

	$transaction = PayPal::Transaction();
	$transaction->setAmount($amount);
	$transaction->setDescription($description);

	$redirectUrls = PayPal:: RedirectUrls();
	$redirectUrls->setReturnUrl(url('/paypal/done'));
	$redirectUrls->setCancelUrl(url('/paypal/cancel'));

	$payment = PayPal::Payment();
	$payment->setIntent('sale');
	$payment->setPayer($payer);
	$payment->setRedirectUrls($redirectUrls);
	$payment->setTransactions(array($transaction));

	$response = $payment->create($this->_apiContext);
	$redirectUrl = $response->links[1]->href;
	
	return Redirect::to( $redirectUrl );
}

public function checkPayment($id,$token,$payer_id){
			
        
        $payment = PayPal::getById($id, $this->_apiContext);

        $paymentExecution = PayPal::PaymentExecution();

        $paymentExecution->setPayerId($payer_id);
        // $executePayment = $payment->execute($paymentExecution, $this->_apiContext);
      return $payment->execute($paymentExecution, $this->_apiContext);

        
        
}




}
