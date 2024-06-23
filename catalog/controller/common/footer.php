<?php
class ControllerCommonFooter extends Controller {
	public function index() {
		$this->load->language('common/footer');

		$this->load->model('catalog/category');

		$this->load->model('catalog/information');

		$data['informations'] = array();

		$data['categories'] = array();

		$categories = $this->model_catalog_category->getCategories(105);

		foreach ($categories as $category) {
			if ($category['top']) {
				// Level 2
				$children_data = array();
	
				$children = $this->model_catalog_category->getCategories($category['category_id']);
	
				foreach ($children as $child) {
	
					// Level 3
					$grandchildren_data = array();
	
					$grandchildren = $this->model_catalog_category->getCategories($child['category_id']);
	
					foreach ($grandchildren as $grandchild) {
	
						$grandchild_filter_data = array(
							'filter_category_id'  => $grandchild['category_id'],
							'filter_sub_category' => true
						);
	
						$grandchildren_data[] = array(
							'name'  => $grandchild['name'],
							'href'  => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id'] . '_' . $grandchild['category_id']),
						);
					}
	
	
					$filter_data = array(
						'filter_category_id'  => $child['category_id'],
						'filter_sub_category' => true
					);
	
					$children_data[] = array(
						'name'  => $child['name'],
						'image' => $category['image'],
						'href'  => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id']),
						'children' => $grandchildren_data,
					);
				}
	
				// Level 1
				$data['categories'][] = array(
					'name'     => $category['name'],             
					'image'    => $category['image'],     
					'href'     => $this->url->link('product/category', 'path=' . $category['category_id']),
					'children' => $children_data,
					'column'   => $category['column'] ? $category['column'] : 1,
				);
			}
		}
		
		

		$data['home'] = $this->url->link('common/home');
		$data['faq'] = $this->url->link('information/faq');
		$data['tracker'] = $this->url->link('information/tracker');
		$data['calculator'] = $this->url->link('information/calculator');
		$data['logged'] = $this->customer->isLogged();
		$data['login'] = $this->url->link('account/login', '', true);
		$data['logout'] = $this->url->link('account/logout', '', true);
		$data['shopping_cart'] = $this->url->link('checkout/cart');
		$data['contact'] = $this->url->link('information/contact');
		$data['return'] = $this->url->link('account/return/add', '', true);
		$data['sitemap'] = $this->url->link('information/sitemap');
		$data['tracking'] = $this->url->link('information/tracking');
		$data['manufacturer'] = $this->url->link('product/manufacturer');
		$data['voucher'] = $this->url->link('account/voucher', '', true);
		$data['special'] = $this->url->link('product/special');
		$data['account'] = $this->url->link('account/account', '', true);
		$data['order'] = $this->url->link('account/order', '', true);
		$data['wishlist'] = $this->url->link('account/wishlist', '', true);
		$data['newsletter'] = $this->url->link('account/newsletter', '', true);
		$data['email'] = $this->config->get('config_email');
		$data['telephone'] = $this->config->get('config_telephone');
		$data['address'] = html_entity_decode($this->config->get('config_address'), ENT_QUOTES, 'UTF-8');

		$data['powered'] = sprintf($this->language->get('text_powered'), $this->config->get('config_name'), date('Y', time()));

		// Whos Online
		if ($this->config->get('config_customer_online')) {
			$this->load->model('tool/online');

			if (isset($this->request->server['REMOTE_ADDR'])) {
				$ip = $this->request->server['REMOTE_ADDR'];
			} else {
				$ip = '';
			}

			if (isset($this->request->server['HTTP_HOST']) && isset($this->request->server['REQUEST_URI'])) {
				$url = ($this->request->server['HTTPS'] ? 'https://' : 'http://') . $this->request->server['HTTP_HOST'] . $this->request->server['REQUEST_URI'];
			} else {
				$url = '';
			}

			if (isset($this->request->server['HTTP_REFERER'])) {
				$referer = $this->request->server['HTTP_REFERER'];
			} else {
				$referer = '';
			}

			$this->model_tool_online->addOnline($ip, $this->customer->getId(), $url, $referer);
		}

		$data['scripts'] = $this->document->getScripts('footer');
		$data['styles'] = $this->document->getStyles('footer');
		
		return $this->load->view('common/footer', $data);
	}
}
