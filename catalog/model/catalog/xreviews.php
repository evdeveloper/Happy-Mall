<?php
class ModelCatalogXreviews extends Model {
	public function getxreviewss($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "xreviews t LEFT JOIN " . DB_PREFIX . "xreviews_to_store t2s ON (t.xreviews_id = t2s.xreviews_id) WHERE t.status = '1' AND t2s.store_id = '" . (int)$this->config->get('config_store_id') . "' ORDER BY t.date_added DESC";
		
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . ", " . (int)$data['limit'];
		} 
		$query = $this->db->query($sql);

		return $query->rows;
	}
	
	public function getTotalxreviewss() {
		$query = $this->db->query("SELECT COUNT(DISTINCT t.xreviews_id) AS total FROM " . DB_PREFIX . "xreviews t LEFT JOIN " . DB_PREFIX . "xreviews_to_store t2s ON (t.xreviews_id = t2s.xreviews_id) WHERE t.status = '1' AND t2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");
			
		return $query->row['total'];
	}
	
	public function addxreviews($data, $status) {
		
		if (!$this->config->get('xreviews_title')) { $data['title'] = ''; }
		if (!$this->config->get('xreviews_city')) { $data['city'] = ''; }
		if (!$this->config->get('xreviews_email')) { $data['email'] = ''; }
		if (!$this->config->get('xreviews_name')) { $data['name'] = ''; }
		if (!$this->config->get('xreviews_text')) { $data['text'] = ''; }
		if (!$this->config->get('xreviews_good')) { $data['good'] = ''; }
		if (!$this->config->get('xreviews_bad')) { $data['bad'] = ''; }
		if (!$this->config->get('xreviews_rating')) { $data['rating'] = ''; }
		if (!$this->config->get('xreviews_photo')) { $data['photo'] = ''; }
		
		$this->db->query("INSERT INTO " . DB_PREFIX . "xreviews SET status = '" . $status . "', rating = '" . $this->db->escape($data['rating']) . "', photo = '" . $this->db->escape($data['photo']) . "', name='" .$this->db->escape($data['name']) . "', city = '" . $this->db->escape($data['city']) . "', email='" . $this->db->escape($data['email']) . "', title = '" . $this->db->escape($data['title']) . "', text = '" . $this->db->escape($data['text']) . "', good = '" . $this->db->escape($data['good']) . "', bad = '" . $this->db->escape($data['bad']) . "', date_added = NOW()");
		
		$xreviews_id = $this->db->getLastId();
		
		$this->db->query("INSERT INTO " . DB_PREFIX . "xreviews_to_store SET xreviews_id = '" . (int)$xreviews_id . "', store_id = '" . (int)$this->config->get('config_store_id') . "'");

		if ($this->config->get('xreviews_mail')) {
			$this->load->language('mail/xreviews');

			$subject = html_entity_decode($this->language->get('text_subject'), ENT_QUOTES, 'UTF-8');
			
			$message = '';
			
			if($data['title']){
				$message .= html_entity_decode($this->language->get('text_title') . $data['title'], ENT_QUOTES, 'UTF-8') . "\n";
			}
			
			if($data['city']){
				$message .= html_entity_decode($this->language->get('text_city') . $data['city'], ENT_QUOTES, 'UTF-8') . "\n";
			}
			
			if($data['email']){
				$message .= html_entity_decode($this->language->get('text_email') . $data['email'], ENT_QUOTES, 'UTF-8') . "\n";
			}
			
			if($data['name']){
				$message .= html_entity_decode($this->language->get('text_name') . $data['name'], ENT_QUOTES, 'UTF-8') . "\n\n";
			}
			
			if($data['good']){
				$message .= html_entity_decode($this->language->get('text_good') . $data['good'], ENT_QUOTES, 'UTF-8') . "\n\n";
			}
			
			if($data['bad']){
				$message .= html_entity_decode($this->language->get('text_bad') . $data['bad'], ENT_QUOTES, 'UTF-8') . "\n\n";
			}
			
			if($data['text']){
				$message .= html_entity_decode($this->language->get('text_text'). $data['text'], ENT_QUOTES, 'UTF-8') . "\n\n";
			}
			
			if($data['rating']){
				$message .= html_entity_decode($this->language->get('text_rating') . 
				(int)$data['rating'], ENT_QUOTES, 'UTF-8');
			}

			$mail = new Mail($this->config->get('config_mail_engine'));
			$mail->protocol = $this->config->get('config_mail_protocol');
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
	
			$mail->setTo($this->config->get('config_email'));
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
			$mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
			$mail->setText($message);
			$mail->send();
	
			// Send to additional alert emails
			$emails = explode(',', $this->config->get('config_alert_email'));
	
			foreach ($emails as $email) {
				if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
					$mail->setTo($email);
					$mail->send();
				}
			}
		}	
	}
	public function resize($filename, $folder) {
		if (!is_file(DIR_IMAGE . $filename) || substr(str_replace('\\', '/', realpath(DIR_IMAGE . $filename)), 0, strlen(DIR_IMAGE)) != DIR_IMAGE) {
			return false;
		}

		$extension = pathinfo($filename, PATHINFO_EXTENSION);

		$image_old = $filename;
		$image_new = $folder . token(32) . '.' . $extension;

		if (!is_file(DIR_IMAGE . $image_new)) {
			list($width, $height, $image_type) = getimagesize(DIR_IMAGE . $image_old);
				 
			if (!in_array($image_type, array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF))) { 
				unlink(DIR_IMAGE . $image_old);
				return false;
			}

			$image = new Image(DIR_IMAGE . $image_old);
			$image->resize($width, $height);
			$image->save(DIR_IMAGE . $image_new);
			unlink(DIR_IMAGE . $image_old);
		}
		
		return $image_new;
	}
}
?>