<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Publications\Helpers\Permissions::getActions('license');

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));
Toolbar::title(Lang::txt('COM_PUBLICATIONS_LICENSE') . ': ' . $text, 'publications');
if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
}
Toolbar::cancel();

$text = preg_replace("/\r\n/", "\r", trim($this->row->text));

?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=licenses'); ?>" method="post" id="item-form" name="adminForm">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_PUBLICATIONS_LICENSE_DETAILS'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-title"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_TITLE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
					<input type="text" name="fields[title]" id="field-title" maxlength="100" value="<?php echo $this->escape($this->row->title); ?>" />
				</div>
				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_PUBLICATIONS_LICENSE_NAME_HINT'); ?>">
					<label for="field-name"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_NAME'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
					<input type="text" name="fields[name]" id="field-name" maxlength="100" value="<?php echo $this->escape($this->row->name); ?>" />
					<span class="hint"><?php echo Lang::txt('COM_PUBLICATIONS_LICENSE_NAME_HINT'); ?></span>
				</div>
				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_PUBLICATIONS_LICENSE_URL_HINT'); ?>">
					<label for="field-url"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_URL'); ?>:</label>
					<input type="text" name="fields[url]" id="field-url" maxlength="100" value="<?php echo $this->escape($this->row->url); ?>" />
					<span class="hint"><?php echo Lang::txt('COM_PUBLICATIONS_LICENSE_URL_HINT'); ?></span>
				</div>
				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_PUBLICATIONS_LICENSE_DESC_HINT'); ?>">
					<label for="field-info"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_ABOUT'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
					<textarea name="fields[info]" id="field-info" cols="40" rows="5"><?php echo $this->row->info; ?></textarea>
					<span class="hint"><?php echo Lang::txt('COM_PUBLICATIONS_LICENSE_DESC_HINT'); ?></span>
				</div>
				<div class="input-wrap">
					<label for="field-text"><?php echo Lang::txt('COM_PUBLICATIONS_FIELDSET_CONTENT'); ?>:</label></td>
					<textarea name="fields[text]" id="field-text" cols="40" rows="20"><?php echo $text; ?></textarea>
				</div>
				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_PUBLICATIONS_FIELD_ICON_HINT'); ?>">
					<label for="field-icon"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_ICON'); ?>:</label>
					<input type="text" name="fields[icon]" id="field-icon" value="<?php echo $this->escape($this->row->icon); ?>" />
					<span class="hint"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_ICON_HINT'); ?></span>
				</div>

				<input type="hidden" name="fields[ordering]" value="<?php echo $this->row->ordering; ?>" />
				<input type="hidden" name="fields[id]" value="<?php echo $this->row->id; ?>" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
				<input type="hidden" name="task" value="save" />
			</fieldset>
		</div>
		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<th><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_ID'); ?></th>
						<td><?php echo $this->row->id; ?></td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_DEFAULT'); ?></th>
						<td><?php echo $this->row->isMain() ? Lang::txt('COM_PUBLICATIONS_LICENSE_YES') : Lang::txt('COM_PUBLICATIONS_LICENSE_NO'); ?></td>
					</tr>
				<?php if ($this->row->id) { ?>
					<tr>
						<th><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_ORDERING'); ?></th>
						<td><?php echo $this->row->ordering; ?></td>
					</tr>
				<?php } ?>
				</tbody>
			</table>

			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_PUBLICATIONS_LICENSE_CONFIGURATION'); ?></span></legend>

				<fieldset>
					<legend><?php echo Lang::txt('COM_PUBLICATIONS_STATUS_ACTIVE'); ?></legend>

					<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_PUBLICATIONS_LICENSE_ACTIVE_EXPLAIN'); ?>">
						<span class="hint"><?php echo Lang::txt('COM_PUBLICATIONS_LICENSE_ACTIVE_EXPLAIN'); ?></span>

						<input class="option" name="fields[active]" id="field-active1" type="radio" value="1" <?php echo $this->row->active == 1 ? 'checked="checked"' : ''; ?> />
						<label for="field-active1"><?php echo Lang::txt('JYES'); ?></label>
						<br />
						<input class="option" name="fields[active]" id="field-active0" type="radio" value="0" <?php echo $this->row->active == 0 ? 'checked="checked"' : ''; ?> />
						<label for="field-active0"><?php echo Lang::txt('JNO'); ?></label>
					</div>
				</fieldset>

				<fieldset>
					<legend><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_CUSTOMIZABLE'); ?></legend>

					<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_PUBLICATIONS_FIELD_CUSTOMIZABLE_HINT'); ?>">
						<span class="hint"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_CUSTOMIZABLE_HINT'); ?></span>

						<input class="option" name="fields[customizable]" id="field-customizable1" type="radio" value="1" <?php echo $this->row->customizable == 1 ? 'checked="checked"' : ''; ?> />
						<label for="field-customizable1"><?php echo Lang::txt('JYES'); ?></label>
						<br />
						<input class="option" name="fields[customizable]" id="field-customizable0" type="radio" value="0" <?php echo $this->row->customizable == 0 ? 'checked="checked"' : ''; ?> />
						<label for="field-customizable0"><?php echo Lang::txt('JNO'); ?></label>
					</div>
				</fieldset>

				<fieldset>
					<legend><?php echo Lang::txt('Agreement required'); ?></legend>

					<div class="input-wrap" data-hint="<?php echo Lang::txt('Do we require publication authors to agree to license terms?'); ?>">
						<span class="hint"><?php echo Lang::txt('Do we require publication authors to agree to license terms?'); ?></span>

						<input class="option" name="fields[agreement]" id="field-agreement1" type="radio" value="1" <?php echo $this->row->agreement == 1 ? 'checked="checked"' : ''; ?> />
						<label for="field-agreement1"><?php echo Lang::txt('JYES'); ?></label>
						<br />
						<input class="option" name="fields[agreement]" id="field-agreement0" type="radio" value="0" <?php echo $this->row->agreement == 0 ? 'checked="checked"' : ''; ?> />
						<label for="field-agreement0"><?php echo Lang::txt('JNO'); ?></label>
					</div>
				</fieldset>

				<fieldset>
					<legend><?php echo Lang::txt('Allow Derivatives'); ?></legend>

					<div class="input-wrap" data-hint="<?php echo Lang::txt('Are derivatives allowed under the terms of this license?'); ?>">
						<span class="hint"><?php echo Lang::txt('Are derivatives allowed under the terms of this license?'); ?></span>

						<input class="option" name="fields[derivatives]" id="field-derivatives1" type="radio" value="1" <?php echo $this->row->derivatives == 1 ? 'checked="checked"' : ''; ?> />
						<label for="field-derivatives1"><?php echo Lang::txt('JYES'); ?></label>
						<br />
						<input class="option" name="fields[derivatives]" id="field-derivatives0" type="radio" value="0" <?php echo $this->row->derivatives == 0 ? 'checked="checked"' : ''; ?> />
						<label for="field-derivatives0"><?php echo Lang::txt('JNO'); ?></label>
					</div>
				</fieldset>
			</fieldset>
		</div>
	</div>

	<?php echo Html::input('token'); ?>
</form>