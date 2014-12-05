<?php
#------------------------------
#------ PHP CLIENT FOR API ----
#------------------------------

class Coinvoy {
	const MINAMOUNT = 0.0005;

	#----------------------------------------------------------------
	# Create new payment
	# Required : $amount    # Billed amount
	# Required : $currency  # Billed currency
	# Required : $address   # Receiving address
	# Optional : $options   # Payment options : orderID,
	#                                           secret, 
	#                                           callback,
	#                                           company,
	#                                           motto,
	#                                           companyLogo,
	#                                           addressLine1,
	#                                           addressLine2,
	#                                           email,
	#                                           item,
	#                                           description,
	#                                           returnAddress,
	#                                           escrow 
	# Returns   : JSON object
	#----------------------------------------------------------------
	public function payment($amount, $currency, $address='', $options=array()) 
	{
		if (floatval($amount) < self::MINAMOUNT)
        	return $this->error('Amount cannot be less than ' . self::MINAMOUNT);

		if ( (!isset($options['escrow']) || $options['escrow'] === false)
		   		&& !$this->validAddress($address) ) {
		 	return $this->error('Invalid address');  		
		}

		$params = array_merge($options, array( 'amount'   => $amount,
		                                       'address'  => $address,
		                                       'currency' => $currency));

		try {
			$res = $this->apiRequest('/api/payment', $params);
		} catch (Exception $e) {
			return $this->error('An error occured: '.$e->getMessage());
		}

		return $res;
	}
	
	#----------------------------------------------------------------
	# Create new payment template to use in client side
	# Required : $amount        # Billed amount
	# Required : $currency      # Billed currency
	# Required : $address       # Receiving address
	# Optional : $options       # Button options: orderID,
	#                                         secret, 
	#                                         callback,
	#                                         company,
	#                                         motto,
	#                                         companyLogo,
	#                                         addressLine1,
	#                                         addressLine2,
	#                                         email,
	#                                         item,
	#                                         description,
	#                                         returnAddress,
	#                                         buttonText,
	#                                         escrow 
	# Returns   : JSON object
	#----------------------------------------------------------------
	public function button($amount, $currency, $address='', $options=array())
	{

		if (floatval($amount) < self::MINAMOUNT)
        	return $this->error('Amount cannot be less than ' . self::MINAMOUNT);

		if ( (!isset($options['escrow']) || $options['escrow'] === false)
		   		&& !$this->validAddress($address) ) {
		 	return $this->error('Invalid address');  		
		}

		$params = array_merge($options, array('amount'   => $amount,
		                                      'address'  => $address,
		                                      'currency' => $currency));

		try {
			$res = $this->apiRequest('/api/button', $params);
		} catch (Exception $e) {
			return $this->error("An error occured: ".$e->getMessage());
		}

		return $res;
	}

	#----------------------------------------------------------------
	# Create new donation template to use in client side
	# Required : $address   # Receiving address
	# Optional : $options   # Donation options: orderID,
	#                                           secret,
	#                                           callback,
	#                                           company,
	#                                           motto,
	#                                           companyLogo,
	#                                           addressLine1,
	#                                           addressLine2,
	#                                           email,
	#                                           item,
	#                                           description,
	#                                           buttonText
	# Returns   : JSON object
	#----------------------------------------------------------------
	public function donation($address, $options=array())
	{
		if ( !$this->validAddress($address) ) {
		 	return $this->error('Invalid address');
		}

		$params = array_merge($options, array('address' => $address ));

		try {
			$res = $this->apiRequest('/api/donation', $arams);
		} catch (Exception $e) {
			return $this->error('An error occured: '.$e->getMessage());
		}

		return $res;
	}


	#----------------------------------------------
	# Completes the escrow process and forwards coins to their last destination
	# Required : $key     # key returned from /api/payment
	# Optional : $options # freeEscrow options: address
	# Returns  : JSON object
	#----------------------------------------------
	public function freeEscrow($key, $options = array())
	{
		if ( isset($options['address']) && !$this->validAddress($options['address']) )
		 	return $this->error('Invalid address');

		$params = array_merge($options, array( 'key' => $key ));

		try {
			$res = $this->apiRequest('/api/freeEscrow', $params);
		} catch (Exception $e) {
			return $this->error('An error occured: '.$e->getMessage());
		}

		return $res;
	}


	#----------------------------------------------
	# Cancels the escrow process and returns coins to owner
	# Required : $key     # key returned from /api/payment
	# Optional : $options # freeEscrow options: returnAddress
	# Returns  : JSON object
	#----------------------------------------------
	public function cancelEscrow($key, $options = array())
	{
		if ( isset($options['returnAddress']) && !$this->validAddress($options['returnAddress']) )
		 	return $this->error('Invalid return address');

		$params = array_merge($options, array( 'key' => $key ));

		try {
			$res = $this->apiRequest('/api/cancelEscrow', $params);
		} catch (Exception $e) {
			return $this->error('An error occured: '.$e->getMessage());
		}

		return $res;
	}

	#---------------------------------------------------
	# Required : $invoiceID
	# Returns  : JSON object
	#---------------------------------------------------
	public function status($invoiceID)
	{
		try {
			if($invoiceID) {
				$res = $this->apiRequest('/api/status', array("invoiceID" => $invoiceID));
				return $res;
			} else {
				return $this->error("Please supply an invoice id");
			}
		} catch (Exception $e) {
			return $this->error("An error occured: ".$e->getMessage());
		}
	}


	#---------------------------------------------------
	# Required : $invoiceID
	# Returns  : JSON object
	#---------------------------------------------------
	public function invoice($invoiceID)
	{
		try {
			if($invoiceID) {
				$res = $this->apiRequest('/api/invoice', array("invoiceID" => $invoiceID));
				return $res;
			} else {
				return $this->error("Please supply an invoice id");
			}
		} catch (Exception $e) {
			return $this->error("An error occured: ".$e->getMessage());
		}
	}
	
	#----------------------------------------------
	# Validates received payment notification (IPN)
	# Required : $hash      # provided by IPN call
	# Required : $orderID   # provided by IPN call
	# Required : $invoiceID # provided by IPN call
	# Required : $secret    # secret used while creating payment
	# Returns  : True/False
	#----------------------------------------------
	public function validateNotification($hash, $orderID, $invoiceID, $secret)
	{
		try {
			return $hash == hash_hmac('sha256', $orderID.":".$invoiceID, $secret, FALSE);
		} catch (Exception $e) {
			return false;
		}
	}



	#---------------------------------------
	# Basic request
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
		$postString = json_encode($postArray);

		$url = "https://coinvoy.net" . $url;
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json' ));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		# Get result object
		$httpres = curl_exec($ch);
		# Close curl
		curl_close ($ch);

		try {
			$res = json_decode($httpres, true);
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
	
	private function validAddress($address) {
		if (strlen($address) < 26 || strlen($address) > 35)
				return false;
				
		if ($address[0] != '1' && $address[0] != '3')
				return false;
				
		return true;
	}
}

?>
