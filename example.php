<?php

	require_once('./coinvoy.php');

	function createInvoice() {
		$coinvoy = new Coinvoy();

		$amount   = 0.93;
		$address  = "your cryptocurrency address";
		$currency = "BTC";

		$invoice = $coinvoy->invoice($amount, $address, $currency);

		var_dump($invoice);

		$status = $coinvoy->getInvoice($invoice->id);

		var_dump($status);
	}

	function getDonation() {
		$coinvoy = new Coinvoy();

		$address  = "your cryptocurrency address";

		$donation = $coinvoy->donation($address);

		var_dump($donation);
	}
    

	function getButton() {
		$coinvoy = new Coinvoy();

		$amount   = 0.93;
		$address  = "your cryptocurrency address";
		$currency = "BTC";

		$button = $coinvoy->button($amount, $address, $currency);

		var_dump($button);

		if ($button->success) {
			$invoice = $coinvoy->invoiceFromHash($button->hash, 'BTC');

			var_dump($invoice);
		}
	}

	createInvoice();
	//getDonation();
	//getButton();

	
	
	
?>