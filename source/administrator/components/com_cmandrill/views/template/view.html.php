<?php
/**
 * @author Daniel Dimitrov - compojoom.com
 * @date: 18.01.13
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

class cmandrillViewTemplate extends JViewLegacy
{
	protected $state;

	protected $item;

	protected $form;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->state = $this->get('State');
		$this->item = $this->get('Item');
		$this->form = $this->get('Form');

		$this->sidebar = $this->addToolbar();
		parent::display($tpl);
	}

	public function addToolbar()
	{
		$input = JFactory::getApplication()->input;

		if ($input->getInt('id')) {
			JToolbarHelper::title('CMandrill - ' . JText::_('COM_CMANDRILL_EDIT_TEMPLATE'));
		} else {
			JToolbarHelper::title('CMandrill - ' . JText::_('COM_CMANDRILL_ADD_TEMPLATE'));
		}

		JToolbarHelper::apply('template.apply');
		JToolbarHelper::save('template.save');


		if (empty($this->item->id)) {
			JToolbarHelper::cancel('template.cancel');
		} else {
			JToolbarHelper::cancel('template.cancel', 'JTOOLBAR_CLOSE');
		}


	}
}
