<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title('<a href="' . Route::url('index.php?option=' . $this->option) . '">' . Lang::txt('COM_PUBLICATIONS_PUBLICATIONS') . '</a>: ' . Lang::txt('COM_PUBLICATIONS_PUBLICATIONS_ADMIN_CONTROLS'), 'publications');
