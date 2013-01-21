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
$urls = cmandrillHelperMandrill::send('urls', 'list');

$info = cmandrillHelperMandrill::send('users', 'info');

$stats = $info->stats;
$delivered7 = $stats->last_7_days->sent - $stats->last_7_days->hard_bounces - $stats->last_7_days->soft_bounces;
$sent7 = $stats->last_7_days->sent;

$days = abs(floor(strtotime('now') / (60 * 60 * 24)) - floor(strtotime($info->created_at) / (60 * 60 * 24)));

?>
<div class="compojoom-bootstrap">
	<?php if (!empty($this->sidebar)): ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
		<?php else : ?>
		<div class="span12">
			<?php endif; ?>
			<div
				class="muted small"><?php echo JText::sprintf('COM_CMANDRILL_BASIC_STATS', 'http://mandrillapp.com'); ?>
				.
			</div>
			<div class="row-fluid">
				<div class="span6">
					<div class="row-fluid">
						<h3><?php echo JText::_('COM_CMANDRILL_LAST_7_DAYS'); ?></h3>
						<table class="table table-striped table-condensed table-bordered">
							<tbody>
							<tr>
								<td><b><?php echo JText::_('COM_CMANDRILL_DELIVERED'); ?></b></td>
								<td><?php  echo $delivered7; ?></td>
							</tr>
							<tr>
								<td><b><?php echo JText::_('COM_MANDRILL_SENT'); ?></b></td>
								<td><?php echo $sent7; ?></td>
							</tr>
							<tr>
								<td><b><?php echo JText::_('COM_MANDRILL_DELIVERED'); ?></b></td>
								<td><?php echo round(($delivered7 / $sent7) * 100, 1); ?>%</td>
							</tr>
							</tbody>
						</table>

						<div class="stat-block span5">
					<span
						class="stat"><?php echo round(($stats->last_7_days->opens / $stats->last_7_days->sent) * 100, 1); ?>
						%</span>
							<span class="label"><?php echo JText::_('COM_MANDRILL_AVG_OPEN_RATE'); ?></span>
						</div>
						<div class="stat-block span5">
					<span
						class="stat"><?php echo round(($stats->last_7_days->clicks / $stats->last_7_days->sent) * 100, 1); ?>
						%</span>
							<span class="label"><?php echo JText::_('COM_MANDRILL_AVG_CLICK_RATE'); ?></span>
						</div>
					</div>
				</div>

				<div class="span6">
					<h3><?php echo JText::_('COM_MANDRILL_ALL_TIME'); ?></h3>
					<table class="table table-striped table-condensed table-bordered">
						<tbody>
						<tr>
							<td><b><?php echo JText::_('COM_MANDRILL_SENT'); ?></b></td>
							<td><?php echo $stats->all_time->sent; ?></td>
						</tr>
						<tr>
							<td><b><?php echo JText::_('COM_MANDRILL_AVG_SENDS_DAILY'); ?></b></td>
							<td><?php echo (int)($stats->all_time->sent / $days); ?></td>
						</tr>
						<tr>
							<td><b><?php echo JText::_('COM_MANDRILL_TOTAL_SPAM_COMPLAINTS'); ?></b></td>
							<td><?php echo $stats->all_time->complaints ?></td>
						</tr>
						<tr>
							<td><b><?php echo JText::_('COM_MANDRILL_AVG_SPAM_COMPLAINTS'); ?></b></td>
							<td><?php echo $stats->all_time->complaints / $days; ?></td>
						</tr>
						<tr>
							<td><b><?php echo JText::_('COM_MANDRILL_HARD_BOUNCES'); ?></b></td>
							<td><?php echo ($stats->all_time->hard_bounces); ?></td>
						</tr>
						<tr>
							<td><b><?php echo JText::_('COM_MANDRILL_AVG_HARD_BOUNCES'); ?></b></td>
							<td><?php echo (int)($stats->all_time->hard_bounces / $days); ?></td>
						</tr>
						<tr>
							<td><b><?php echo JText::_('COM_MANDRILL_SOFT_BOUNCES'); ?></b></td>
							<td><?php echo ($stats->all_time->soft_bounces); ?></td>
						</tr>
						<tr>
							<td><b><?php echo JText::_('COM_MANDRILL_AVG_SOFT_BOUNCES'); ?></b></td>
							<td><?php echo (int)($stats->all_time->soft_bounces / $days); ?></td>
						</tr>
						<tr>
							<td><b><?php echo JText::_('COM_MANDRILL_AVG_BOUNCES_DAILY'); ?></b></td>
							<td><?php echo (int)(($stats->all_time->hard_bounces + $stats->all_time->soft_bounces) / $days); ?></td>
						</tr>
						<tr>
							<td><b><?php echo JText::_('COM_MANDRILL_AVG_UNSUB'); ?></b></td>
							<td><?php echo $stats->all_time->unsubs / $days; ?></td>
						</tr>
						</tbody>
					</table>
				</div>
			</div>

			<div class="clearfix"></div>
			<div class="row-fluid">
				<h3><?php echo JText::_('COM_MANDRILL_TOP_TRACKED_URLS'); ?></h3>
				<table class="table table-condensed table-bordered table-hover">
					<thead>
					<tr>
						<th><?php echo JText::_('COM_CMANDRILL_URL'); ?></th>
						<th><?php echo JText::_('COM_CMANDRILL_DELIVERED'); ?></th>
						<th><?php echo JText::_('COM_CMANDRILL_UNIQUE_CLICKS'); ?></th>
						<th><?php echo JText::_('COM_CMANDRILL_TOTAL_CLICKS'); ?></th>
					</tr>
					</thead>
					<tbody>
					<?php foreach ($urls as $url) : ?>
						<tr>
							<td><a href="<?php echo $url->url; ?>" target="_blank"><?php echo $url->url; ?></a></td>
							<td><?php echo $url->sent;?></td>
							<td><?php echo $url->unique_clicks; ?></td>
							<td><?php echo $url->clicks; ?></td>
						</tr>
					<?php endforeach; ?>
					</tbody>

				</table>
			</div>
			<?php echo cmandrillHelperUtility::footer(); ?>
		</div>
	</div>