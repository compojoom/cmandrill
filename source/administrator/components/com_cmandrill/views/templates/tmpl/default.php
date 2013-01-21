<?php
/**
 * @author Daniel Dimitrov - compojoom.com
 * @date: 14.01.13
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
?>
<div class="compojoom-bootstrap">
	<form action="<?php echo JRoute::_('index.php?option=com_cmandrill&view=templates'); ?>" method="post"
		  name="adminForm"
		  id="adminForm">

		<?php if (!empty($this->sidebar)): ?>
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
		<div id="j-main-container" class="span10">
			<?php else : ?>
			<div class="span12">
				<?php endif; ?>
				<div id="filter-bar" class="btn-toolbar">
					<div class="filter-search btn-group pull-left">
						<label for="filter_search"
							   class="element-invisible"><?php echo JText::_('COM_CMANDRILL_SEARCH_IN_TITLE');?></label>
						<input type="text" name="filter_search" id="filter_search"
							   placeholder="<?php echo JText::_('COM_MANDRILL_SEARCH_IN_TITLE'); ?>"
							   value="<?php echo $this->escape($this->state->get('filter.search')); ?>"
							   title="<?php echo JText::_('COM_WEBLINKS_SEARCH_IN_TITLE'); ?>"/>
					</div>
					<div class="btn-group pull-left">
						<button class="btn hasTooltip" type="submit"
								title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i>
						</button>
						<button class="btn hasTooltip" type="button"
								title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"
								onclick="document.id('filter_search').value='';this.form.submit();"><i
								class="icon-remove"></i></button>
					</div>
					<div class="btn-group pull-right hidden-phone">
						<label for="limit"
							   class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
						<?php echo $this->pagination->getLimitBox(); ?>
					</div>
				</div>
				<div class="clearfix"></div>
				<table class="table table-condensed table-striped">
					<thead>
					<tr>
						<th>#</th>
						<th><?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?></th>
						<th><?php echo JHtml::_('grid.sort', 'COM_CMANDRILL_STATE', 'a.state', $listDirn, $listOrder); ?></th>
						<th><?php echo JHtml::_('grid.sort', 'COM_MANDRILL_PUBLISH_UP', 'a.publish_up', $listDirn, $listOrder); ?></th>
						<th><?php echo JHtml::_('grid.sort', 'COM_MANDRILL_PUBLISH_DOWN', 'a.publish_down', $listDirn, $listOrder); ?></th>
						<th><?php echo JHtml::_('grid.sort', 'COM_MANDRILL_TEMPLATE', 'a.template', $listDirn, $listOrder); ?></th>
						<th><?php echo JHtml::_('grid.sort', 'COM_MANDRILL_COMPONENT', 'a.component', $listDirn, $listOrder); ?></th>
						<th><?php echo JHtml::_('grid.sort', 'COM_MANDRILL_VIEW', 'a.view', $listDirn, $listOrder); ?></th>
						<th><?php echo JHtml::_('grid.sort', 'COM_MANDRILL_TASK', 'a.task', $listDirn, $listOrder); ?></th>
					</tr>
					</thead>
					<tbody>
					<?php if (count($this->items)) : ?>
						<?php foreach ($this->items as $i => $template) : ?>
							<tr>
								<td><?php echo JHtml::_('grid.id', $i, $template->id); ?></td>
								<td>
									<a href="<?php echo JRoute::_('index.php?option=com_cmandrill&task=template.edit&id=' . $template->id); ?>"
									   class="">
										<?php echo $template->title; ?>
									</a>
								</td>
								<td>
									<?php echo JHtml::_('jgrid.published', $template->state, $i, 'templates.', true, 'cb', $template->publish_up, $template->publish_down); ?>
								</td>
								<td><?php echo $template->publish_up ?></td>
								<td><?php echo $template->publish_down ?></td>
								<td><?php echo $template->template ?></td>
								<td><?php echo $template->component ?></td>
								<td><?php echo $template->view ?></td>
								<td><?php echo $template->task ?></td>
							</tr>
						<?php endforeach; ?>
					<?php else : ?>
						<tr>
							<td colspan="9">
								<?php echo JText::_('COM_MANDRILL_YOU_HAVE_NO_TEMPLATES'); ?>
							</td>
						</tr>
					<?php endif; ?>
					</tbody>
				</table>


				<?php echo cmandrillHelperUtility::footer(); ?>
			</div>
			<input type="hidden" name="task" value=""/>
			<input type="hidden" name="boxchecked" value="0"/>
			<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
			<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
			<?php echo JHtml::_('form.token'); ?>
	</form>
</div>