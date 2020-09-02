<?php  
class ModelExtensionPaymentPayDollar extends Model {
  	public function getMethod($address, $total) {
		$this->load->language('extension/payment/paydollar');
				
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('payment_paydollar_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");
		
		if ($this->config->get('paydollar_total') > 0 && $this->config->get('paydollar_total') > $total) {
			$status = false;
		} elseif (!$this->config->get('payment_paydollar_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}
		
		$method_data = array();
	
		if ($status) {  
      		$method_data = array( 
        		'code'         => 'paydollar',
        		'title'      => $this->language->get('text_title'),
      			'terms'      => '',
				'sort_order' => $this->config->get('payment_paydollar_sort_order')
      		);
    	}
   
    	return $method_data;
  	}

  	public function getDBAcctLogin($email){
    	$query = $this->db->query("SELECT date_added FROM `" . DB_PREFIX . "customer_login` WHERE email = '" . $this->db->escape(utf8_strtolower($email)) . "' ORDER BY date_added DESC");
    	if ($query->num_rows) {
    		return $orderQuery->row['date_added'];
    	}else{
    		return false;
    	}
    	
    }


    public function getDBOrders($cid,$status){
    	$orderStatus = 0;
    	switch ($status) {
    		case '6':// threeDSAcctPurchaseCount purchases during the previous six months
    			$timeQ = date('Y-m-d H:i:s', strtotime("-6 months"));
    			$orderStatus = 5; // complete status only
    			break;
    		case '24'://threeDSAcctNumTransDay purchases during the previous 24 hours(successful/abandoned) 
    			$timeQ = date('Y-m-d H:i:s', strtotime("-1 day"));
    			break;
    		case '12'://threeDSAcctNumTransYear purchases during the previous 1 Year(successful/abandoned) 
    			$timeQ = date('Y-m-d H:i:s', strtotime("-1 year"));
    			break;
    		default:
    			# code...
    			break;
    	}
    	if($orderStatus>0){
    		$orderQuery = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE customer_id = '" . (int)$cid . "' AND customer_id != '0' AND order_status_id > '0' AND order_status_id = '".$orderStatus."' AND date_added >= '$timeQ'");
    		$q = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE customer_id = '" . (int)$cid . "' AND customer_id != '0' AND order_status_id > '0' AND order_status_id = '".$orderStatus."' AND date_added >= '$timeQ'";
    	}else{
    		$orderQuery = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE customer_id = '" . (int)$cid . "' AND customer_id != '0' AND order_status_id > '0' AND date_added >= '$timeQ'");	
    		$q = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE customer_id = '" . (int)$cid . "' AND customer_id != '0' AND order_status_id > '0' AND date_added >= '$timeQ'";
    	}
    	

    	if ($orderQuery->num_rows) {
    		return $orderQuery->row['total'];
    	}else{
    		return false;
    	}
    }

    public function getDateAdded($cid) {
		$query = $this->db->query("SELECT date_added FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$cid . "'");

		return $query->row['date_added'];
	}
}
?>