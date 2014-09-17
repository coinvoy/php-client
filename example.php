<?php

	require_once('./coinvoy.php');

	$coinvoy = new Coinvoy();

    $invoice = $coinvoy->invoice(0.001, '1LLmwn5cgZWDVA6UQPN8ggiPCRpbaRVUxp', 'BTC');

	var_dump($invoice);

	$status = $coinvoy->getStatus($invoice->id);

	var_dump($status);

	/*$donation = $coinvoy->donation('1LLmwn5cgZWDVA6UQPN8ggiPCRpbaRVUxp');

	var_dump($donation);*/

	/*$button = $coinvoy->button(0.001, '1Ejo8iRSbeGWjz5uDs6Ab2GoXjsHFw2wLB', 'BTC');

	var_dump($button);

	if ($button->success) {
		$invoice = $coinvoy->invoiceFromHash($button->hash, 'BTC');

		var_dump($invoice);
	}*/

	
	
?>