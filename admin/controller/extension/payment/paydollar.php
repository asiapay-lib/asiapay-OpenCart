<?php
class ControllerExtensionPaymentPayDollar extends Controller {
	
	private $error = array(); 

	public function index() {
		$this->load->language('extension/payment/paydollar');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
			
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {			
			$this->model_setting_setting->editSetting('payment_paydollar', $this->request->post);				
			
			$this->session->data['success'] = $this->language->get('text_success');

		$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
		}


  		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

 		if (isset($this->error['payserverurl'])) {
			$data['error_payserverurl'] = $this->error['payserverurl'];
		} else {
			$data['error_payserverurl'] = '';
		}
		
 		if (isset($this->error['merchant'])) {
			$data['error_merchant'] = $this->error['merchant'];
		} else {
			$data['error_merchant'] = '';
		}

 		if (isset($this->error['security'])) {
			$data['error_security'] = $this->error['security'];
		} else {
			$data['error_security'] = '';
		}
		
		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

   		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true)
		);
		
   		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/payment/paydollar', 'user_token=' . $this->session->data['user_token'], true)
		);
				
		$data['action'] = $this->url->link('extension/payment/paydollar', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);
		
	
		if (isset($this->request->post['payment_paydollar_payserverurl'])) {
			$data['payment_paydollar_payserverurl'] = $this->request->post['payment_paydollar_payserverurl'];
		} else {
			$data['payment_paydollar_payserverurl'] = $this->config->get('payment_paydollar_payserverurl');
		}
		
		$data['paydollar_payserverurls'] = array();
		$data['paydollar_payserverurls'][] = array(
			'url' => "https://www.paydollar.com/b2c2/eng/payment/payForm.jsp"
		);
		$data['paydollar_payserverurls'][] = array(
			'url' => "https://test.paydollar.com/b2cDemo/eng/payment/payForm.jsp"
		);
		$data['paydollar_payserverurls'][] = array(
			'url' => "https://www.pesopay.com/b2c2/eng/payment/payForm.jsp"
		);
		$data['paydollar_payserverurls'][] = array(
			'url' => "https://test.pesopay.com/b2cDemo/eng/payment/payForm.jsp"
		);
		$data['paydollar_payserverurls'][] = array(
			'url' => "https://www.siampay.com/b2c2/eng/payment/payForm.jsp"
		);
		$data['paydollar_payserverurls'][] = array(
			'url' => "https://test.siampay.com/b2cDemo/eng/payment/payForm.jsp"
		);
		

		if (isset($this->request->post['payment_paydollar_merchant'])) {
			$data['payment_paydollar_merchant'] = $this->request->post['payment_paydollar_merchant'];
		} else {
			$data['payment_paydollar_merchant'] = $this->config->get('payment_paydollar_merchant');
		}

		if (isset($this->request->post['payment_paydollar_security'])) {
			$data['payment_paydollar_security'] = $this->request->post['payment_paydollar_security'];
		} else {
			$data['payment_paydollar_security'] = $this->config->get('payment_paydollar_security');
		}
		
		
		
	
		if (isset($this->request->post['payment_paydollar_order_status_id'])) {
			$data['payment_paydollar_order_status_id'] = $this->request->post['payment_paydollar_order_status_id'];
		} else {
			$data['payment_paydollar_order_status_id'] = $this->config->get('payment_paydollar_order_status_id'); 
		} 
	
		
		$this->load->model('localisation/order_status');
		
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		
	
	
		
	
		if (isset($this->request->post['payment_paydollar_geo_zone_id'])) {
			$data['payment_paydollar_geo_zone_id'] = $this->request->post['payment_paydollar_geo_zone_id'];
		} else {
			$data['payment_paydollar_geo_zone_id'] = $this->config->get('payment_paydollar_geo_zone_id'); 
		} 

		$this->load->model('localisation/geo_zone');
										
		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		
		
	
		
		if (isset($this->request->post['payment_paydollar_status'])) {
			$data['payment_paydollar_status'] = $this->request->post['payment_paydollar_status'];
		} else {
			$data['payment_paydollar_status'] = $this->config->get('payment_paydollar_status');
		}
		
		
		
		
			
		if (isset($this->request->post['payment_paydollar_sort_order'])) {
			$data['payment_paydollar_sort_order'] = $this->request->post['payment_paydollar_sort_order'];
		} else {
			$data['payment_paydollar_sort_order'] = $this->config->get('payment_paydollar_sort_order');
		}
		
		
		
		
			
		if (isset($this->request->post['payment_paydollar_lang'])) {
			$data['payment_paydollar_lang'] = $this->request->post['payment_paydollar_lang'];
		} else {
			$data['payment_paydollar_lang'] = $this->config->get('payment_paydollar_lang'); 
		}
		
		
		$data['paydollar_langs'] = array();
		$data['paydollar_langs'][] = array (
		'lang' => "E-English");
		$data['paydollar_langs'][] = array (
		'lang' => "C-Traditional Chinese");
		$data['paydollar_langs'][] = array (
		'lang' => "X-Simplified Chinese");
		$data['paydollar_langs'][] = array (
		'lang' => "K-Korean");
		$data['paydollar_langs'][] = array (
		'lang' => "J-Japanese");
		$data['paydollar_langs'][] = array (	
		'lang' => "T-Thai");
		$data['paydollar_langs'][] = array (
		'lang' => "F-French");
		$data['paydollar_langs'][] = array (
		'lang' => "G-German");
		$data['paydollar_langs'][] = array (
		'lang' => "R-Russian");
		$data['paydollar_langs'][] = array (	
		'lang' => "S-Spanish");
		$data['paydollar_langs'][] = array (
		'lang' => "V-Vietnamese"

		);
		

		
		if (isset($this->request->post['payment_paydollar_payment_type'])) {
			$data['payment_paydollar_payment_type'] = $this->request->post['payment_paydollar_payment_type'];
		} else {
			$data['payment_paydollar_payment_type'] = $this->config->get('payment_paydollar_payment_type'); 
		} 
		
		$data['paydollar_payment_types'] = array();
		$data['paydollar_payment_types'][] = array(
			'type' => "N-Normal Payment (Sales)");
		$data['paydollar_payment_types'][] = array(	
			'type' => "H-Hold Payment (Authorize only)");		
	

		
		if (isset($this->request->post['payment_paydollar_mps_mode'])) {
			$data['payment_paydollar_mps_mode'] = $this->request->post['payment_paydollar_mps_mode'];
		} else {
			$data['payment_paydollar_mps_mode'] = $this->config->get('payment_paydollar_mps_mode'); 
		} 
		
		$data['paydollar_mps_modes'] = array();
		$data['paydollar_mps_modes'][] = array(
			'mps' => "NIL");
		$data['paydollar_mps_modes'][] = array(	
			'mps' => "SCP");
		$data['paydollar_mps_modes'][] = array(	
			'mps' => "DCC");
		$data['paydollar_mps_modes'][] = array(	
			'mps' => "MCP"
			
			
	);		
		
		
		
		
		if (isset($this->request->post['payment_paydollar_paymethod'])) {
			$data['payment_paydollar_paymethod'] = $this->request->post['payment_paydollar_paymethod'];
		} else {
			$data['payment_paydollar_paymethod'] = $this->config->get('payment_paydollar_paymethod'); 
		} 
		
		$data['paydollar_paymethods'] = array();
		$data['paydollar_paymethods'][] = array(
			'paymethod' => "ALL");
		$data['paydollar_paymethods'][] = array(	
			'paymethod' => "CC");
		$data['paydollar_paymethods'][] = array(	
			'paymethod' => "VISA");
		$data['paydollar_paymethods'][] = array(	
			'paymethod' => "Master");
		$data['paydollar_paymethods'][] = array(	
			'paymethod' => "JCB");
		$data['paydollar_paymethods'][] = array(	
			'paymethod' => "AMEX");
		$data['paydollar_paymethods'][] = array(	
			'paymethod' => "Diners");
		$data['paydollar_paymethods'][] = array(	
			'paymethod' => "PPS");
		$data['paydollar_paymethods'][] = array(	
			'paymethod' => "PAYPAL");
		$data['paydollar_paymethods'][] = array(	
			'paymethod' => "CHINAPAY");
		$data['paydollar_paymethods'][] = array(	
			'paymethod' => "ALIPAY");
		$data['paydollar_paymethods'][] = array(	
			'paymethod' => "TENPAY");
		$data['paydollar_paymethods'][] = array(	
			'paymethod' => "99BILL");
		$data['paydollar_paymethods'][] = array(	
			'paymethod' => "MEPS");
		$data['paydollar_paymethods'][] = array(	
			'paymethod' => "SCB");
		$data['paydollar_paymethods'][] = array(	
			'paymethod' => "BPM");
		$data['paydollar_paymethods'][] = array(	
			'paymethod' => "KTB");
		$data['paydollar_paymethods'][] = array(	
			'paymethod' => "UOB");
		$data['paydollar_paymethods'][] = array(	
			'paymethod' => "KRUNGSRIONLINE");
		$data['paydollar_paymethods'][] = array(	
			'paymethod' => "TMB");
		$data['paydollar_paymethods'][] = array(	
			'paymethod' => "IBANKING");
		$data['paydollar_paymethods'][] = array(	
			'paymethod' => "BancNet");
		$data['paydollar_paymethods'][] = array(	
			'paymethod' => "GCash");
		$data['paydollar_paymethods'][] = array(	
			'paymethod' => "SMARTMONEY");
		$data['paydollar_paymethods'][] = array(	
			'paymethod' => "PAYCASH"
		);	
	
		
		
		if (isset($this->request->post['payment_paydollar_currency'])) {
			$data['payment_paydollar_currency'] = $this->request->post['payment_paydollar_currency'];
		} else {
			$data['payment_paydollar_currency'] = $this->config->get('payment_paydollar_currency'); 
		} 
		
		$data['paydollar_currencies'] = array();
		$data['paydollar_currencies'][] = array(
			'currency' => "784-AED");
		$data['paydollar_currencies'][] = array(	
			'currency' => "036-AUD");
		$data['paydollar_currencies'][] = array(	
			'currency' => "096-BND");
		$data['paydollar_currencies'][] = array(	
			'currency' => "124-CAD");
		$data['paydollar_currencies'][] = array(	
			'currency' => "156-CNY (RMB)");
		$data['paydollar_currencies'][] = array(	
			'currency' => "978-EUR");
		$data['paydollar_currencies'][] = array(	
			'currency' => "826-GBP");
		$data['paydollar_currencies'][] = array(	
			'currency' => "344-HKD");
		$data['paydollar_currencies'][] = array(	
			'currency' => "360-IDR");
		$data['paydollar_currencies'][] = array(	
			'currency' => "356-INR");
		$data['paydollar_currencies'][] = array(	
			'currency' => "392-JPY");
		$data['paydollar_currencies'][] = array(	
			'currency' => "410-KRW");
		$data['paydollar_currencies'][] = array(	
			'currency' => "446-MOP");
		$data['paydollar_currencies'][] = array(	
			'currency' => "458-MYR");
		$data['paydollar_currencies'][] = array(	
			'currency' => "554-NZD");
		$data['paydollar_currencies'][] = array(	
			'currency' => "608-PHP");
		$data['paydollar_currencies'][] = array(	
			'currency' => "682-SAR");
		$data['paydollar_currencies'][] = array(	
			'currency' => "702-SGD");
		$data['paydollar_currencies'][] = array(	
			'currency' => "764-THB");
		$data['paydollar_currencies'][] = array(	
			'currency' => "901-TWD");
		$data['paydollar_currencies'][] = array(	
			'currency' => "840-USD");
		$data['paydollar_currencies'][] = array(	
			'currency' => "704-VND");

		if (isset($this->request->post['payment_paydollar_transaction_type'])) {
			$data['payment_paydollar_transaction_type'] = $this->request->post['payment_paydollar_transaction_type'];
		} else {
			$data['payment_paydollar_transaction_type'] = $this->config->get('payment_paydollar_transaction_type'); 
		}
		$data['paydollar_transaction_types'] = array();
		$data['paydollar_transaction_types'][] = array(
			'type' => "01-Goods/ Service Purchase");
		$data['paydollar_transaction_types'][] = array(	
			'type' => "03-Check Acceptance");	
		$data['paydollar_transaction_types'][] = array(	
			'type' => "10-Account Funding");	
		$data['paydollar_transaction_types'][] = array(	
			'type' => "11-Quasi-Cash Transaction");	
		$data['paydollar_transaction_types'][] = array(	
			'type' => "28-Prepaid Activation and Load");	



		if (isset($this->request->post['payment_paydollar_challenge_pref'])) {
			$data['payment_paydollar_challenge_pref'] = $this->request->post['payment_paydollar_challenge_pref'];
		} else {
			$data['payment_paydollar_challenge_pref'] = $this->config->get('payment_paydollar_challenge_pref'); 
		}

		$data['paydollar_challenge_prefs'] = array();
		$data['paydollar_challenge_prefs'][] = array(
			'type' => "01-No preference");
		$data['paydollar_challenge_prefs'][] = array(	
			'type' => "02-No challenge requested *");	
		$data['paydollar_challenge_prefs'][] = array(	
			'type' => "03-Challenge requested (Merchant preference)");	
		$data['paydollar_challenge_prefs'][] = array(	
			'type' => "04-Challenge requested (Mandate)");	
		$data['paydollar_challenge_prefs'][] = array(	
			'type' => "05-No challenge requested (transactional risk analysis is already performed) *");
		$data['paydollar_challenge_prefs'][] = array(	
			'type' => "06-No challenge requested (Data share only)*");
		$data['paydollar_challenge_prefs'][] = array(	
			'type' => "07-No challenge requested (strong consumer authentication is already performed) *");
		$data['paydollar_challenge_prefs'][] = array(	
			'type' => "08-No challenge requested (utilise whitelist exemption if no challenge required) *");
		$data['paydollar_challenge_prefs'][] = array(	
			'type' => "09-Challenge requested (whitelist prompt requested if challenge required)");	
		
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		$this->response->setOutput($this->load->view('extension/payment/paydollar', $data));
	}
	
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'extension/payment/paydollar')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		//if (isset($_POST['paydollar_payserverurl'])) { 
		if (!$this->request->post['payment_paydollar_payserverurl']) {
			$this->error['error_payserverurl'] = $this->language->get('error_payserverurl');
		}
		
	
	
	//if (isset($_POST['paydollar_merchant'])) { 
		if (!$this->request->post['payment_paydollar_merchant']) {
			$this->error['error_merchant'] = $this->language->get('error_merchant');
		}
		
		return !$this->error;
	}
}

?>