<?php
/**
 * @package    Com_Cmandrill
 * @author     DanielDimitrov <daniel@compojoom.com>
 * @date       07.10.2014
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

/**
 * Class HotspotsHelperMenu
 *
 * @since  4.0
 */
class CMandrillHelperMenu
{
	/**
	 * Generates the menu
	 *
	 * @return  array
	 */
	public static function getMenu()
	{
		$menu = array();

		$menu['dashboard'] = array(
			'link' => 'index.php?option=com_cmandrill&view=dashboard',
			'title' => 'COM_CMANDRILL_DASHBOARD',
			'icon' => 'fa-dashboard',
			'anchor' => '',
			'children' => array(),
			'label' => '',
			'keywords' => 'dashboard home overview cpanel'
		);
		$menu['templates'] = array(
			'link' => 'index.php?option=com_cmandrill&view=templates',
			'title' => 'COM_CMANDRILL_TEMPLATES',
			'icon' => 'fa-comments',
			'anchor' => '',
			'children' => array(),
			'label' => '',
			'keywords' => 'lists comments'
		);


		return $menu;
	}
}
