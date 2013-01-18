<?php
/**
 * @author Daniel Dimitrov - compojoom.com
 * @date: 18.01.13
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
 
defined('_JEXEC') or die('Restricted access');

class JFormFieldComponents extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 */
	protected $type = 'Components';

	/**
	 * Gets the created templates in mandrill
	 * @return array
	 */
	protected function getOptions() {
		$options = array();

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('element')->from('#__extensions')->where('type='.$db->q('component'))->where('enabled='.$db->q(1));

		$db->setQuery($query);

		$components = $db->loadObjectList();

		$options[] = JHtml::_(
			'select.option', 'global',
			JText::_('COM_CMANDRILL_GLOBAL'), 'value', 'text'
		);
		foreach ($components as $component) {
			// Create a new option object based on the <option /> element.
			$options[] = JHtml::_(
				'select.option', (string) $component->element,
				$component->element, 'value', 'text'
			);
		}

		return $options;
	}
}
