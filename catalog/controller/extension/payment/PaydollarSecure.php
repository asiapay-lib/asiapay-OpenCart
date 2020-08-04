<?php 

interface PaydollarSecure {

	public function generatePaymentSecureHash($merchantId,
			$merchantReferenceNumber, $currencyCode, $amount,
			$paymentType, $secureHashSecret);
	
 

	public function verifyPaymentDatafeed($src, $prc, $successCode,
			$merchantReferenceNumber, $paydollarReferenceNumber,
			$currencyCode, $amount,
			$payerAuthenticationStatus,$secureHashSecret,
			$secureHash);

}


?>
