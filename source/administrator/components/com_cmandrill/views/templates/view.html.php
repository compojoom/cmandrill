<?php
/**
 * @author Daniel Dimitrov - compojoom.com
 * @date: 14.01.13
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

defined('_JEXEC') or die('Restricted access');

class cmandrillViewTemplates extends JViewLegacy
{

	public function display($tpl = null)
	{

		$this->state = $this->get('State');
		$this->items = $this->get('Items');

		$this->pagination = $this->get('Pagination');
		$this->sidebar = $this->addToolbar();

		parent::display($tpl);
	}

	private function addToolbar()
	{
		JToolbarHelper::title('CMandrill - ' . JText::_('COM_CMANDRILL_TEMPLATES'), 'article.png');

		JToolbarHelper::preferences('com_cmandrill');
		$view = JFactory::getApplication()->input->getCmd('view');
		if (!$view) {
			$view = 'dashboard';
		}

		JToolbarHelper::addNew('template.add');

		JToolbarHelper::editList('template.edit');

		JToolbarHelper::publish('templates.publish', 'JTOOLBAR_PUBLISH', true);
		JToolbarHelper::unpublish('templates.unpublish', 'JTOOLBAR_UNPUBLISH', true);

		JToolbarHelper::deleteList('', 'templates.delete', 'JTOOLBAR_DELETE');


		JHtmlSidebar::addEntry(JText::_('COM_CMANDRILL_DASHBOARD'), 'index.php?option=com_cmandrill', $view == 'dashboard');
		JHtmlSidebar::addEntry(JText::_('COM_CMANDRILL_TEMPLATES'), 'index.php?option=com_cmandrill&view=templates', $view == 'templates');

		return JHtmlSidebar::render();
	}
}