<?php
/**
 * @author Daniel Dimitrov - compojoom.com
 * @date: 18.01.13
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
 
defined('_JEXEC') or die('Restricted access');

class JFormFieldTemplates extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 */
	protected $type = 'Templates';

	/**
	 * Gets the created templates in mandrill
	 * @return array
	 */
	protected function getOptions() {
		$options = array();
		$templates = cmandrillHelperMandrill::send('templates', 'list');

		foreach ($templates as $template) {
			// Create a new option object based on the <option /> element.
			$options[] = JHtml::_(
				'select.option', (string) $template->slug,
				JText::_(trim((string) $template->name)), 'value', 'text'
			);
		}

		return $options;
	}
}
