<?php
class ControllerExtensionModuleCategoryWall extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/category_wall');

		$this->document->setTitle($this->language->get('page_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_category_wall', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'], true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['width'])) {
			$data['error_width'] = $this->error['width'];
		} else {
			$data['error_width'] = '';
		}

		if (isset($this->error['height'])) {
			$data['error_height'] = $this->error['height'];
		} else {
			$data['error_height'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_module'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('page_title'),
			'href' => $this->url->link('extension/module/category_wall', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/category_wall', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'], true);

		if (isset($this->request->post['module_category_wall_status'])) {
			$data['module_category_wall_status'] = $this->request->post['module_category_wall_status'];
		} else {
			$data['module_category_wall_status'] = $this->config->get('module_category_wall_status');
		}

		if (isset($this->request->post['module_category_wall_width'])) {
            $data['width'] = $this->request->post['module_category_wall_width'];
        } else {
        	$data['width'] = $this->config->get('module_category_wall_width');
        }

        if (isset($this->request->post['module_category_wall_height'])) {
        	$data['height'] = $this->request->post['module_category_wall_height'];
        } else{
            $data['height'] = $this->config->get('module_category_wall_height');
        }
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/category_wall', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/category_wall')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		if (!$this->request->post['module_category_wall_width']) {
			$this->error['width'] = $this->language->get('error_width');
		}
		if (!$this->request->post['module_category_wall_height']) {
			$this->error['height'] = $this->language->get('error_height');
		}
		return !$this->error;
	}
}