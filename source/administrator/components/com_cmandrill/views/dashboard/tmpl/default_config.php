<?php
/**
 * @author     Daniel Dimitrov - compojoom.com
 * @date       : 14.01.13
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

JHtml::stylesheet('media/com_cmandrill/css/dashboard.css');
echo CompojoomHtmlCtemplate::getHead(CMandrillHelperMenu::getMenu(), 'dashboard', 'COM_CMANDRILL_DASHBOARD', '');
?>
	<div class="row">
		<div class="col-sm-6">
			<div class="box-info">
				<h2>Don't panic!</h2>
				<?php echo JText::sprintf('COM_CMANDRILL_GET_API_KEY', 'http://mandrillapp.com'); ?>
				<br />
				<?php echo JText::sprintf('COM_CMANDRILL_READ_THE_DOCS', 'https://compojoom.com/support/documentation/mandrill'); ?>

				<div class="android offset2">

				</div>

				<?php echo cmandrillHelperUtility::footer(); ?>
				<div class="small">
					Robot image was taken from
					<a href="http://openclipart.org/detail/20510/-by--20510" target="_blank">openclipart.org</a>.
				</div>
			</div>
		</div>
	</div>
<?php
// Show Footer
echo CompojoomHtmlCTemplate::getFooter(CmandrillHelperUtility::footer());
