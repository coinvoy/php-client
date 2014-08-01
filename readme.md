Coinvoy API - PHP Client Library
================================

PHP client library for Coinvoy API


##About Coinvoy

Coinvoy is an online payment processor with an integrated exchange feature for established cryptocurrencies, namely Bitcoin, Litecoin and Dogecoin. It's objective is to provide an easiest yet the most secure way to accept cryptocurrencies.

##Get started

Just include coinvoy.php in your document and use it freely.

```
$options = array(
	"address" => "receiving address",
	"callback"=> "http://yourwebsite/ipn.php",
	"provider"=> "company name",
	"email"   => "email@email.com",
	"escrow"  => false,
);
$cv = new coinvoy($options);

$payment = array(
	'orderID'     => '{secret_identifier}',
	'amount'      => '{amount_to_charge}'.
	'currency'    => '{currency_of_amount}',
	'payWith'     => '{currency_of_payment}',
	'description' => '{item_or_service_description}'
);

$invoice = $cv->invoice($payment);

echo $invoice->url; 	#payment url to display with an iframe
echo $invoice->html; 	#default behaviour, includes an iframe and js listener
echo $invoice->address; #display payment address
echo $invoice->key;		#this key is used for completing the escrow !important! do not lose

$button = array(
	'orderID'     => '{secret_identifier}',
	'amount'      => '{amount_to_charge}'.
	'currency'    => '{currency_of_amount}',
	'description' => '{item_or_service_description}',
	'address'	  => '{newReceiverAddress}'
);
//Create payment button
$button = $cv->button($button);

echo $button->hash; #unique hash for occurring usage

//use your hash anywhere
$payWith = "LTC";	# Payment currency - defaults to "BTC"
$amount = false; 	# Set amount for donations
$cv->invoiceFromHash($button->hash, $payWith, $amount)
```

###List of all commands:
- invoice($payment);
- button($button);
- donation($donation);
- invoiceFromHash($hash, $payWith, $amount); 
- validateNotification($invoiceId, $hash, $orderId);
- getStatus($invoiceId);
- getInvoice($invoiceId);
- completeEscrow($key);

Your feedback and suggestions are very much welcome. Please contact support@coinvoy.net for any input. 

Enjoy!

Coinvoy

