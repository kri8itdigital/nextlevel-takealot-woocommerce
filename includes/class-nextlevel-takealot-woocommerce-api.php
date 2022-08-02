<?php



class Nextlevel_Takealot_Woocommerce_API{

	var $_URL 		= false;
	var $_KEY 		= false;
	var $_WAREHOUSE = false;
	var $_LEAD_TIME = false;


	public function __construct(){

		$this->_URL 		= get_option('nextlevel_takealot_woocommerce_endpoint');
		$this->_KEY 		= get_option('nextlevel_takealot_woocommerce_key');
		$this->_WAREHOUSE 	= get_option('nextlevel_takealot_woocommerce_warehouse');
		$this->_LEAD_TIME 	= get_option('nextlevel_takealot_woocommerce_lead_days');

	}



	public function DOTAKEALOT(){

		$_CUR_PAGE = 1;

		$_PAGE_SIZE = 50;

		$_TOTAL = $this->GETOFFERCOUNT();
		$_PAGES = $this->GETPAGECOUNT($_TOTAL, $_PAGE_SIZE);
		
		while($_CUR_PAGE <= $_PAGES):
			
			$_OFFERS = $this->GETOFFERS($_CUR_PAGE,$_PAGE_SIZE);

			$_UPDATE = $this->BUILDUPDATE($_OFFERS);
			
			if(count($_UPDATE) > 0):
				
				foreach($_UPDATE as $_ID=>$_DATA):
					$this->RUNUPDATE($_ID, $_DATA);
				endforeach;
				
			endif;
			
			$_CUR_PAGE++;
			
		endwhile;
		

	}




	public function DOCALL($_ENDPOINT, $_DATA = null, $_METHOD = 'GET', $_DEBUG = false){
	
		$_URL = trailingslashit($this->_URL).$_ENDPOINT;

		$_HEADERS = array();
		$_HEADERS[] = 'Content-Type: application/json';
		$_HEADERS[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36';
		$_HEADERS[] = 'Authorization: Key '.$this->_KEY;

		$_CURL = curl_init(); 

		curl_setopt($_CURL,CURLOPT_URL,$_URL);
		curl_setopt($_CURL,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($_CURL,CURLOPT_HTTPHEADER, $_HEADERS);

		if($_METHOD != "GET"){		
			curl_setopt($_CURL, CURLOPT_CUSTOMREQUEST, strtoupper($_METHOD));
		}

		if($_DATA):
			curl_setopt($_CURL, CURLOPT_POSTFIELDS, $_DATA);
		endif;

		$_RES = curl_exec($_CURL);

		curl_close($_CURL);

		$_RES = json_decode($_RES);

		if($_DEBUG):
			echo '<pre>'; print_r($_RES); echo '</pre>';
		endif;

		return $_RES;


	}




	public function GETOFFERCOUNT(){

		$_ENDPOINT = 'offers/count/';

		$_RES = $this->DOCALL($_ENDPOINT);

		return $_RES->count;
	}




	public function GETPAGECOUNT($_TOTAL, $_PAGE_SIZE){

		return (int)ceil(($_TOTAL/$_PAGE_SIZE));

	}




	public function GETOFFERS($_CUR_PAGE,$_PAGE_SIZE){
		
		$_ENDPOINT = 'offers/?page_number='.$_CUR_PAGE.'&page_size='.$_PAGE_SIZE;

		$_RES = $this->DOCALL($_ENDPOINT);

		return $_RES->offers;

	}




	public function BUILDUPDATE($_OFFERS){

		$_UPDATED_OFFERS = array();		

		foreach($_OFFERS as $_O):

			$_PROD 	= wc_get_product_id_by_sku($_O->sku);

			try{

				if($_PROD && (int)$_PROD > 0):
					
					$_CLASS 	= wc_get_product($_PROD);
					
					if(strstr($_O->sku, '/')):
						$_OFFER = 'ID'.$_O->offer_id;
					else:
						$_OFFER = 'SKU'.$_O->sku;
					endif;

					$_ITEM 		= $_O->sku;
					$_LEAD 		= $_O->leadtime_days;
					$_STATUS 	= $_O->status;
					$_STOCK 	= $_CLASS->get_stock_quantity();

					if(!$_STOCK):
						$_STOCK = 0;
					endif;

					$_PRICE 	= (float)$_CLASS->get_price();
					$_RRP 		= (float)$_CLASS->get_regular_price();

					if(!$_RRP):
						$_RRP = $_PRICE;
					endif;

					$_STOCK = (int)$_STOCK;

					if((int)$_LEAD != $this->_LEAD_TIME):
						$_LEAD = $this->_LEAD_TIME;
					endif;

					if($_STOCK > 0 && $_PRICE > 0):
						$_STATUS = 'Re-enable';
					endif;

					if($_PRICE > 0):
						$_UPDATED_OFFERS[$_OFFER] = array(
							'sku' => $_ITEM, 
							'selling_price' => $_PRICE, 
							'rrp' => $_RRP, 
							'leadtime_days' => $_LEAD, 
							'leadtime_stock' => array(
								array('merchant_warehouse_id' => $this->_WAREHOUSE, 'quantity' => $_STOCK)
							),
							'status_action' => $_STATUS
						);
					endif;

					

				endif;

			}catch(Exception $e){

			}

		endforeach;

		return $_UPDATED_OFFERS;

	}






	public function RUNUPDATE($_ID, $_DATA){

		$_ENDPOINT = 'offers/offer/'.$_ID.'/';

		$_DATA = json_encode($_DATA, JSON_NUMERIC_CHECK);

		$this->DOCALL($_ENDPOINT, $_DATA, "PATCH");

	}


	


}





?>