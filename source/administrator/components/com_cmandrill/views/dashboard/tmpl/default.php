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
			<ul class="nav nav-tabs nav-justified">
				<li class="active">
					<a data-toggle="tab" href="#7days">
						<?php echo JText::_('COM_CMANDRILL_LAST_7_DAYS'); ?>
					</a>
				</li>
				<li>
					<a data-toggle="tab" href="#30days">
						<?php echo JText::_('COM_CMANDRILL_ALL_TIME'); ?>
					</a>
				</li>
			</ul>
			<div class="tab-content">
				<div id="7days" class="active tab-pane">
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
				<div id="30days" class="tab-pane">
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
	</div>
	<div class="col-sm-6">
		<div class=" box-info full">
			<ul class="nav nav-tabs nav-justified">
				<li class="active">
					<a data-toggle="tab" href="#rss">
						<?php echo JText::_('LIB_COMPOJOOM_LATEST_NEWS'); ?>
					</a>
				</li>
				<li>
					<a data-toggle="tab" href="#version">
						<?php echo JText::_('LIB_COMPOJOOM_VERSION_INFO'); ?>
					</a>
				</li>
			</ul>
			<div class="tab-content">
				<div id="rss" class="tab-pane active">
					<?php echo CompojoomHtmlFeed::renderFeed('https://compojoom.com/blog/tags/listings/cmandrill?format=feed&amp;type=rss'); ?>
				</div>
				<div id="version" class="tab-pane">
					<?php echo $this->loadTemplate('version'); ?>
				</div>
			</div>
		</div>
		<div class="box-info">
			<h2>Ads from compojoom.com</h2>

			<div class="text-center">
				<!--/* Ads for our products */-->

				<script type='text/javascript'><!--//<![CDATA[
					var m3_u = (location.protocol == 'https:' ? 'https://matangazo.compojoom.com/www/delivery/ajs.php' : 'http://matangazo.compojoom.com/www/delivery/ajs.php');
					var m3_r = Math.floor(Math.random() * 99999999999);
					if (!document.MAX_used) document.MAX_used = ',';
					document.write("<scr" + "ipt type='text/javascript' src='" + m3_u);
					document.write("?zoneid=1");
					document.write('&amp;cb=' + m3_r);
					document.write('&amp;isPro=0');
					if (document.MAX_used != ',') document.write("&amp;exclude=" + document.MAX_used);
					document.write(document.charset ? '&amp;charset=' + document.charset : (document.characterSet ? '&amp;charset=' + document.characterSet : ''));
					document.write("&amp;loc=" + escape(window.location));
					if (document.referrer) document.write("&amp;referer=" + escape(document.referrer));
					if (document.context) document.write("&context=" + escape(document.context));
					if (document.mmm_fo) document.write("&amp;mmm_fo=1");
					document.write("'><\/scr" + "ipt>");
					//]]>--></script>
				<noscript>
					<a href='http://matangazo.compojoom.com/www/delivery/ck.php?n=a8ed4360&amp;cb=INSERT_RANDOM_NUMBER_HERE' target='_blank'><img src='http://matangazo.compojoom.com/www/delivery/avw.php?zoneid=1&amp;cb=INSERT_RANDOM_NUMBER_HERE&amp;n=a8ed4360' border='0' alt='' /></a>
				</noscript>

			</div>
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
	(function ($) {
		$(document).ready(function () {
			$.ajax('index.php?option=com_cmandrill&task=update.updateinfo&tmpl=component', {
				success: function (msg, textStatus, jqXHR) {
					// Get rid of junk before and after data
					var match = msg.match(/###([\s\S]*?)###/);
					data = match[1];

					if (data.length) {
						$('#updateNotice').html(data);
					}
				}
			})
		});
		$.ajax('index.php?option=com_cmandrill&task=jed.reviewed&tmpl=component&<?php echo JSession::getFormToken(); ?>=1', {
			success: function (msg, textStatus, jqXHR) {
				// Get rid of junk before and after data
				var match = msg.match(/###([\s\S]*?)###/);
				data = match[1];

				if (data.length) {
					$('#jedNotice').html(data);
				}
			}
		})
	})(jQuery);
</script>

<?php if ($this->updateStats): ?>
	<script type="text/javascript">
		(function ($) {
			$(document).ready(function () {
				$.ajax('index.php?option=com_cmandrill&task=stats.send&tmpl=component&<?php echo JSession::getFormToken(); ?>=1', {
					dataType: 'json',
					success : function (msg) {
					}
				});
			});
		})(jQuery);
	</script>
<?php endif; ?>


