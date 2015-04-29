<?php
/**
 * @package    Com_CMandrill
 * @author     DanielDimitrov <daniel@compojoom.com>
 * @date       29.04.15
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

JHtml::stylesheet('media/com_cmandrill/css/dashboard.css');
$mandrill = CmandrillHelperMandrill::initMandrill();
$urls     = $mandrill->urls->getList();

$info = $mandrill->users->info();

$stats      = $info->stats;
$delivered7 = $stats->last_7_days->sent - $stats->last_7_days->hard_bounces - $stats->last_7_days->soft_bounces;
$sent7      = $stats->last_7_days->sent;

$days = abs(floor(strtotime('now') / (60 * 60 * 24)) - floor(strtotime($info->created_at) / (60 * 60 * 24)));

echo CompojoomHtmlCtemplate::getHead(CMandrillHelperMenu::getMenu(), 'dashboard', 'COM_CMANDRILL_DASHBOARD', '');
?>

	<div id="updateNotice"></div>
	<div id="jedNotice"></div>

	<div class="row">
		<div
			class="col-sm-12 muted small"><?php echo JText::sprintf('COM_CMANDRILL_BASIC_STATS', 'http://mandrillapp.com'); ?>
		</div>
	</div>
	<div class="row">

		<div class="col-sm-6">
			<div class="box-info full">
				<h2><?php echo JText::_('COM_CMANDRILL_LAST_7_DAYS'); ?></h2>
				<table class="table table-striped">
					<tbody>
					<tr>
						<td><b><?php echo JText::_('COM_CMANDRILL_DELIVERED'); ?></b></td>
						<td><?php echo $delivered7; ?></td>
					</tr>
					<tr>
						<td><b><?php echo JText::_('COM_CMANDRILL_SENT'); ?></b></td>
						<td><?php echo $sent7; ?></td>
					</tr>
					<tr>
						<td><b><?php echo JText::_('COM_CMANDRILL_DELIVERABILITY'); ?></b></td>
						<td><?php echo ($sent7) ? round(($delivered7 / $sent7) * 100, 1) : 0; ?>%</td>
					</tr>
					</tbody>
				</table>

				<div class="row">
					<div class="stat-block col-sm-6">
					<span
						class="stat"><?php echo ($stats->last_7_days->sent) ? round(($stats->last_7_days->opens / $stats->last_7_days->sent) * 100, 1) : 0; ?>
						%</span>
						<span class="label"><?php echo JText::_('COM_CMANDRILL_AVG_OPEN_RATE'); ?></span>
					</div>
					<div class="stat-block col-sm-6">
					<span
						class="stat"><?php echo ($stats->last_7_days->sent) ? round(($stats->last_7_days->clicks / $stats->last_7_days->sent) * 100, 1) : 0; ?>
						%</span>
						<span class="label"><?php echo JText::_('COM_CMANDRILL_AVG_CLICK_RATE'); ?></span>
					</div>
				</div>
			</div>

		</div>
		<div class="col-sm-6">
			<div class="box-info full">
				<h2><?php echo JText::_('COM_CMANDRILL_ALL_TIME'); ?></h2>
				<table class="table table-striped table-condensed">
					<tbody>
					<tr>
						<td><b><?php echo JText::_('COM_CMANDRILL_SENT'); ?></b></td>
						<td><?php echo $stats->all_time->sent; ?></td>
					</tr>
					<tr>
						<td><b><?php echo JText::_('COM_CMANDRILL_AVG_SENDS_DAILY'); ?></b></td>
						<td><?php echo ($days) ? (int) ($stats->all_time->sent / $days) : 0; ?></td>
					</tr>
					<tr>
						<td><b><?php echo JText::_('COM_CMANDRILL_TOTAL_SPAM_COMPLAINTS'); ?></b></td>
						<td><?php echo $stats->all_time->complaints ?></td>
					</tr>
					<tr>
						<td><b><?php echo JText::_('COM_CMANDRILL_AVG_SPAM_COMPLAINTS'); ?></b></td>
						<td><?php echo ($days) ? $stats->all_time->complaints / $days : 0; ?></td>
					</tr>
					<tr>
						<td><b><?php echo JText::_('COM_CMANDRILL_HARD_BOUNCES'); ?></b></td>
						<td><?php echo($stats->all_time->hard_bounces); ?></td>
					</tr>
					<tr>
						<td><b><?php echo JText::_('COM_CMANDRILL_AVG_HARD_BOUNCES'); ?></b></td>
						<td><?php echo ($days) ? (int) ($stats->all_time->hard_bounces / $days) : 0; ?></td>
					</tr>
					<tr>
						<td><b><?php echo JText::_('COM_CMANDRILL_SOFT_BOUNCES'); ?></b></td>
						<td><?php echo($stats->all_time->soft_bounces); ?></td>
					</tr>
					<tr>
						<td><b><?php echo JText::_('COM_CMANDRILL_AVG_SOFT_BOUNCES'); ?></b></td>
						<td><?php echo ($days) ? (int) ($stats->all_time->soft_bounces / $days) : 0; ?></td>
					</tr>
					<tr>
						<td><b><?php echo JText::_('COM_CMANDRILL_AVG_BOUNCES_DAILY'); ?></b></td>
						<td><?php echo ($days) ? (int) (($stats->all_time->hard_bounces + $stats->all_time->soft_bounces) / $days) : 0; ?></td>
					</tr>
					<tr>
						<td><b><?php echo JText::_('COM_CMANDRILL_AVG_UNSUB'); ?></b></td>
						<td><?php echo ($days) ? $stats->all_time->unsubs / $days : 0; ?></td>
					</tr>
					</tbody>
				</table>
			</div>

		</div>
	</div>

	<div class="row">
		<div class="col-sm-12">
			<div class="box-info full">
				<h2><?php echo JText::_('COM_CMANDRILL_TOP_TRACKED_URLS'); ?></h2>
				<table class="table table-striped">
					<thead>
					<tr>
						<th><?php echo JText::_('COM_CMANDRILL_URL'); ?></th>
						<th><?php echo JText::_('COM_CMANDRILL_DELIVERED'); ?></th>
						<th><?php echo JText::_('COM_CMANDRILL_UNIQUE_CLICKS'); ?></th>
						<th><?php echo JText::_('COM_CMANDRILL_TOTAL_CLICKS'); ?></th>
					</tr>
					</thead>
					<tbody>
					<?php if (count($urls)) : ?>
						<?php foreach ($urls as $url) : ?>
							<tr>
								<td><a href="<?php echo $url->url; ?>" target="_blank"><?php echo $url->url; ?></a></td>
								<td><?php echo $url->sent; ?></td>
								<td><?php echo $url->unique_clicks; ?></td>
								<td><?php echo $url->clicks; ?></td>
							</tr>
						<?php endforeach; ?>
					<?php else : ?>
						<tr>
							<td colspan="4">
								<?php echo JText::_('COM_MANDRILL_THERE_ARE_NO_TRACKED_URLS_YET'); ?>
							</td>
						</tr>
					<?php endif; ?>
					</tbody>

				</table>
			</div>
		</div>

	</div>

	<div class="row">
		<div class="col-sm-12">

			<div class="box-info">
				<h2>
					CMandrill <?php echo CompojoomComponentHelper::getManifest('com_cmandrill')->get('version'); ?>
					<span style="font-size: x-small">
						Copyright &copy;2008&ndash;<?php echo date('Y'); ?> Daniel Dimitrov / compojoom.com
					</span>
				</h2>
				<p>
					<?php echo JText::sprintf('LIB_COMPOJOOM_LANGUAGE_PACK', 'CMandrill', 'https://compojoom.com/downloads/languages-cool-geil/mandrill'); ?>
				</p>
				<br>

				<div>
					<?php echo CompojoomHtmlTemplates::renderSocialMediaInfo(); ?>
				</div>

				<div style="font-size: x-small">
					CMandrill is Free software released under the
					<a href="http://www.gnu.org/licenses/gpl.html">GNU General Public License,</a>
					version 2 of the license or &ndash;at your option&ndash; any later version
					published by the Free Software Foundation.
				</div>
				<div style="font-size: x-small">
					<a href="http://mandrill.com">Mandrill®</a> &
					<a href="https://mailchimp.com/?pid=compojoom&source=website">Mailchimp®</a> are a registered trademarks of
					<a href="http://rocketsciencegroup.com/" target="_blank">The Rocket Science Group</a>.

				</div>
			</div>
		</div>
	</div>

<?php
// Show Footer
echo CompojoomHtmlCTemplate::getFooter(CmandrillHelperUtility::footer());
?>

<script type="text/javascript">
	(function($) {
		$(document).ready(function(){
			$.ajax('index.php?option=com_cmandrill&task=update.updateinfo&tmpl=component', {
				success: function(msg, textStatus, jqXHR)
				{
					// Get rid of junk before and after data
					var match = msg.match(/###([\s\S]*?)###/);
					data = match[1];

					if (data.length)
					{
						$('#updateNotice').html(data);
					}
				}
			})
		});
		$.ajax('index.php?option=com_cmandrill&task=jed.reviewed&tmpl=component&<?php echo JSession::getFormToken(); ?>=1', {
			success: function(msg, textStatus, jqXHR)
			{
				// Get rid of junk before and after data
				var match = msg.match(/###([\s\S]*?)###/);
				data = match[1];

				if (data.length)
				{
					$('#jedNotice').html(data);
				}
			}
		})
	})(jQuery);
</script>

<?php if($this->updateStats): ?>
	<script type="text/javascript">
		(function($) {
			$(document).ready(function(){
				$.ajax('index.php?option=com_cmandrill&task=stats.send&tmpl=component&<?php echo JSession::getFormToken(); ?>=1', {
					dataType: 'json',
					success: function(msg) {}
				});
			});
		})(jQuery);
	</script>
<?php endif; ?>


