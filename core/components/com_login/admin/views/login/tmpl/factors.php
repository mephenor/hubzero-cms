<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

Request::setVar('hidemainmenu', 1);
Toolbar::title(Lang::txt('COM_LOGIN_FACTORS_VERIFICATION'));

$this->css('factors');
?>

<div class="factors">
	<?php foreach ($this->factors as $factor) : ?>
		<div class="factor-wrap">
			<div class="factor">
				<?php echo $factor->html; ?>
			</div>
		</div>
	<?php endforeach; ?>
</div>