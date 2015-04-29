<?php
/**
 * @package    Com_Comment
 * @author     DanielDimitrov <daniel@compojoom.com>
 * @date       26.04.2015
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

/**
 * The updates provisioning Controller
 *
 * @since  4.0
 */
class CMandrillControllerJed extends CompojoomControllerJed
{
	protected $component = 'com_cmandrill';

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 * Recognized key values include 'name', 'default_task', 'model_path', and
	 * 'view_path' (this list is not meant to be comprehensive).
	 *
	 * @since   12.2
	 */
	public function __construct($config = array())
	{
		$url = 'http://extensions.joomla.org/extensions/extension/marketing/mailing-a-distribution-lists/cmandrill';

		$this->data = array(
				'component' => $this->component,
				'title' => 'CMandrill',
				'jed_url' => $url
		);

		parent::__construct();
	}
}
