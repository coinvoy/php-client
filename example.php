<?php

	require_once('./coinvoy.php');

	function createPayment($amount, $currency, $address) {
		$coinvoy = new Coinvoy();

		$payment = $coinvoy->payment($amount, $currency, $address);

		var_dump($payment);

		if ($payment['success']) {
			$status = $coinvoy->status($payment['id']);

			var_dump($status);
		}
	}
	
	function createEscrow($amount, $currency, $address, $returnAddress) {
		$coinvoy = new Coinvoy();

		$escrow = $coinvoy->payment($amount, $currency, $address, array( 'escrow'        => true,
		                                                                 'returnAddress' => $returnAddress ));

		var_dump($escrow);

		if ($escrow['success']) {
			$invoice = $coinvoy->invoice($escrow['id']);

			var_dump($invoice);
		}
	}

	function getDonation($address) {
		$coinvoy = new Coinvoy();

		$donation = $coinvoy->donation($address);

		var_dump($donation);
	}
    

	function getButton($amount, $currency, $address) {
		$coinvoy = new Coinvoy();

		$button = $coinvoy->button($amount, $currency, $address);

		var_dump($button);
	}
	
	function freeEscrow($key) {
		$coinvoy = new Coinvoy();
		
		$result = $coinvoy->freeEscrow($key);
		
		var_dump($result);
	}
	
	function cancelEscrow($key) {
		$coinvoy = new Coinvoy();
		
		$result = $coinvoy->cancelEscrow($key);
		
		var_dump($result);
	}
	
	$amount        = 0.0005;
	$address       = "receiving address";
	$returnAddress = "return address";
	$currency      = "BTC";
	$key           = "key returned from escrow payment";

	//createPayment($amount, $currency, $address);
	//createEscrow($amount, $currency, $address, $returnAddress);
	//getDonation($address);
	//getButton($amount, $currency, $address);
	//freeEscrow($key);
	cancelEscrow($key);

?>
