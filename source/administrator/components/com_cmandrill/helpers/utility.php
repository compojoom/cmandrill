<?php
/**
 * @author Daniel Dimitrov - compojoom.com
 * @date: 15.01.13
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

class cmandrillHelperUtility
{

	/**
	 * @return string
	 */
	public static function footer()
	{
		$output = '<div class="footer small">
									<strong>CMandrill</strong><br />
									Copyright ©2008–' . date('Y') . ' Daniel Dimitrov / <a href="https://compojoom.com">compojoom.com</a><br />
					CMandrill is Free software released under the GNU General Public License, version 2 of the license or –at your option– any later version published by the Free Software Foundation. <br />
					<a href="http://mandrill.com">Mandrill®</a> & <a href="https://mailchimp.com/?pid=compojoom&source=website">Mailchimp®</a> are a registered trademarks of <a href="http://rocketsciencegroup.com/" target="_blank">The Rocket Science Group</a>.
					</div>';

		return $output;
	}

	/**
	 * This function checks if the mandrill plugin is enabled
	 * (Only if the user has provided API credentials)
	 */
	public static function checkStatus()
	{
		$params = JComponentHelper::getParams('com_cmandrill');
		$appl = JFactory::getApplication();
		if ($params->get('apiKey')) {
			// so we have an api key? Let us see if it seems to be correct
			$result = cmandrillHelperMandrill::send('users', 'ping');
			if ($result !== 'PONG!') {
				if ($result->status === 'error') {
					//unfortunately we need to throw an exception
					throw new Exception('Invalid API key provided for the mandrill Service');
				}
			}

			// is the plugin enabled
			if (!JPluginHelper::isEnabled('system', 'mandrill')) {
				$appl->enqueueMessage(JText::sprintf('COM_CMANDRILL_PLG_MANDRILL_NOT_ENABLED', JRoute::_('index.php?option=com_plugins&view=plugins&filter_folder=system&filter_search=mandrill')), 'warning');
			}
		}
	}

	/**
	 * include the bootstrap css
	 * @return void
	 */
	public static function bootstrap() {
		if(JVERSION < 3.0) {
			JHTML::_('stylesheet', 'media/com_cmc/css/bootstrap.css');
		}
	}

	/**
	 * Function that will search plain-text for urls in it and will add the
	 * html <a> tag.
	 * @param $text
	 * @return mixed
	 */
	public static function makeClickableUrls($text) {
		return preg_replace_callback(
			'#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#',
			create_function(
				'$matches',
				'return "<a href=\'{$matches[0]}\'>{$matches[0]}</a>";'
			),
			$text
		);
	}

}