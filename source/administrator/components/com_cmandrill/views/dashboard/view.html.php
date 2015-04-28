<?php
/**
 * @author     Daniel Dimitrov - compojoom.com
 * @date       : 14.01.13
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

class cmandrillViewDashboard extends JViewLegacy
{

	public function display($tpl = null)
	{
		$params = JComponentHelper::getParams('com_cmandrill');
		$appl   = JFactory::getApplication();

		try
		{
			cmandrillHelperUtility::checkStatus();
		} catch (Exception $e)
		{
			$tpl = 'wrong';
		}

		if ($params->get('apiKey') == '')
		{
			$appl->enqueueMessage(JText::_('COM_CMANDRILL_EXTENSION_NOT_CONFIGURED_YET'), 'error');
			$tpl = 'config';
		}

		$this->sidebar = $this->addToolbar();

		parent::display($tpl);
	}

	private function addToolbar()
	{
		JToolbarHelper::preferences('com_cmandrill');

		return false;
	}
}
