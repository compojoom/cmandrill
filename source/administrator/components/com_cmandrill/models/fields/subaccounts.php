<?php
/**
 * @author     Daniel Dimitrov <daniel@compojoom.com>
 * @date       12.05.15
 *
 * @copyright  Copyright (C) 2008 - 2015 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

JFormHelper::loadFieldClass('list');

/**
 * Class JFormFieldSubaccounts
 *
 * @since  3.0
 */
class JFormFieldSubaccounts extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var        string
	 */
	protected $type = 'Subaccounts';

	/**
	 * Gets the created templates in mandrill
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		$options = array();

		if (JComponentHelper::getParams('com_cmandrill')->get('apiKey', ''))
		{
			JLoader::discover('cmandrillHelper', JPATH_ADMINISTRATOR . '/components/com_cmandrill/helpers/');
			$subaccounts = cmandrillHelperMandrill::initMandrill(false)->subaccounts->getList();

			// Create a new option object based on the <option /> element.
			$options[] = JHtml::_(
				'select.option', '',
				'', 'value', 'text'
			);

			foreach ($subaccounts as $subaccount)
			{
				if ($subaccount->status == 'active')
				{
					// Create a new option object based on the <option /> element.
					$options[] = JHtml::_(
						'select.option', (string) $subaccount->id,
						JText::_(trim((string) $subaccount->name)), 'value', 'text'
					);
				}
			}
		}
		else
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_MANDRILL_SUBACCOUNT_SAVE_API_KEY_FIRST'));
		}

		return $options;
	}
}
