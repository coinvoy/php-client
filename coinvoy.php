<?php
#------------------------------
#------ PHP CLIENT FOR API ----
#------------------------------

class Coinvoy {
	const MINAMOUNT = 0.0005;

	#----------------------				
	#----------------------------------------------------------------
	# Create new invoice to receive a payment
	# Required : $amount		# Billed amount
	# Required : $address		# Receiving address
	# Optional : $currency		# Billed currency - "BTC","LTC" or "DOGE" - defaults to "BTC"
	# Optional : $options 		# Invoice options : orderID, callback, payWith, provider, email, item, description, escrow 
	#----------------------------------------------------------------
	public function invoice($amount, $address, $currency="BTC", $options=array()) 
	{
		if (floatval($amount) < self::MINAMOUNT)
        	return $this->error("Amount cannot be less than 0.0002");

		if (!in_array($currency, array("BTC","LTC","DOGE","USD")))
			return $this->error("Currency invalid");

		if (strlen($address) < 30 || strlen($address) > 50)
			return $this->error("Address invalid");

		$payment = array_merge($options, array("amount"=>$amount,"address"=>$address,"currency"=>$currency));

		try {
			$res = $this->apiRequest('/api/newInvoice', $payment);
		} catch (Exception $e) {
			return $this->error("An error occured: ".$e->getMessage());
		}

		return $res;
	}

	#----------------------------------------------------------------
	# Create new donation template to use in client side
	# Required : $address		# Receiving address
	# Optional : $options		# Donation options: orderID, callback, payWith, provider, email, item, description, buttonText
	#----------------------------------------------------------------
	public function donation($address, $options=array())
	{
		if (strlen($address) < 30 || strlen($address) > 50)
			return $this->error("Address invalid");

		$donation = array_merge($options, array("address"=>$address));

		try {
			$res = $this->apiRequest('/api/getDonation', $donation);
		} catch (Exception $e) {
			return $this->error("An error occured: ".$e->getMessage());
		}

		return $res;
	}

	#----------------------------------------------------------------
	# Create new invoice template to use in client side
	# Required : $amount		# Billed amount
	# Required : $address		# Receiving address
	# Optional : $currency		# Billed currency - "BTC","LTC" or "DOGE"
	# Optional : $options		# Donation options: orderID, callback, payWith, provider, email, item, description, buttonText
	#----------------------------------------------------------------
	public function button($amount, $address, $currency="BTC", $options=array())
	{

		if (floatval($amount) < self::MINAMOUNT)
        	return $this->error("Amount cannot be less than 0.0002");

		if (!in_array($currency, array("BTC","LTC","DOGE","USD")))
			return $this->error("Currency invalid");

		if (strlen($address) < 30 || strlen($address) > 50)
			return $this->error("Address invalid");

		$button = array_merge($options, array("amount"=>$amount,"address"=>$address,"currency"=>$currency));

		try {
			$res = $this->apiRequest('/api/getButton', $button);
		} catch (Exception $e) {
			return $this->error("An error occured: ".$e->getMessage());
		}

		return $res;
	}

	#----------------------------------------------
	# Creates a live invoice from hash
	# Params : (string) $hash
	#		   (string) $payWith
	# Returns: JSON response
	#----------------------------------------------
	public function invoiceFromHash($hash = false, $payWith = "BTC")
	{
		if ( empty( $hash ) )
			return $this->error("Missing information. Please supply an invoice hash.");

		$hash = array('hash'=>$hash, 'payWith'=>$payWith);

		try {
			$res = $this->apiRequest('/api/invoiceHash', $hash);
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
	public function freeEscrow($key)
	{
		if ( empty( $key ) )
			return $this->error("Missing information. Please supply an invoice hash.");

		$data = array('key'=>$key);

		try {
			$res = $this->apiRequest('/api/freeEscrow', $data);
		} catch (Exception $e) {
			return $this->error("An error occured: ".$e->getMessage());
		}

		return $res;
	}


	#----------------------------------------------
	# Validates received payment notification (IPN)
	# Params : (string) $invoiceId
	#		   (string) $hash
	#		   (string) $orderID
	#		   (string) $address
	# Returns: (bool) True/False
	#----------------------------------------------
	public function validateNotification($invoiceId, $hash, $orderID, $address)
	{
		try {
			return $hash == hash_hmac('sha256', $orderID.":".$invoiceId, $address, TRUE);
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
				$res = $this->apiRequest('/api/status', array("invoiceId" => $invoiceId));
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
				$res = $this->apiRequest('/api/invoice', array("invoiceId" => $invoiceId));
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

		$url = "https://coinvoy.net" . $url;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		# Get result object
		$httpres = curl_exec($ch);
		# Close curl
		curl_close ($ch);

		try {
			$res = json_decode($httpres);	
		} catch (Exception $e) {
			$res = $httpres;
		}

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