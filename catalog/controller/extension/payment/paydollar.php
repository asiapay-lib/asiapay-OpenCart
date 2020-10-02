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
		
		$payType = substr($this->config->get ( 'payment_paydollar_payment_type' ),0,1);
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
		//print_r($_POST) ;
				
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
			// while ( list ( $key, $value ) = each ( $secureHashs ) ) {
			foreach($secureHashs as $key => $value){
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
		// while ( list ( $key, $value ) = each ( $_POST ) ) {
		foreach ($_POST as $key => $value) {
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
	
}
?>