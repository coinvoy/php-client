<?php

	require_once('./coinvoy.php');

	$coinvoy = new Coinvoy();

	$amount   = 0.93;
	$address  = "your cryptocurrency address";
	$currency = "BTC";

	createInvoice($coinvoy, $amount, $address, $currency);
	//getDonation($coinvoy, $address);
	//getButton($coinvoy, $amount, $address, $currency);

	function createInvoice($coinvoy, $amount, $address, $currency) {
		$invoice = $coinvoy->invoice($amount, $address, $currency);

		var_dump($invoice);

		$status = $coinvoy->getInvoice($invoice->id);

		var_dump($status);
	}

	function getDonation($coinvoy, $address) {
		$donation = $coinvoy->donation($address);

		var_dump($donation);
	}
    

	function getButton($coinvoy, $amount, $address, $currency) {
		$button = $coinvoy->button($amount, $address, $currency);

		var_dump($button);

		if ($button->success) {
			$invoice = $coinvoy->invoiceFromHash($button->hash, 'BTC');

			var_dump($invoice);
		}
	}

	
	
?>