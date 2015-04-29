<?php
/**
 * @package    Com_CMandrill
 * @author     DanielDimitrov <daniel@compojoom.com>
 * @date       29.04.15
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

/**
 * Class CmandrillViewDashboard
 *
 * @since  1.0
 */
class CmandrillViewDashboard extends JViewLegacy
{
	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 */
	public function display($tpl = null)
	{
		$params = JComponentHelper::getParams('com_cmandrill');
		$appl   = JFactory::getApplication();

		$updateModel = JModelLegacy::getInstance('Updates', 'CMandrillModel');
		$statsModel = JModelLegacy::getInstance('Stats', 'CMandrillModel');

		// Run the automatic database check
		$updateModel->checkAndFixDatabase();
		$this->currentVersion = $updateModel->getVersion();

		$this->updateStats = $statsModel->needsUpdate();

		// Run the automatic update site refresh
		$updateModel->refreshUpdateSite();

		try
		{
			cmandrillHelperUtility::checkStatus();
		}
		catch (Exception $e)
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

	/**
	 * Add the toolbar buttons
	 *
	 * @return bool
	 */
	private function addToolbar()
	{
		JToolbarHelper::preferences('com_cmandrill');

		return false;
	}
}
