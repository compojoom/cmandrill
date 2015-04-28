<?php
/**
 * @author     Daniel Dimitrov - compojoom.com
 * @date       : 18.01.13
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

echo CompojoomHtmlCtemplate::getHead(CMandrillHelperMenu::getMenu(), 'template', 'COM_CMANDRILL_TEMPLATE', '');
?>
	<div class="box-info">
		<form action="<?php echo JRoute::_('index.php?option=com_cmandrill&layout=edit&id=' . (int) $this->item->id); ?>"
			method="post" name="adminForm"
			id="adminForm">
			<div class="row-fluid">
				<div class="span10 form-horizontal">
					<h2>
						<?php if((int) $this->item->id): ?>
							<?php echo JText::_('COM_CMANDRILL_EDIT_TEMPLATE'); ?>
						<?php else: ?>
							<?php echo JText::_('COM_CMANDRILL_NEW_TEMPLATE'); ?>
						<?php endif; ?>
					</h2>

					<fieldset>
						<div class="tab-content">
							<div class="form-group">
								<?php echo $this->form->getLabel('title'); ?>
								<div class="col-sm-10"><?php echo $this->form->getInput('title'); ?></div>
							</div>
							<div class="form-group">
								<?php echo $this->form->getLabel('state'); ?>
								<div class="col-sm-10"><?php echo $this->form->getInput('state'); ?></div>
							</div>

							<div class="form-group">
								<?php echo $this->form->getLabel('publish_up'); ?>
								<div class="col-sm-10"><?php echo $this->form->getInput('publish_up'); ?></div>
							</div>
							<div class="form-group">
								<?php echo $this->form->getLabel('publish_down'); ?>
								<div class="col-sm-10"><?php echo $this->form->getInput('publish_down'); ?></div>
							</div>
							<div class="form-group">
								<?php echo $this->form->getLabel('template'); ?>
								<div class="col-sm-10"><?php echo $this->form->getInput('template'); ?></div>
							</div>
							<div class="form-group">
								<?php echo $this->form->getLabel('component'); ?>
								<div class="col-sm-10"><?php echo $this->form->getInput('component'); ?></div>
							</div>
							<div class="form-group">
								<?php echo $this->form->getLabel('class_name'); ?>
								<div class="col-sm-10"><?php echo $this->form->getInput('class_name'); ?></div>
							</div>
							<div class="form-group">
								<?php echo $this->form->getLabel('function_name'); ?>
								<div class="col-sm-10"><?php echo $this->form->getInput('function_name'); ?></div>
							</div>
						</div>
					</fieldset>
				</div>
			</div>
			<input type="hidden" name="task" value="" />
			<?php echo JHtml::_('form.token'); ?>
		</form>
	</div>
<?php
// Show Footer
echo CompojoomHtmlCTemplate::getFooter(CmandrillHelperUtility::footer());
