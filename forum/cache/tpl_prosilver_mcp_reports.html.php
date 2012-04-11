<?php if (!defined('IN_PHPBB')) exit; $this->_tpl_include('mcp_header.html'); ?>


<form id="mcp" method="post" action="<?php echo (isset($this->_rootref['S_MCP_ACTION'])) ? $this->_rootref['S_MCP_ACTION'] : ''; ?>">

<?php if (! $this->_rootref['S_PM']) {  ?>

<fieldset class="forum-selection">
	<label for="fo"><?php echo ((isset($this->_rootref['L_FORUM'])) ? $this->_rootref['L_FORUM'] : ((isset($user->lang['FORUM'])) ? $user->lang['FORUM'] : '{ FORUM }')); ?>: <select name="f" id="fo"><?php echo (isset($this->_rootref['S_FORUM_OPTIONS'])) ? $this->_rootref['S_FORUM_OPTIONS'] : ''; ?></select></label>
	<input type="submit" name="sort" value="<?php echo ((isset($this->_rootref['L_GO'])) ? $this->_rootref['L_GO'] : ((isset($user->lang['GO'])) ? $user->lang['GO'] : '{ GO }')); ?>" class="button2" />
	<?php echo (isset($this->_rootref['S_FORM_TOKEN'])) ? $this->_rootref['S_FORM_TOKEN'] : ''; ?>

</fieldset>
<?php } ?>


<h2><?php echo ((isset($this->_rootref['L_TITLE'])) ? $this->_rootref['L_TITLE'] : ((isset($user->lang['TITLE'])) ? $user->lang['TITLE'] : '{ TITLE }')); ?></h2>

<div class="panel">
	<div class="inner"><span class="corners-top"><span></span></span>

	<p><?php echo ((isset($this->_rootref['L_EXPLAIN'])) ? $this->_rootref['L_EXPLAIN'] : ((isset($user->lang['EXPLAIN'])) ? $user->lang['EXPLAIN'] : '{ EXPLAIN }')); ?></p>

	<?php if (sizeof($this->_tpldata['postrow'])) {  ?>

		<ul class="linklist">
			<li class="rightside pagination">
				<?php if ($this->_rootref['TOTAL']) {  echo (isset($this->_rootref['TOTAL_REPORTS'])) ? $this->_rootref['TOTAL_REPORTS'] : ''; } if ($this->_rootref['PAGE_NUMBER']) {  if ($this->_rootref['PAGINATION']) {  ?> &bull; <a href="#" onclick="jumpto(); return false;" title="<?php echo ((isset($this->_rootref['L_JUMP_TO_PAGE'])) ? $this->_rootref['L_JUMP_TO_PAGE'] : ((isset($user->lang['JUMP_TO_PAGE'])) ? $user->lang['JUMP_TO_PAGE'] : '{ JUMP_TO_PAGE }')); ?>"><?php echo (isset($this->_rootref['PAGE_NUMBER'])) ? $this->_rootref['PAGE_NUMBER'] : ''; ?></a> &bull; <span><?php echo (isset($this->_rootref['PAGINATION'])) ? $this->_rootref['PAGINATION'] : ''; ?></span><?php } else { ?> &bull; <?php echo (isset($this->_rootref['PAGE_NUMBER'])) ? $this->_rootref['PAGE_NUMBER'] : ''; } } ?>

			</li>
		</ul>
		<ul class="topiclist">
			<li class="header">
				<dl>
					<dt><?php echo ((isset($this->_rootref['L_VIEW_DETAILS'])) ? $this->_rootref['L_VIEW_DETAILS'] : ((isset($user->lang['VIEW_DETAILS'])) ? $user->lang['VIEW_DETAILS'] : '{ VIEW_DETAILS }')); ?></dt>
					<dd class="moderation"><span><?php echo ((isset($this->_rootref['L_REPORTER'])) ? $this->_rootref['L_REPORTER'] : ((isset($user->lang['REPORTER'])) ? $user->lang['REPORTER'] : '{ REPORTER }')); if (! $this->_rootref['S_PM']) {  ?> &amp; <?php echo ((isset($this->_rootref['L_FORUM'])) ? $this->_rootref['L_FORUM'] : ((isset($user->lang['FORUM'])) ? $user->lang['FORUM'] : '{ FORUM }')); } ?></span></dd>
					<dd class="mark"><?php echo ((isset($this->_rootref['L_MARK'])) ? $this->_rootref['L_MARK'] : ((isset($user->lang['MARK'])) ? $user->lang['MARK'] : '{ MARK }')); ?></dd>
				</dl>
			</li>
		</ul>
		<ul class="topiclist cplist">

		<?php $_postrow_count = (isset($this->_tpldata['postrow'])) ? sizeof($this->_tpldata['postrow']) : 0;if ($_postrow_count) {for ($_postrow_i = 0; $_postrow_i < $_postrow_count; ++$_postrow_i){$_postrow_val = &$this->_tpldata['postrow'][$_postrow_i]; ?>

			<li class="row<?php if (($_postrow_val['S_ROW_COUNT'] & 1)  ) {  ?> bg1<?php } else { ?> bg2<?php } ?>">
				<dl>
					<?php if ($this->_rootref['S_PM']) {  ?>

					<dt>
						<a href="<?php echo $_postrow_val['U_VIEW_DETAILS']; ?>" class="topictitle"><?php echo $_postrow_val['PM_SUBJECT']; ?></a> <?php echo $_postrow_val['ATTACH_ICON_IMG']; ?><br />
						<span><?php echo ((isset($this->_rootref['L_MESSAGE_BY_AUTHOR'])) ? $this->_rootref['L_MESSAGE_BY_AUTHOR'] : ((isset($user->lang['MESSAGE_BY_AUTHOR'])) ? $user->lang['MESSAGE_BY_AUTHOR'] : '{ MESSAGE_BY_AUTHOR }')); ?> <?php echo $_postrow_val['PM_AUTHOR_FULL']; ?> &raquo; <?php echo $_postrow_val['PM_TIME']; ?></span><br />
						<span><?php echo ((isset($this->_rootref['L_MESSAGE_TO'])) ? $this->_rootref['L_MESSAGE_TO'] : ((isset($user->lang['MESSAGE_TO'])) ? $user->lang['MESSAGE_TO'] : '{ MESSAGE_TO }')); ?> <?php echo $_postrow_val['RECIPIENTS']; ?></span>
					</dt>
					<dd class="moderation">
						<span><?php echo $_postrow_val['REPORTER_FULL']; ?> &laquo; <?php echo $_postrow_val['REPORT_TIME']; ?></span>
					</dd>
					<?php } else { ?>

					<dt>
						<a href="<?php echo $_postrow_val['U_VIEW_DETAILS']; ?>" class="topictitle"><?php echo $_postrow_val['POST_SUBJECT']; ?></a> <?php echo $_postrow_val['ATTACH_ICON_IMG']; ?><br />
						<span><?php echo ((isset($this->_rootref['L_POSTED'])) ? $this->_rootref['L_POSTED'] : ((isset($user->lang['POSTED'])) ? $user->lang['POSTED'] : '{ POSTED }')); ?> <?php echo ((isset($this->_rootref['L_POST_BY_AUTHOR'])) ? $this->_rootref['L_POST_BY_AUTHOR'] : ((isset($user->lang['POST_BY_AUTHOR'])) ? $user->lang['POST_BY_AUTHOR'] : '{ POST_BY_AUTHOR }')); ?> <?php echo $_postrow_val['POST_AUTHOR_FULL']; ?> &raquo; <?php echo $_postrow_val['POST_TIME']; ?></span>
					</dt>
					<dd class="moderation">
						<span><?php echo $_postrow_val['REPORTER_FULL']; ?> &laquo; <?php echo $_postrow_val['REPORT_TIME']; ?><br />
						<?php if ($_postrow_val['U_VIEWFORUM']) {  echo ((isset($this->_rootref['L_FORUM'])) ? $this->_rootref['L_FORUM'] : ((isset($user->lang['FORUM'])) ? $user->lang['FORUM'] : '{ FORUM }')); ?>: <a href="<?php echo $_postrow_val['U_VIEWFORUM']; ?>"><?php echo $_postrow_val['FORUM_NAME']; ?></a><?php } else { echo $_postrow_val['FORUM_NAME']; } ?></span>
					</dd>
					<?php } ?>

					<dd class="mark"><input type="checkbox" name="report_id_list[]" value="<?php echo $_postrow_val['REPORT_ID']; ?>" /></dd>
				</dl>
			</li>
		<?php }} ?>

		</ul>

		<fieldset class="display-options">
			<?php if ($this->_rootref['NEXT_PAGE']) {  ?><a href="<?php echo (isset($this->_rootref['NEXT_PAGE'])) ? $this->_rootref['NEXT_PAGE'] : ''; ?>" class="right-box <?php echo (isset($this->_rootref['S_CONTENT_FLOW_END'])) ? $this->_rootref['S_CONTENT_FLOW_END'] : ''; ?>"><?php echo ((isset($this->_rootref['L_NEXT'])) ? $this->_rootref['L_NEXT'] : ((isset($user->lang['NEXT'])) ? $user->lang['NEXT'] : '{ NEXT }')); ?></a><?php } if ($this->_rootref['PREVIOUS_PAGE']) {  ?><a href="<?php echo (isset($this->_rootref['PREVIOUS_PAGE'])) ? $this->_rootref['PREVIOUS_PAGE'] : ''; ?>" class="left-box <?php echo (isset($this->_rootref['S_CONTENT_FLOW_BEGIN'])) ? $this->_rootref['S_CONTENT_FLOW_BEGIN'] : ''; ?>"><?php echo ((isset($this->_rootref['L_PREVIOUS'])) ? $this->_rootref['L_PREVIOUS'] : ((isset($user->lang['PREVIOUS'])) ? $user->lang['PREVIOUS'] : '{ PREVIOUS }')); ?></a><?php } ?>

			<label><?php echo ((isset($this->_rootref['L_DISPLAY_POSTS'])) ? $this->_rootref['L_DISPLAY_POSTS'] : ((isset($user->lang['DISPLAY_POSTS'])) ? $user->lang['DISPLAY_POSTS'] : '{ DISPLAY_POSTS }')); ?>: <?php echo (isset($this->_rootref['S_SELECT_SORT_DAYS'])) ? $this->_rootref['S_SELECT_SORT_DAYS'] : ''; ?></label>
			<label><?php echo ((isset($this->_rootref['L_SORT_BY'])) ? $this->_rootref['L_SORT_BY'] : ((isset($user->lang['SORT_BY'])) ? $user->lang['SORT_BY'] : '{ SORT_BY }')); ?> <?php echo (isset($this->_rootref['S_SELECT_SORT_KEY'])) ? $this->_rootref['S_SELECT_SORT_KEY'] : ''; ?></label><label><?php echo (isset($this->_rootref['S_SELECT_SORT_DIR'])) ? $this->_rootref['S_SELECT_SORT_DIR'] : ''; ?></label>
			<?php if ($this->_rootref['TOPIC_ID']) {  ?><label><input type="checkbox" class="radio" name="t" value="<?php echo (isset($this->_rootref['TOPIC_ID'])) ? $this->_rootref['TOPIC_ID'] : ''; ?>" checked="checked" />&nbsp; <strong><?php echo ((isset($this->_rootref['L_ONLY_TOPIC'])) ? $this->_rootref['L_ONLY_TOPIC'] : ((isset($user->lang['ONLY_TOPIC'])) ? $user->lang['ONLY_TOPIC'] : '{ ONLY_TOPIC }')); ?></strong></label><?php } ?>

			<input type="submit" name="sort" value="<?php echo ((isset($this->_rootref['L_GO'])) ? $this->_rootref['L_GO'] : ((isset($user->lang['GO'])) ? $user->lang['GO'] : '{ GO }')); ?>" class="button2" />
		</fieldset>
		<hr />
		<ul class="linklist">
			<li class="rightside pagination">
				<?php if ($this->_rootref['TOTAL']) {  echo (isset($this->_rootref['TOTAL_REPORTS'])) ? $this->_rootref['TOTAL_REPORTS'] : ''; } if ($this->_rootref['PAGE_NUMBER']) {  if ($this->_rootref['PAGINATION']) {  ?> &bull; <a href="#" onclick="jumpto(); return false;" title="<?php echo ((isset($this->_rootref['L_JUMP_TO_PAGE'])) ? $this->_rootref['L_JUMP_TO_PAGE'] : ((isset($user->lang['JUMP_TO_PAGE'])) ? $user->lang['JUMP_TO_PAGE'] : '{ JUMP_TO_PAGE }')); ?>"><?php echo (isset($this->_rootref['PAGE_NUMBER'])) ? $this->_rootref['PAGE_NUMBER'] : ''; ?></a> &bull; <span><?php echo (isset($this->_rootref['PAGINATION'])) ? $this->_rootref['PAGINATION'] : ''; ?></span><?php } else { ?> &bull; <?php echo (isset($this->_rootref['PAGE_NUMBER'])) ? $this->_rootref['PAGE_NUMBER'] : ''; } } ?>

			</li>
		</ul>

	<?php } else { ?>

		<p><strong><?php echo ((isset($this->_rootref['L_NO_REPORTS'])) ? $this->_rootref['L_NO_REPORTS'] : ((isset($user->lang['NO_REPORTS'])) ? $user->lang['NO_REPORTS'] : '{ NO_REPORTS }')); ?></strong></p>
	<?php } ?>


	<span class="corners-bottom"><span></span></span></div>
</div>

<?php if (sizeof($this->_tpldata['postrow'])) {  ?>

	<fieldset class="display-actions">
		<input class="button2" type="submit" value="<?php echo ((isset($this->_rootref['L_DELETE_REPORTS'])) ? $this->_rootref['L_DELETE_REPORTS'] : ((isset($user->lang['DELETE_REPORTS'])) ? $user->lang['DELETE_REPORTS'] : '{ DELETE_REPORTS }')); ?>" name="action[delete]" />
		<?php if (! $this->_rootref['S_CLOSED']) {  ?>&nbsp;<input class="button1" type="submit" name="action[close]" value="<?php echo ((isset($this->_rootref['L_CLOSE_REPORTS'])) ? $this->_rootref['L_CLOSE_REPORTS'] : ((isset($user->lang['CLOSE_REPORTS'])) ? $user->lang['CLOSE_REPORTS'] : '{ CLOSE_REPORTS }')); ?>" /><?php } ?>

		<div><a href="#" onclick="marklist('mcp', 'report_id_list', true); return false;"><?php echo ((isset($this->_rootref['L_MARK_ALL'])) ? $this->_rootref['L_MARK_ALL'] : ((isset($user->lang['MARK_ALL'])) ? $user->lang['MARK_ALL'] : '{ MARK_ALL }')); ?></a> :: <a href="#" onclick="marklist('mcp', 'report_id_list', false); return false;"><?php echo ((isset($this->_rootref['L_UNMARK_ALL'])) ? $this->_rootref['L_UNMARK_ALL'] : ((isset($user->lang['UNMARK_ALL'])) ? $user->lang['UNMARK_ALL'] : '{ UNMARK_ALL }')); ?></a></div>
	</fieldset>
<?php } ?>

</form>

<?php $this->_tpl_include('mcp_footer.html'); ?>