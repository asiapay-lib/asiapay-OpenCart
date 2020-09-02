<?php 
class ControllerExtensionPaymentPayDollar extends Controller {

	public function index() {
		$this->logger= new Log('paydollar.log');
		$data ['button_confirm'] = $this->language->get ( 'button_confirm' );
		
		$this->load->model ( 'checkout/order' );
		
		$order_info = $this->model_checkout_order->getOrder ( $this->session->data ['order_id'] );
		
		$data ['action'] = $this->config->get ( 'payment_paydollar_payserverurl' );
		
		$mpsMode = $this->config->get ( 'payment_paydollar_mps_mode' );
		$data ['mpsMode'] = $mpsMode;
		
		$currency = $order_info ['currency_code'];
		$currCode = $this->_getCurrencyIso($currency);	
		//$currCode = $this->_getCurrencyIso("HKD");		
		$data ['currCode'] = $currCode;
		
		$settingCurrCode = $this->config->get('payment_paydollar_currency');
		$settingCurrCode = substr($settingCurrCode,0,3);
		
		$amount = $this->currency->format($order_info['total'], $order_info['currency_code'] , false, false);

		$data ['amount'] = $amount;		
		
		if($mpsMode=='NIL' && $currCode!=$settingCurrCode){
			 $settingCurr=$this->_getCurrency($settingCurrCode);
			 $data ['currCode'] = $settingCurrCode;
			 $data ['amount'] = $this->currency->format (  $order_info ['total'], $settingCurr, '', FALSE );
		}
		
		/* $lang = $this->session->data ['language'];
		if ($lang == "en") {
			$lang = "E";
		} */
		$paydollar_lang = $this->config->get ( 'payment_paydollar_lang' );
		$data ['lang'] = substr($paydollar_lang,0,1);
		
		
		$merchantId = $this->config->get ( 'payment_paydollar_merchant' );
		$data ['merchantId'] = $merchantId;
		
		$orderRef = $this->session->data ['order_id'];
		$data ['orderRef'] = $orderRef;
		
		$payType = $this->config->get ( 'payment_paydollar_payment_type' );
		$data ['payType'] = $payType;
		
		$data ['payMethod'] = $this->config->get ( 'payment_paydollar_paymethod' );
		
		$failUrl = HTTPS_SERVER . 'index.php?route=checkout/failure';
		$data ['failUrl'] = $failUrl;
		
		$successUrl = HTTPS_SERVER . 'index.php?route=checkout/success';
		$data ['successUrl'] = $successUrl;
		
		$cancelUrl = HTTPS_SERVER . 'index.php?route=checkout/checkout';
		$data ['cancelUrl'] = $cancelUrl;
		
		$remark = $order_info ['comment'];
		$data ['remark'] = $remark;
		
		$redirect = "";
		$data ['redirect'] = $redirect;
		
		$oriCountry = "";
		$data ['oriCountry'] = $oriCountry;
		
		$destCountry = "";
		$data ['destCountry'] = $destCountry;
		
		$data ['secureHashSecret'] = trim ( $this->config->get ( 'payment_paydollar_security' ) );
		
		//logger Data Print
		$this->logger->write("Paydollar Payment data : ".$orderRef);
		foreach ($data as $key => $value) {
    		$this->logger->write("{$key} => {$value} ");
		}

		$secureHashSecret = trim ( $this->config->get ( 'payment_paydollar_security' ) );
		if ($secureHashSecret) {
			require_once ('SHAPaydollarSecure.php');
			$paydollarSecure = new SHAPaydollarSecure ();
			$secureHash = $paydollarSecure->generatePaymentSecureHash ( $merchantId, $orderRef, $currCode, $amount, $payType, $secureHashSecret );
			$data ['secureHash'] = $secureHash;
		} else {
			$data ['secureHash'] = '';
		}

		//3DS 2.0
		$transactionType = $this->config->get ( 'payment_paydollar_transaction_type' );
		$data ['threeDSTransType'] = substr($transactionType,0,2);

		// $isLogged = $this->customer->isLogged();

		if($this->customer->isLogged()){
			// echo "<pre>";
			// print_r($this->session->data);
			// echo "<br>";
			// echo "<br>";
			$customerid = $this->session->data['customer_id'];
			$address_id = $this->customer->getAddressId();
			$this->load->model('account/address');
			$address = $this->model_account_address->getAddress($address_id); 
			$customerEmail = $this->customer->getEmail();
			// print_r($address);

			$data ['threeDSCustomerEmail'] = $customerEmail;
			$data ['threeDSDeliveryEmail'] = $customerEmail;
			$data ['threeDSMobilePhoneCountryCode'] = $this->_getCountryPhoneCode($address['iso_code_2']);

			$customerPhone = preg_replace('/\D/', '',$this->customer->getTelephone());

			$data ['threeDSMobilePhoneNumber'] = $customerPhone;
			$data ['threeDSHomePhoneCountryCode'] = $this->_getCountryPhoneCode($address['iso_code_2']);
			$data ['threeDSHomePhoneNumber'] = $customerPhone;

			$data['threeDSWorkPhoneCountryCode'] = $this->_getCountryPhoneCode($address['iso_code_2']);
			$data ['threeDSWorkPhoneNumber'] = $customerPhone;


			$customer_acctAuthMethod = "02"; // Login to the cardholder account at the merchant system using merchant‘s own credentials


			$this->load->model ( 'extension/payment/paydollar' );
			
			$data['threeDSAcctCreateDate'] = date('Ymd', strtotime($this->model_extension_payment_paydollar->getDateAdded($customerid)));
			$customer_daydiff = $this->_getDateDiff($data['threeDSAcctCreateDate']);
			$customer_acct_ageind =$this->_getAcctAgeInd($customer_daydiff);
			$data['threeDSAcctAgeInd'] = $customer_acct_ageind;

			$data['threeDSAcctPurchaseCount'] = $this->model_extension_payment_paydollar->getDBOrders($customerid,6);
			$data['threeDSAcctNumTransDay'] = $this->model_extension_payment_paydollar->getDBOrders($customerid,24);
			$data['threeDSAcctNumTransYear'] = $this->model_extension_payment_paydollar->getDBOrders($customerid,12);
			$data['threeDSAcctAuthTimestamp'] = $this->model_extension_payment_paydollar->getDBAcctLogin($customerEmail);

		}else{
			// echo "<pre>";
			// print_r($this->session->data);
			// echo "<br>";
			// echo "<br>";

			// print_r($this->session->data['payment_address']['iso_code_2']);

			$data ['threeDSCustomerEmail'] = $this->session->data['guest']['email'];
			$data ['threeDSDeliveryEmail'] = $this->session->data['guest']['email'];
			$data ['threeDSMobilePhoneCountryCode'] = $this->_getCountryPhoneCode($this->session->data['payment_address']['iso_code_2']);
			$guestPhoneNumber = preg_replace('/\D/', '',$this->session->data['guest']['telephone']);
			$data['threeDSMobilePhoneNumber'] = $guestPhoneNumber;
			$data['threeDSHomePhoneCountryCode'] = $this->_getCountryPhoneCode($this->session->data['payment_address']['iso_code_2']);
			$data['threeDSHomePhoneNumber'] = $guestPhoneNumber;
			$data['threeDSWorkPhoneCountryCode'] = $this->_getCountryPhoneCode($this->session->data['payment_address']['iso_code_2']);
			$data ['threeDSWorkPhoneNumber'] = $guestPhoneNumber;
			$data['threeDSIsFirstTimeItemOrder'] = "T";
			$customer_acctAuthMethod = "01"; // as guest

		}
		//Recurring / Installment Payment Related (Provide only if it is a recurring / installment payment)
		// $data['threeDSRecurringFrequency'] = "";
		// $data['threeDSRecurringExpiry'] = "";

		// Billing Address Related (Provide only if billing address is available)
		$data['threeDSBillingCountryCode'] = $this->_getCountryCodeNumeric($this->session->data['payment_address']['iso_code_2']);
		$data['threeDSBillingState'] = $this->session->data['payment_address']['iso_code_2'];
		$data['threeDSBillingCity'] = $this->session->data['payment_address']['city'];
		$data['threeDSBillingLine1'] = $this->session->data['payment_address']['address_1'];
		$data['threeDSBillingLine2'] = $this->session->data['payment_address']['address_2'];
		// $data['threeDSBillingLine3'] = "";
		$data['threeDSBillingPostalCode'] = $this->session->data['payment_address']['postcode'];

		// Shipping / Delivery Related (Provide only if the payment requires shipping / delivery)
		// $data['threeDSDeliveryTime'] = "";
		// $data['threeDSDeliveryEmail'] = $this->session->data['guest']['email']; // using electronic delivery
		
		$data['threeDSShippingCountryCode'] = $this->_getCountryCodeNumeric($this->session->data['shipping_address']['iso_code_2']);
		$data['threeDSShippingState'] = $this->session->data['shipping_address']['iso_code_2'];
		$data['threeDSShippingCity'] = $this->session->data['shipping_address']['city'];
		$data['threeDSShippingLine1'] = $this->session->data['shipping_address']['address_1'];
		$data['threeDSShippingLine2'] = $this->session->data['shipping_address']['address_2'];
		// $data['threeDSBillingLine3'] = "";
		$data['threeDSShippingPostalCode'] = $this->session->data['shipping_address']['postcode'];

		// $data['threeDSIsAddrMatch'] = "T";

		// Gift Card / Prepaid Card Purchase Related (Provide only if the purchase related to gift card / prepaid card)
		// $data['threeDSGiftCardAmount'] = "";
		// $data['threeDSGiftCardCurr'] = "";
		// $data['threeDSGiftCardCount'] = "";

		// Pre-Order Purchase Related (Provide only if the payment is related to Pre-Order)
		// $data['threeDSPreOrderReason'] = "";
		// $data['threeDSPreOrderReadyDate'] = "";

		$data['threeDSIsAddrMatch'] = $this->_getDiffAddress();

		$data['threeDSShippingDetails'] = ($this->_getDiffAddress()=="T")?'01':'03';
		
		
		// $data ['threeDSCustomerEmail'] = ;

		$challengePref = $this->config->get ( 'payment_paydollar_challenge_pref' );
		$data ['threeDSChallengePreference'] = substr($challengePref,0,2);
		$data['threeDSAcctAuthMethod'] = $customer_acctAuthMethod;
	
		// echo "<pre>";print_r($data);exit;
		if (file_exists ( DIR_TEMPLATE . $this->config->get ( 'config_template' ) . '/template/extension/payment/paydollar.twig' )) {
			return $this->load->view($this->config->get('config_template') . '/template/extension/payment/paydollar', $data);
		} else {
			return $this->load->view('extension/payment/paydollar', $data);
		}		
	}
	
	private function _getCurrencyIso($opencart_currency_code) {
		switch($opencart_currency_code){
		case 'HKD':
			$cur = '344';
			break;
		case 'USD':
			$cur = '840';
			break;
		case 'SGD':
			$cur = '702';
			break;
		case 'CNY':
			$cur = '156';
			break;
		case 'JPY':
			$cur = '392';
			break;		
		case 'TWD':
			$cur = '901';
			break;
		case 'AUD':
			$cur = '036';
			break;
		case 'EUR':
			$cur = '978';
			break;
		case 'GBP':
			$cur = '826';
			break;
		case 'CAD':
			$cur = '124';
			break;
		case 'MOP':
			$cur = '446';
			break;
		case 'PHP':
			$cur = '608';
			break;
		case 'THB':
			$cur = '764';
			break;
		case 'MYR':
			$cur = '458';
			break;
		case 'IDR':
			$cur = '360';
			break;
		case 'KRW':
			$cur = '410';
			break;
		case 'SAR':
			$cur = '682';
			break;
		case 'NZD':
			$cur = '554';
			break;
		/**case 'AED':
			$cur = '784';
			break;**/
		case 'BND':
			$cur = '096';
			break;
		case 'VND':
			$cur = '704';
			break;
		case 'INR':
			$cur = '356';
			break;
		default:
			$cur = '344';
		}		
		return $cur;
	}
	
	
	private function _getCurrency($currCode){
		$currency = "USD";
		if ($currCode = '344') {
			$currency == "HKD";
		} elseif ($currCode = '840') {
			$currency == "USD";
		} elseif ($currCode = '702') {
			$currency == "SGD";
		} elseif ($currCode = '156') {
			$currency == "CNY";
		} elseif ($currCode = '392') {
			$currency == "JPY";
		} elseif ($currCode = '901') {
			$currency == "TWD";
		} elseif ($currCode = '036') {
			$currency == "AUD";
		} elseif ($currCode = '978') {
			$currency == "EUR";
		} elseif ($currCode = '826') {
			$currency == "GBP";
		} elseif ($currCode = '124') {
			$currency == "CAD";
		} elseif ($currCode = '446') {
			$currency == "MOP";
		} elseif ($currCode = '608') {
			$currency == "PHP";
		} elseif ($currCode = '764') {
			$currency == "THB";
		} elseif ($currCode = '458') {
			$currency == "MYR";
		} elseif ($currCode = '360') {
			$currency == "IDR";
		} elseif ($currCode = '410') {
			$currency == "KRW";
		} elseif ($currCode = '682') {
			$currency == "SAR";
		} elseif ($currCode = '554') {
			$currency == "NZD";
		} elseif ($currCode = '784') {
			$currency == "AED";
		} elseif ($currCode = '096') {
			$currency == "BND";
		} elseif ($currCode = '704') {
			$currency == "VND";
		} elseif ($currCode = '356') {
			$currency == "INR";
		} 
		
		return $currency;
	}
	
	public function callback() {		
		// Note: Datafeed URL?
		// E.g. http://localhost/opencart_1_5_1/index.php?route=payment/paydollar/callback
		
		//Testing if Datafeed can recieve $_POST data properly
		// print_r($_POST) ;
				
		//get post data start
		$successcode = isset($_POST ['successcode']) ? $_POST ['successcode']  : '' ; 
		$src = isset($_POST ['src']) ? $_POST ['src']  : '' ; 
		$prc = isset($_POST ['prc']) ? $_POST ['prc']  : '' ; 
		$ref = isset($_POST ['Ref']) ? $_POST ['Ref']  : '' ;
		$payRef = isset($_POST ['PayRef']) ? $_POST ['PayRef']  : '' ;
		$amt = isset($_POST ['Amt']) ? $_POST ['Amt']  : '' ; 
		$cur = isset($_POST ['Cur']) ? $_POST ['Cur']  : '' ; 
		$payerAuth = isset($_POST ['payerAuth']) ? $_POST ['payerAuth']  : '' ; 
		$ord = isset($_POST ['Ord']) ? $_POST ['Ord']  : '' ; 
		$holder = isset($_POST ['Holder']) ? $_POST ['Holder']  : '' ; 
		$remark = isset($_POST ['remark']) ? $_POST ['remark']  : '' ; 
		$authId = isset($_POST ['AuthId']) ? $_POST ['AuthId']  : '' ; 
		$eci = isset($_POST ['eci']) ? $_POST ['eci']  : '' ; 
		$sourceIp = isset($_POST ['sourceIp']) ? $_POST ['sourceIp']  : '' ; 
		$ipCountry = isset($_POST ['ipCountry']) ? $_POST ['ipCountry']  : '' ; 		
		$mpsAmt = isset($_POST ['mpsAmt']) ? $_POST ['mpsAmt']  : '' ;
		$mpsCur = isset($_POST ['mpsCur']) ? $_POST ['mpsCur']  : '' ;
		$mpsForeignAmt = isset($_POST ['mpsForeignAmt']) ? $_POST ['mpsForeignAmt']  : '' ;
		$mpsForeignCur = isset($_POST ['mpsForeignCur']) ? $_POST ['mpsForeignCur']  : '' ;
		$mpsRate = isset($_POST ['mpsRate']) ? $_POST ['mpsRate']  : '' ; 
		$cardlssuingCountry = isset($_POST ['cardlssuingCountry']) ? $_POST ['cardlssuingCountry']  : '' ; 
		$payMethod = isset($_POST ['payMethod']) ? $_POST ['payMethod']  : '' ; 
		//get post data end
		
		echo 'OK';
		
		/* Secure Hash Start */
		require_once ('SHAPaydollarSecure.php');
		if(isset( $_POST ['secureHash'] )){
			$secureHash = $_POST ['secureHash'];
		}		
		$secureHashSecret = trim ( $this->config->get ( 'payment_paydollar_security' ) );		
		if (isset ( $secureHash ) && $secureHash && $secureHashSecret) {			
			$secureHashs = explode ( ',', $secureHash );			
			$paydollarSecure = new SHAPaydollarSecure ();
			while ( list ( $key, $value ) = each ( $secureHashs ) ) {
				$verifyResult = $paydollarSecure->verifyPaymentDatafeed ( $src, $prc, $successcode, $ref, $payRef, $cur, $amt, $payerAuth, $secureHashSecret, $value );
				echo '$secureHash=[' . $value . ']';
				if ($verifyResult) {
					echo ' - verifyResult= true';
					break;
				} else {
					echo ' - verifyResult= false';
				}
			}			
			if (! $verifyResult) {
				echo ' - Verify Fail';
				return;
			} else {
				echo ' - Verify Success';
			}
		}
		/* Secure Hash End */
		
		$paramsReceived = '';
		while ( list ( $key, $value ) = each ( $_POST ) ) {
			$paramsReceived .= '[' . $key . ']=[' . $value . '],';
		}
		echo $paramsReceived;
		
		//list of order status from opencart start
		$Processing = 2;
		$Failed = 10;
		//list of order status from opencart end
		
		//retrieve the order record start
		//$order_id = substr ( $ref, 0, strpos ( $ref, '-' ) );
		$order_id = $ref ;
		$order_id = trim($order_id);
		$this->load->model ( 'checkout/order' );
		$order_info = $this->model_checkout_order->getOrder ( $order_id );
		//retrieve the order record end

		if (isset ( $successcode ) && $successcode == "0") {
			//if accepted/authorized
			if (isset ( $ref )) {				
				if ($order_info) {
					//update order status to Processing
					$comment = $paramsReceived ;
					$notify = true;
					$this->model_checkout_order->addOrderHistory($order_id, $Processing, str_replace('],',']<br/>',$comment), $notify);
					echo " - Order status updated to: Processing";
				}
			}
		} else {
			//if rejected						
			echo " - Order Failed.";
		}
		//	else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
		//		$this->load->model('checkout/order');
		//		$this->model_checkout_order->addOrderHistory($order_id, $this->config->get('payment_alipay_order_status_id'));
		//	}
	}


	private function _getCountryPhoneCode($c){
		$countrycode = array('AD'=>'376','AE'=>'971','AF'=>'93','AG'=>'1268','AI'=>'1264','AL'=>'355','AM'=>'374','AN'=>'599','AO'=>'244','AQ'=>'672','AR'=>'54','AS'=>'1684','AT'=>'43','AU'=>'61','AW'=>'297','AZ'=>'994','BA'=>'387','BB'=>'1246','BD'=>'880','BE'=>'32','BF'=>'226','BG'=>'359','BH'=>'973','BI'=>'257','BJ'=>'229','BL'=>'590','BM'=>'1441','BN'=>'673','BO'=>'591','BR'=>'55','BS'=>'1242','BT'=>'975','BW'=>'267','BY'=>'375','BZ'=>'501','CA'=>'1','CC'=>'61','CD'=>'243','CF'=>'236','CG'=>'242','CH'=>'41','CI'=>'225','CK'=>'682','CL'=>'56','CM'=>'237','CN'=>'86','CO'=>'57','CR'=>'506','CU'=>'53','CV'=>'238','CX'=>'61','CY'=>'357','CZ'=>'420','DE'=>'49','DJ'=>'253','DK'=>'45','DM'=>'1767','DO'=>'1809','DZ'=>'213','EC'=>'593','EE'=>'372','EG'=>'20','ER'=>'291','ES'=>'34','ET'=>'251','FI'=>'358','FJ'=>'679','FK'=>'500','FM'=>'691','FO'=>'298','FR'=>'33','GA'=>'241','GB'=>'44','GD'=>'1473','GE'=>'995','GH'=>'233','GI'=>'350','GL'=>'299','GM'=>'220','GN'=>'224','GQ'=>'240','GR'=>'30','GT'=>'502','GU'=>'1671','GW'=>'245','GY'=>'592','HK'=>'852','HN'=>'504','HR'=>'385','HT'=>'509','HU'=>'36','ID'=>'62','IE'=>'353','IL'=>'972','IM'=>'44','IN'=>'91','IQ'=>'964','IR'=>'98','IS'=>'354','IT'=>'39','JM'=>'1876','JO'=>'962','JP'=>'81','KE'=>'254','KG'=>'996','KH'=>'855','KI'=>'686','KM'=>'269','KN'=>'1869','KP'=>'850','KR'=>'82','KW'=>'965','KY'=>'1345','KZ'=>'7','LA'=>'856','LB'=>'961','LC'=>'1758','LI'=>'423','LK'=>'94','LR'=>'231','LS'=>'266','LT'=>'370','LU'=>'352','LV'=>'371','LY'=>'218','MA'=>'212','MC'=>'377','MD'=>'373','ME'=>'382','MF'=>'1599','MG'=>'261','MH'=>'692','MK'=>'389','ML'=>'223','MM'=>'95','MN'=>'976','MO'=>'853','MP'=>'1670','MR'=>'222','MS'=>'1664','MT'=>'356','MU'=>'230','MV'=>'960','MW'=>'265','MX'=>'52','MY'=>'60','MZ'=>'258','NA'=>'264','NC'=>'687','NE'=>'227','NG'=>'234','NI'=>'505','NL'=>'31','NO'=>'47','NP'=>'977','NR'=>'674','NU'=>'683','NZ'=>'64','OM'=>'968','PA'=>'507','PE'=>'51','PF'=>'689','PG'=>'675','PH'=>'63','PK'=>'92','PL'=>'48','PM'=>'508','PN'=>'870','PR'=>'1','PT'=>'351','PW'=>'680','PY'=>'595','QA'=>'974','RO'=>'40','RS'=>'381','RU'=>'7','RW'=>'250','SA'=>'966','SB'=>'677','SC'=>'248','SD'=>'249','SE'=>'46','SG'=>'65','SH'=>'290','SI'=>'386','SK'=>'421','SL'=>'232','SM'=>'378','SN'=>'221','SO'=>'252','SR'=>'597','ST'=>'239','SV'=>'503','SY'=>'963','SZ'=>'268','TC'=>'1649','TD'=>'235','TG'=>'228','TH'=>'66','TJ'=>'992','TK'=>'690','TL'=>'670','TM'=>'993','TN'=>'216','TO'=>'676','TR'=>'90','TT'=>'1868','TV'=>'688','TW'=>'886','TZ'=>'255','UA'=>'380','UG'=>'256','US'=>'1','UY'=>'598','UZ'=>'998','VA'=>'39','VC'=>'1784','VE'=>'58','VG'=>'1284','VI'=>'1340','VN'=>'84','VU'=>'678','WF'=>'681','WS'=>'685','XK'=>'381','YE'=>'967','YT'=>'262','ZA'=>'27','ZM'=>'260','ZW'=>'263');

		return $countrycode[$c];
	}
	private function _getCountryCodeNumeric($code){
		$countrycode = array('AF'=>'4','AL'=>'8','DZ'=>'12','AS'=>'16','AD'=>'20','AO'=>'24','AI'=>'660','AQ'=>'10','AG'=>'28','AR'=>'32','AM'=>'51','AW'=>'533','AU'=>'36','AT'=>'40','AZ'=>'31','BS'=>'44','BH'=>'48','BD'=>'50','BB'=>'52','BY'=>'112','BE'=>'56','BZ'=>'84','BJ'=>'204','BM'=>'60','BT'=>'64','BO'=>'68','BO'=>'68','BA'=>'70','BW'=>'72','BV'=>'74','BR'=>'76','IO'=>'86','BN'=>'96','BN'=>'96','BG'=>'100','BF'=>'854','BI'=>'108','KH'=>'116','CM'=>'120','CA'=>'124','CV'=>'132','KY'=>'136','CF'=>'140','TD'=>'148','CL'=>'152','CN'=>'156','CX'=>'162','CC'=>'166','CO'=>'170','KM'=>'174','CG'=>'178','CD'=>'180','CK'=>'184','CR'=>'188','CI'=>'384','CI'=>'384','HR'=>'191','CU'=>'192','CY'=>'196','CZ'=>'203','DK'=>'208','DJ'=>'262','DM'=>'212','DO'=>'214','EC'=>'218','EG'=>'818','SV'=>'222','GQ'=>'226','ER'=>'232','EE'=>'233','ET'=>'231','FK'=>'238','FO'=>'234','FJ'=>'242','FI'=>'246','FR'=>'250','GF'=>'254','PF'=>'258','TF'=>'260','GA'=>'266','GM'=>'270','GE'=>'268','DE'=>'276','GH'=>'288','GI'=>'292','GR'=>'300','GL'=>'304','GD'=>'308','GP'=>'312','GU'=>'316','GT'=>'320','GG'=>'831','GN'=>'324','GW'=>'624','GY'=>'328','HT'=>'332','HM'=>'334','VA'=>'336','HN'=>'340','HK'=>'344','HU'=>'348','IS'=>'352','IN'=>'356','ID'=>'360','IR'=>'364','IQ'=>'368','IE'=>'372','IM'=>'833','IL'=>'376','IT'=>'380','JM'=>'388','JP'=>'392','JE'=>'832','JO'=>'400','KZ'=>'398','KE'=>'404','KI'=>'296','KP'=>'408','KR'=>'410','KR'=>'410','KW'=>'414','KG'=>'417','LA'=>'418','LV'=>'428','LB'=>'422','LS'=>'426','LR'=>'430','LY'=>'434','LY'=>'434','LI'=>'438','LT'=>'440','LU'=>'442','MO'=>'446','MK'=>'807','MG'=>'450','MW'=>'454','MY'=>'458','MV'=>'462','ML'=>'466','MT'=>'470','MH'=>'584','MQ'=>'474','MR'=>'478','MU'=>'480','YT'=>'175','MX'=>'484','FM'=>'583','MD'=>'498','MC'=>'492','MN'=>'496','ME'=>'499','MS'=>'500','MA'=>'504','MZ'=>'508','MM'=>'104','MM'=>'104','NA'=>'516','NR'=>'520','NP'=>'524','NL'=>'528','AN'=>'530','NC'=>'540','NZ'=>'554','NI'=>'558','NE'=>'562','NG'=>'566','NU'=>'570','NF'=>'574','MP'=>'580','NO'=>'578','OM'=>'512','PK'=>'586','PW'=>'585','PS'=>'275','PA'=>'591','PG'=>'598','PY'=>'600','PE'=>'604','PH'=>'608','PN'=>'612','PL'=>'616','PT'=>'620','PR'=>'630','QA'=>'634','RE'=>'638','RO'=>'642','RU'=>'643','RU'=>'643','RW'=>'646','SH'=>'654','KN'=>'659','LC'=>'662','PM'=>'666','VC'=>'670','VC'=>'670','VC'=>'670','WS'=>'882','SM'=>'674','ST'=>'678','SA'=>'682','SN'=>'686','RS'=>'688','SC'=>'690','SL'=>'694','SG'=>'702','SK'=>'703','SI'=>'705','SB'=>'90','SO'=>'706','ZA'=>'710','GS'=>'239','ES'=>'724','LK'=>'144','SD'=>'736','SR'=>'740','SJ'=>'744','SZ'=>'748','SE'=>'752','CH'=>'756','SY'=>'760','TW'=>'158','TW'=>'158','TJ'=>'762','TZ'=>'834','TH'=>'764','TL'=>'626','TG'=>'768','TK'=>'772','TO'=>'776','TT'=>'780','TT'=>'780','TN'=>'788','TR'=>'792','TM'=>'795','TC'=>'796','TV'=>'798','UG'=>'800','UA'=>'804','AE'=>'784','GB'=>'826','US'=>'840','UM'=>'581','UY'=>'858','UZ'=>'860','VU'=>'548','VE'=>'862','VE'=>'862','VN'=>'704','VN'=>'704','VG'=>'92','VI'=>'850','WF'=>'876','EH'=>'732','YE'=>'887','ZM'=>'894','ZW'=>'716');
		return $countrycode[$code];

	}

	private function _getDiffAddress(){
		$b = $this->session->data['payment_address'];
		$s = $this->session->data['shipping_address'];

		$cnt = 0;

		if($b['address_1'] == $s['address_1'])$cnt++;
		if($b['address_2'] == $s['address_2'])$cnt++;
		if($b['postcode'] == $s['postcode'])$cnt++;
		if($b['country_id'] == $s['country_id'])$cnt++;
		if($b['zone_id'] == $s['zone_id'])$cnt++;
		if($b['country'] == $s['country'])$cnt++;
		if($b['zone'] == $s['zone'])$cnt++;
		if($b['zone_code'] == $s['zone_code'])$cnt++;
		if($b['iso_code_2'] == $s['iso_code_2'])$cnt++;
		if($b['iso_code_3'] == $s['iso_code_3'])$cnt++;

		if($cnt==10)return "T";
		else return "F";



	}

	private function _getDateDiff($d){
    		$datenow = date('Ymd');
			$dt1 = new \DateTime($datenow);
			$dt2 = new \DateTime($d);
			$interval = $dt1->diff($dt2)->format('%a');
			return $interval;
    }

	private function _getAcctAgeInd($d){
    	switch ($d) {
    		case 0:
    			# code...
    			$ret = "02";
    			break;
    		case $d<30:
    			# code...
    			$ret = "03";
    			break;
    		case $d>30 && $d<60:
    			# code...
    			$ret = "04";
    			break;
    		case $d>60:
    			$ret = "05"	;
				break;	
    		default:
    			# code...
    			break;
    	}
    	return $ret;

    }


	
}
?>