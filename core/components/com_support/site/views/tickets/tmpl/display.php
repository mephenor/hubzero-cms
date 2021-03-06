<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$live_site = rtrim(Request::base(), '/');

$this->css()
     ->css('conditions.css')
     ->js('jquery.hoverIntent.js', 'system')
     ->js('json2.js')
     ->js('condition.builder.js')
     ->js('tickets.js');
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<ul id="useroptions">
		<?php if ($this->acl->check('read', 'tickets')) { ?>
			<li>
				<a class="icon-stats stats btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=stats'); ?>">
					<?php echo Lang::txt('COM_SUPPORT_STATS'); ?>
				</a>
			</li>
		<?php } ?>
			<li class="last">
				<a class="icon-add add btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=new'); ?>">
					<?php echo Lang::txt('COM_SUPPORT_NEW_TICKET'); ?>
				</a>
			</li>
		</ul>
	</div><!-- / #content-header-extra -->
</header><!-- / #content-header -->

<section class="panel tickets">
	<div class="panel-row">

		<div class="pane pane-queries" id="queries" data-update="<?php echo Route::url('index.php?option=' . $this->option . '&controller=queries&task=saveordering&' . Session::getFormToken() . '=1'); ?>">
			<div class="pane-inner">

				<?php if ($this->acl->check('read', 'tickets')) { ?>
					<ul id="watch-list">
						<li id="folder_watching" class="open">
							<span class="icon-watch folder"><?php echo Lang::txt('COM_SUPPORT_WATCH_LIST'); ?></span>
							<ul id="queries_watching" class="wqueries">
								<li<?php if (intval($this->filters['show']) == -1) { echo ' class="active"'; }?>>
									<a class="aquery" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=display&show=-1&limitstart=0' . (intval($this->filters['show']) != -1 ? '&search=' : '')); ?>">
										<?php echo $this->escape(Lang::txt('COM_SUPPORT_WATCH_LIST_OPEN')); ?> <span><?php echo $this->watch['open']; ?></span>
									</a>
								</li>
								<li<?php if (intval($this->filters['show']) == -2) { echo ' class="active"'; }?>>
									<a class="aquery" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=display&show=-2&limitstart=0' . (intval($this->filters['show']) != -2 ? '&search=' : '')); ?>">
										<?php echo $this->escape(Lang::txt('COM_SUPPORT_WATCH_LIST_CLOSED')); ?> <span><?php echo $this->watch['closed']; ?></span>
									</a>
								</li>
							</ul>
						</li>
					</ul>
				<?php } ?>

				<ul id="query-list">
					<?php if (count($this->folders) > 0) { ?>
						<?php foreach ($this->folders as $folder) { ?>
							<li id="folder_<?php echo $this->escape($folder->id); ?>" class="open">
								<span class="icon-folder folder" id="<?php echo $this->escape($folder->id); ?>-title" data-id="<?php echo $this->escape($folder->id); ?>"><?php echo $this->escape($folder->title); ?></span>
								<?php if ($this->acl->check('read', 'tickets')) { ?>
									<span class="folder-options">
										<a class="delete" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=queries&task=removefolder&id=' . $folder->id . '&' . Session::getFormToken() . '=1'); ?>" data-confirm="<?php echo Lang::txt('COM_SUPPORT_QUERIES_CONFIRM_DELETE'); ?>" title="<?php echo Lang::txt('JACTION_DELETE'); ?>">
											<?php echo Lang::txt('JACTION_DELETE'); ?>
										</a>
										<a class="edit editfolder" data-id="<?php echo $this->escape($folder->id); ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=queries&task=editfolder&id=' . $folder->id . '&tmpl=component&' . Session::getFormToken() . '=1'); ?>" data-href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=queries&task=savefolder&' . Session::getFormToken() . '=1&fields[id]=' . $folder->id); ?>" data-name="<?php echo Lang::txt('COM_SUPPORT_FOLDER_NAME'); ?>" title="<?php echo Lang::txt('JACTION_EDIT'); ?>">
											<?php echo Lang::txt('JACTION_EDIT'); ?>
										</a>
									</span>
								<?php } ?>
								<ul id="queries_<?php echo $this->escape($folder->id); ?>" class="queries">
									<?php foreach ($folder->queries as $query) { ?>
										<li id="query_<?php echo $this->escape($query->id); ?>" <?php if (intval($this->filters['show']) == $query->id) { echo ' class="active"'; }?>>
											<a class="aquery" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=display&show=' . $query->id . (intval($this->filters['show']) != $query->id ? '&search=&limitstart=0' : '')); ?>">
												<?php echo $this->escape(stripslashes($query->title)); ?> <span><?php echo $query->get('count'); ?></span>
											</a>
											<?php if ($this->acl->check('read', 'tickets')) { ?>
												<span class="query-options">
													<a class="delete" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=queries&task=remove&id=' . $query->id . '&' . Session::getFormToken() . '=1'); ?>" data-confirm="<?php echo Lang::txt('COM_SUPPORT_QUERIES_CONFIRM_DELETE'); ?>" title="<?php echo Lang::txt('JACTION_DELETE'); ?>">
														<?php echo Lang::txt('JACTION_DELETE'); ?>
													</a>
													<a class="modal edit" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=queries&task=edit&id=' . $query->id . '&tmpl=component&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('JACTION_EDIT'); ?>" rel="{handler: 'iframe', size: {x: 570, y: 550}}">
														<?php echo Lang::txt('JACTION_EDIT'); ?>
													</a>
												</span>
											<?php } ?>
										</li>
									<?php } ?>
								</ul>
							</li>
						<?php } ?>
					<?php } ?>
				</ul>
				<?php if ($this->acl->check('read', 'tickets')) { ?>
					<ul class="controls">
						<li>
							<a class="icon-list modal" id="new-query" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=queries&task=add&' . Session::getFormToken() . '=1'); ?>" rel="{handler: 'iframe', size: {x: 570, y: 550}}" title="<?php echo Lang::txt('COM_SUPPORT_ADD_QUERY'); ?>">
								<?php echo Lang::txt('COM_SUPPORT_ADD_QUERY'); ?>
							</a>
						</li>
						<li>
							<a class="icon-folder" id="new-folder" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=queries&task=addfolder&' . Session::getFormToken() . '=1'); ?>" data-href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=queries&task=savefolder&' . Session::getFormToken() . '=1'); ?>" data-name="<?php echo Lang::txt('COM_SUPPORT_FOLDER_NAME'); ?>" title="<?php echo Lang::txt('COM_SUPPORT_ADD_FOLDER'); ?>">
								<?php echo Lang::txt('COM_SUPPORT_ADD_FOLDER'); ?>
							</a>
						</li>
					</ul>
				<?php } ?>

			</div><!-- / .pane-inner -->
		</div><!-- / .pane -->
		<div class="pane pane-list">
			<div class="pane-inner" id="tickets">
				<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=display'); ?>" method="post" id="ticketForm">
					<div class="list-options">
						<?php $direction = (strtolower($this->filters['sortdir']) == 'desc') ? 'asc' : 'desc'; ?>
						<ul class="sort-options">
							<li>
								<span class="sort-header"><?php echo Lang::txt('COM_SUPPORT_SORT_RESULTS'); ?></span>
								<ul>
									<li>
										<a class="sort-age<?php if ($this->filters['sort'] == 'created') { echo ' active ' . strtolower($this->filters['sortdir']); } ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=display&show=' . $this->filters['show'] . '&search=' . $this->filters['search'] . '&sort=created&sortdir=' . $direction . '&limit=' . $this->filters['limit'] . '&limitstart=0'); ?>" title="<?php echo Lang::txt('COM_SUPPORT_CLICK_TO_SORT'); ?>">
											<?php echo Lang::txt('COM_SUPPORT_COL_AGE'); ?>
										</a>
									</li>
									<li>
										<a class="sort-status<?php if ($this->filters['sort'] == 'status') { echo ' active ' . strtolower($this->filters['sortdir']); } ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=display&show=' . $this->filters['show'] . '&search=' . $this->filters['search'] . '&sort=status&sortdir=' . $direction . '&limit=' . $this->filters['limit'] . '&limitstart=0'); ?>" title="<?php echo Lang::txt('COM_SUPPORT_CLICK_TO_SORT'); ?>">
											<?php echo Lang::txt('COM_SUPPORT_COL_STATUS'); ?>
										</a>
									</li>
									<li>
										<a class="sort-severity<?php if ($this->filters['sort'] == 'severity') { echo ' active ' . strtolower($this->filters['sortdir']); } ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=display&show=' . $this->filters['show'] . '&search=' . $this->filters['search'] . '&sort=severity&sortdir=' . $direction . '&limit=' . $this->filters['limit'] . '&limitstart=0'); ?>" title="<?php echo Lang::txt('COM_SUPPORT_CLICK_TO_SORT'); ?>">
											<?php echo Lang::txt('COM_SUPPORT_COL_SEVERITY'); ?>
										</a>
									</li>
									<li>
										<a class="sort-summary<?php if ($this->filters['sort'] == 'summary') { echo ' active ' . strtolower($this->filters['sortdir']); } ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=display&show=' . $this->filters['show'] . '&search=' . $this->filters['search'] . '&sort=summary&sortdir=' . $direction . '&limit=' . $this->filters['limit'] . '&limitstart=0'); ?>" title="<?php echo Lang::txt('COM_SUPPORT_CLICK_TO_SORT'); ?>">
											<?php echo Lang::txt('COM_SUPPORT_COL_SUMMARY'); ?>
										</a>
									</li>
									<li>
										<a class="sort-group<?php if ($this->filters['sort'] == 'group') { echo ' active ' . strtolower($this->filters['sortdir']); } ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=display&show=' . $this->filters['show'] . '&search=' . $this->filters['search'] . '&sort=group&sortdir=' . $direction . '&limit=' . $this->filters['limit'] . '&limitstart=0'); ?>" title="<?php echo Lang::txt('COM_SUPPORT_CLICK_TO_SORT'); ?>">
											<?php echo Lang::txt('COM_SUPPORT_COL_GROUP'); ?>
										</a>
									</li>
									<li>
										<a class="sort-owner<?php if ($this->filters['sort'] == 'owner') { echo ' active ' . strtolower($this->filters['sortdir']); } ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=display&show=' . $this->filters['show'] . '&search=' . $this->filters['search'] . '&sort=owner&sortdir=' . $direction . '&limit=' . $this->filters['limit'] . '&limitstart=0'); ?>" title="<?php echo Lang::txt('COM_SUPPORT_CLICK_TO_SORT'); ?>">
											<?php echo Lang::txt('COM_SUPPORT_COL_OWNER'); ?>
										</a>
									</li>
								</ul>
							</li>
						</ul>
						<fieldset id="filter-bar">
							<label for="filter_search"><?php echo Lang::txt('COM_SUPPORT_FIND'); ?>:</label>
							<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_SUPPORT_SEARCH_THIS_QUERY'); ?>" />

							<input type="hidden" name="sort" value="<?php echo $this->escape($this->filters['sort']); ?>" />
							<input type="hidden" name="sortdir" value="<?php echo $this->escape($this->filters['sortdir']); ?>" />
							<input type="hidden" name="show" value="<?php echo $this->escape($this->filters['show']); ?>" />

							<input type="submit" class="submit" value="<?php echo Lang::txt('COM_SUPPORT_GO'); ?>" />
						</fieldset>
					</div>
					<table id="tktlist">
						<tfoot>
							<tr>
								<td colspan="8">
									<?php
									$pageNav = $this->pagination(
										$this->total,
										$this->filters['start'],
										$this->filters['limit']
									);
									$pageNav->setAdditionalUrlParam('show', $this->filters['show']);
									$pageNav->setAdditionalUrlParam('search', $this->filters['search']);
									echo $pageNav->render();
									?>
								</td>
							</tr>
						</tfoot>
						<tbody>
					<?php
					$k = 0;
					if (count($this->rows) > 0)
					{
						$cls = 'even';

						$i = 0;
						$statuses = array();
						foreach ($this->rows as $row)
						{
							// Was there any activity on this item?
							$lastcomment = $row->comments()
								->order('created', 'desc')
								->row()
								->get('created');

							$tags = $row->tags('linkedlist');

							if (!in_array($row->status->get('id'), $statuses))
							{
								$statuses[] = $row->status->get('id');
								$this->css('#tktlist tbody tr td.status-' . $row->status->get('id') . ' { border-left-color: #' . $row->status->get('color') . '; }');
							}
							?>
							<tr class="<?php echo $cls == 'odd' ? 'even' : 'odd'; ?>">
								<td class="status-<?php echo $row->status->get('id'); ?>">
									<span class="hasTip" title="<?php echo Lang::txt('COM_SUPPORT_DETAILS'); ?> :: <?php echo Lang::txt('COM_SUPPORT_COL_STATUS') . ': ' . $row->status->get('text'); ?>">
										<span class="ticket-id">
											<?php echo $row->get('id'); ?>
										</span>
										<span class="<?php echo ($row->isOpen() ? 'open' : 'closed') . ' ' . $row->status->get('class'); ?> status">
											<?php
											echo $row->status->get('text');
											echo (!$row->isOpen()) ? ' (' . $this->escape($row->get('resolved')) . ')' : '';
											?>
										</span>
										<?php if ($row->get('target_date') && $row->get('target_date') != '0000-00-00 00:00:00') { ?>
											<span class="ticket-target_date tooltips" title="<?php echo Lang::txt('COM_SUPPORT_TARGET_DATE', Date::of($row->get('target_date'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'))); ?>">
												<time datetime="<?php echo Date::of($row->get('target_date'))->format('Y-m-d\TH:i:s\Z'); ?>"><?php echo Date::of($row->get('target_date'))->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></time>
											</span>
										<?php } ?>
									</span>
								</td>
								<td colspan="6">
									<p>
										<span class="ticket-author">
											<?php
											echo $this->escape($row->get('name'));
											echo ($row->submitter->get('id')) ? ' (<a href="' . Route::url('index.php?option=com_members&id=' . $row->submitter->get('id')) . '">' . $this->escape($row->get('login')) . '</a>)' : ($row->get('login') ? ' (' . $this->escape($row->get('login')) . ')' : '');
											?>
										</span>
										<span class="ticket-datetime">
											@ <time datetime="<?php echo Date::of($row->get('created'))->format('Y-m-d\TH:i:s\Z'); ?>"><?php echo Date::of($row->get('created'))->toLocal(); ?></time>
										</span>
										<?php if ($lastcomment && $lastcomment != '0000-00-00 00:00:00') { ?>
											<span class="ticket-activity">
												<time datetime="<?php echo Date::of($lastcomment)->format('Y-m-d\TH:i:s\Z'); ?>"><?php echo Date::of($lastcomment)->relative(); ?></time>
											</span>
										<?php } ?>
									</p>
									<p>
										<a class="ticket-content" title="<?php echo $this->escape(str_replace(array('<br />', '&amp;'), array('', '&'), $row->content)); ?>" href="<?php echo Route::url($row->link() . '&show=' . $this->filters['show'] . '&search=' . $this->filters['search'] . '&limit=' . $this->filters['limit'] . '&limitstart=' . $this->filters['start']); ?>">
											<?php echo $row->content ? \Hubzero\Utility\Str::truncate(strip_tags($row->content), 200) : Lang::txt('COM_SUPPORT_NO_CONTENT_FOUND'); ?>
										</a>
									</p>
									<?php if ($tags || $row->isOwned() || $row->get('group_id')) { ?>
										<p class="ticket-details">
										<?php if ($this->acl->check('update', 'tickets') && $tags) { ?>
											<span class="ticket-tags">
												<?php echo $tags; ?>
											</span>
										<?php } ?>
										<?php if ($row->get('group_id')) { ?>
											<span class="ticket-group">
												<?php
												$gname = Lang::txt('COM_SUPPORT_UNKNOWN');
												if ($group = \Hubzero\User\Group::getInstance($row->get('group_id')))
												{
													$gname = $group->get('cn');
												}
												echo $this->escape($gname);
												?>
											</span>
										<?php } ?>
										<?php if ($row->isOwned()) { ?>
											<span class="ticket-owner hasTip" title="<?php echo Lang::txt('COM_SUPPORT_ASSIGNED_TO'); ?>::<img border=&quot;1&quot; src=&quot;<?php echo $row->assignee->picture(); ?>&quot; name=&quot;imagelib&quot; alt=&quot;User photo&quot; width=&quot;40&quot; height=&quot;40&quot; style=&quot;float: left; margin-right: 0.5em;&quot; /><?php echo $this->escape(stripslashes($row->assignee->get('username'))); ?><br /><?php echo $this->escape(stripslashes($row->assignee->get('organization', Lang::txt('COM_SUPPORT_UNKNOWN')))); ?>">
												<?php echo $this->escape(stripslashes($row->assignee->get('name'))); ?>
											</span>
										<?php } ?>
										</p>
									<?php } ?>
								</td>
								<td class="tkt-severity">
									<span class="ticket-severity <?php echo $this->escape($row->get('severity', 'normal')); ?> hasTip" title="<?php echo Lang::txt('COM_SUPPORT_PRIORITY'); ?>:&nbsp;<?php echo $this->escape($row->get('severity', 'normal')); ?>">
										<span><?php echo $this->escape($row->get('severity', 'normal')); ?></span>
									</span>
									<?php if ($this->acl->check('delete', 'tickets')) { ?>
										<a class="delete" href="<?php echo Route::url($row->link('delete')); ?>" data-confirm="<?php echo Lang::txt('COM_SUPPORT_QUERIES_CONFIRM_DELETE'); ?>" title="<?php echo Lang::txt('JACTION_DELETE'); ?>">
											<?php echo Lang::txt('JACTION_DELETE'); ?>
										</a>
									<?php } ?>
								</td>
							</tr>
							<?php
							$k = 1 - $k;
						}
					} else {
					?>
							<tr class="odd noresults">
								<td colspan="7">
									<?php echo Lang::txt('COM_SUPPORT_NO_RESULTS_FOUND'); ?>
								</td>
							</tr>
					<?php
					}
					?>
						</tbody>
					</table>

					<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="task" value="display" />
				</form>
			</div><!-- / .pane-inner -->
		</div><!-- / .pane -->
	</div><!-- / .panel-row -->
</section><!-- / .panel -->
