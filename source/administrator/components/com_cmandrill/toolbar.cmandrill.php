<?php
/**
 * This file will be userd only on joomla 2.5
 *
 * @author Daniel Dimitrov - compojoom.com
 * @date: 21.01.13
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
 
defined('_JEXEC') or die('Restricted access');

$view = JFactory::getApplication()->input->getCmd('view');
if (!$view) {
	$view = 'dashboard';
}

JSubMenuHelper::addEntry(JText::_('COM_CMANDRILL_DASHBOARD'), 'index.php?option=com_cmandrill'  , $view == 'dashboard' );
JSubMenuHelper::addEntry(JText::_('COM_CMANDRILL_TEMPLATES') , 'index.php?option=com_cmandrill&view=templates' , $view == 'templates' );