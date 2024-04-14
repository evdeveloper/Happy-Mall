<?php  
class ControllerExtensionModuleXreviews extends Controller {
	public function index($setting) {
		$this->language->load('extension/module/xreviews');
		$this->document->addStyle('catalog/view/theme/default/stylesheet/xreviews.css');
		$data['heading_title'] = $this->language->get('heading_title');

		$data['show_all_url'] = $this->url->link('product/xreviews', '');

		$data['title'] = $setting['title'];
		$data['rating'] = $setting['rating'];
		$data['name_status'] = $setting['name_status'];
		$data['city'] = $setting['city'];
		$data['date_added'] = $setting['date_added'];	
		$data['text'] = $setting['text'];
		$data['bad'] = $setting['bad'];
		$data['good'] = $setting['good'];	
		$data['photo'] = $setting['photo'];

		$this->load->model('catalog/xreviews');
		
		$this->load->model('tool/image');
		
		$data['xreviewss'] = array();
		
		$filter_data = array(
			'start' => 0,
			'limit' => $setting['limit']
		);
		
		$data['total'] = $this->model_catalog_xreviews->getTotalxreviewss();
		$results = $this->model_catalog_xreviews->getxreviewss($filter_data);

		foreach ($results as $result) {

			if (mb_strlen($result['good'],'UTF-8') > 350){
				$good = mb_substr($result['good'], 0, 350, 'UTF-8').'...';
			}
			else{
				$good = $result['good'];
			}
			
			if (mb_strlen($result['bad'],'UTF-8') > 350){
				$bad = mb_substr($result['bad'], 0, 350, 'UTF-8').'...';
			}
			else{
				$bad = $result['bad'];
			}
			
			if (mb_strlen($result['text'],'UTF-8') > 350){
				$text = mb_substr($result['text'], 0, 350, 'UTF-8').'...';
			}
			else{
				$text = $result['text'];
			}
			
			if ($result['photo']) {
				$photo = $this->model_tool_image->resize($result['photo'], $setting['width'], $setting['height']);
			} else {
				$photo = $this->model_tool_image->resize('catalog/avatar/no_photo.png', $setting['width'], $setting['height']);
			}

			$data['xreviewss'][] = array(
				'title'			=> $result['title'],
				'city'			=> $result['city'],
				'name'			=> $result['name'],
				'text'			=> $text,
				'good'			=> $good,
				'bad'			=> $bad,
				'rating'		=> $result['rating'],
				'photo'			=> $photo,
				'date_added' 	=> date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);
		}
		
		return $this->load->view('extension/module/xreviews', $data);
	}
}
?>