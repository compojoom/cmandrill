<?php
/**
 * @author Daniel Dimitrov - compojoom.com
 * @date: 14.01.13
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

JHtml::stylesheet('media/com_cmandrill/css/dashboard.css');
?>

<div id="j-main-container" class="span12">
	<div class="span12">
		<div class="span2" style="height: 256px; padding-top: 70px;">
			<h1 style="font-weight: bold; color:#ff0000; text-align: center">Don't panic</h1>
			<?php echo JText::sprintf('COM_MANDRILL_WRONG_API_KEY', 'http://mandrillapp.com'); ?>
			<br />
			<?php echo JText::_('COM_MANDRILL_READ_THE_DOCS'); ?>
		</div>
		<div class="android offset2">

		</div>

		<?php echo cmandrillHelperUtility::footer(); ?>
		<div class="small">
			Robot image was taken from <a href="http://openclipart.org/detail/20510/-by--20510" target="_blank">openclipart.org</a>.
		</div>
	</div>

</div>