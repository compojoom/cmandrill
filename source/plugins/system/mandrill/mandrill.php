<?php
/**
 * @author Daniel Dimitrov
 *
 * @copyright  Copyright (C) 2008 - 2012 compojoom.com, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
// no direct access
defined('_JEXEC') or die('Restricted access');


class plgSystemMandrill extends JPlugin
{
	public function onAfterInitialise()
	{
		$params = JComponentHelper::getParams('com_cmandrill');
		$appl = JFactory::getApplication();

		$this->loadLanguage('plg_system_mandrill.sys');

		$key = $params->get('apiKey');

		if (strlen($key)) {

			$path = JPATH_ROOT . '/plugins/system/mandrill/mailer/mail.php';

			JLoader::register('JMail', $path);
			JLoader::load('JMail');

		} else {
			$appl->enqueueMessage(JText::sprintf('PLG_SYSTEM_MANDRILL_NO_API_KEY_SPECIFIED', JRoute::_('index.php?option=com_cmandrill')), 'warning');
			return false;
		}
	}

}

