<?php
/**
 * @package    Com_CMandrill
 * @author     Daniel Dimitrov <daniel@compojoom.com>
 * @copyright  Copyright (C) 2008 - 2014 Compojoom.com . All rights reserved.
 * @license    GNU GPL version 3 or later <http://www.gnu.org/licenses/gpl.html>
 */

// Protect from unauthorized access
defined('_JEXEC') or die;

/**
 * The updates provisioning Controller
 *
 * @since  4.0
 */
class CMandrillControllerUpdate extends JControllerLegacy
{
	/**
	 * Looks for an update to the extension
	 *
	 * @return string
	 */
	public function updateinfo()
	{
		$updateModel = JModelLegacy::getInstance('Updates', 'CMandrillModel');
		$updateInfo = (object) $updateModel->getUpdates(true);
		$extensionName = 'CMandrill';

		$result = '';
var_dump($updateInfo);
		var_dump($updateInfo->hasUpdate);
		if ($updateInfo->hasUpdate)
		{
			$strings = array(
				'header'  => JText::sprintf('LIB_COMPOJOOM_DASHBOARD_MSG_UPDATEFOUND', $extensionName, $updateInfo->version),
				'button'  => JText::sprintf('LIB_COMPOJOOM_DASHBOARD_MSG_UPDATENOW', $updateInfo->version),
				'infourl' => $updateInfo->infoURL,
				'infolbl' => JText::_('LIB_COMPOJOOM_DASHBOARD_MSG_MOREINFO'),
			);

			$layout = new CompojoomLayoutFile('update.info');

			$result = $layout->render($strings);
		}

		echo '###' . $result . '###';

		// Cut the execution short
		JFactory::getApplication()->close();
	}

	/**
	 * Force joomla to check for updates
	 *
	 * @return void
	 */
	public function force()
	{
		JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));

		JModelLegacy::getInstance('Updates', 'CMandrillModel')->getUpdates(true);

		$url = 'index.php?option=' . JFactory::getApplication()->input->getCmd('option', '');
		$msg = JText::_('LIB_COMPOJOOM_UPDATE_INFORMATION_RELOADED');
		$this->setRedirect($url, $msg);
	}
}
