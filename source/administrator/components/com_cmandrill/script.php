<?php
/**
 * @author Daniel Dimitrov - compojoom.com
 * @date: 14.01.13
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');


class com_cmandrillInstallerScript extends CompojoomInstaller
{
	/*
	 * The release value to be displayed and checked against throughout this file.
	 */
	public $release = '1.0';
	public $minimum_joomla_release = '2.5.6';
	public $extension = 'com_cmandrill';
	private $type = '';
	private $status = '';

	private $installationQueue = array(
		// modules => { (folder) => { (module) => { (position), (published) } }* }*
		'modules' => array(

		),
		'plugins' => array(
			'plg_system_mandrill' => 1
		)
	);


	/**
	 * method to uninstall the component
	 *
	 * @param $parent
	 * @return void
	 */
	public function uninstall($parent)
	{
		$this->type = 'uninstall';
		$this->parent = $parent;

		$this->status->plugins = $this->uninstallPlugins($this->installationQueue['plugins']);
		$this->status->modules = $this->uninstallModules($this->installationQueue['modules']);

		$this->droppedTables = false;

		if (hotspotsInstallerDatabase::isCompleteUninstall()) {
			hotspotsInstallerDatabase::dropTables();
			$this->droppedTables = true;
		}

		echo $this->displayInfoUninstallation();


	}

	/**
	 * method to run after an install/update/discover method
	 *
	 * @param $type
	 * @param $parent
	 * @return void
	 */
	public function postflight($type, $parent)
	{
		$this->loadLanguage();
		$this->status = new stdClass();
		$this->update = CmandrillInstallerHelper::checkIfUpdating();

		switch ($this->update) {
			case 'plugin':
				CmandrillInstallerHelper::updateFromOldPlugin();
			case 'new':

				break;
		}

		// let us install the modules

		$this->status->plugins = $this->installPlugins($this->installationQueue['plugins']);
		$this->status->modules = $this->installModules($this->installationQueue['modules']);

		echo $this->displayInfoInstallation();

	}

	private function displayInfoInstallation()
	{
		$html[] = '<div>';
		$html[] = '<h2>' . JText::_('COM_CMANDRILL_INSTALLATION_SUCCESS') . '</h2>';
		$html[] = '<a href="http://www.compojoom.com" target="_blank">http://compojoom.com</a>';

		$html[] = '</div>';


		if ($this->status->plugins) {
			$html[] = $this->renderPluginInfoInstall($this->status->plugins);
		}

		if ($this->status->modules) {
			$html[] = $this->renderModuleInfoInstall($this->status->modules);
		}

		return implode('', $html);
	}

	public function displayInfoUninstallation()
	{
		$html[] = '<div class="header">CMandrill is now removed from your system</div>';
		if ($this->droppedTables) {
			$html[] = '<p>The option uninstall complete mode was set to true. Database tables were removed</p>';
		} else {
			$html[] = '<p>The option uninstall complete mode was set to false. The database tables were not removed.</p>';
		}

		$html[] = $this->renderPluginInfoUninstall($this->status->plugins);
		$html[] = $this->renderModuleInfoUninstall($this->status->modules);

		return implode('', $html);
	}

}

class CompojoomInstaller
{
	public function loadLanguage()
	{
		$extension = $this->extension;
		$jlang = JFactory::getLanguage();
		$path = $this->parent->getParent()->getPath('source') . '/administrator';
		$jlang->load($extension, $path, 'en-GB', true);
		$jlang->load($extension, $path, $jlang->getDefault(), true);
		$jlang->load($extension, $path, null, true);
		$jlang->load($extension . '.sys', $path, 'en-GB', true);
		$jlang->load($extension . '.sys', $path, $jlang->getDefault(), true);
		$jlang->load($extension . '.sys', $path, null, true);
	}

	public function installModules($modulesToInstall)
	{
		$src = $this->parent->getParent()->getPath('source');
		$status = array();
		// Modules installation
		if (count($modulesToInstall)) {
			foreach ($modulesToInstall as $folder => $modules) {
				if (count($modules)) {
					foreach ($modules as $module => $modulePreferences) {
						// Install the module
						if (empty($folder)) {
							$folder = 'site';
						}
						$path = "$src/modules/$module";
						if ($folder == 'admin') {
							$path = "$src/administrator/modules/$module";
						}
						if (!is_dir($path)) {
							continue;
						}
						$db = JFactory::getDbo();
						// Was the module alrady installed?
						$query = $db->getQuery('true');
						$query->select('COUNT(*)')->from($db->qn('#__modules'))
							->where($db->qn('module').'='.$db->q($module));
						$db->setQuery($query);

						$count = $db->loadResult();

						$installer = new JInstaller;
						$result = $installer->install($path);
						$status[] = array('name' => $module, 'client' => $folder, 'result' => $result);
						// Modify where it's published and its published state
						if (!$count) {
							list($modulePosition, $modulePublished) = $modulePreferences;
							$query->clear();
							$query->update($db->qn('#__modules'))->set($db->qn('position').'='.$db->q($modulePosition));
							if ($modulePublished) {
								$query->set($db->qn('published').'='.$db->q(1));
							}
							$query->set($db->qn('params').'='.$db->q($installer->getParams()));
							$query->where($db->qn('module').'='.$db->q($module));
							$db->setQuery($query);
							$db->query();
						}
//	                    get module id
						$query->clear();
						$query->select('id')->from($db->qn('#__modules'))
							->where($db->qn('module').'='.$db->q($module));
						$db->setQuery($query);

						$moduleId = $db->loadObject()->id;

						$query->clear();
						$query->select('COUNT(*) as count')->from($db->qn('#__modules_menu'))
							->where($db->qn('moduleid').'='.$db->q($moduleId));

						$db->setQuery($query);

						if(!$db->loadObject()->count) {
							// insert the module on all pages, otherwise we can't use it
							$query->clear();
							$query->insert($db->qn('#__modules_menu'))->columns($db->qn('moduleid').','.$db->qn('menuid'))->values($db->q($moduleId) . ' , ' . $db->q('0'));
							$db->setQuery($query);
							$db->query();
						}
					}
				}
			}
		}
		return $status;
	}

	public function uninstallModules($modulesToUninstall = array())
	{
		$status = array();
		if (count($modulesToUninstall)) {
			$db = JFactory::getDbo();
			foreach ($modulesToUninstall as $folder => $modules) {
				if (count($modules)) {

					foreach ($modules as $module => $modulePreferences) {
						// Find the module ID
						$query = $db->getQuery(true);
						$query->select('extension_id')->from('#__extensions')->where($db->qn('element').'='.$db->q($module))
							->where($db->qn('type') . '='.$db->q('module'));
						$db->setQuery($query);

						$id = $db->loadResult();
						// Uninstall the module
						$installer = new JInstaller;
						$result = $installer->uninstall('module', $id, 1);
						$status[] = array('name' => $module, 'client' => $folder, 'result' => $result);
					}
				}
			}
		}
		return $status;
	}

	public function installPlugins($plugins)
	{
		$src = $this->parent->getParent()->getPath('source');

		$db = JFactory::getDbo();
		$status = array();

		foreach ($plugins as $plugin => $published) {
			$parts = explode('_', $plugin);
			$pluginType = $parts[1];
			$pluginName = $parts[2];

			$path = $src . "/plugins/$pluginType/$pluginName";

			$query = $db->getQuery(true);
			$query->select('COUNT(*)')
				->from('#__extensions')
				->where($db->qn('element') . '=' .$db->q($pluginName))
				->where($db->qn('folder') . '=' . $db->q($pluginType));

			$db->setQuery($query);
			$count = $db->loadResult();

			$installer = new JInstaller;
			$result = $installer->install($path);
			$status[] = array('name' => $plugin, 'group' => $pluginType, 'result' => $result);

			if ($published && !$count) {
				$query->clear();
				$query->update('#__extensions')
					->set($db->qn('enabled').'='.$db->q(1))
					->where($db->qn('element').'='.$db->q($pluginName))
					->where($db->qn('folder'). '='.$db->q($pluginType));
				$db->setQuery($query);
				$db->query();
			}
		}

		return $status;
	}

	public function uninstallPlugins($plugins)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$status = array();

		foreach ($plugins as $plugin => $published) {
			$parts = explode('_', $plugin);
			$pluginType = $parts[1];
			$pluginName = $parts[2];
			$query->clear();
			$query->select('extension_id')->from($db->qn('#__extensions'))
				->where($db->qn('type').'='.$db->q('plugin'))
				->where($db->qn('element').'='.$db->q($pluginName))
				->where($db->qn('folder').'='.$db->q($pluginType));
			$db->setQuery($query);

			$id = $db->loadResult();

			if ($id) {
				$installer = new JInstaller;
				$result = $installer->uninstall('plugin', $id, 1);
				$status[] = array('name' => $plugin, 'group' => $pluginType, 'result' => $result);
			}
		}

		return $status;
	}

	/*
		  * get a variable from the manifest file (actually, from the manifest cache).
		  */
	public function getParam($name)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery('true');
		$query->select($db->qn('manifest_cache'))
			->from($db->qn('#__extensions'))
			->where($db->qn('name').'='.$db->q($this->extension));
		$manifest = json_decode($db->loadResult(), true);
		return $manifest[$name];
	}

	public function renderModuleInfoInstall($modules) {
		$rows = 0;

		$html = array();
		if (count($modules)) {
			$html[] = '<table class="table">';
			$html[] = '<tr>';
			$html[] = '<th>' . JText::_(strtoupper($this->extension) .'_MODULE') . '</th>';
			$html[] = '<th>' . JText::_(strtoupper($this->extension) .'_CLIENT') . '</th>';
			$html[] = '<th>' . JText::_(strtoupper($this->extension) .'_STATUS') . '</th>';
			$html[] = '</tr>';
			foreach ($modules as $module) {
				$html[] = '<tr class="row' . (++$rows % 2) . '">';
				$html[] = '<td class="key">' . $module['name'] . '</td>';
				$html[] = '<td class="key">' . ucfirst($module['client']) . '</td>';
				$html[] = '<td>';
				$html[] = '<span style="color:' . (($module['result']) ? 'green' : 'red') . '; font-weight: bold;">';
				$html[] = ($module['result']) ? JText::_(strtoupper($this->extension) .'_MODULE_INSTALLED') : JText::_(strtoupper($this->extension) .'_MODULE_NOT_INSTALLED');
				$html[] = '</span>';
				$html[] = '</td>';
				$html[] = '</tr>';
			}
			$html[] = '</table>';
		}


		return implode('', $html);
	}

	public function renderModuleInfoUninstall($modules)
	{
		$rows = 0;
		$html = array();
		if (count($modules)) {
			$html[] = '<table class="table">';
			$html[] = '<tr>';
			$html[] = '<th>' . JText::_(strtoupper($this->extension) . '_MODULE') . '</th>';
			$html[] = '<th>' . JText::_(strtoupper($this->extension) . '_CLIENT') . '</th>';
			$html[] = '<th>' . JText::_(strtoupper($this->extension) . '_STATUS') . '</th>';
			$html[] = '</tr>';
			foreach ($modules as $module) {
				$html[] = '<tr class="row' . (++$rows % 2) . '">';
				$html[] = '<td class="key">' . $module['name'] . '</td>';
				$html[] = '<td class="key">' . ucfirst($module['client']) . '</td>';
				$html[] = '<td>';
				$html[] = '<span style="color:' . (($module['result']) ? 'green' : 'red') . '; font-weight: bold;">';
				$html[] = ($module['result']) ? JText::_(strtoupper($this->extension) . '_MODULE_UNINSTALLED') : JText::_(strtoupper($this->extension) . '_MODULE_COULD_NOT_UNINSTALL');
				$html[] = '</span>';
				$html[] = '</td>';
				$html[] = '</tr>';
			}
			$html[] = '</table>';
		}

		return implode('', $html);
	}

	public function renderPluginInfoInstall($plugins)
	{
		$rows = 0;
		$html[] = '<table class="table">';
		if (count($plugins)) {
			$html[] = '<tr>';
			$html[] = '<th>' . JText::_(strtoupper($this->extension) . '_PLUGIN') . '</th>';
			$html[] = '<th>' . JText::_(strtoupper($this->extension) . '_GROUP') . '</th>';
			$html[] = '<th>' . JText::_(strtoupper($this->extension) . '_STATUS') . '</th>';
			$html[] = '</tr>';
			foreach ($plugins as $plugin) {
				$html[] = '<tr class="row' . (++$rows % 2) . '">';
				$html[] = '<td class="key">' . $plugin['name'] . '</td>';
				$html[] = '<td class="key">' . ucfirst($plugin['group']) . '</td>';
				$html[] = '<td>';
				$html[] = '<span style="color: ' . (($plugin['result']) ? 'green' : 'red') . '; font-weight: bold;">';
				$html[] = ($plugin['result']) ? JText::_(strtoupper($this->extension) . '_PLUGIN_INSTALLED') : JText::_(strtoupper($this->extension) . 'PLUGIN_NOT_INSTALLED');
				$html[] = '</span>';
				$html[] = '</td>';
				$html[] = '</tr>';
			}
		}
		$html[] = '</table>';

		return implode('', $html);
	}

	public function renderPluginInfoUninstall($plugins)
	{
		$rows = 0;
		$html = array();
		if (count($plugins)) {
			$html[] = '<table class="table">';
			$html[] = '<tbody>';
			$html[] = '<tr>';
			$html[] = '<th>Plugin</th>';
			$html[] = '<th>Group</th>';
			$html[] = '<th></th>';
			$html[] = '</tr>';
			foreach ($plugins as $plugin) {
				$html[] = '<tr class="row' . (++$rows % 2) . '">';
				$html[] = '<td class="key">' . $plugin['name'] . '</td>';
				$html[] = '<td class="key">' . ucfirst($plugin['group']) . '</td>';
				$html[] = '<td>';
				$html[] = '	<span style="color:' . (($plugin['result']) ? 'green' : 'red') . '; font-weight: bold;">';
				$html[] = ($plugin['result']) ? JText::_(strtoupper($this->extension) . '_PLUGIN_UNINSTALLED') : JText::_(strtoupper($this->extension) . '_PLUGIN_NOT_UNINSTALLED');
				$html[] = '</span>';
				$html[] = '</td>';
				$html[] = ' </tr> ';
			}
			$html[] = '</tbody > ';
			$html[] = '</table > ';
		}

		return implode('', $html);
	}

	/**
	 * method to run before an install/update/discover method
	 *
	 * @param $type
	 * @param $parent
	 * @return void
	 */
	public function preflight($type, $parent)
	{
		$jversion = new JVersion();

		// Extract the version number from the manifest file
		$this->release = $parent->get("manifest")->version;

		// Find mimimum required joomla version from the manifest file
		$this->minimum_joomla_release = $parent->get("manifest")->attributes()->version;

		if (version_compare($jversion->getShortVersion(), $this->minimum_joomla_release, 'lt')) {
			Jerror::raiseWarning(null, 'Cannot install ' . $this->extension . ' in a Joomla release prior to '
				. $this->minimum_joomla_release);
			return false;
		}

		// abort if the component being installed is not newer than the currently installed version
		if ($type == 'update') {
			$oldRelease = $this->getParam('version');
			$rel = $oldRelease . ' to ' . $this->release;
			if (!strstr($this->release, 'git_')) {
				if (version_compare($this->release, $oldRelease, 'lt')) {
					Jerror::raiseWarning(null, 'Incorrect version sequence. Cannot upgrade ' . $rel);
					return false;
				}
			}
		}

	}

	/**
	 * method to update the component
	 *
	 * @param $parent
	 * @return void
	 */
	public function update($parent)
	{
		$this->parent = $parent;
	}

	/**
	 * method to install the component
	 *
	 * @param $parent
	 * @return void
	 */
	public function install($parent)
	{
		$this->parent = $parent;

	}

}

class CmandrillInstallerHelper {
	public static function checkIfUpdating(){
		jimport('joomla.plugin.plugin');
		$update = 'new';
		$plugin = JPluginHelper::getPlugin('system', 'mandrill');

		// if the mandrill plugin is there let us have a look at the manifest cache
		// to determine if it is from a version that needs updating
		if(is_object($plugin)) {
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);

			$query->select('manifest_cache')
				->from('#__extensions')
				->where('type =' . $db->Quote('plugin'))
				->where('folder='.$db->q('system'))
				->where('element='.$db->q('mandrill'));

			$db->setQuery($query,0,1);
			$params = new JRegistry($db->loadObject()->manifest_cache);

			if(version_compare($params->get('version'),'1.0.1', 'le')) {
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
	 * that is why if we are updating from a version previous of 1.0.1 we will
	 * copy the settings over to the component and will delete the settings for the plugin
	 */
	public static function updateFromOldPlugin() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('params')
			->from('#__extensions')
			->where('type =' . $db->q('plugin'))
			->where('folder='.$db->q('system'))
			->where('element='.$db->q('mandrill'));

		$db->setQuery($query,0,1);
		$params = new JRegistry($db->loadObject()->params);


		//update the component params if we have anapi key
		if($params->get('apiKey')) {
			$query->clear();
			$query->update('#__extensions')->set('params = '.$db->q(($params->toString())))
				->where('type='.$db->q('component'))
				->where('element='.$db->q('com_cmandrill'));

			$db->setQuery($query);
			if($db->execute()) {
				return true;
			}
		}

		return false;
	}
}