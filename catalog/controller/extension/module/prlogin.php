<?php

class ControllerExtensionModulePrLogin extends Controller {

    private $error = [];

    public function index() {
        if (!$this->config->get('module_prlogin_status') || $this->customer->isLogged()) {
            return '';
        }

        $this->document->addScript('catalog/view/javascript/progroman/prlogin.js');
        $this->language->load('account/login');
        $this->load->language('account/register');
        $this->load->language('extension/captcha/basic_captcha');

        $data['action'] = $this->url->link('extension/module/prlogin/login', '', 'SSL');
        $data['register'] = $this->url->link('extension/module/prlogin/register', '', 'SSL');
        $data['forgotten'] = $this->url->link('account/forgotten', '', 'SSL');

        $this->load->model('account/custom_field');
        $customer_group_id = $this->config->get('config_customer_group_id');
        $data['custom_fields'] = $this->model_account_custom_field->getCustomFields($customer_group_id);

        if ($this->config->get('config_account_id')) {
            $this->load->model('catalog/information');

            $information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));

            if ($information_info) {
                $data['text_agree'] = sprintf($this->language->get('text_agree'),
                    $this->url->link('information/information/agree', 'information_id=' . $this->config->get('config_account_id'), 'SSL'),
                    $information_info['title'], $information_info['title']);
            } else {
                $data['text_agree'] = '';
            }
        } else {
            $data['text_agree'] = '';
        }

        return $this->load->view('extension/module/prlogin', $data);
    }

    public function login() {
        $json = [];

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateLogin()) {
            unset($this->session->data['guest']);

            // Default Shipping Address
            $this->load->model('account/address');

            if ($this->config->get('config_tax_customer') == 'payment') {
                $this->session->data['payment_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
            }

            if ($this->config->get('config_tax_customer') == 'shipping') {
                $this->session->data['shipping_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
            }

            // Add to activity log
            $this->load->model('account/activity');

            $activity_data = [
                'customer_id' => $this->customer->getId(),
                'name' => $this->customer->getFirstName() . ' ' . $this->customer->getLastName()
            ];

            $this->model_account_activity->addActivity('login', $activity_data);

            $redirect = $this->request->server['HTTP_REFERER'] && strpos($this->request->server['HTTP_REFERER'], 'logout') === false
                ? $this->request->server['HTTP_REFERER'] : HTTP_SERVER;

            $json['redirect'] = str_replace(['&amp;', "\n", "\r"], ['&', '', ''], $redirect);
        }

        if (!$json) {
            foreach ($this->error as $k => $v) {
                $json['error'][$k] = $v;
            }
        }

        $this->response->setOutput(json_encode($json));
    }

    public function register() {
        $json = [];
        $this->load->model('account/customer');
        $this->language->load('account/register');

        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateRegister()) {
            $this->model_account_customer->addCustomer($this->request->post);
            $this->model_account_customer->deleteLoginAttempts($this->request->post['email']);
            $this->customer->login($this->request->post['email'], $this->request->post['password']);
            unset($this->session->data['guest']);

            // Add to activity log
            $this->load->model('account/activity');

            $activity_data = [
                'customer_id' => $this->customer->getId(),
                'name' => $this->request->post['firstname'] . ' ' . $this->request->post['lastname']
            ];

            $this->model_account_activity->addActivity('register', $activity_data);

            $this->language->load('account/success');
            $json['success'] = $this->language->get('text_message');

            $redirect = $this->request->server['HTTP_REFERER'] && strpos($this->request->server['HTTP_REFERER'],
                'logout') === false ? $this->request->server['HTTP_REFERER'] : '/';

            $json['redirect'] = str_replace(['&amp;', "\n", "\r"], ['&', '', ''], $redirect);
        }

        if (!$json) {
            foreach ($this->error as $k => $v) {
                $json['error'][$k] = $v;
            }
        }

        $this->response->setOutput(json_encode($json));
    }

    private function validateLogin() {
        $this->load->model('account/customer');
        $this->language->load('account/login');
        $this->language->load('extension/module/prlogin');

        // Check how many login attempts have been made.
        $login_info = $this->model_account_customer->getLoginAttempts($this->request->post['email']);

        if ($login_info && ($login_info['total'] > $this->config->get('config_login_attempts')) && strtotime('-1 hour') < strtotime($login_info['date_modified'])) {
            $this->error['warning'] = $this->language->get('error_attempts');
        } else {

            // Check if customer has been approved.
            $customer_info = $this->model_account_customer->getCustomerByEmail($this->request->post['email']);

            if ($customer_info && !$customer_info['status']) {
                $this->error['warning'] = $this->language->get('error_approved');
            }
        }

        if (!$this->error) {
            if (!$this->customer->login($this->request->post['email'], $this->request->post['password'])) {
                $this->error['warning'] = $this->language->get('error_login');

                $this->model_account_customer->addLoginAttempt($this->request->post['email']);
            } else {
                $this->model_account_customer->deleteLoginAttempts($this->request->post['email']);
            }
        }

        return !$this->error;
    }

    private function validateRegister() {
        $this->language->load('extension/module/prlogin');

        if ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
            $this->error['firstname'] = $this->language->get('error_firstname');
        }

        if ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
            $this->error['lastname'] = $this->language->get('error_lastname');
        }

        if ((utf8_strlen($this->request->post['email']) > 96) || !preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $this->request->post['email'])) {
            $this->error['email'] = $this->language->get('error_email');
        }

        if ($this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
            $this->error['warning'] = $this->language->get('error_exists');
        }

        if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
            $this->error['telephone'] = $this->language->get('error_telephone');
        }

        // Custom field validation
        $this->load->model('account/custom_field');
        $customer_group_id = $this->config->get('config_customer_group_id');
        $custom_fields = $this->model_account_custom_field->getCustomFields($customer_group_id);

        foreach ($custom_fields as $custom_field) {
            if ($custom_field['required'] && empty($this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']])) {
                $this->error['custom_field' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'),
                    $custom_field['name']);
            }
        }

        if ((utf8_strlen($this->request->post['password']) < 4) || (utf8_strlen($this->request->post['password']) > 20)) {
            $this->error['password'] = $this->language->get('error_password');
        }

        if ($this->request->post['confirm'] != $this->request->post['password']) {
            $this->error['confirm'] = $this->language->get('error_confirm');
        }

        $captcha = $this->load->controller('extension/captcha/basic_captcha/validate');
        if ($captcha) {
            $this->error['captcha'] = $captcha;
        }

        // Agree to terms
        if ($this->config->get('config_account_id')) {
            $this->load->model('catalog/information');

            $information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));

            if ($information_info && !isset($this->request->post['agree'])) {
                $this->error['warning'] = sprintf($this->language->get('error_agree'), $information_info['title']);
            }
        }

        return !$this->error;
    }
}