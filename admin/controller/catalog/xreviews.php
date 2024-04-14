<?php
class ControllerCatalogXreviews extends Controller { 
	
	private $error = array();
	
	public function index() {
		$this->load->language('catalog/xreviews');
		$this->document->setTitle($this->language->get('page_title'));
		$this->document->addStyle('view/stylesheet/xreviews.css');
		$this->load->model('catalog/xreviews');
		$this->model_catalog_xreviews->createDatabaseTables();
		$this->getList();
	}

	public function add() {
		$this->load->language('catalog/xreviews');

		$this->document->setTitle($this->language->get('page_title'));
		
		$this->load->model('catalog/xreviews');
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_xreviews->addxreviews($this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';
			
			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}
			
			$this->response->redirect($this->url->link('catalog/xreviews', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('catalog/xreviews');

		$this->document->setTitle( $this->language->get('page_title') );
		
		$this->load->model('catalog/xreviews');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_xreviews->editxreviews($this->request->get['xreviews_id'], $this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';
			
			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}
			
			$this->response->redirect($this->url->link('catalog/xreviews', 'user_token=' . $this->session->data['user_token'] . $url, true));

		}
		$this->getForm();
	}
 
	public function delete() {
		$this->load->language('catalog/xreviews');

		$this->document->setTitle( $this->language->get('page_title'));
		
		$this->load->model('catalog/xreviews');
		
		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $xreviews_id) {
				$this->model_catalog_xreviews->deletexreviews($xreviews_id);
		}
			
			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';
			
			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}
			
			$this->response->redirect($this->url->link('catalog/xreviews', 'user_token=' . $this->session->data['user_token'] . $url, true));

		}

		$this->getList();
	}

	private function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'date_added';
		}
		
		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		
		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('page_title'),
			'href' => $this->url->link('catalog/xreviews', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('catalog/xreviews/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('catalog/xreviews/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['xreviewss'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);
		
		$xreviews_total = $this->model_catalog_xreviews->getTotalxreviewss();
		$data['xreviews_total'] = $xreviews_total;
		
		$results = $this->model_catalog_xreviews->getxreviewss($filter_data);


    	foreach ($results as $result) {		
			$data['xreviewss'][] = array(
				'xreviews_id' => $result['xreviews_id'],
				'name'			 => $result['name'],
				'text'			 => mb_substr(strip_tags(html_entity_decode($result['text'])), 0, 90, 'UTF-8').'...',
				'good'			 => mb_substr(strip_tags(html_entity_decode($result['good'])), 0, 90, 'UTF-8').'...',
				'bad'			 => mb_substr(strip_tags(html_entity_decode($result['bad'])), 0, 90, 'UTF-8').'...',
				'city'			 => $result['city'],
				'title'          => $result['title'],
				'date_added' 	 => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'status' 		 => ($result['status'] ? '<span style="color:green">'.$this->language->get('text_enabled').'</span>' : '<span style="color:red">'.$this->language->get('text_disabled').'</span>'),
				'edit'       => $this->url->link('catalog/xreviews/edit', 'user_token=' . $this->session->data['user_token'] . '&xreviews_id=' . $result['xreviews_id'] . $url, true)
			);
		}

 		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		if ($this->config->get('xreviews_title')) {
			$data['xreviews_title'] = $this->config->get('xreviews_title');
		} else {
			$data['xreviews_title'] = 0;
		}

		if ($this->config->get('xreviews_text')) {
			$data['xreviews_text'] = $this->config->get('xreviews_text');
		} else {
			$data['xreviews_text'] = 0;
		}

		if ($this->config->get('xreviews_bad')) {
			$data['xreviews_bad'] = $this->config->get('xreviews_bad');
		} else {
			$data['xreviews_bad'] = 0;
		}

		if ($this->config->get('xreviews_good')) {
			$data['xreviews_good'] = $this->config->get('xreviews_good');
		} else {
			$data['xreviews_good'] = 0;
		}

		if ($this->config->get('xreviews_name')) {
			$data['xreviews_name'] = $this->config->get('xreviews_name');
		} else {
			$data['xreviews_name'] = 0;
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		$data['sort_title'] = $this->url->link('catalog/xreviews', 'user_token=' . $this->session->data['user_token'] . '&sort=title' . $url, true);
		$data['sort_date_added'] = $this->url->link('catalog/xreviews', 'user_token=' . $this->session->data['user_token'] . '&sort=date_added' . $url, true);		
		$data['sort_status'] = $this->url->link('catalog/xreviews', 'user_token=' . $this->session->data['user_token'] . '&sort=status' . $url, true);		
		$data['sort_name'] = $this->url->link('catalog/xreviews', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);		
		$data['sort_text'] = $this->url->link('catalog/xreviews', 'user_token=' . $this->session->data['user_token'] . '&sort=text' . $url, true);
		$data['sort_good'] = $this->url->link('catalog/xreviews', 'user_token=' . $this->session->data['user_token'] . '&sort=good' . $url, true);
		$data['sort_bad'] = $this->url->link('catalog/xreviews', 'user_token=' . $this->session->data['user_token'] . '&sort=bad' . $url, true);		
		
		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
												
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $xreviews_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('catalog/xreviews', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);
		
		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($xreviews_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($xreviews_total - $this->config->get('config_limit_admin'))) ? $xreviews_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $xreviews_total, ceil($xreviews_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/xreviews_list', $data));
	}

	protected function getForm() {
		$this->document->addStyle('view/stylesheet/xreviews.css');
		$data['text_form'] = !isset($this->request->get['xreviews_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

 		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$url = '';
			
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('page_title'),
			'href' => $this->url->link('catalog/xreviews', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if ($this->config->get('xreviews_title')) {
			$data['xreviews_title'] = $this->config->get('xreviews_title');
		} else {
			$data['xreviews_title'] = 0;
		}

		if ($this->config->get('xreviews_text')) {
			$data['xreviews_text'] = $this->config->get('xreviews_text');
		} else {
			$data['xreviews_text'] = 0;
		}

		if ($this->config->get('xreviews_bad')) {
			$data['xreviews_bad'] = $this->config->get('xreviews_bad');
		} else {
			$data['xreviews_bad'] = 0;
		}

		if ($this->config->get('xreviews_good')) {
			$data['xreviews_good'] = $this->config->get('xreviews_good');
		} else {
			$data['xreviews_good'] = 0;
		}

		if ($this->config->get('xreviews_name')) {
			$data['xreviews_name'] = $this->config->get('xreviews_name');
		} else {
			$data['xreviews_name'] = 0;
		}

		if ($this->config->get('xreviews_city')) {
			$data['xreviews_city'] = $this->config->get('xreviews_city');
		} else {
			$data['xreviews_city'] = 0;
		}

		if ($this->config->get('xreviews_photo')) {
			$data['xreviews_photo'] = $this->config->get('xreviews_photo');
		} else {
			$data['xreviews_photo'] = 0;
		}

		if ($this->config->get('xreviews_rating')) {
			$data['xreviews_rating'] = $this->config->get('xreviews_rating');
		} else {
			$data['xreviews_rating'] = 0;
		}

		if ($this->config->get('xreviews_email')) {
			$data['xreviews_email'] = $this->config->get('xreviews_email');
		} else {
			$data['xreviews_email'] = 0;
		}

		if (!isset($this->request->get['xreviews_id'])) {
			$data['action'] = $this->url->link('catalog/xreviews/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('catalog/xreviews/edit', 'user_token=' . $this->session->data['user_token'] . '&xreviews_id=' . $this->request->get['xreviews_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('catalog/xreviews', 'user_token=' . $this->session->data['user_token'] . $url, true);
		
		if (isset($this->request->get['xreviews_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$xreviews_info = $this->model_catalog_xreviews->getxreviews($this->request->get['xreviews_id']);
		}
		
		if (isset($this->request->post['text'])) {
			$data['text'] = $this->request->post['text'];
		} elseif (isset($xreviews_info)) {
			$data['text'] = $xreviews_info['text'];
		} else {
			$data['text'] = '';
		}
		
		if (isset($this->request->post['good'])) {
			$data['good'] = $this->request->post['good'];
		} elseif (isset($xreviews_info)) {
			$data['good'] = $xreviews_info['good'];
		} else {
			$data['good'] = '';
		}
		
		if (isset($this->request->post['bad'])) {
			$data['bad'] = $this->request->post['bad'];
		} elseif (isset($xreviews_info)) {
			$data['bad'] = $xreviews_info['bad'];
		} else {
			$data['bad'] = '';
		}
		
		if (isset($this->request->post['comment'])) {
			$data['comment'] = $this->request->post['comment'];
		} elseif (isset($xreviews_info)) {
			$data['comment'] = $xreviews_info['comment'];
		} else {
			$data['comment'] = '';
		}

		if (isset($this->request->post['title'])) {
			$data['title'] = $this->request->post['title'];
		} elseif (isset($xreviews_info)) {
			$data['title'] = $xreviews_info['title'];
		} else {
			$data['title'] = '';
		}
		
		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (isset($xreviews_info)) {
			$data['name'] = $xreviews_info['name'];
		} else {
			$data['name'] = '';
		}

		if (isset($this->request->post['city'])) {
			$data['city'] = $this->request->post['city'];
		} elseif (isset($xreviews_info)) {
			$data['city'] = $xreviews_info['city'];
		} else {
			$data['city'] = '';
		}		

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (isset($xreviews_info)) {
			$data['status'] = $xreviews_info['status'];
		} else {
			$data['status'] = 1;
		}

		if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		} elseif (isset($xreviews_info)) {
			$data['email'] = $xreviews_info['email'];
		} else {
			$data['email'] = '';
		}

		if (isset($this->request->post['date_added'])) {
			$data['date_added'] = $this->request->post['date_added'];
		} elseif (isset($xreviews_info)) {
			$data['date_added'] = $xreviews_info['date_added'];
		} else {
			$data['date_added'] = date("Y-m-d H:i:s");
		}

		if (isset($this->request->post['rating'])) {
			$data['rating'] = $this->request->post['rating'];
		} elseif (isset($xreviews_info)) {
			$data['rating'] = $xreviews_info['rating'];
		} else {
			$data['rating'] = '5';
		}
		
		// Photo
		if (isset($this->request->post['photo'])) {
			$data['photo'] = $this->request->post['photo'];
		} elseif (!empty($xreviews_info)) {
			$data['photo'] = $xreviews_info['photo'];
		} else {
			$data['photo'] = '';
		}

		$this->load->model('tool/image');

		if (isset($this->request->post['photo']) && is_file(DIR_IMAGE . $this->request->post['photo'])) {
			$data['thumb'] = $this->model_tool_image->resize($this->request->post['photo'], 100, 100);
		} elseif (!empty($xreviews_info) && is_file(DIR_IMAGE . $xreviews_info['photo'])) {
			$data['thumb'] = $this->model_tool_image->resize($xreviews_info['photo'], 100, 100);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		
		$this->load->model('setting/store');

		$data['stores'] = array();
		
		$data['stores'][] = array(
			'store_id' => 0,
			'name'     => $this->language->get('text_default')
		);
		
		$stores = $this->model_setting_store->getStores();

		foreach ($stores as $store) {
			$data['stores'][] = array(
				'store_id' => $store['store_id'],
				'name'     => $store['name']
			);
		}

		if (isset($this->request->post['xreviews_store'])) {
			$data['xreviews_store'] = $this->request->post['xreviews_store'];
		} elseif (isset($this->request->get['xreviews_id'])) {
			$data['xreviews_store'] = $this->model_catalog_xreviews->getxreviewsStores($this->request->get['xreviews_id']);
		} else {
			$data['xreviews_store'] = array(0);
		}
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/xreviews_form', $data));
	}

	public function setting() {

		$this->load->language('catalog/xreviews_setting');

		$this->document->setTitle($this->language->get('page_title'));
		$this->document->addStyle('view/stylesheet/xreviews.css');

		$this->load->model('setting/setting');

		if (isset($this->request->get['store_id'])) {
			$store_id = $this->request->get['store_id'];
		} else {
			$store_id = 0;
		}

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('xreviews', $this->request->post, $store_id);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('catalog/xreviews/setting', 'user_token=' . $this->session->data['user_token'], true));
		}

 		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$url = '';
			
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('page_title'),
			'href' => $this->url->link('catalog/xreviews/setting', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['action'] = $this->url->link('catalog/xreviews/setting', 'user_token=' . $this->session->data['user_token'] . $url, true);
		
		if (isset($this->request->post['xreviews_guest'])) {
			$data['xreviews_guest'] = $this->request->post['xreviews_guest'];
		} else {
			$data['xreviews_guest'] = $this->config->get('xreviews_guest');
		}
		if (isset($this->request->post['xreviews_mail'])) {
			$data['xreviews_mail'] = $this->request->post['xreviews_mail'];
		} else {
			$data['xreviews_mail'] = $this->config->get('xreviews_mail');
		}
		
		if (isset($this->request->post['xreviews_status'])) {
			$data['xreviews_status'] = $this->request->post['xreviews_status'];
		} else {
			$data['xreviews_status'] = $this->config->get('xreviews_status');
		}
		
		if (isset($this->request->post['xreviews_title'])) {
			$data['xreviews_title'] = $this->request->post['xreviews_title'];
		} else {
			$data['xreviews_title'] = $this->config->get('xreviews_title');
		}
		
		if (isset($this->request->post['xreviews_city'])) {
			$data['xreviews_city'] = $this->request->post['xreviews_city'];
		} else {
			$data['xreviews_city'] = $this->config->get('xreviews_city');
		}
		
		if (isset($this->request->post['xreviews_text'])) {
			$data['xreviews_text'] = $this->request->post['xreviews_text'];
		} else {
			$data['xreviews_text'] = $this->config->get('xreviews_text');
		}
		
		if (isset($this->request->post['xreviews_email'])) {
			$data['xreviews_email'] = $this->request->post['xreviews_email'];
		} else {
			$data['xreviews_email'] = $this->config->get('xreviews_email');
		}
		
		if (isset($this->request->post['xreviews_name'])) {
			$data['xreviews_name'] = $this->request->post['xreviews_name'];
		} else {
			$data['xreviews_name'] = $this->config->get('xreviews_name');
		}
		
		if (isset($this->request->post['xreviews_good'])) {
			$data['xreviews_good'] = $this->request->post['xreviews_good'];
		} else {
			$data['xreviews_good'] = $this->config->get('xreviews_good');
		}
		
		if (isset($this->request->post['xreviews_bad'])) {
			$data['xreviews_bad'] = $this->request->post['xreviews_bad'];
		} else {
			$data['xreviews_bad'] = $this->config->get('xreviews_bad');
		}
		
		if (isset($this->request->post['xreviews_rating'])) {
			$data['xreviews_rating'] = $this->request->post['xreviews_rating'];
		} else {
			$data['xreviews_rating'] = $this->config->get('xreviews_rating');
		}
		
		if (isset($this->request->post['xreviews_photo'])) {
			$data['xreviews_photo'] = $this->request->post['xreviews_photo'];
		} else {
			$data['xreviews_photo'] = $this->config->get('xreviews_photo');
		}
		
		if (isset($this->request->post['xreviews_photo_width'])) {
			$data['xreviews_photo_width'] = $this->request->post['xreviews_photo_width'];
		} elseif ($this->config->get('xreviews_photo_width')) {
			$data['xreviews_photo_width'] = $this->config->get('xreviews_photo_width');
		} else {
			$data['xreviews_photo_width'] = 100;
		}
		
		if (isset($this->request->post['xreviews_photo_height'])) {
			$data['xreviews_photo_height'] = $this->request->post['xreviews_photo_height'];
		} elseif ($this->config->get('xreviews_photo_height')) {
			$data['xreviews_photo_height'] = $this->config->get('xreviews_photo_height');
		} else {
			$data['xreviews_photo_height'] = 100;
		}
		
		if (isset($this->request->post['xreviews_date_added'])) {
			$data['xreviews_date_added'] = $this->request->post['xreviews_date_added'];
		} else {
			$data['xreviews_date_added'] = $this->config->get('xreviews_date_added');
		}
		
		if (isset($this->request->post['xreviews_limit'])) {
			$data['xreviews_limit'] = $this->request->post['xreviews_limit'];
		} else {
			if ((int)$this->config->get('xreviews_limit') == 0) {
				$data['xreviews_limit'] = 10;
			} else {
				$data['xreviews_limit'] = $this->config->get('xreviews_limit');
			}
		}
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/xreviews_setting', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'catalog/xreviews')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	private function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/xreviews')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	private function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/information')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
?>