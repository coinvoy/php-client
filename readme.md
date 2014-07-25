Coinvoy API - PHP Client Library
================================

PHP client library for Coinvoy API


##About Coinvoy

Coinvoy is an online payment processor with an integrated exchange feature for established cryptocurrencies, namely Bitcoin, Litecoin and Dogecoin. It's objective is to provide an easiest yet the most secure way to accept cryptocurrencies.

##Get started

Just include coinvoy.php in your document and use it freely.

```
include_once('./lib/coinvoy.php');

$cv = new coinvoy();
$cv->setItem($orderID, $description);

//Create invoice
$invoice = $cv->invoice($amount, $currency, $payWith);

echo $invoice->url; 	#payment url to display with an iframe
echo $invoice->html; 	#default behaviour, includes an iframe and js listener
echo $invoice->address; #display payment address

//Create payment button
$button = $cv->button($amount, $currency, $buttonText);

echo $button->hash; #unique hash for occurring usage

//use your hash anywhere
$payWith = "LTC";	# Payment currency - defaults to "BTC"
$amount = false; 	# Set amount for donations
$cv->invoiceFromHash($button->hash, $payWith, $amount)
```

###List of all commands:
- invoice($amount, $currency, $payWith);
- button($amount, $currency, $buttonText);
- donation($buttonText);
- invoiceFromHash($hash, $payWith, $amount); 
- validateNotification($invoiceId, $hash, $orderId);
- getStatus($invoiceId);

Your feedback and suggestions are very much welcome. Please contact support@coinvoy.net for any input. 

Enjoy!

Coinvoy

