<?php
/**
 * @author     Daniel Dimitrov <daniel@compojoom.com>
 * @date       10.05.15
 *
 * @copyright  Copyright (C) 2008 - 2015 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

/**
 * Class CmandrillViewActivity
 *
 * @since  4.0
 */
class CmandrillViewActivity extends JViewLegacy
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
		$this->items = $this->get('items');
		$this->state = $this->get('State');
		$this->pagination = $this->get('Pagination');
		$this->filterForm    = $this->get('FilterForm');

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the toolbar buttons
	 *
	 * @return void
	 */
	private function addToolbar()
	{
		JToolbarHelper::preferences('com_cmandrill');
	}
}
