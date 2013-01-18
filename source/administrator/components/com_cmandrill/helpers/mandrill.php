<?php
/**
 * @author Daniel Dimitrov - compojoom.com
 * @date: 15.01.13
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
 
defined('_JEXEC') or die('Restricted access');

class cmandrillHelperMandrill {

	/**
	 * @param $category - api call category (users, messages, tags etc..)
	 * @param $action
	 * @param $data - the data for the request
	 * @return mixed
	 */
	public static function send($category, $action, $data = array()) {

		$params = JComponentHelper::getParams('com_cmandrill');

		$url = self::getUrl() . '/'.$category.'/'.$action.'.json';

		$data['key'] = $params->get('apiKey');
		$data = json_encode($data);

		$id = md5($category.'/'.$action.'/'.$data);
//var_dump();
		$cache = JFactory::getCache('com_cmandrill', 'output');
		$cache->setCaching(true);

		$response = $cache->get($id);
		if(!$response) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

			if ($params->get('secure')) {
				curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
			}

			$response = curl_exec($ch);
//			$data = json_decode(curl_exec($ch));
			$cache->store($response, $id);
			curl_close($ch);
		}



		return json_decode($response);
	}

	private static function getUrl() {
		$scheme = 'http';
		$params = JComponentHelper::getParams('com_cmandrill');

		if ($params->get('secure')) {
			$scheme = 'https';
		}

		$url = $scheme . '://mandrillapp.com/api/1.0';

		return $url;
	}
}