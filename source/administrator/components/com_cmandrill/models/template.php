<?php
/**
 * @author     Daniel Dimitrov <daniel@compojoom.com>
 * @date       18.01.13
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Class CmandrillModelTemplate
 *
 * @since  1.0
 */
class CmandrillModelTemplate extends JModelAdmin
{
	/**
	 * @var        string    The prefix to use with controller messages.
	 */
	protected $text_prefix = 'COM_CMANDRILL';

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   string  $type    - The table name. Optional.
	 * @param   string  $prefix  - The class prefix. Optional.
	 * @param   array   $config  - Configuration array for model. Optional.
	 *
	 * @internal param \The $type table type to instantiate
	 * @return    JTable    A database object
	 */
	public function getTable($type = 'Template', $prefix = 'cmandrillTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      - An optional array of data for the form to interogate.
	 * @param   boolean  $loadData  - True if the form is to load its own data (default case), false if not.
	 *
	 * @return  JForm A JForm object on success, false on failure
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_cmandrill.template', 'template', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return mixed The data for the form
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_cmandrill.edit.template.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  - The id of the primary key.
	 *
	 * @return mixed Object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
		$item = parent::getItem($pk);

		return $item;
	}

	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @param   JTable  $table  - the table
	 *
	 * @return void
	 */
	protected function prepareTable($table)
	{
		$date = JFactory::getDate();
		$user = JFactory::getUser();

		$table->title = htmlspecialchars_decode($table->title, ENT_QUOTES);

		if (empty($table->id))
		{
			$table->created = $date->toSql();
			$table->created_by = $user->id;
		}
	}
}
