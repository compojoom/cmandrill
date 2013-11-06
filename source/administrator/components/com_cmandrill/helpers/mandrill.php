<?php
/**
 * @author     Daniel Dimitrov <daniel@compojoom.com>
 * @date       15.01.13
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

require_once JPATH_LIBRARIES . '/cmandrill/include.php';

/**
 * Class CmandrillHelperMandrill
 *
 * @since  1.0
 */
class CmandrillHelperMandrill
{
	/**
	 * Creates a new Mandrill object and passes the necessary parameters
	 *
	 * @param   bool  $cache  - true if we should cache the response
	 *
	 * @return CmandrillQuery
	 */
	public static function initMandrill($cache = true)
	{
		// Get the component parameters
		$params = JComponentHelper::getParams('com_cmandrill');

		return new CmandrillQuery($params->get('apiKey'), $params->get('secure', 0), $cache);
	}

	/**
	 * Try to figure out which class and function is sending the Mail
	 *
	 * @param   array  $knownTrace  - known classes to exclude from the backtrace
	 *
	 * @return mixed
	 */
	public static function whoIsSendingIt($knownTrace = array())
	{
		$backtrace = debug_backtrace(false);

		// Attempt to remove the known Classes out of the backtrace
		foreach ($backtrace as $key => $trace)
		{
			if (isset($trace['class']))
			{
				if (($trace['class'] == 'JMail' || $trace['class'] == 'CmandrillHelperMandrill'))
				{
					unset($backtrace[$key]);
				}

				foreach ($knownTrace as $ktrace)
				{
					if ($trace['class'] == $ktrace)
					{
						unset($backtrace[$key]);
					}
				}
			}
		}

		// The first element in the backtrace should be the calling function
		return array_shift($backtrace);
	}

	/**
	 * Returns the template name or false when no template was found
	 *
	 * @param   array  $knownTrace  - which classes should we exclude from the backtrace?
	 *
	 * @return bool|string
	 */
	public static function getTemplate($knownTrace = array())
	{
		// Find out who is trying to send a mail
		$who = self::whoIsSendingIt($knownTrace);

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Filter by start and end dates.
		$nullDate = $db->q($db->getNullDate());
		$nowDate = $db->q(JFactory::getDate()->toSql());

		$where[] = $db->qn('component') . '=' . $db->q('global');

		if (isset($who['class']))
		{
			$where[] = $db->qn('class_name') . '=' . $db->q($who['class']);
		}

		$where[] = $db->qn('function_name') . '=' . $db->q($who['function']);

		// Get all matches including the global template
		$query->select($db->qn('template') . ',component, class_name, function_name')->from($db->qn('#__cmandrill_templates'))
			->where(
				implode(' OR ', $where)
			)
			->where('(' . $db->qn('publish_up') . ' = ' . $nullDate . ' OR ' . $db->qn('publish_up') . ' <= ' . $nowDate . ')')
			->where('(' . $db->qn('publish_down') . ' = ' . $nullDate . ' OR ' . $db->qn('publish_down') . ' >= ' . $nowDate . ')')
			->where($db->qn('state') . '=' . $db->q(1));

		$db->setQuery($query);
		$rows  = $db->loadObjectList();

		$matches = array();

		foreach ($rows as $key => $row)
		{
			$match = 0;

			// At least we have a global template
			if ($row->component == 'global')
			{
				$match++;
			}

			if ($row->class_name == $who['class'])
			{
				$match++;
			}

			if ($row->function_name == $who['function'])
			{
				$match++;
			}

			$matches[$key] = $match;
		}

		// If we have a match, let us find out which template to use
		if (count($matches))
		{
			$maxs = array_keys($matches, max($matches));

			if ($maxs[0] !== 0)
			{
				return $rows[$maxs[0]]->template;
			}
		}

		return false;
	}
}
