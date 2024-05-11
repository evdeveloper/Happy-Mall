<?php
/**
 * Dream Filter v2.6
 * @link http://dreamfilter.ru/
 * @license Commercial
 * @copyright Copyright (c) 2016-2023 reDream
 * @author Ivan Grigorev <ig@redream.ru>
 */
class ModelToolDreamFilterCache extends Model
{

	/** @var bool */
	private $_ionCube;

	public function __construct($registry)
	{
		parent::__construct($registry);
		if(($this->_ionCube = (extension_loaded('ionCube Loader') && version_compare(ioncube_loader_version(), '10', '>=')))) {
			if(version_compare(phpversion(), '7.1', '>=')) {
				include_once DIR_SYSTEM . 'library/dream_filter/cache_7.1.php';
			} else {
				include_once DIR_SYSTEM . 'library/dream_filter/cache.php';
			}
		}
	}

	public function flush()
	{
		if($this->_ionCube && class_exists('dream_filter_cache')) {
			dream_filter_cache::flush();
		}
	}
}
