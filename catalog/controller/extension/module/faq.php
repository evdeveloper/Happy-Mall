<?php  
class ControllerExtensionModuleFaq extends Controller {
    public function index($setting) {

        $data = [];
        $lang_id = $this->config->get('config_language_id');
        
        $this->document->addStyle('catalog/view/theme/default/css/faq.css');
        
        if (!empty($setting['faq_module'])) {
            $data['title'] = $setting['faq_module']['title'][$lang_id];
            $this->sortData($setting['faq_module']['items'], 'order');
            foreach($setting['faq_module']['items'] as $index => $arr) {
                $data['items'][$index]['question'] = html_entity_decode($arr['question'][$lang_id], ENT_QUOTES, 'UTF-8');
                $data['items'][$index]['answer'] = html_entity_decode($arr['answer'][$lang_id], ENT_QUOTES, 'UTF-8');
            }
        }

        return $this->load->view('extension/module/faq', $data);
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
