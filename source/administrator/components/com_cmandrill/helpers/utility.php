<?php
/**
 * @author     Daniel Dimitrov <daniel@compojoom.com>
 * @date       11.05.15
 *
 * @copyright  Copyright (C) 2008 - 2015 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

/**
 * Class CmandrillHelperUtility
 *
 * @since  3.0
 */
class CmandrillHelperUtility
{
	/**
	 * Outputs the footer in the backend
	 *
	 * @return string
	 */
	public static function footer()
	{
		$layout = new CompojoomLayoutFile('footer.powered');

		return $layout->render(array());
	}

	/**
	 * This function checks if the mandrill plugin is enabled
	 * (Only if the user has provided API credentials)
	 *
	 * @throws Exception
	 * @return void
	 */
	public static function checkStatus()
	{
		$params = JComponentHelper::getParams('com_cmandrill');
		$appl   = JFactory::getApplication();

		if ($params->get('apiKey'))
		{
			// So we have an api key? Let us see if it seems to be correct
			$result = cmandrillHelperMandrill::initMandrill()->users->ping();

			if ($result !== 'PONG!')
			{
				if ($result->status === 'error')
				{
					// Unfortunately we need to throw an exception
					throw new Exception('Invalid API key provided for the mandrill Service');
				}
			}

			// Is the plugin enabled
			if (!JPluginHelper::isEnabled('system', 'mandrill'))
			{
				$appl->enqueueMessage(
					JText::sprintf(
						'COM_CMANDRILL_PLG_MANDRILL_NOT_ENABLED',
						JRoute::_('index.php?option=com_plugins&view=plugins&filter_folder=system&filter_search=mandrill')
					),
					'warning'
				);
			}
		}
	}

	/**
	 * Function that will search plain-text for urls in it and will add the
	 * html <a> tag.
	 *
	 * @param   string  $text  - the message text
	 *
	 * @return mixed
	 */
	public static function makeClickableUrls($text)
	{
		return preg_replace_callback(
			'#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#',
			create_function(
				'$matches',
				'return "<a href=\'{$matches[0]}\'>{$matches[0]}</a>";'
			),
			$text
		);
	}

	/**
	 * Returns a bootstrap class name for a message state
	 *
	 * @param   string  $state  - the state
	 *
	 * @return string
	 */
	public static function getClassForState($state)
	{
		$classes = array(
			'sent' => 'success',
			'soft-bounced' => 'warning',
			'bounced' => 'danger',
			'rejected' => 'info',
			'spam' => 'warning',
			'unsub' => 'warning'
		);

		return isset($classes[$state]) ? $classes[$state] : '';
	}

	/**
	 * Map the state to a proper translation
	 *
	 * @param   string  $state  - the state as it comes from mandrill
	 *
	 * @return string
	 */
	public static function getTranslationForState($state)
	{
		$translations = array(
			'sent' => JText::_('COM_CMANDRILL_DELIVERED'),
			'bounced' => JText::_('COM_CMANDRILL_HARD_BOUNCES'),
			'soft-bounced' => JText::_('COM_CMANDRILL_SOFT_BOUNCES'),
			'rejected' => JText::_('COM_CMANDRLL_REJECTED'),
			'spam' => JText::_('COM_CMANDRILL_SPAM_COMPLAINTS'),
			'unsub' => JText::_('COM_CMANDRILL_UNSUBSCRIBES'),
		);

		return isset($translations[$state]) ? $translations[$state] : '';
	}

	/**
	 * Aggregates the available stats for the filter
	 *
	 * @return array
	 */
	public static function getAggregatedStats()
	{
		$model = JModelLegacy::getInstance('Activity', 'CMandrillModel');
		$stats = $model->getItems(true);
		$aggregated = array(
			'sent' => 0,
			'bounced' => 0,
			'soft-bounced' => 0,
			'rejected' => 0,
			'spam' => 0,
			'unsub' => 0
		);

		foreach ($stats as $item)
		{
			$aggregated['sent'] += $item->sent;
			$aggregated['bounced'] += $item->hard_bounces;
			$aggregated['soft-bounced'] += $item->soft_bounces;
			$aggregated['rejected'] += $item->rejects;
			$aggregated['spam'] += $item->complaints;
			$aggregated['unsub'] += $item->unsubs;
		}

		return $aggregated;
	}

	/**
	 * Gets the data for our charts
	 *
	 * @return array
	 */
	public static function getDataForChart()
	{
		$model = JModelLegacy::getInstance('Activity', 'CMandrillModel');
		$stats = $model->getItems(true);
		$accumulatedStats = array();

		$delivered[] = 'Date, Delivered, Bounced, Rejected\n';
		$opens[] = 'Date, Open rate, Click rate\n';

		foreach ($stats as $key => $item)
		{
			$row = array(
				$item->time,
				$item->sent,
				$item->soft_bounces + $item->hard_bounces,
				$item->rejects
			);

			$delivered[] = implode(',', $row) . '\n';
		}

		// Calculate the sent, open and clicks for a whole day
		foreach ($stats as $item)
		{
			$date = JFactory::getDate($item->time)->format('Y-m-d');

			if (!isset($accumulatedStats[$date]))
			{
				$accumulatedStats[$date] = array(
					'opens' => 0,
					'clicks' => 0,
					'sent' => 0
				);
			}

			$accumulatedStats[$date]['opens'] += $item->opens;
			$accumulatedStats[$date]['clicks'] += $item->clicks;
			$accumulatedStats[$date]['sent'] += $item->sent;
		}

		// Now prepare this for the data array
		foreach ($accumulatedStats as $key => $value)
		{
			$row = array(
				$key,
				$value['sent'] ? ($value['opens'] / $value['sent']) * 100 : 0,
				$value['sent'] ? ($value['clicks'] / $value['sent']) * 100 : 0
			);
			$opens[] = implode(',', $row) . '\n';
		}

		return array('delivered' => implode('', $delivered), 'opens' => implode('', $opens));
	}
}
