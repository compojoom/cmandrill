<?php
/**
 * @package    Com_CMandrill
 * @author     DanielDimitrov <daniel@compojoom.com>
 * @date       29.04.15
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * The stats reporting Model
 *
 * @since  4.0
 */
class CMandrillModelStats extends CompojoomModelStats
{
	protected $extension = 'com_cmandrill';

	protected $exclude = array(
		'downloadid',
		'apiKey'
	);

	/**
	 * Here we set a custom extension name
	 *
	 * @return array
	 */
	public function getCustomExtensionData()
	{
		// Nothing to do here
		return false;
	}
}
