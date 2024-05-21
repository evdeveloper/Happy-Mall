<?php
class ControllerExtensionModuleFaq extends Controller {
	private $error = array(); 
	 
	public function index() {   
		$this->language->load('extension/module/faq');

		$data['heading_title'] = $this->language->get('heading_title');
		$data['text_modules'] = $this->language->get('text_modules');
		$data['text_here'] = $this->language->get('text_here');
		$data['text_faq'] = $this->language->get('text_faq');
		$data['text_setting'] = $this->language->get('text_setting');
		$data['text_question'] = $this->language->get('text_question');
		$data['text_answer'] = $this->language->get('text_answer');
		$data['text_sort'] = $this->language->get('text_sort');
		$data['text_remove'] = $this->language->get('text_remove');
		$data['text_add'] = $this->language->get('text_add');
		$data['text_name'] = $this->language->get('text_name');
		$data['text_show'] = $this->language->get('text_show');
		$data['text_hide'] = $this->language->get('text_hide');
		$data['text_accordion'] = $this->language->get('text_accordion');
		$data['text_collapsed'] = $this->language->get('text_collapsed');
		$data['text_visible'] = $this->language->get('text_visible');
		$data['text_save'] = $this->language->get('text_save');
		$data['text_title'] = $this->language->get('text_title');

		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();
		$current_language_id = (int)$this->config->get('config_language_id');

		$this->load->model('setting/module');
				
		$this->document->addStyle('view/stylesheet/faq.css');
        
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

			$this->request->post['name'] = $this->request->post['faq_module']['title'][$current_language_id];
			
			if (!isset($this->request->get['module_id'])) {
				$this->model_setting_module->addModule('faq', $this->request->post);
			} else {
				$this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);
			}
						
			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'], 'SSL').'&type=module');			
		}
		
		$module_info = [];
		if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$module_info = $this->model_setting_module->getModule($this->request->get['module_id']);
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
		
		$data['action'] = $this->url->link('extension/module/faq', 'user_token=' . $this->session->data['user_token'], 'SSL').(isset($this->request->get['module_id'])?'&module_id='.$this->request->get['module_id']:'');

		$data['user_token'] = $this->session->data['user_token'];
	
		if (isset($this->request->post['faq_module'])) {
			$data['module'] = $this->request->post['faq_module'];
		} elseif ($module_info) {
			$data['module'] = $module_info['faq_module'];
		} else {
			$data['module'] = '';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif ($module_info) {
			$data['status'] = $module_info['status'];
		} else {
			$data['status'] = '';
		}

		if ($module_info) {
			$this->document->setTitle($data['heading_title'].' - '.$module_info['faq_module']['title'][$current_language_id]);
		} else {
			$this->document->setTitle($data['heading_title']);
		}

        if(isset($data['module']['items']) && !empty($data['module']['items'])){
            $this->sortData($data['module']['items'], 'order');
        } 
		
		$data['breadcrumbs'] = array();
		
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], 'SSL')
		);
		
		$data['breadcrumbs'][] = array(
			'text' => $data['text_modules'],
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'], 'SSL').'&type=module'
		);
				
		$data['breadcrumbs'][] = array(
			'text' => $data['heading_title'],
			'href' => $this->url->link('extension/module/faq', 'user_token=' . $this->session->data['user_token'], 'SSL')
		);
				
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
        $data['current_lang_id'] = $this->config->get('config_language_id');
				
		$this->response->setOutput($this->load->view('extension/module/faq', $data));
	}
	
	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/faq')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
        
		$this->load->model('localisation/language');
        $languages = $this->model_localisation_language->getLanguages();

        $this->language->load('extension/module/faq');
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
    
    function sortData(&$data, $col)
    {
        usort($data, function($a, $b) use ($col){
            if ($a[$col] == $b[$col]) {
                return 0;
            }
            return ($a[$col] < $b[$col]) ? -1 : 1;
        });
    }
}
?>