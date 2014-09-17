Coinvoy API - PHP Client Library
================================

PHP client library for Coinvoy API


##About Coinvoy

Coinvoy is an online payment processor with an integrated exchange feature for established cryptocurrencies, namely Bitcoin, Litecoin and Dogecoin. It's objective is to provide an easiest yet the most secure way to accept cryptocurrencies.

##Get started

Just include coinvoy.php in your document and use it freely.

```
require_once('./coinvoy.php');

$coinvoy = new Coinvoy();

$amount   = 1.42;                           // Amount of invoice value
$address  = "your cryptocurrency address"   // Your receiving address for Bitcoin, Litecoin or Dogecoin
$currency = "BTC";                          // Currency of invoice value

$invoice = $coinvoy->invoice(0.001, '1LLmwn5cgZWDVA6UQPN8ggiPCRpbaRVUxp', 'BTC');

var_dump($invoice);


// $invoice->url; 	    - always find your invoice at https://coinvoy.net/invoice/{id}
// $invoice->key
// $invoice->html; 	    # default behaviour, includes an iframe and js listener
// $invoice->address;   # display payment address
// $invoice->key;		# this key is used for completing the escrow !important! do not lose

```

###List of all commands:
- invoice($amount, $address, $currency, $options);                - creates live invoice
- button($amount, $address, $currency, $options);                 - prepares a button template
- donation($address, $options);                                   - prepares a donation template
- invoiceFromHash($hash, $payWith);                               - creates live invoice from template hash
- validateNotification(($invoiceId, $hash, $orderID, $address);   - checks if incoming payment notification is valid.
- getStatus($invoiceId);                                          - current status of invoice [new,approved,confirmed,completed,cancelled]
- getInvoice($invoiceId);                                         - get latest invoice object
- freeEscrow($key);                                               - finalize an escrow with its unique key. This action sends funds to receiver

Your feedback and suggestions are very much welcome. Please contact support@coinvoy.net for any input. 

Enjoy!

Coinvoy

