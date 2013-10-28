<?php
/**
 * @author Daniel Dimitrov - compojoom.com
 * @date: 18.01.13
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

?>
<div class="compojoom-bootstrap">
	<form action="<?php echo JRoute::_('index.php?option=com_cmandrill&layout=edit&id=' . (int)$this->item->id); ?>"
		  method="post" name="adminForm"
		  id="adminForm">
		<div class="row-fluid">
			<div class="span10 form-horizontal">

				<fieldset>
					<div class="tab-content">
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('title'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('title'); ?></div>
						</div>
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('state'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('state'); ?></div>
						</div>

						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('publish_up'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('publish_up'); ?></div>
						</div>
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('publish_down'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('publish_down'); ?></div>
						</div>
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('template'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('template'); ?></div>
						</div>
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('component'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('component'); ?></div>
						</div>
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('view'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('view'); ?></div>
						</div>
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('task'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('task'); ?></div>
						</div>
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('class_name'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('class_name'); ?></div>
						</div>
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('function_name'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('function_name'); ?></div>
						</div>
					</div>
				</fieldset>
			</div>
		</div>
		<input type="hidden" name="task" value=""/>
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>