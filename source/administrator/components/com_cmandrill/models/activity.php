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

jimport('joomla.application.component.modellist');

/**
 * Class CmandrillModelTemplates
 *
 * @since  2.0
 */
class CmandrillModelActivity extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 'a.title',
				'state', 'a.state',
				'publish_up', 'a.publish_up',
				'publish_down', 'a.publish_down',
				'template'
			);
		}

		$this->mandrill = CmandrillHelperMandrill::initMandrill();

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$state = $this->getUserStateFromRequest($this->context . '.filter.state', 'filter_state');
		$this->setState('filter.state', $state);

		$range = $this->getUserStateFromRequest($this->context . '.list.date_range', 'list_date_range', 'P7D', 'string');
		$this->setState('filter.date_range', $range);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_cmandrill');
		$this->setState('params', $params);

		parent::populateState($ordering, $direction);
	}

	/**
	 * Get the activity items
	 *
	 * @param   bool  $stats  - should we return the aggregated stats?
	 *
	 * @return mixed
	 */
	public function getItems($stats = false)
	{
		$query = '*';
		$search = 'search';
		$state = $this->getState('filter.state');
		$to = JFactory::getDate();
		$from = JFactory::getDate()->sub(new DateInterval($this->getState('list.date_range')));

		if ($stats)
		{
			$search = 'searchTimeSeries';
		}

		if ($this->getState('filter.search'))
		{
			$query = $this->getState('filter.search');
		}

		if ($state)
		{
			$query .= ' AND (' . implode(' OR ', $state) . ')';
		}

		$items = $this->mandrill->messages->$search($query, $from->format('Y-m-d'), $to->format('Y-m-d'), null, null, null, 1000);

		return $items;
	}
}
