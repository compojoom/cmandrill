<?php
/**
 * @author     Daniel Dimitrov <daniel@compojoom.com>
 * @date       14.01.13
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_cmandrill'))
{
	throw new Exception(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

require_once JPATH_LIBRARIES . '/compojoom/include.php';

if (!defined('COMPOJOOM_INCLUDED'))
{
	throw new Exception(
		'Your CMandrill installation is broken; please re-install. Alternatively, extract the installation archive and copy the libraries/compojoom
		 directory inside your site\'s directory.',
		500
	);
}

// Let us load the necessary langs
CompojoomLanguage::load('com_cmandrill', JPATH_SITE, true);
CompojoomLanguage::load('com_cmandrill', JPATH_ADMINISTRATOR);

JLoader::discover('cmandrillHelper', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/');
JTable::addIncludePath(JPATH_COMPONENT . '/tables');

$controller = JControllerLegacy::getInstance('Cmandrill');
$controller->execute( JFactory::getApplication()->input->getCmd('task'));
$controller->redirect();
