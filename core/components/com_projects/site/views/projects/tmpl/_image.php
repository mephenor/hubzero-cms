<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>
	<div id="pimage" class="pimage">
		<a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias')); ?>" title="<?php echo $this->escape($this->model->get('title')) . ' - ' . Lang::txt('COM_PROJECTS_VIEW_UPDATES'); ?>">
			<img src="<?php echo $this->model->picture('master');  ?>" alt="<?php echo $this->escape($this->model->get('title')); ?>" />
		</a>
	</div>

