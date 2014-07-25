<?php
#------------------------------
#------ PHP CLIENT FOR API ----
#------------------------------

class coinvoy {
	# Common Variables
	#----------------------
	public $cpOptions = array(
		"address" => "{yourAddress}",			# Receiving Address: Payments are directed to this address
		"callback"=> "{yourNotification}",		# Notification url: Payments are notified here for further processing
		"escrow"  => false,						# Escrow, if needed
	);

	# Optional : $orderID		# Order ID for identification purposes
	# Optional : $description   # Description for your item to show up in payment window
	#---------------------------------------------------------------
	function setItem($orderID="", $description="")
	{
		$this->cpOptions['orderID'] = $orderID;
		$this->cpOptions['description'] = $description;
	}


	#----------------------				
	#----------------------------------------------------------------
	# Create new invoice to receive a payment
	# Required : $amount		# Billed amount
	# Optional : $currency		# Billed currency - "BTC","LTC" or "DOGE" - defaults to "BTC"
	# Optional : $payWith		# Payment Currency - "BTC", "LTC" or "DOGE" - defaults to "BTC"
	#----------------------------------------------------------------
	function invoice($amount = false, $currency = "BTC", $payWith = "BTC") 
	{
		$this->cpOptions['amount'] = $amount;
		$this->cpOptions['currency'] = $currency;
		$this->cpOptions['payWith'] = $payWith;

		try {
			$res = $this->apiRequest('https://coinvoy.net/api/newInvoice', $this->cpOptions);
		} catch (Exception $e) {
			return array('success' => false, "error" => "An error occured: ".$e->getMessage());
		}

		if ($res->success == false) {
			return array('success' => false, "error" => "An error occured while creating invoice: ".$res->message);
		} else {
			return $res;
		}
	}

	#----------------------------------------------------------------
	# Create new invoice template to use in client side
	# Required : $amount		# Billed amount
	# Optional : $currency		# Billed currency - "BTC","LTC" or "DOGE"
	# Optional : $payWith		# Payment Currency - "BTC", "LTC" or "DOGE"
	#----------------------------------------------------------------
	function donation($btnText = "Donate")
	{
		$this->cpOptions['amount'] = false;
		$this->cpOptions['currency'] = false;
		$this->cpOptions['buttonText'] = $btnText;
		$this->cpOptions['escrow'] = false;

		try {
			$res = $this->apiRequest('https://coinvoy.net/api/getDonation', $this->cpOptions);
		} catch (Exception $e) {
			return array('success' => false, "error" => "An error occured: ".$e->getMessage());
		}

		if ($res->success == false) {
			return array('success' => false, "error" => "An error occured while creating invoice: ".$res->message);
		} else {
			return $res;
		}
	}

	#----------------------------------------------------------------
	# Create new invoice template to use in client side
	# Required : $amount		# Billed amount
	# Optional : $currency		# Billed currency - "BTC","LTC" or "DOGE"
	# Optional : $payWith		# Payment Currency - "BTC", "LTC" or "DOGE"
	#----------------------------------------------------------------
	function button($amount = false, $currency = "BTC", $btnText = "Pay now")
	{
		$this->cpOptions['amount'] = $amount;
		$this->cpOptions['currency'] = $currency;
		$this->cpOptions['buttonText'] = $btnText;

		try {
			$res = $this->apiRequest('https://coinvoy.net/api/getButton', $this->cpOptions);
		} catch (Exception $e) {
			return array('success' => false, "error" => "An error occured: ".$e->getMessage());
		}

		if ($res->success == false) {
			return array('success' => false, "error" => "An error occured while creating invoice: ".$res->message);
		} else {
			return $res;
		}
	}

	#----------------------------------------------
	# Validates received payment notification (IPN)
	# Params : (string) $invoiceId
	#		   (string) $hash
	# Returns: (bool) True/False
	#----------------------------------------------
	function invoiceFromHash($hash='', $payWith=false, $amount=false)
	{
		$this->cpOptions['amount'] = $amount;
		$this->cpOptions['hash'] = $hash;
		$this->cpOptions['payWith'] = $payWith;

		try {
			$res = $this->apiRequest('https://coinvoy.net/api/invoiceHash', $this->cpOptions);
		} catch (Exception $e) {
			return array('success' => false, "error" => "An error occured: ".$e->getMessage());
		}

		if ($res->success == false) {
			return array('success' => false, "error" => "An error occured while creating invoice: ".$res->message);
		} else {
			return $res;
		}
	}


	#----------------------------------------------
	# Validates received payment notification (IPN)
	# Params : (string) $invoiceId
	#		   (string) $hash
	# Returns: (bool) True/False
	#----------------------------------------------
	function validateNotification($invoiceId, $hash, $orderID)
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
	function getStatus($invoiceId)
	{
		try {
			if($invoiceId) {
				$res = $this->apiRequest('https://coinvoy.net/api/status', array("invoiceId" => $invoiceId));
				return $res;
			} else {
				return array("success"=>false,"error"=>"Please supply an invoice id");
			}
		} catch (Exception $e) {
			return array("success"=>false,"error"=>"An error occured: ".$e->getMessage());
		}
	}


	#---------------------------------------
	# Get Invoice by ID
	# Params : (string) $invoiceId
	# Returns: (array) $invoice 
	#---------------------------------------
	function getInvoice($invoiceId)
	{
		try {
			if($invoiceId) {
				$res = $this->apiuest('https://coinvoy.net/api/invoice', array("invoiceId" => $invoiceId));
				return $res;
			} else {
				return array("success"=>false,"error"=>"Please supply an invoice id");
			}
		} catch (Exception $e) {
			return array("success"=>false,"error"=>"An error occured: ".$e->getMessage());
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
		$res = json_decode(curl_exec($ch));
		# Close curl
		curl_close ($ch);

		return $res;
	}
}

?>