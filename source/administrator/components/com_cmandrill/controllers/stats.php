<?php
/**
 * @package    CMandrill
 * @author     Daniel Dimitrov <daniel@compojoom.com>
 * @copyright  Copyright (C) 2008 - 2014 Compojoom.com . All rights reserved.
 * @license    GNU GPL version 3 or later <http://www.gnu.org/licenses/gpl.html>
 */

// Protect from unauthorized access
defined('_JEXEC') or die;

/**
 * The updates provisioning Controller
 *
 * @since  4.0
 */
class CMandrillControllerStats extends CompojoomControllerStats
{
	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  object  The model.
	 */
	public function getModel($name = 'Stats', $prefix = 'CmandrillModel', $config = array())
	{
		return parent::getModel($name, $prefix, $config);
	}
}
