<?php
/**
 * @author     Daniel Dimitrov - compojoom.com
 * @date       : 14.01.13
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
echo CompojoomHtmlCtemplate::getHead(CMandrillHelperMenu::getMenu(), 'templates', 'COM_CMANDRILL_TEMPLATES', '');

?>

	<div class="row">
		<form action="<?php echo JRoute::_('index.php?option=com_cmandrill&view=templates'); ?>" method="post"
			name="adminForm"
			id="adminForm">

			<div class="box-info full">
				<h2><?php echo $this->pagination->getResultsCounter(); ?></h2>

				<div class="additional-btn">
					<?php echo $this->pagination->getLimitBox(); ?>
				</div>

				<div class="col-md-4">
					<div class="input-group">
						<input type="text" name="filter_search" id="filter_search"
							placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>"
							value="<?php echo $this->escape($this->state->get('filter.search')); ?>"
							class="form-control"
							onchange="document.adminForm.submit();" />
					<span class="input-group-btn">
						<button onclick="this.form.submit();" class="btn btn-default" type="submit">
							<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>
						</button>
						<button class="btn btn-default" type="button" onclick="document.getElementById('filter_search').value='';
												this.form.getElementById('filter_published').value='*';
												this.form.getElementById('component').value='';
												this.form.submit();">
							<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>
						</button>
					</span>
					</div>
				</div>

				<div class="table-responsive">
					<table class="table table-hover table-striped">
						<thead>
						<tr>
							<th>#</th>
							<th><?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?></th>
							<th><?php echo JHtml::_('grid.sort', 'COM_CMANDRILL_STATE', 'a.state', $listDirn, $listOrder); ?></th>
							<th><?php echo JHtml::_('grid.sort', 'COM_CMANDRILL_PUBLISH_UP', 'a.publish_up', $listDirn, $listOrder); ?></th>
							<th><?php echo JHtml::_('grid.sort', 'COM_CMANDRILL_PUBLISH_DOWN', 'a.publish_down', $listDirn, $listOrder); ?></th>
							<th><?php echo JHtml::_('grid.sort', 'COM_CMANDRILL_TEMPLATE', 'a.template', $listDirn, $listOrder); ?></th>
							<th><?php echo JHtml::_('grid.sort', 'COM_CMANDRILL_COMPONENT', 'a.component', $listDirn, $listOrder); ?></th>
							<th><?php echo JHtml::_('grid.sort', 'COM_CMANDRILL_CLASS', 'a.class_name', $listDirn, $listOrder); ?></th>
							<th><?php echo JHtml::_('grid.sort', 'COM_CMANDRILL_FUNCTION', 'a.function_name', $listDirn, $listOrder); ?></th>
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
									<td><?php echo $template->class_name ?></td>
									<td><?php echo $template->function_name ?></td>
								</tr>
							<?php endforeach; ?>
						<?php else : ?>
							<tr>
								<td colspan="9">
									<?php echo JText::_('COM_CMANDRILL_YOU_HAVE_NO_TEMPLATES'); ?>
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
