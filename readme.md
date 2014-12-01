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

$amount        = 0.012;
$address       = "receiving address";
$returnAddress = "return address"
$currency      = "BTC";

$payment = $coinvoy->payment($amount, $currency, $address);

var_dump($payment);


// $payment['url']; 	    # always find your invoice at https://coinvoy.net/invoice/{id}
// $payment['html']; 	    # default behaviour, includes an iframe and js listener
// $payment['address'];   # display payment address
// $payment['key'];		    # this key is used for completing the escrow !important! do not lose

```

###List of all commands:
- payment($amount, $currency, $address, $options);                - creates payment
- button($amount, $currency, $address, $options);                 - prepares a button template
- donation($address, $options);                                   - prepares a donation template
- validateNotification($hash, $orderID, $invoiceID, $secret);     - checks if incoming payment notification is valid.
- status($invoiceID);                                             - current status of invoice [new,approved,confirmed,completed,cancelled]
- invoice($invoiceID);                                            - get latest invoice object
- freeEscrow($key, $options);                                     - finalize an escrow with its unique key. This action sends funds to receiver
- cancelEscrow($key, $options);                                   - cancel an escrow with its unique key. This action sends funds to owner

Your feedback and suggestions are very much welcome. Please contact support@coinvoy.net for any input. 

Enjoy!

Coinvoy

