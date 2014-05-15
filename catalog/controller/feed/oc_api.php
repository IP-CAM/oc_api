<?php

class Controllerfeedocapi extends Controller {

	var $request_params;
	
	private function check_api_key_pairs(){
		$enc_request = $this->request->get['request'];
		$app_id = $this->request->get['appid'];
		
		if ($this->config->get('oc_api_appid') != $app_id){
		  $this->error(3001, 'Application does not exist!');
		}
			
		$this->request_params = json_decode(trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->config->get('oc_api_key'), base64_decode($enc_request), MCRYPT_MODE_ECB)));
			
		if( $this->request_params == false || isset($this->request_params->publickkey) == false ){
		  	$this->error(2004, 'Requests made not valid.');
		}else{
			if($this->request_params->publickkey != $this->config->get('oc_api_pubkey')){
				$this->error(2003, 'Invalid Public key.');
			}
		}
	}#check_api_key_pairs

	private function init() {
		$this->response->addHeader('Content-Type: application/json');
		//BEGIN:: Check configuration settings
		if (!$this->config->get('oc_api_status')) {
			$this->error(1001, 'API is disabled'); 
		}#if api status
		if (!$this->config->get('oc_api_appid')){
			$this->error(1002, 'API is disabled as there are no API ID was set.');
		}#if appid	
		if (!$this->config->get('oc_api_key')) {
			$this->error(1003, 'API is disabled as there are no API Key was set.');
		}#if key
		//END:: Check configuration settings
		
		//BEGIN:: Check parameters
		if(!isset($this->request->get['appid'])){
			$this->error(2001, 'Invalid APP ID. App ID was not passed.');
		}#if appid
		if(!isset($this->request->get['request'])){
			$this->error(2002, 'No Valid Requests made.');
		}#if request
		//END:: Check parameters
		
		
		$this->check_api_key_pairs();
		
	}#function init

	/**
	 * Error message responser
	 *
	 * @param string $message  Error message
	 */
	private function error($code = 0, $message = '') {
		# setOutput() is not called, set headers manually
		header('Content-Type: application/json');

		$json = array(
			'success'       => false,
			'code'          => $code,
			'message'       => $message
		);

		echo json_encode($json);
		exit();
	}#function error
	
	public function categories() {
		$this->init();
		$this->load->model('catalog/category');
		$json = array('success' => true);
		
		$json['categories'] = $this->getCategories(0, 1);

		$this->response->setOutput(json_encode($json));
	}#function categories

	private function getCategories($parent = 0, $level = 1) {
		$this->load->model('catalog/category');
		$this->load->model('tool/image');
		
		$result = array();

		$categories = $this->model_catalog_category->getCategories($parent);

		if ($categories && $level > 0) {
			$level--;

			foreach ($categories as $category) {

				if ($category['image']) {
					$image = $this->model_tool_image->resize($category['image'], $this->config->get('config_image_category_width'), $this->config->get('config_image_category_height'));
				} else {
					$image = false;
				}

				$result[] = array(
					'category_id'   => $category['category_id'],
					'parent_id'     => $category['parent_id'],
					'name'          => $category['name'],
					'image'         => $image,
					'href'          => $this->url->link('product/category', 'path=' . $category['category_id']),
					'categories'    => $this->getCategories($category['category_id'], $level)
				);
			}

			return $result;
		}
	}#getCategories
	
	/*
	possible  arguments
	filter_category_id
	filter_sub_category
	filter_filter
	filter_name
	filter_tag
	filter_description
	filter_manufacturer_id

	'sort'  => 'p.date_added',
	'order' => 'DESC',
	'start' => 0,
	'limit' => 2
	*/
	private function getProducts($args){
		$this->load->model('catalog/product');
		$this->load->model('tool/image');
		$result = array();
		$products = $this->model_catalog_product->getProducts($args);
		
		foreach ($products as $product) {
			if ($product['image']) {
				$image = $this->model_tool_image->resize($product['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
			} else {
				$image = false;
			}

			if ((float)$product['special']) {
				$special = $this->currency->format($this->tax->calculate($product['special'], $product['tax_class_id'], $this->config->get('config_tax')));
			} else {
				$special = false;
			}

			$result[] = array(
				'id'                    => $product['product_id'],
				'name'                  => $product['name'],
				'description'           => $product['description'],
				'price'                 => $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'))),
				'href'                  => $this->url->link('product/product', 'product_id=' . $product['product_id']),
				'thumb'                 => $image,
				'special'               => $special,
				'rating'                => $product['rating']
			);
		}#foreach
		return $result;
	}#function getProducts
	
	/*parameters
	category
	limit
	latest = 1
	*/
	public function products() {
		$this->init();
		$this->load->model('catalog/product');
		$this->load->model('tool/image');
		$json = array('success' => true, 'products' => array());


		# -- $_GET params ------------------------------
		
		if (isset($this->request_params->category)) {
			$category_id = $this->request_params->category;
		} else {
			$category_id = 0;
		}
		
		if (isset($this->request->get['limit'])) {
			$limit = $this->request->get['limit'];
		} else {
			$limit = 5;
		}
		if (isset($this->request->get['latest']) && $this->request->get['latest'] == 1) {
			$sort = 'p.date_added';
			$order = 'DESC';
		}else{
			$sort = '';
			$order = '';
		}

		# -- End $_GET params --------------------------

		$args = array(
			'filter_category_id' => $category_id,
			'sort'  => $sort,
			'order' => $order,
			'start' => 0,
			'limit' => $limit,
		);

		$json['products'] = $this->getProducts($args);
		
		$this->response->setOutput(json_encode($json));
	}#function products

}#class
