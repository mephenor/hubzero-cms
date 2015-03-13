<?php
defined('_JEXEC') or die('Restricted access');

	$juser = JFactory::getUser();

	$cls = isset($this->cls) ? $this->cls : 'odd';

	$name = Lang::txt('COM_BLOG_ANONYMOUS');
	if (!$this->comment->get('anonymous'))
	{
		$name = $this->escape(stripslashes($this->comment->creator()->get('name', $name)));
		if ($this->comment->creator()->get('public'))
		{
			$name = '<a href="' . Route::url($this->comment->creator()->getLink()) . '">' . $name . '</a>';
		}
	}

	if ($this->comment->isReported())
	{
		$comment = '<p class="warning">' . Lang::txt('COM_BLOG_COMMENT_REPORTED_AS_ABUSIVE') . '</p>';
	}
	else
	{
		$comment  = $this->comment->content('parsed');
	}
?>
	<li class="comment <?php echo $cls; ?>" id="c<?php echo $this->comment->get('id'); ?>">
		<p class="comment-member-photo">
			<img src="<?php echo $this->comment->creator()->getPicture($this->comment->get('anonymous')); ?>" alt="" />
		</p>
		<div class="comment-content">
			<p class="comment-title">
				<strong><?php echo $name; ?></strong>
				<a class="permalink" href="<?php echo Route::url($this->base . '#c' . $this->comment->get('id')); ?>" title="<?php echo Lang::txt('COM_BLOG_PERMALINK'); ?>">
					<span class="comment-date-at"><?php echo Lang::txt('COM_BLOG_AT'); ?></span>
					<span class="time"><time datetime="<?php echo $this->comment->get('created'); ?>"><?php echo $this->comment->created('time'); ?></time></span>
					<span class="comment-date-on"><?php echo Lang::txt('COM_BLOG_ON'); ?></span>
					<span class="date"><time datetime="<?php echo $this->comment->get('created'); ?>"><?php echo $this->comment->created('date'); ?></time></span>
					<?php if ($this->comment->wasModified()) { ?>
						&mdash; <?php echo Lang::txt('COM_BLOG_EDITED'); ?>
						<span class="comment-date-at"><?php echo Lang::txt('COM_BLOG_AT'); ?></span> 
						<span class="time"><time datetime="<?php echo $this->comment->modified(); ?>"><?php echo $this->comment->modified('time'); ?></time></span> 
						<span class="comment-date-on"><?php echo Lang::txt('COM_BLOG_ON'); ?></span> 
						<span class="date"><time datetime="<?php echo $this->comment->modified(); ?>"><?php echo $this->comment->modified('date'); ?></time></span>
					<?php } ?>
				</a>
			</p>

		<?php if (JRequest::getWord('action') == 'editcomment'
				&& JRequest::getInt('comment') == $this->comment->get('id')
				&& ($this->config->get('access-edit-comment') || $juser->get('id') == $this->comment->get('created_by'))) { ?>
			<form id="cform<?php echo $this->comment->get('id'); ?>" class="comment-edit" action="<?php echo Route::url($this->base); ?>" method="post" enctype="multipart/form-data">
				<fieldset>
					<legend><span><?php echo Lang::txt('COM_BLOG_COMMENT_EDIT'); ?></span></legend>

					<input type="hidden" name="comment[id]" value="<?php echo $this->comment->get('id'); ?>" />
					<input type="hidden" name="comment[entry_id]" value="<?php echo $this->comment->get('entry_id'); ?>" />
					<input type="hidden" name="comment[parent]" value="<?php echo $this->comment->get('parent'); ?>" />
					<input type="hidden" name="comment[created]" value="<?php echo $this->comment->get('created'); ?>" />
					<input type="hidden" name="comment[created_by]" value="<?php echo $this->comment->get('created_by'); ?>" />

					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="task" value="savecomment" />

					<?php echo JHTML::_('form.token'); ?>

					<label for="comment_<?php echo $this->comment->get('id'); ?>_content">
						<span class="label-text"><?php echo Lang::txt('COM_BLOG_FIELD_COMMENTS'); ?></span>
						<?php
						echo $this->editor('comment[content]', $this->comment->content('raw'), 35, 4, 'comment_' . $this->comment->get('id') . '_content', array('class' => 'minimal no-footer'));
						?>
					</label>

					<label class="comment-anonymous" for="comment_<?php echo $this->comment->get('id'); ?>_anonymous">
						<input class="option" type="checkbox" name="comment[anonymous]" id="comment_<?php echo $this->comment->get('id'); ?>_anonymous" value="1" <?php if ($this->comment->get('anonymous')) { echo ' checked="checked"'; } ?> />
						<?php echo Lang::txt('COM_BLOG_POST_ANONYMOUS'); ?>
					</label>

					<p class="submit">
						<input type="submit" value="<?php echo Lang::txt('COM_BLOG_SUBMIT'); ?>" />
					</p>
				</fieldset>
			</form>
	<?php } else { ?>

			<div class="comment-body">
				<?php echo $comment; ?>
			</div>

			<p class="comment-options">
			<?php if ($this->config->get('access-delete-comment')) { ?>
				<a class="icon-delete delete" data-confirm="<?php echo Lang::txt('COM_BLOG_CONFIRM_DELETE'); ?>" href="<?php echo Route::url($this->base . '&action=deletecomment&comment=' . $this->comment->get('id')); ?>"><!--
					--><?php echo Lang::txt('COM_BLOG_DELETE'); ?><!--
				--></a>
			<?php } ?>
			<?php if (!$this->comment->isReported()) { ?>
				<?php if ($this->config->get('access-edit-comment') || $juser->get('id') == $this->comment->get('created_by')) { ?>
					<a class="icon-edit edit" href="<?php echo Route::url($this->base . '&action=editcomment&comment=' . $this->comment->get('id')); ?>"><!--
						--><?php echo Lang::txt('COM_BLOG_EDIT'); ?><!--
					--></a>
				<?php } ?>
				<?php if ($this->depth < $this->config->get('comments_depth', 3)) { ?>
					<?php if (JRequest::getInt('reply', 0) == $this->comment->get('id')) { ?>
					<a class="icon-reply reply active" data-txt-active="<?php echo Lang::txt('COM_BLOG_CANCEL'); ?>" data-txt-inactive="<?php echo Lang::txt('COM_BLOG_REPLY'); ?>" href="<?php echo Route::url($this->base); ?>" rel="comment-form<?php echo $this->comment->get('id'); ?>"><!--
					--><?php echo Lang::txt('COM_BLOG_CANCEL'); ?><!--
				--></a>
					<?php } else { ?>
					<a class="icon-reply reply" data-txt-active="<?php echo Lang::txt('COM_BLOG_CANCEL'); ?>" data-txt-inactive="<?php echo Lang::txt('COM_BLOG_REPLY'); ?>" href="<?php echo Route::url($this->base . '&reply=' . $this->comment->get('id')); ?>" rel="comment-form<?php echo $this->comment->get('id'); ?>"><!--
					--><?php echo Lang::txt('COM_BLOG_REPLY'); ?><!--
				--></a>
					<?php } ?>
				<?php } ?>
				<a class="icon-abuse abuse" data-txt-flagged="<?php echo Lang::txt('COM_BLOG_COMMENT_REPORTED_AS_ABUSIVE'); ?>" href="<?php echo Route::url('index.php?option=com_support&task=reportabuse&category=blogcomment&id=' . $this->comment->get('id') . '&parent=' . $this->comment->get('entry_id')); ?>"><!--
					--><?php echo Lang::txt('COM_BLOG_REPORT_ABUSE'); ?><!--
				--></a>
			<?php } ?>
			</p>

		<?php if ($this->depth < $this->config->get('comments_depth', 3)) { ?>
			<div class="addcomment comment-add<?php if (JRequest::getInt('reply', 0) != $this->comment->get('id')) { echo ' hide'; } ?>" id="comment-form<?php echo $this->comment->get('id'); ?>">
				<form id="cform<?php echo $this->comment->get('id'); ?>" action="<?php echo Route::url($this->base); ?>" method="post" enctype="multipart/form-data">
					<fieldset>
						<legend><span><?php echo Lang::txt('COM_BLOG_REPLYING_TO', (!$this->comment->get('anonymous') ? $name : Lang::txt('COM_BLOG_ANONYMOUS'))); ?></span></legend>

						<input type="hidden" name="comment[id]" value="0" />
						<input type="hidden" name="comment[entry_id]" value="<?php echo $this->comment->get('entry_id'); ?>" />
						<input type="hidden" name="comment[parent]" value="<?php echo $this->comment->get('id'); ?>" />
						<input type="hidden" name="comment[created]" value="" />
						<input type="hidden" name="comment[created_by]" value="<?php echo $juser->get('id'); ?>" />
						<input type="hidden" name="comment[state]" value="1" />
						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="task" value="savecomment" />

						<?php echo JHTML::_('form.token'); ?>

						<label for="comment_<?php echo $this->comment->get('id'); ?>_content">
							<span class="label-text"><?php echo Lang::txt('COM_BLOG_FIELD_COMMENTS'); ?></span>
							<?php
							echo $this->editor('comment[content]', '', 35, 4, 'comment_' . $this->comment->get('id') . '_content', array('class' => 'minimal no-footer'));
							?>
						</label>

						<label id="comment-anonymous-label" for="comment-anonymous">
							<input class="option" type="checkbox" name="comment[anonymous]" id="comment-anonymous" value="1" />
							<?php echo Lang::txt('COM_BLOG_POST_ANONYMOUS'); ?>
						</label>

						<p class="submit">
							<input type="submit" value="<?php echo Lang::txt('COM_BLOG_SUBMIT'); ?>" />
						</p>
					</fieldset>
				</form>
			</div><!-- / .addcomment -->
		<?php } ?>
	<?php } ?>
		</div><!-- / .comment-content -->
		<?php
		if ($this->depth < $this->config->get('comments_depth', 3))
		{
			$this->view('_list')
			     ->set('parent', $this->comment->get('id'))
			     ->set('option', $this->option)
			     ->set('comments', $this->comment->replies())
			     ->set('config', $this->config)
			     ->set('depth', $this->depth)
			     ->set('cls', $cls)
			     ->set('base', $this->base)
			     ->display();
		}
		?>
	</li>