<?php
/**
 * @author     Daniel Dimitrov <daniel@compojoom.com>
 * @date       11.05.15
 *
 * @copyright  Copyright (C) 2008 - 2015 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

JFormHelper::loadFieldClass('list');

/**
 * Class JFormFieldMandrillState
 *
 * @since  4.0
 */
class JFormFieldMandrillState extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var        string
	 */
	protected $type = 'MandrillState';

	/**
	 * Get the stats for each item
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		$options = parent::getOptions();
		$stats = CmandrillHelperUtility::getAggregatedStats();

		foreach ($options as $key => $option)
		{
			if (isset($stats[$option->value]))
			{
				$options[$key]->text = $options[$key]->text . ' (' . $stats[$option->value] . ')';
			}
		}

		return $options;
	}
}
