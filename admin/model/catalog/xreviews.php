<?php
class ModelCatalogXreviews extends Model {

	public function addxreviews($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "xreviews SET status = '" . (int)$data['status'] . "',rating = '".(int)$data['rating'] . "',date_added = '" . $this->db->escape($data['date_added']) . "', title = '" . $this->db->escape($data['title']) . "', text = '" . $this->db->escape($data['text']) . "', good = '" . $this->db->escape($data['good']) . "', comment = '" . $this->db->escape($data['comment']) . "', bad = '" . $this->db->escape($data['bad']) . "', name = '" . $this->db->escape($data['name']) . "', city = '" . $this->db->escape($data['city']) . "', photo = '" . $this->db->escape($data['photo']) . "', email='" . $this->db->escape($data['email']) . "'");
		
		$xreviews_id = $this->db->getLastId();
		
		if (isset($data['xreviews_store'])) {
			foreach ($data['xreviews_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "xreviews_to_store SET xreviews_id = '" . (int)$xreviews_id . "', store_id = '" . (int)$store_id . "'");
			}
		}
	}
	
	public function editxreviews($xreviews_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "xreviews SET status = '" . (int)$data['status'] . "', date_added = '" . $this->db->escape($data['date_added']) . "', rating = '" . (int)$data['rating'] . "', title = '" . $this->db->escape($data['title']) . "', text = '" . $this->db->escape($data['text']) . "', good = '" . $this->db->escape($data['good']) . "', comment = '" . $this->db->escape($data['comment']) . "', bad = '" . $this->db->escape($data['bad']) . "', name = '" . $this->db->escape($data['name']) . "', city = '" . $this->db->escape($data['city']) . "', photo = '" . $this->db->escape($data['photo']) . "', email='" . $this->db->escape($data['email']) . "' WHERE xreviews_id = '" . (int)$xreviews_id . "'");
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "xreviews_to_store WHERE xreviews_id = '" . (int)$xreviews_id . "'");

		if (isset($data['xreviews_store'])) {
			foreach ($data['xreviews_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "xreviews_to_store SET xreviews_id = '" . (int)$xreviews_id . "', store_id = '" . (int)$store_id . "'");
			}
		}
	}
	
	public function deletexreviews($xreviews_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "xreviews WHERE xreviews_id = '" . (int)$xreviews_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "xreviews_to_store WHERE xreviews_id = '" . (int)$xreviews_id . "'");
	}	

	public function getxreviews($xreviews_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "xreviews WHERE xreviews_id = '" . (int)$xreviews_id . "'");
		
		return $query->row;
	}
		
	public function getxreviewss($data = array()) {
	
		$sql = "SELECT * FROM " . DB_PREFIX . "xreviews";
		
			$sort_data = array(			
				'title',
				'name',
				'date_added',
				'status'
			);		
		
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];	
			} else {
				$sql .= " ORDER BY date_added";	
			}
	
			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$sql .= " DESC";
			} else {
				$sql .= " ASC";
			}
		
			if (isset($data['start']) || isset($data['limit'])) {
				if ($data['start'] < 0) {
					$data['start'] = 0;
				}		

				if ($data['limit'] < 1) {
					$data['limit'] = 20;
				}	
			
				$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
			}	
			
			$query = $this->db->query($sql);
			
			return $query->rows;
	}

	public function getxreviewsStores($xreviews_id) {
		$xreviews_store_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "xreviews_to_store WHERE xreviews_id = '" . (int)$xreviews_id . "'");

		foreach ($query->rows as $result) {
			$xreviews_store_data[] = $result['store_id'];
		}

		return $xreviews_store_data;
	}
	
	public function getTotalxreviewss() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "xreviews");
		
		return $query->row['total'];
	}

	public function createDatabaseTables() {
		$sql  = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "xreviews` ( ";
		$sql .= "`xreviews_id` int(11) NOT NULL AUTO_INCREMENT, "; 
		$sql .= "`email` varchar(96) COLLATE utf8_unicode_ci NOT NULL DEFAULT '', ";
		$sql .= "`title` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '', ";
		$sql .= "`text` text COLLATE utf8_unicode_ci NOT NULL DEFAULT '', ";
		$sql .= "`good` text COLLATE utf8_unicode_ci NOT NULL DEFAULT '', ";
		$sql .= "`comment` text COLLATE utf8_unicode_ci NOT NULL DEFAULT '', ";
		$sql .= "`bad` text COLLATE utf8_unicode_ci NOT NULL DEFAULT '', ";
		$sql .= "`name` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '', ";
		$sql .= "`city` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '', ";
		$sql .= "`status` int(1) NOT NULL DEFAULT '0', ";
		$sql .= "`rating` int(1) NOT NULL DEFAULT '0', ";
		$sql .= "`photo` varchar(256) COLLATE utf8_unicode_ci NOT NULL DEFAULT '', ";
		$sql .= "`date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00', ";
		$sql .= "PRIMARY KEY (`xreviews_id`) ";
		$sql .= ") ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;";
		$this->db->query($sql);
		
		$sql  = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "xreviews_to_store` ( ";
		$sql .= "`xreviews_id` int(11) NOT NULL, ";
		$sql .= "`store_id` int(11) NOT NULL, ";
		$sql .= "PRIMARY KEY (`xreviews_id`,`store_id`) ";
		$sql .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
		$this->db->query($sql);
		
		// SEO
		$chk = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'product/xreviews' AND store_id = '0' AND language_id = '1'");
		if (!$chk->num_rows) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET query = 'product/xreviews', keyword = 'reviews', store_id = '0', language_id = '1'");
		}
	}
}
?>