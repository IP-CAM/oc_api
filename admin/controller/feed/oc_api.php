<?php

class Controllerfeedocapi extends Controller {
	
	function _generate_key($length = 32) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyz';
		$string = '';
		for ($p = 0; $p < $length; $p++) {
			$string .= $characters[mt_rand(0, strlen($characters)-1)];
		}
		return $string;
	}

	public function index() {
		$this->load->language('feed/oc_api');
		$this->load->model('setting/setting');

		$this->document->setTitle($this->language->get('heading_title'));


		$this->data = array(
			'version'             => '1.0',
			'heading_title'       => $this->language->get('heading_title'),
			
			'text_enabled'        => $this->language->get('text_enabled'),
			'text_disabled'       => $this->language->get('text_disabled'),
			'text_homepage'       => $this->language->get('text_homepage'),
			'tab_general'         => $this->language->get('tab_general'),

			'entry_status'        => $this->language->get('entry_status'),
			'entry_appid'		  => $this->language->get('entry_appid'),
			'entry_key'           => $this->language->get('entry_key'),
			'entry_pubkey'        => $this->language->get('entry_pubkey'),

			'button_save'         => $this->language->get('button_save'),
			'button_cancel'       => $this->language->get('button_cancel'),

			'action'              => $this->url->link('feed/oc_api', 'token=' . $this->session->data['token'], 'SSL'),
			'cancel'              => $this->url->link('extension/feed', 'token=' . $this->session->data['token'], 'SSL')
		);

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$this->model_setting_setting->editSetting('oc_api', $this->request->post);				
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect($this->url->link('extension/feed', 'token=' . $this->session->data['token'], 'SSL'));
		}

  		$this->data['breadcrumbs'] = array();

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_feed'),
			'href'      => $this->url->link('extension/feed', 'token=' . $this->session->data['token'], 'SSL'),       		
			'separator' => ' :: '
   		);

   		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('feed/oc_api', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
   		);

   		if (isset($this->request->post['oc_api_status'])) {
			$this->data['oc_api_status'] = $this->request->post['oc_api_status'];
		} else {
			$this->data['oc_api_status'] = $this->config->get('oc_api_status');
		}
		
		
		if (isset($this->request->post['oc_api_appid'])) {
			$this->data['oc_api_appid'] = $this->request->post['oc_api_appid'];
		} else {
			if(strlen($this->config->get('oc_api_pubkey'))==0){
				$this->data['oc_api_appid'] = 'APP001';
			}else{
				$this->data['oc_api_appid'] = $this->config->get('oc_api_appid');
			}
		}

		if (isset($this->request->post['oc_api_pubkey'])) {
			$this->data['oc_api_pubkey'] = $this->request->post['oc_api_pubkey'];
		} else {
			if(strlen($this->config->get('oc_api_pubkey'))==0){
				$this->data['oc_api_pubkey'] = $this->_generate_key();
			}else{
				$this->data['oc_api_pubkey'] = $this->config->get('oc_api_pubkey');
			}
		}

		if (isset($this->request->post['oc_api_key'])) {
			$this->data['oc_api_key'] = $this->request->post['oc_api_key'];
		} else {
			if(strlen($this->config->get('oc_api_key'))==0){
				$this->data['oc_api_key'] = $this->_generate_key();
			}else{
				$this->data['oc_api_key'] = $this->config->get('oc_api_key');
			}
		}


   		$this->template = 'feed/oc_api.tpl';
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

}
