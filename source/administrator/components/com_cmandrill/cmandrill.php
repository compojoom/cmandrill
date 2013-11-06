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

// Magic: merge the default translation with the current translation
$jlang = JFactory::getLanguage();
$jlang->load('com_cmandrill', JPATH_ADMINISTRATOR, 'en-GB', true);
$jlang->load('com_cmandrill', JPATH_ADMINISTRATOR, $jlang->getDefault(), true);
$jlang->load('com_cmandrill', JPATH_ADMINISTRATOR, null, true);

JLoader::discover('cmandrillHelper', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/');
JTable::addIncludePath(JPATH_COMPONENT . '/tables');

$input = JFactory::getApplication()->input;

if ($input->getCmd('view', '') == 'liveupdate')
{
	JToolBarHelper::preferences('com_cmandrill');
	LiveUpdate::handleRequest();

	return;
}

// On 2.5 we need the bootstrap css so let's add it here
cmandrillHelperUtility::bootstrap();

$controller = JControllerLegacy::getInstance('Cmandrill');
$controller->execute($input->getCmd('task'));
$controller->redirect();