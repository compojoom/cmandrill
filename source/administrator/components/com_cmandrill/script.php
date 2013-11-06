<?php
/**
 * @author     Daniel Dimitrov <daniel@compojoom.com>
 * @date       14.01.13
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');


/**
 * Class Com_CmandrillInstallerScript
 *
 * @since  1.0
 */
class Com_CmandrillInstallerScript
{
	/**
	 * @var CompojoomInstaller
	 */
	private $installer;

	private $installationQueue = array(
		// Modules => { (folder) => { (module) => { (position), (published) } }* }*
		'modules' => array(),
		'plugins' => array(
			// The plugin should be deactivated when installed because it shows a warning. The user needs to manually activate it
			'plg_system_mandrill' => 0
		),
		// Key is the name without the lib_ prefix, value if the library should be autopublished
		'libraries' => array(
			'compojoom' => 1,
			'cmandrill' => 1
		)
	);

	/**
	 * Executed on install/update/discover
	 *
	 * @param   string                      $type    - the type of th einstallation
	 * @param   JInstallerAdapterComponent  $parent  - the parent JInstaller obeject
	 *
	 * @return boolean - true if everything is OK and we should continue with the installation
	 */
	public function preflight($type, $parent)
	{
		$path = $parent->getParent()->getPath('source') . '/libraries/compojoom/libraries/compojoom/include.php';
		$langPath = $parent->getParent()->getPath('source') . '/administrator';

		require_once $path;

		$this->installer = new CompojoomInstaller($type, $parent, 'com_cmandrill');

		if (!$this->installer->allowedInstall())
		{
			return false;
		}

		return true;
	}

	/**
	 * method to uninstall the component
	 *
	 * @param   JInstallerAdapterComponent  $parent  - the parent object
	 *
	 * @return void
	 */
	public function uninstall($parent)
	{
		require_once JPATH_LIBRARIES . '/compojoom/include.php';

		$this->installer = new CompojoomInstaller('uninstall', $parent, 'com_cmandrill');

		$this->status = new stdClass;
		$this->status->plugins = $this->installer->uninstallPlugins($this->installationQueue['plugins']);
		$this->status->modules = $this->installer->uninstallModules($this->installationQueue['modules']);

		echo $this->displayInfoUninstallation();
	}

	/**
	 * method to run after an install/update/discover method
	 *
	 * @param   string                      $type    - the type
	 * @param   JInstallerAdapterComponent  $parent  - the parent object
	 *
	 * @return void
	 */
	public function postflight($type, $parent)
	{
		$this->update = CmandrillInstallerHelper::checkIfUpdating();

		switch ($this->update)
		{
			case 'plugin':
				CmandrillInstallerHelper::updateFromOldPlugin();
			case 'new':

				break;
		}

		// Let us install the modules
		$this->status = new stdClass;
		$this->status->plugins = $this->installer->installPlugins($this->installationQueue['plugins']);
		$this->status->modules = $this->installer->installModules($this->installationQueue['modules']);
		$this->status->libraries = $this->installer->installLibraries($this->installationQueue['libraries']);

		echo $this->displayInfoInstallation();
	}

	/**
	 * Displays info about the status of the current install
	 *
	 * @return string
	 */
	private function displayInfoInstallation()
	{
		$html[] = '<div>';
		$html[] = '<h2>' . JText::_('COM_CMANDRILL_INSTALLATION_SUCCESS') . '</h2>';
		$html[] = '<a href="http://www.compojoom.com" target="_blank">http://compojoom.com</a>';
		$html[] = '</div>';
		$html[] = CompojoomHtmlTemplates::renderSocialMediaInfo();

		if ($this->status->plugins)
		{
			$html[] = $this->installer->renderPluginInfoInstall($this->status->plugins);
		}

		if ($this->status->modules)
		{
			$html[] = $this->installer->renderModuleInfoInstall($this->status->modules);
		}

		if ($this->status->libraries)
		{
			$html[] = $this->installer->renderLibraryInfoInstall($this->status->libraries);
		}

		return implode('', $html);
	}

	/**
	 * Display uninstall info
	 *
	 * @return string
	 */
	public function displayInfoUninstallation()
	{
		$html[] = '<h2>' . JText::_('COM_MANDRILL_UNINSTALL_SUCCESSFULL') . '</h2>';

		$html[] = $this->installer->renderPluginInfoUninstall($this->status->plugins);
		$html[] = $this->installer->renderModuleInfoUninstall($this->status->modules);
		$html[] = CompojoomHtmlTemplates::renderSocialMediaInfo();

		return implode('', $html);
	}
}

/**
 * Class CmandrillInstallerHelper
 * Helper class to do the update from the old version
 *
 * @since  1.0
 */
class CmandrillInstallerHelper
{
	/**
	 * Check if we are updating
	 *
	 * @return string
	 */
	public static function checkIfUpdating()
	{
		jimport('joomla.plugin.plugin');
		$update = 'new';
		$plugin = JPluginHelper::getPlugin('system', 'mandrill');

		// If the mandrill plugin is there let us have a look at the manifest cache
		// to determine if it is from a version that needs updating
		if (is_object($plugin))
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);

			$query->select('manifest_cache')
				->from('#__extensions')
				->where('type =' . $db->Quote('plugin'))
				->where('folder=' . $db->q('system'))
				->where('element=' . $db->q('mandrill'));

			$db->setQuery($query, 0, 1);
			$params = new JRegistry($db->loadObject()->manifest_cache);

			if (version_compare($params->get('version'), '1.0.2', 'le'))
			{
				$update = 'plugin';
			}
		}

		return $update;
	}

	/**
	 * This function handles the update to CMandrill from a
	 * version of the extension where we only had a plugin
	 * & the plugin was handling everything
	 *
	 * In the new CMandrill the settings are moved over to the component
	 * that is why if we are updating from a version previous to 1.0.1 we will
	 * copy the settings over to the component and will delete the settings for the plugin
	 *
	 * @return boolean
	 */
	public static function updateFromOldPlugin()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('params')
			->from('#__extensions')
			->where('type =' . $db->q('plugin'))
			->where('folder=' . $db->q('system'))
			->where('element=' . $db->q('mandrill'));

		$db->setQuery($query, 0, 1);
		$params = new JRegistry($db->loadObject()->params);

		// Update the component params if we have an api key
		if ($params->get('apiKey'))
		{
			$query->clear();
			$query->update('#__extensions')->set('params = ' . $db->q(($params->toString())))
				->where('type=' . $db->q('component'))
				->where('element=' . $db->q('com_cmandrill'));

			$db->setQuery($query);

			if ($db->execute())
			{
				// Ok we've copied the plugin params. Now let us clear the plugin params
				$query->clear();
				$query->update('#__extensions')->set('params = ' . $db->q(''))
					->where('type =' . $db->q('plugin'))
					->where('folder=' . $db->q('system'))
					->where('element=' . $db->q('mandrill'));
				$db->setQuery($query);
				$db->execute();

				return true;
			}
		}

		return false;
	}
}
