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
	public static function send($category, $action, stdClass $data = null) {

		$params = JComponentHelper::getParams('com_cmandrill');

		$url = self::getUrl() . '/'.$category.'/'.$action.'.json';

		if($data === null) {
			$data = new stdClass();
		}

		$data->key = $params->get('apiKey');
		$data = json_encode($data);

		$id = md5($category.'/'.$action.'/'.$data);

		$cache = JFactory::getCache('com_cmandrill', 'output');
		$cache->setCaching(true);

		$response = $cache->get($id);
		// always send the data if the category is messages
		if(!$response || $category != 'messages') {
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

	/**
	 * Returns the template name or false when no template was found
	 * @return bool|string
	 */
	public static function getTemplate() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$input = JFactory::getApplication()->input;
		$view = $input->getCmd('view');
		$task = $input->getCmd('task');
		$component = $input->getCmd('option');

		$query->select($db->qn('template'))->from($db->qn('#__cmandrill_templates'))
			->where($db->qn('component').'=' . $db->q($input->getCmd('option')))
			->where($db->qn('view').'='.$db->q($view))
			->where($db->qn('task').'='.$db->q($task));

		$db->setQuery($query,0,1);
		$template = $db->loadObject();

		if(!$template) {
			// try to find a template only for this component
			$query->clear('where');
			$query->where($db->qn('component').'='.$db->q($component));
			$query->where($db->qn('view').'='.$db->q(''));
			$query->where($db->qn('task').'='.$db->q(''));
			$db->setQuery($query,0,1);

			$template = $db->loadObject();

			if(!$template) {
				// find a global template?
				$query->clear('where');
				$query->where($db->qn('component').'='.$db->q('global'));

				$query->setQuery($query,0,1);
				$template = $db->loadObject();

				if(!$template) {
					// all this work for nothing...
					return false;
				}
			}
		}

		return $template->template;
	}
}