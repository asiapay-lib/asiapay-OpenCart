<?php 
// Heading
$_['heading_title']      = 'PayDollar';

// Text 
$_['text_payment']       = 'Payment';
$_['text_success']       = 'Success: You have modified PayDollar account details!';
$_['text_edit']          = 'Edit PayDollar';
      
// Entry
$_['entry_merchant']     = 'Merchant ID:';
$_['entry_security']     = 'Secure Hash Secret Key:';
$_['entry_payserverurl'] = 'Gateway URL:';
$_['entry_mps_mode'] 	 = 'MPS Mode:';
$_['entry_currency']     = 'Currency:';
$_['entry_payment_type'] = 'Payment Type:';
$_['entry_paymethod']    = 'Payment Method:';
$_['entry_lang']    	 = 'Language:';
$_['entry_callback']     = 'Important:';
$_['callback'] 			 = '- You need to set the datafeed URL on your PayDollar Merchant Admin Panel > Profile > Profile Settings > Payment Options > Return Value Link (Datafeed).<br/>- URL to be set: ' .HTTP_CATALOG . 'index.php?route=extension/payment/paydollar/callback';
$_['entry_order_status'] = 'Initial Order Status:';
$_['entry_geo_zone']     = 'Geo Zone:';
$_['entry_status']       = 'Status:';
$_['entry_sort_order']   = 'Sort Order:';
$_['entry_transaction_type'] = 'Transaction Type:';
$_['entry_challenge_pref'] = 'Challenge Preference:';

// Error
$_['error_permission']   = 'Warning: You do not have permission to modify payment PayDollar!';
$_['error_payserverurl'] = 'PayDollar Server URL Required!';
$_['error_merchant']     = 'Merchant ID Required!';
$_['error_security']     = 'Security Code Required!';
?>