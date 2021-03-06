<?php
/**
 * @author     Daniel Dimitrov <daniel@compojoom.com>
 * @date       07.05.15
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

/**
 * Class cmandrillViewTemplates
 *
 * @since  1.0
 */
class CmandrillViewTemplates extends JViewLegacy
{
	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 */
	public function display($tpl = null)
	{
		$this->state = $this->get('State');
		$this->items = $this->get('Items');

		$this->pagination = $this->get('Pagination');
		$this->sidebar = $this->addToolbar();

		parent::display($tpl);
	}

	/**
	 * Add the toolbar buttons
	 *
	 * @return void
	 */
	private function addToolbar()
	{
		JToolbarHelper::title('CMandrill - ' . JText::_('COM_CMANDRILL_TEMPLATES'), 'article.png');

		JToolbarHelper::addNew('template.add');

		JToolbarHelper::editList('template.edit');

		JToolbarHelper::publish('templates.publish', 'JTOOLBAR_PUBLISH', true);
		JToolbarHelper::unpublish('templates.unpublish', 'JTOOLBAR_UNPUBLISH', true);

		JToolbarHelper::deleteList('', 'templates.delete', 'JTOOLBAR_DELETE');
	}
}
