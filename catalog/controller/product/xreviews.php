<?php 
class ControllerProductXreviews extends Controller {
	
	public function index() {  
    	$this->language->load('product/xreviews');
		
		$this->load->model('catalog/xreviews');
		
		$this->document->setTitle ($this->language->get('heading_title'));
		$this->document->addStyle('catalog/view/theme/default/stylesheet/xreviews.css');

		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home')
   		);
		
		$data['breadcrumbs'][] = array(
	       	'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('product/xreviews')
	   	);

		$data['text_login'] = sprintf($this->language->get('text_login'), $this->url->link('account/login', ''), $this->url->link('account/register', ''));
			
		$data['continue'] = $this->url->link('common/home', '');
		
		$data['review_status'] = $this->config->get('config_review_status');

		if ($this->config->get('xreviews_guest') || $this->customer->isLogged()) {
			$data['guest'] = true;
		} else {
			$data['guest'] = false;
		}
		
		if ($this->config->get('xreviews_title')) {
			$data['title'] = $this->config->get('xreviews_title');
		} else {
			$data['title'] = 0;
		}
		
		if ($this->config->get('xreviews_city')) {
			$data['city'] = $this->config->get('xreviews_city');
		} else {
			$data['city'] = 0;
		}
		
		if ($this->config->get('xreviews_email')) {
			$data['email'] = $this->config->get('xreviews_email');
		} else {
			$data['email'] = 0;
		}
		
		if ($this->config->get('xreviews_name')) {
			$data['name'] = $this->config->get('xreviews_name');
		} else {
			$data['name'] = 0;
		}
		
		if ($this->config->get('xreviews_text')) {
			$data['text'] = $this->config->get('xreviews_text');
		} else {
			$data['text'] = 0;
		}
		
		if ($this->config->get('xreviews_good')) {
			$data['good'] = $this->config->get('xreviews_good');
		} else {
			$data['good'] = 0;
		}
		
		if ($this->config->get('xreviews_bad')) {
			$data['bad'] = $this->config->get('xreviews_bad');
		} else {
			$data['bad'] = 0;
		}
		
		if ($this->config->get('xreviews_rating')) {
			$data['rating'] = $this->config->get('xreviews_rating');
		} else {
			$data['rating'] = 0;
		}
		
		if ($this->config->get('xreviews_photo')) {
			$data['photo'] = $this->config->get('xreviews_photo');
		} else {
			$data['photo'] = 0;
		}
		
		// Captcha
		if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('review', (array)$this->config->get('config_captcha_page'))) {
			$data['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'));
		} else {
			$data['captcha'] = '';
		}
			
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');		
			
		$this->response->setOutput($this->load->view('product/xreviews', $data));
  	}

	public function xreviews() {
		$this->load->language('product/xreviews');

		$this->load->model('catalog/xreviews');

		$data['text_no_xreviewss'] = $this->language->get('text_no_xreviewss');
		$data['entry_text'] = $this->language->get('entry_text');
		$data['entry_good'] = $this->language->get('entry_good');
		$data['entry_bad'] = $this->language->get('entry_bad');
		$data['entry_comment'] = $this->language->get('entry_comment');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		if (isset($this->request->get['limit'])) {
			$limit = (int)$this->request->get['limit'];
		} else {
			$limit = $this->config->get('xreviews_limit');
		}
		
		$data['xreviewss'] = array();
		
		$this->page_limit = $this->config->get('limit');
		
		$filter_data = array(
				'start'              => ($page - 1) * $limit,
				'limit'              => $limit
		);
		
		$xreviews_total = $this->model_catalog_xreviews->getTotalxreviewss();
			
		$results = $this->model_catalog_xreviews->getxreviewss($filter_data);

		$this->load->model('tool/image');
		
		$data['photo'] = $this->config->get('xreviews_photo');
		$data['rating'] = $this->config->get('xreviews_rating');
		$data['date_added'] = $this->config->get('xreviews_date_added');
		
		foreach ($results as $result) {

			if ($result['photo']) {
				$photo = $this->model_tool_image->resize($result['photo'], $this->config->get('xreviews_photo_width'), $this->config->get('xreviews_photo_height'));
			} else {
				$photo = $this->model_tool_image->resize('catalog/avatar/no_photo.png', $this->config->get('xreviews_photo_width'), $this->config->get('xreviews_photo_height'));;
			}
				
			$data['xreviewss'][] = array(
				'title'    		=> $result['title'],
				'city'			=> $result['city'],
				'name'			=> $result['name'],
				'text'			=> $result['text'],
				'good'			=> $result['good'],
				'bad'			=> $result['bad'],
				'rating'		=> $result['rating'],
				'photo'			=> $photo,
				'comment'		=> $result['comment'],
				'date_added' 	=> date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);
		}

		$pagination = new Pagination();
		$pagination->total = $xreviews_total;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->url = $this->url->link('product/xreviews/xreviews', '&page={page}');

		$data['pagination'] = $pagination->render();

		$this->response->setOutput($this->load->view('product/xreviews_list', $data));
	}
	
	public function upload() {
		$this->load->language('tool/upload');

		$json = array();

		if (!empty($this->request->files['file']['name']) && is_file($this->request->files['file']['tmp_name'])) {
			// Sanitize the filename
			$filename = basename(preg_replace('/[^a-zA-Z0-9\.\-\s+]/', '', html_entity_decode($this->request->files['file']['name'], ENT_QUOTES, 'UTF-8')));

			// Validate the filename length
			if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 64)) {
				$json['error'] = $this->language->get('error_filename');
			}

			// Allowed file extension types
			$allowed = array();

			$extension_allowed = 'png,jpe,jpeg,jpg,gif';

			$filetypes = explode(",", $extension_allowed);

			foreach ($filetypes as $filetype) {
				$allowed[] = trim($filetype);
			}

			if (!in_array(strtolower(substr(strrchr($filename, '.'), 1)), $allowed)) {
				$json['error'] = $this->language->get('error_filetype');
			}

			// Allowed file mime types
			$allowed = array();

			$mime_allowed = 'image/png,image/jpeg,image/gif';

			$filetypes = explode(",", $mime_allowed);

			foreach ($filetypes as $filetype) {
				$allowed[] = trim($filetype);
			}

			if (!in_array($this->request->files['file']['type'], $allowed)) {
				$json['error'] = $this->language->get('error_filetype');
			}

			// Check to see if any PHP files are trying to be uploaded
			$content = file_get_contents($this->request->files['file']['tmp_name']);

			if (preg_match('/\<\?php/i', $content)) {
				$json['error'] = $this->language->get('error_filetype');
			}

			// Return any upload error
			if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
				$json['error'] = $this->language->get('error_upload_' . $this->request->files['file']['error']);
			}
		} else {
			$json['error'] = $this->language->get('error_upload');
		}

		if (!$json) {
			$folder = 'catalog/avatar/';
			$file = $folder . token(32) . $filename;

			move_uploaded_file($this->request->files['file']['tmp_name'], DIR_IMAGE . $file);

			$this->load->model('catalog/xreviews');
			
			$result = $this->model_catalog_xreviews->resize($file, $folder);
			
			if ($result) {
				$json['file'] = $result;
				$json['success'] = $this->language->get('text_upload');
			} else {
				$json['error'] = $this->language->get('error_upload');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function write() {
		$this->load->language('product/xreviews');

		$json = array();

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {

			if (($this->config->get('xreviews_title') == 2) && (utf8_strlen($this->request->post['title']) < 2)) {
				$json['error'] = $this->language->get('error_title');
			}
			
			if (($this->config->get('xreviews_city') == 2) && (utf8_strlen($this->request->post['city']) < 2)) {
				$json['error'] = $this->language->get('error_city');
			}
			if($this->config->get('xreviews_email') == 2){
				if (utf8_strlen($this->request->post['email']) > 96 || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
					$json['error'] = $this->language->get('error_email');
				}
			}
			
			if (($this->config->get('xreviews_name') == 2) && (utf8_strlen($this->request->post['name']) < 2)) {
				$json['error'] = $this->language->get('error_name');
			} 

			if($this->config->get('xreviews_text') == 2){
				if (utf8_strlen($this->request->post['text']) < 4 || utf8_strlen($this->request->post['text']) > 1000) {
					$json['error'] = $this->language->get('error_text');
				}
			}
			
			if($this->config->get('xreviews_good') == 2){
				if (utf8_strlen($this->request->post['good']) < 4 || utf8_strlen($this->request->post['good']) > 1000) {
					$json['error'] = $this->language->get('error_good');
				}
			}
			
			if($this->config->get('xreviews_bad') == 2){
				if (utf8_strlen($this->request->post['bad']) < 4 || utf8_strlen($this->request->post['bad']) > 1000) {
					$json['error'] = $this->language->get('error_bad');
				}
			}
			
			if ($this->config->get('xreviews_photo') == 2) {
				if (utf8_strlen($this->request->post['photo']) == 0) {
					$json['error'] = $this->language->get('error_photo');
				}
			}

			if ($this->config->get('xreviews_rating')) {
				if (!isset($this->request->post['rating'])) {
					$json['error'] = $this->language->get('error_rating');
				}
			}
			
			// Captcha
			if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('review', (array)$this->config->get('config_captcha_page'))) {
				$captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');

				if ($captcha) {
					$json['error'] = $captcha;
				}
			}

			if (!isset($json['error'])) {
				$this->load->model('catalog/xreviews');
				
			if ($this->config->get('xreviews_status') == 1) {
				$status = 0;
				$status_message = $this->language->get('text_success_status');
			} else {
				$status = 1;
				$status_message = '';
			}

				$this->model_catalog_xreviews->addxreviews($this->request->post, $status);

				$json['success'] = $this->language->get('text_success') . ' ' . $status_message;
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
?>