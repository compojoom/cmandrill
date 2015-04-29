<?php
/**
 * @package    Com_CMandrill
 * @author     DanielDimitrov <daniel@compojoom.com>
 * @date       07.10.14
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * The updates provisioning Model
 *
 * @since  5.1
 */
class CMandrillModelUpdates extends CompojoomModelUpdate
{
	/**
	 * Public constructor. Initialises the protected members as well.
	 *
	 * @param   array  $config  - the config object
	 */
	public function __construct($config = array())
	{
		// If a valid Download ID is found, add it to extra_query (Needed for Joomla! 3.2+)
		$extraQuery = null;

		$updateURL = 'https://compojoom.com/index.php?option=com_ars&view=update&task=stream&format=xml&id=11&dummy=extension.xml';


		$config = array(
			'update_site'		=> $updateURL,
			'update_extraquery'	=> $extraQuery,
			'update_sitename'	=> 'CMandrill'
		);

		parent::__construct($config);
	}

	/**
	 * Checks the database for missing / outdated tables and installs or
	 * updates the database using the SQL xml file if necessary.
	 *
	 * @return	void
	 */
	public function checkAndFixDatabase()
	{
		// Makes sure that the compojoom library tables are created
		$libraryInstaller = new CompojoomDatabaseInstaller(
			array(
				'dbinstaller_directory' => JPATH_LIBRARIES . '/compojoom/sql/xml'
			)
		);

		$libraryInstaller->updateSchema();
		$dbInstaller = new CompojoomDatabaseInstaller(
			array(
				'dbinstaller_directory' => JPATH_ADMINISTRATOR . '/components/' . $this->component . '/sql/xml'
			)
		);

		$dbInstaller->updateSchema();
	}
}
