<?php
#------------------------------
#------ PHP CLIENT FOR API ----
#------------------------------

class coinvoy {
	# Common Variables
	# address  - bitcoin, litecoin or dogecoin address to receive to
	# callback - notification url to be called at all stages : new > approved > confirmed > completed or cancelled
	# escrow   - if payment is escrowed or not : true / false
	#----------------------
	private $cpOptions;

	function __construct($options = array())
	{
		$this->cpOptions = $options;
	}

	#----------------------				
	#----------------------------------------------------------------
	# Create new invoice to receive a payment
	# Required : $amount		# Billed amount
	# Optional : $currency		# Billed currency - "BTC","LTC" or "DOGE" - defaults to "BTC"
	# Optional : $payWith		# Payment Currency - "BTC", "LTC" or "DOGE" - defaults to "BTC"
	#----------------------------------------------------------------
	public function invoice($payment = array()) 
	{

		if ( empty( $payment ) || !isset($payment['amount']))
			return $this->error("Missing payment information");

        if (floatval($payment['amount']) < 0.0005)
        	return $this->error("Amount cannot be less than 0.0005");

		$payment = array_merge($this->cpOptions, $payment);

		try {
			$res = $this->apiRequest('https://coinvoy.net/api/newInvoice', $payment);
		} catch (Exception $e) {
			return $this->error("An error occured: ".$e->getMessage());
		}

		return $res;
	}

	#----------------------------------------------------------------
	# Create new donation template to use in client side
	# Optional : $description	# Description displayed in payment
	# Optional : $orderID		# Donation request ID
	# Optional : $btnText		# Button text for default donation button
	#----------------------------------------------------------------
	public function donation($donation = array())
	{

		$donation = array_merge($this->cpOptions, $donation);

		try {
			$res = $this->apiRequest('https://coinvoy.net/api/getDonation', $donation);
		} catch (Exception $e) {
			return $this->error("An error occured: ".$e->getMessage());
		}

		return $res;
	}

	#----------------------------------------------------------------
	# Create new invoice template to use in client side
	# Required : $amount		# Billed amount
	# Optional : $currency		# Billed currency - "BTC","LTC" or "DOGE"
	# Optional : $payWith		# Payment Currency - "BTC", "LTC" or "DOGE"
	#----------------------------------------------------------------
	public function button($button)
	{

		if ( empty( $button ) || !isset($button['amount']))
			return $this->error("Missing payment information");

        if (floatval($button['amount']) < 0.0005)
        	return $this->error("Amount cannot be less than 0.0005");

		$button = array_merge($this->cpOptions, $button);

		try {
			$res = $this->apiRequest('https://coinvoy.net/api/getButton', $button);
		} catch (Exception $e) {
			return $this->error("An error occured: ".$e->getMessage());
		}

		return $res;
	}

	#----------------------------------------------
	# Creates a live invoice from hash
	# Params : (string) $hash
	#		   (string) $payWith
	#		   (string) $amount [only for donations]
	# Returns: (bool) True/False
	#----------------------------------------------
	public function invoiceFromHash($hash = false, $payWith = "BTC", $amount = false)
	{
		if ( empty( $hash ) )
			return $this->error("Missing information. Please supply an invoice hash.");

		$hash = array('hash'=>$hash, 'payWith'=>$payWith, 'amount'=>$amount);
		try {
			$res = $this->apiRequest('https://coinvoy.net/api/invoiceHash', $hash);
		} catch (Exception $e) {
			return $this->error("An error occured: ".$e->getMessage());
		}

		return $res;
	}

	#----------------------------------------------
	# Completes the escrow process and forwards coins to their last destination
	# Params : (string) $key
	# Returns: (object) response or error message
	#----------------------------------------------
	public function completeEscrow($key)
	{
		if ( empty( $key ) )
			return $this->error("Missing information. Please supply an invoice hash.");

		$data = array('key'=>$key);
		try {
			$res = $this->apiRequest('https://coinvoy.net/api/freeEscrow', $data);
		} catch (Exception $e) {
			return $this->error("An error occured: ".$e->getMessage());
		}

		return $res;
	}


	#----------------------------------------------
	# Validates received payment notification (IPN)
	# Params : (string) $invoiceId
	#		   (string) $hash
	# Returns: (bool) True/False
	#----------------------------------------------
	public function validateNotification($invoiceId, $hash, $orderID)
	{
		try {
			if($hash == hash_hmac('sha256', $orderID.":".$invoiceId, $this->cpOptions['address'], TRUE)) {
				return true;
			} else {
				return false;
			}
		} catch (Exception $e) {
			return false;
		}
	}


	#---------------------------------------------------
	# Get Invoice Status:
	#		"new"			-> Invoice has just been created and is waiting for payment
	#		"approved"		-> Transaction is analyzed and approved as valid by our server
	#		"confirmed"		-> Transaction is confirmed by the network
	#		"completed"		-> Payment is forwarded to your address and the invoice is completed
	#		"cancelled"		-> Payment is not received
	#		"error"			-> An error occured during the process
	#		"insufficient"	-> User paid insufficient amount, waiting for complimentary payment
	#
	# Returns the Status result or an error
	#---------------------------------------------------
	public function getStatus($invoiceId)
	{
		try {
			if($invoiceId) {
				$res = $this->apiRequest('https://coinvoy.net/api/status', array("invoiceId" => $invoiceId));
				return $res;
			} else {
				return $this->error("Please supply an invoice id");
			}
		} catch (Exception $e) {
			return $this->error("An error occured: ".$e->getMessage());
		}
	}


	#---------------------------------------
	# Get Invoice by ID
	# Params : (string) $invoiceId
	# Returns: (array) $invoice 
	#---------------------------------------
	public function getInvoice($invoiceId)
	{
		try {
			if($invoiceId) {
				$res = $this->apiRequest('https://coinvoy.net/api/invoice', array("invoiceId" => $invoiceId));
				return $res;
			} else {
				return $this->error("Please supply an invoice id");
			}
		} catch (Exception $e) {
			return $this->error("An error occured: ".$e->getMessage());
		}
	}


	#---------------------------------------
	# Basic request
	# Params : (string) $url, (array) $postArray
	# Returns: (array) $res
	#---------------------------------------
	private function apiRequest($url, $postArray=array())
	{
		# Filter false elements
		foreach ($postArray as $var => $value) {
			if ($value === false) {
				unset($postArray[$var]);
			}
		}
		# Fill post string
		$postString = http_build_query($postArray);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		# Get result object
		$res = curl_exec($ch);
		$res = json_decode($res);
		# Close curl
		curl_close ($ch);

		return $res;
	}

	private function error($message = "") {
		$res = new stdClass;
		$res->success = false;
		$res->error   = $message;
		return $res;
	}
}

?>