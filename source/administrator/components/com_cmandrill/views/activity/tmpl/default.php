<?php
/**
 * @author     Daniel Dimitrov <daniel@compojoom.com>
 * @date       07.05.15
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

JHtml::stylesheet('media/com_cmandrill/css/dashboard.css');
JHtml::script('https://cdnjs.cloudflare.com/ajax/libs/dygraph/1.1.0/dygraph-combined.js');

echo CompojoomHtmlCtemplate::getHead(CMandrillHelperMenu::getMenu(), 'activity', '', '');

$chartData = CmandrillHelperUtility::getDataForChart();
$search = new CompojoomLayoutFile('search.activity');
?>

	<div class="row">
		<form action="<?php echo JRoute::_('index.php?option=com_cmandrill&view=activity'); ?>" method="post"
			name="adminForm"
			id="adminForm">
			<script type="text/javascript">
				jQuery(document).ready(function() {
					var options = 	{
						showRangeSelector: true,
						rangeSelectorHeight: 30,
						legend: 'always',
						colors: ['#24890D', '#f09d3e', '#da1e3f'],
						labelsDivStyles: {
							'text-align': 'right',
							'background': 'none',
							'width': '400px'
						},
						labelsDivWidth: 400,
						strokeWidth: 1.3
					};
					new Dygraph(

						// containing div
						document.getElementById("chart1"),

						// CSV or path to a CSV file.
						"<?php echo $chartData['delivered']; ?>",
						options
					);
					new Dygraph(

						// containing div
						document.getElementById("chart2"),

						// CSV or path to a CSV file.
						"<?php echo $chartData['opens']; ?>",
						options
					);
				})

			</script>

			<div class="box-info full">
				<h2><?php echo JText::_('COM_CMANDRILL_ACTIVITY'); ?></h2>
				<div class="col-md-12">
					<div class="col-md-6">
						<div id="chart1" style="width:100%; height:300px;"></div>
					</div>
					<div class="col-md-6">
						<div id="chart2" style="width:100%; height:300px;"></div>
					</div>
				</div>


				<div class="col-md-12 top-buffer">
					<?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
				</div>

				<div class="table-responsive top-buffer">
					<table class="table table-hover table-striped">
						<thead>
						<tr>
							<th><?php echo JText::_('COM_CMANDRILL_STATE'); ?></th>
							<th><?php echo JText::_('COM_CMANDRILL_SENDER'); ?></th>
							<th><?php echo JText::_('COM_CMANDRILL_EMAIL'); ?></th>
							<th><?php echo JText::_('COM_CMANDRILL_SUBJECT'); ?></th>
							<th><?php echo JText::_('COM_CMANDRILL_OPENS'); ?></th>
							<th><?php echo JText::_('COM_CMANDRILL_CLICKS'); ?></th>
							<th><?php echo JText::_('COM_CMANDRILL_TEMPLATE'); ?></th>
						</tr>
						</thead>
						<tbody>
						<?php if (count($this->items)) : ?>
							<?php foreach ($this->items as $i => $item) : ?>
								<tr>
									<td class="<?php echo CmandrillHelperUtility::getClassForState($item->state); ?>">
										<a href="<?php echo JRoute::_('index.php?option=com_cmandrill&view=activity&filter_state=' . $item->state); ?>">
										<?php echo CmandrillHelperUtility::getTranslationForState($item->state); ?>
										</a><br />
										<span class="muted"><?php echo JFactory::getDate($item->ts)->format('F d, Y H:i:s'); ?></span>
									</td>
									<td>
										<a href="<?php echo JRoute::_('index.php?option=com_cmandrill&view=activity&filter_search=sender:' . $item->sender); ?>">
										<?php echo $item->sender; ?>
										</a>
									</td>
									<td>
										<a href="<?php echo JRoute::_('index.php?option=com_cmandrill&view=activity&filter_search=full_email:' . $item->email); ?>">
											<?php echo $item->email; ?>
										</a>
									</td>
									<td>
										<a href="<?php echo JRoute::_('index.php?option=com_cmandrill&view=activity&filter_search=subject:' . $item->subject); ?>">
											<?php echo $item->subject; ?>
										</a>

										<?php if(count($item->tags)) : ?>
											<ul class="list-inline">
											<?php foreach($item->tags as $tag) : ?>
												<li><?php echo $tag; ?></li>
											<?php endforeach; ?>
											</ul>
										<?php endif; ?>
									</td>
									<td><?php echo $item->opens; ?></td>
									<td><?php echo $item->clicks; ?></td>
									<td><?php echo $item->template; ?></td>
								</tr>
							<?php endforeach; ?>
						<?php else : ?>
							<tr>
								<td colspan="9">
									<?php echo JText::_('COM_CMANDRILL_NO_RESULTS_FOR_YOUR_CURRENT_SEARCH_CRITERIA'); ?>
								</td>
							</tr>
						<?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
			<?php echo JHtml::_('form.token'); ?>
		</form>
	</div>
<?php
// Show Footer
echo CompojoomHtmlCTemplate::getFooter(CmandrillHelperUtility::footer());
