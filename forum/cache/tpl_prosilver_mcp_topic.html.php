<?php if (!defined('IN_PHPBB')) exit; $this->_tpl_include('mcp_header.html'); ?>


<h2><a href="<?php echo (isset($this->_rootref['U_VIEW_TOPIC'])) ? $this->_rootref['U_VIEW_TOPIC'] : ''; ?>"><?php echo ((isset($this->_rootref['L_TOPIC'])) ? $this->_rootref['L_TOPIC'] : ((isset($user->lang['TOPIC'])) ? $user->lang['TOPIC'] : '{ TOPIC }')); ?>: <?php echo (isset($this->_rootref['TOPIC_TITLE'])) ? $this->_rootref['TOPIC_TITLE'] : ''; ?></a></h2>

<script type="text/javascript">
// <![CDATA[
var panels = new Array('display-panel', 'split-panel', 'merge-panel');

<?php if ($this->_rootref['S_MERGE_VIEW']) {  ?>

	var show_panel = 'merge-panel';
<?php } else if ($this->_rootref['S_SPLIT_VIEW']) {  ?>

	var show_panel = 'split-panel';
<?php } else { ?>

	var show_panel = 'display-panel';
<?php } ?>


onload_functions.push('subPanels()');

// ]]>
</script>

<div id="minitabs">
	<ul>
		<li id="display-panel-tab"<?php if (! $this->_rootref['S_MERGE_VIEW']) {  ?> class="activetab"<?php } ?>>
			<span class="corners-top"><span></span></span>
			<a href="#minitabs" onclick="subPanels('display-panel'); return false;"><span><?php echo ((isset($this->_rootref['L_DISPLAY_OPTIONS'])) ? $this->_rootref['L_DISPLAY_OPTIONS'] : ((isset($user->lang['DISPLAY_OPTIONS'])) ? $user->lang['DISPLAY_OPTIONS'] : '{ DISPLAY_OPTIONS }')); ?></span></a>
		</li>
		<li id="split-panel-tab">
			<span class="corners-top"><span></span></span>
			<a href="#minitabs" onclick="subPanels('split-panel'); return false;"><span><?php echo ((isset($this->_rootref['L_SPLIT_TOPIC'])) ? $this->_rootref['L_SPLIT_TOPIC'] : ((isset($user->lang['SPLIT_TOPIC'])) ? $user->lang['SPLIT_TOPIC'] : '{ SPLIT_TOPIC }')); ?></span></a>
		</li>
		<li id="merge-panel-tab"<?php if ($this->_rootref['S_MERGE_VIEW']) {  ?> class="activetab"<?php } ?>>
			<span class="corners-top"><span></span></span>
			<a href="#minitabs" onclick="subPanels('merge-panel'); return false;"><span><?php echo ((isset($this->_rootref['L_MERGE_POSTS'])) ? $this->_rootref['L_MERGE_POSTS'] : ((isset($user->lang['MERGE_POSTS'])) ? $user->lang['MERGE_POSTS'] : '{ MERGE_POSTS }')); ?></span></a>
		</li>
	</ul>
</div>

<form id="mcp" method="post" action="<?php echo (isset($this->_rootref['S_MCP_ACTION'])) ? $this->_rootref['S_MCP_ACTION'] : ''; ?>">

<div class="panel">
	<div class="inner"><span class="corners-top"><span></span></span>

	<fieldset id="display-panel" class="fields2">
	<dl>
		<dt><label for="posts_per_page"><?php echo ((isset($this->_rootref['L_POSTS_PER_PAGE'])) ? $this->_rootref['L_POSTS_PER_PAGE'] : ((isset($user->lang['POSTS_PER_PAGE'])) ? $user->lang['POSTS_PER_PAGE'] : '{ POSTS_PER_PAGE }')); ?>:</label><br /><span><?php echo ((isset($this->_rootref['L_POSTS_PER_PAGE_EXPLAIN'])) ? $this->_rootref['L_POSTS_PER_PAGE_EXPLAIN'] : ((isset($user->lang['POSTS_PER_PAGE_EXPLAIN'])) ? $user->lang['POSTS_PER_PAGE_EXPLAIN'] : '{ POSTS_PER_PAGE_EXPLAIN }')); ?></span></dt>
		<dd><input class="inputbox autowidth" type="text" name="posts_per_page" id="posts_per_page" size="6" value="<?php echo (isset($this->_rootref['POSTS_PER_PAGE'])) ? $this->_rootref['POSTS_PER_PAGE'] : ''; ?>" /></dd>
	</dl>
	<dl>
		<dt><label><?php echo ((isset($this->_rootref['L_DISPLAY_POSTS'])) ? $this->_rootref['L_DISPLAY_POSTS'] : ((isset($user->lang['DISPLAY_POSTS'])) ? $user->lang['DISPLAY_POSTS'] : '{ DISPLAY_POSTS }')); ?>:</label></dt>
		<dd><?php echo (isset($this->_rootref['S_SELECT_SORT_DAYS'])) ? $this->_rootref['S_SELECT_SORT_DAYS'] : ''; ?>&nbsp;&nbsp;<label><?php echo ((isset($this->_rootref['L_SORT_BY'])) ? $this->_rootref['L_SORT_BY'] : ((isset($user->lang['SORT_BY'])) ? $user->lang['SORT_BY'] : '{ SORT_BY }')); ?> <?php echo (isset($this->_rootref['S_SELECT_SORT_KEY'])) ? $this->_rootref['S_SELECT_SORT_KEY'] : ''; ?></label><label><?php echo (isset($this->_rootref['S_SELECT_SORT_DIR'])) ? $this->_rootref['S_SELECT_SORT_DIR'] : ''; ?></label> <input type="submit" name="sort" value="<?php echo ((isset($this->_rootref['L_GO'])) ? $this->_rootref['L_GO'] : ((isset($user->lang['GO'])) ? $user->lang['GO'] : '{ GO }')); ?>" class="button2" /></dd>
	</dl>
	</fieldset>

<?php if ($this->_rootref['S_CAN_SPLIT']) {  ?>

	<fieldset id="split-panel" class="fields2">
		<p><?php echo ((isset($this->_rootref['L_SPLIT_TOPIC_EXPLAIN'])) ? $this->_rootref['L_SPLIT_TOPIC_EXPLAIN'] : ((isset($user->lang['SPLIT_TOPIC_EXPLAIN'])) ? $user->lang['SPLIT_TOPIC_EXPLAIN'] : '{ SPLIT_TOPIC_EXPLAIN }')); ?></p>

	<?php if ($this->_rootref['S_SHOW_TOPIC_ICONS']) {  ?>

		<dl>
			<dt><label for="icon"><?php echo ((isset($this->_rootref['L_TOPIC_ICON'])) ? $this->_rootref['L_TOPIC_ICON'] : ((isset($user->lang['TOPIC_ICON'])) ? $user->lang['TOPIC_ICON'] : '{ TOPIC_ICON }')); ?>:</label></dt>
			<dd><label for="icon"><input type="radio" name="icon" id="icon" value="0" checked="checked" /> <?php echo ((isset($this->_rootref['L_NO_TOPIC_ICON'])) ? $this->_rootref['L_NO_TOPIC_ICON'] : ((isset($user->lang['NO_TOPIC_ICON'])) ? $user->lang['NO_TOPIC_ICON'] : '{ NO_TOPIC_ICON }')); ?></label>
			<?php $_topic_icon_count = (isset($this->_tpldata['topic_icon'])) ? sizeof($this->_tpldata['topic_icon']) : 0;if ($_topic_icon_count) {for ($_topic_icon_i = 0; $_topic_icon_i < $_topic_icon_count; ++$_topic_icon_i){$_topic_icon_val = &$this->_tpldata['topic_icon'][$_topic_icon_i]; ?><label for="icon-<?php echo $_topic_icon_val['ICON_ID']; ?>"><input type="radio" name="icon" id="icon-<?php echo $_topic_icon_val['ICON_ID']; ?>" value="<?php echo $_topic_icon_val['ICON_ID']; ?>" <?php echo $_topic_icon_val['S_ICON_CHECKED']; ?> /><img src="<?php echo $_topic_icon_val['ICON_IMG']; ?>" width="<?php echo $_topic_icon_val['ICON_WIDTH']; ?>" height="<?php echo $_topic_icon_val['ICON_HEIGHT']; ?>" alt="" title="" /></label> <?php }} ?></dd>
		</dl>
	<?php } ?>


	<dl>
		<dt><label for="subject"><?php echo ((isset($this->_rootref['L_SPLIT_SUBJECT'])) ? $this->_rootref['L_SPLIT_SUBJECT'] : ((isset($user->lang['SPLIT_SUBJECT'])) ? $user->lang['SPLIT_SUBJECT'] : '{ SPLIT_SUBJECT }')); ?>:</label></dt>
		<dd><input type="text" name="subject" id="subject" size="45" maxlength="64" tabindex="2" value="<?php echo (isset($this->_rootref['SPLIT_SUBJECT'])) ? $this->_rootref['SPLIT_SUBJECT'] : ''; ?>" title="<?php echo ((isset($this->_rootref['L_SPLIT_SUBJECT'])) ? $this->_rootref['L_SPLIT_SUBJECT'] : ((isset($user->lang['SPLIT_SUBJECT'])) ? $user->lang['SPLIT_SUBJECT'] : '{ SPLIT_SUBJECT }')); ?>" class="inputbox" /></dd>
	</dl>
	<dl>
		<dt><label><?php echo ((isset($this->_rootref['L_SPLIT_FORUM'])) ? $this->_rootref['L_SPLIT_FORUM'] : ((isset($user->lang['SPLIT_FORUM'])) ? $user->lang['SPLIT_FORUM'] : '{ SPLIT_FORUM }')); ?>:</label></dt>
		<dd><select name="to_forum_id"><?php echo (isset($this->_rootref['S_FORUM_SELECT'])) ? $this->_rootref['S_FORUM_SELECT'] : ''; ?></select></dd>
	</dl>
	</fieldset>
<?php } if ($this->_rootref['S_CAN_MERGE']) {  ?>

	<fieldset id="merge-panel" class="fields2">
		<p><?php echo ((isset($this->_rootref['L_MERGE_TOPIC_EXPLAIN'])) ? $this->_rootref['L_MERGE_TOPIC_EXPLAIN'] : ((isset($user->lang['MERGE_TOPIC_EXPLAIN'])) ? $user->lang['MERGE_TOPIC_EXPLAIN'] : '{ MERGE_TOPIC_EXPLAIN }')); ?></p>
	<dl>
		<dt><label for="to_topic_id"><?php echo ((isset($this->_rootref['L_MERGE_TOPIC_ID'])) ? $this->_rootref['L_MERGE_TOPIC_ID'] : ((isset($user->lang['MERGE_TOPIC_ID'])) ? $user->lang['MERGE_TOPIC_ID'] : '{ MERGE_TOPIC_ID }')); ?>:</label></dt>
		<dd>
			<input class="inputbox autowidth" type="text" size="6" name="to_topic_id" id="to_topic_id" value="<?php echo (isset($this->_rootref['TO_TOPIC_ID'])) ? $this->_rootref['TO_TOPIC_ID'] : ''; ?>" />
			<a href="<?php echo (isset($this->_rootref['U_SELECT_TOPIC'])) ? $this->_rootref['U_SELECT_TOPIC'] : ''; ?>" ><?php echo ((isset($this->_rootref['L_SELECT_TOPIC'])) ? $this->_rootref['L_SELECT_TOPIC'] : ((isset($user->lang['SELECT_TOPIC'])) ? $user->lang['SELECT_TOPIC'] : '{ SELECT_TOPIC }')); ?></a>
		</dd>
		<?php if ($this->_rootref['TO_TOPIC_INFO']) {  ?><dd><?php echo (isset($this->_rootref['TO_TOPIC_INFO'])) ? $this->_rootref['TO_TOPIC_INFO'] : ''; ?></dd><?php } ?>

	</dl>
	</fieldset>
<?php } ?>


	<span class="corners-bottom"><span></span></span></div>
</div>

<div class="panel">
	<div class="inner"><span class="corners-top"><span></span></span>

	<h3 id="review">
		<span class="right-box"><a href="#review" onclick="viewableArea(getElementById('topicreview'), true); var rev_text = getElementById('review').getElementsByTagName('a').item(0).firstChild; if (rev_text.data == '<?php echo ((isset($this->_rootref['LA_EXPAND_VIEW'])) ? $this->_rootref['LA_EXPAND_VIEW'] : ((isset($this->_rootref['L_EXPAND_VIEW'])) ? addslashes($this->_rootref['L_EXPAND_VIEW']) : ((isset($user->lang['EXPAND_VIEW'])) ? addslashes($user->lang['EXPAND_VIEW']) : '{ EXPAND_VIEW }'))); ?>'){rev_text.data = '<?php echo ((isset($this->_rootref['LA_COLLAPSE_VIEW'])) ? $this->_rootref['LA_COLLAPSE_VIEW'] : ((isset($this->_rootref['L_COLLAPSE_VIEW'])) ? addslashes($this->_rootref['L_COLLAPSE_VIEW']) : ((isset($user->lang['COLLAPSE_VIEW'])) ? addslashes($user->lang['COLLAPSE_VIEW']) : '{ COLLAPSE_VIEW }'))); ?>'; } else if (rev_text.data == '<?php echo ((isset($this->_rootref['LA_COLLAPSE_VIEW'])) ? $this->_rootref['LA_COLLAPSE_VIEW'] : ((isset($this->_rootref['L_COLLAPSE_VIEW'])) ? addslashes($this->_rootref['L_COLLAPSE_VIEW']) : ((isset($user->lang['COLLAPSE_VIEW'])) ? addslashes($user->lang['COLLAPSE_VIEW']) : '{ COLLAPSE_VIEW }'))); ?>'){rev_text.data = '<?php echo ((isset($this->_rootref['LA_EXPAND_VIEW'])) ? $this->_rootref['LA_EXPAND_VIEW'] : ((isset($this->_rootref['L_EXPAND_VIEW'])) ? addslashes($this->_rootref['L_EXPAND_VIEW']) : ((isset($user->lang['EXPAND_VIEW'])) ? addslashes($user->lang['EXPAND_VIEW']) : '{ EXPAND_VIEW }'))); ?>'};"><?php echo ((isset($this->_rootref['L_EXPAND_VIEW'])) ? $this->_rootref['L_EXPAND_VIEW'] : ((isset($user->lang['EXPAND_VIEW'])) ? $user->lang['EXPAND_VIEW'] : '{ EXPAND_VIEW }')); ?></a></span>
		<?php echo ((isset($this->_rootref['L_TOPIC_REVIEW'])) ? $this->_rootref['L_TOPIC_REVIEW'] : ((isset($user->lang['TOPIC_REVIEW'])) ? $user->lang['TOPIC_REVIEW'] : '{ TOPIC_REVIEW }')); ?>: <?php echo (isset($this->_rootref['TOPIC_TITLE'])) ? $this->_rootref['TOPIC_TITLE'] : ''; ?>

	</h3>

	<div id="topicreview">
		<?php $_postrow_count = (isset($this->_tpldata['postrow'])) ? sizeof($this->_tpldata['postrow']) : 0;if ($_postrow_count) {for ($_postrow_i = 0; $_postrow_i < $_postrow_count; ++$_postrow_i){$_postrow_val = &$this->_tpldata['postrow'][$_postrow_i]; ?>

		<div class="post <?php if (($_postrow_val['S_ROW_COUNT'] & 1)  ) {  ?>bg1<?php } else { ?>bg2<?php } ?>">
			<div class="inner"><span class="corners-top"><span></span></span>

			<div class="postbody" id="pr<?php echo $_postrow_val['POST_ID']; ?>">
				<ul class="profile-icons"><li class="info-icon"><a href="<?php echo $_postrow_val['U_POST_DETAILS']; ?>" title="<?php echo ((isset($this->_rootref['L_POST_DETAILS'])) ? $this->_rootref['L_POST_DETAILS'] : ((isset($user->lang['POST_DETAILS'])) ? $user->lang['POST_DETAILS'] : '{ POST_DETAILS }')); ?>"><span><?php echo ((isset($this->_rootref['L_POST_DETAILS'])) ? $this->_rootref['L_POST_DETAILS'] : ((isset($user->lang['POST_DETAILS'])) ? $user->lang['POST_DETAILS'] : '{ POST_DETAILS }')); ?></span></a></li><li><?php echo ((isset($this->_rootref['L_SELECT'])) ? $this->_rootref['L_SELECT'] : ((isset($user->lang['SELECT'])) ? $user->lang['SELECT'] : '{ SELECT }')); ?>: <input type="checkbox" name="post_id_list[]" value="<?php echo $_postrow_val['POST_ID']; ?>"<?php if ($_postrow_val['S_CHECKED']) {  ?> checked="checked"<?php } ?> /></li></ul>

				<h3><a href="<?php echo $_postrow_val['U_POST_DETAILS']; ?>"><?php echo $_postrow_val['POST_SUBJECT']; ?></a></h3>
				<p class="author"><a href="#pr<?php echo $_postrow_val['POST_ID']; ?>"><?php echo $_postrow_val['MINI_POST_IMG']; ?></a> <?php echo ((isset($this->_rootref['L_POSTED'])) ? $this->_rootref['L_POSTED'] : ((isset($user->lang['POSTED'])) ? $user->lang['POSTED'] : '{ POSTED }')); ?> <?php echo $_postrow_val['POST_DATE']; ?> <?php echo ((isset($this->_rootref['L_POST_BY_AUTHOR'])) ? $this->_rootref['L_POST_BY_AUTHOR'] : ((isset($user->lang['POST_BY_AUTHOR'])) ? $user->lang['POST_BY_AUTHOR'] : '{ POST_BY_AUTHOR }')); ?> <strong><?php echo $_postrow_val['POST_AUTHOR_FULL']; ?></strong><?php if ($_postrow_val['U_MCP_DETAILS']) {  ?> [ <a href="<?php echo $_postrow_val['U_MCP_DETAILS']; ?>"><?php echo ((isset($this->_rootref['L_POST_DETAILS'])) ? $this->_rootref['L_POST_DETAILS'] : ((isset($user->lang['POST_DETAILS'])) ? $user->lang['POST_DETAILS'] : '{ POST_DETAILS }')); ?></a> ]<?php } ?></p>

				<?php if ($_postrow_val['S_POST_UNAPPROVED'] || $_postrow_val['S_POST_REPORTED']) {  ?>

				<p class="rules">
					<?php if ($_postrow_val['S_POST_UNAPPROVED']) {  echo (isset($this->_rootref['UNAPPROVED_IMG'])) ? $this->_rootref['UNAPPROVED_IMG'] : ''; ?> <a href="<?php echo $_postrow_val['U_MCP_APPROVE']; ?>"><strong><?php echo ((isset($this->_rootref['L_POST_UNAPPROVED'])) ? $this->_rootref['L_POST_UNAPPROVED'] : ((isset($user->lang['POST_UNAPPROVED'])) ? $user->lang['POST_UNAPPROVED'] : '{ POST_UNAPPROVED }')); ?></strong></a><br /><?php } if ($_postrow_val['S_POST_REPORTED']) {  echo (isset($this->_rootref['REPORTED_IMG'])) ? $this->_rootref['REPORTED_IMG'] : ''; ?> <a href="<?php echo $_postrow_val['U_MCP_REPORT']; ?>"><strong><?php echo ((isset($this->_rootref['L_POST_REPORTED'])) ? $this->_rootref['L_POST_REPORTED'] : ((isset($user->lang['POST_REPORTED'])) ? $user->lang['POST_REPORTED'] : '{ POST_REPORTED }')); ?></strong></a><?php } ?>

				</p>
				<?php } ?>


				<div class="content" id="message_<?php echo $_postrow_val['POST_ID']; ?>"><?php echo $_postrow_val['MESSAGE']; ?></div>

				<?php if ($_postrow_val['S_HAS_ATTACHMENTS']) {  ?>

					<dl class="attachbox">
						<dt><?php echo ((isset($this->_rootref['L_ATTACHMENTS'])) ? $this->_rootref['L_ATTACHMENTS'] : ((isset($user->lang['ATTACHMENTS'])) ? $user->lang['ATTACHMENTS'] : '{ ATTACHMENTS }')); ?></dt>
						<?php $_attachment_count = (isset($_postrow_val['attachment'])) ? sizeof($_postrow_val['attachment']) : 0;if ($_attachment_count) {for ($_attachment_i = 0; $_attachment_i < $_attachment_count; ++$_attachment_i){$_attachment_val = &$_postrow_val['attachment'][$_attachment_i]; ?>

							<dd><?php echo $_attachment_val['DISPLAY_ATTACHMENT']; ?></dd>
						<?php }} ?>

					</dl>
				<?php } ?>


			</div>

			<span class="corners-bottom"><span></span></span></div>
		</div>
		<?php }} ?>

	</div>

	<hr />

	<?php if ($this->_rootref['PAGINATION'] || $this->_rootref['TOTAL_POSTS']) {  ?>

	<ul class="linklist">
		<li class="rightside pagination">
			<?php if ($this->_rootref['TOTAL_POSTS']) {  ?> <?php echo (isset($this->_rootref['TOTAL_POSTS'])) ? $this->_rootref['TOTAL_POSTS'] : ''; } if ($this->_rootref['PAGE_NUMBER']) {  if ($this->_rootref['PAGINATION']) {  ?> &bull; <a href="#" onclick="jumpto(); return false;" title="<?php echo ((isset($this->_rootref['L_JUMP_TO_PAGE'])) ? $this->_rootref['L_JUMP_TO_PAGE'] : ((isset($user->lang['JUMP_TO_PAGE'])) ? $user->lang['JUMP_TO_PAGE'] : '{ JUMP_TO_PAGE }')); ?>"><?php echo (isset($this->_rootref['PAGE_NUMBER'])) ? $this->_rootref['PAGE_NUMBER'] : ''; ?></a> &bull; <span><?php echo (isset($this->_rootref['PAGINATION'])) ? $this->_rootref['PAGINATION'] : ''; ?></span><?php } else { ?> &bull; <?php echo (isset($this->_rootref['PAGE_NUMBER'])) ? $this->_rootref['PAGE_NUMBER'] : ''; } } ?>

		</li>
	</ul>
	<?php } ?>


	<span class="corners-bottom"><span></span></span></div>
</div>

<fieldset class="display-actions">
	<select name="action">
		<option value="" selected="selected"><?php echo ((isset($this->_rootref['L_SELECT_ACTION'])) ? $this->_rootref['L_SELECT_ACTION'] : ((isset($user->lang['SELECT_ACTION'])) ? $user->lang['SELECT_ACTION'] : '{ SELECT_ACTION }')); ?></option>
		<?php if ($this->_rootref['S_CAN_APPROVE']) {  ?><option value="approve"><?php echo ((isset($this->_rootref['L_APPROVE_POSTS'])) ? $this->_rootref['L_APPROVE_POSTS'] : ((isset($user->lang['APPROVE_POSTS'])) ? $user->lang['APPROVE_POSTS'] : '{ APPROVE_POSTS }')); ?></option><?php } if ($this->_rootref['S_CAN_LOCK']) {  ?><option value="lock_post"><?php echo ((isset($this->_rootref['L_LOCK_POST_POSTS'])) ? $this->_rootref['L_LOCK_POST_POSTS'] : ((isset($user->lang['LOCK_POST_POSTS'])) ? $user->lang['LOCK_POST_POSTS'] : '{ LOCK_POST_POSTS }')); ?> [ <?php echo ((isset($this->_rootref['L_LOCK_POST_EXPLAIN'])) ? $this->_rootref['L_LOCK_POST_EXPLAIN'] : ((isset($user->lang['LOCK_POST_EXPLAIN'])) ? $user->lang['LOCK_POST_EXPLAIN'] : '{ LOCK_POST_EXPLAIN }')); ?> ]</option><option value="unlock_post"><?php echo ((isset($this->_rootref['L_UNLOCK_POST_POSTS'])) ? $this->_rootref['L_UNLOCK_POST_POSTS'] : ((isset($user->lang['UNLOCK_POST_POSTS'])) ? $user->lang['UNLOCK_POST_POSTS'] : '{ UNLOCK_POST_POSTS }')); ?></option><?php } if ($this->_rootref['S_CAN_DELETE']) {  ?><option value="delete_post"><?php echo ((isset($this->_rootref['L_DELETE_POSTS'])) ? $this->_rootref['L_DELETE_POSTS'] : ((isset($user->lang['DELETE_POSTS'])) ? $user->lang['DELETE_POSTS'] : '{ DELETE_POSTS }')); ?></option><?php } if ($this->_rootref['S_CAN_MERGE']) {  ?><option value="merge_posts"<?php if ($this->_rootref['S_MERGE_VIEW']) {  ?> selected="selected"<?php } ?>><?php echo ((isset($this->_rootref['L_MERGE_POSTS'])) ? $this->_rootref['L_MERGE_POSTS'] : ((isset($user->lang['MERGE_POSTS'])) ? $user->lang['MERGE_POSTS'] : '{ MERGE_POSTS }')); ?></option><?php } if ($this->_rootref['S_CAN_SPLIT']) {  ?><option value="split_all"<?php if ($this->_rootref['S_SPLIT_VIEW']) {  ?> selected="selected"<?php } ?>><?php echo ((isset($this->_rootref['L_SPLIT_POSTS'])) ? $this->_rootref['L_SPLIT_POSTS'] : ((isset($user->lang['SPLIT_POSTS'])) ? $user->lang['SPLIT_POSTS'] : '{ SPLIT_POSTS }')); ?></option><option value="split_beyond"><?php echo ((isset($this->_rootref['L_SPLIT_AFTER'])) ? $this->_rootref['L_SPLIT_AFTER'] : ((isset($user->lang['SPLIT_AFTER'])) ? $user->lang['SPLIT_AFTER'] : '{ SPLIT_AFTER }')); ?></option><?php } ?>

	</select>&nbsp;
	<input class="button1" type="submit" name="mcp_topic_submit" value="<?php echo ((isset($this->_rootref['L_SUBMIT'])) ? $this->_rootref['L_SUBMIT'] : ((isset($user->lang['SUBMIT'])) ? $user->lang['SUBMIT'] : '{ SUBMIT }')); ?>" />
	<div><a href="#" onclick="marklist('mcp', 'post', true); return false;"><?php echo ((isset($this->_rootref['L_MARK_ALL'])) ? $this->_rootref['L_MARK_ALL'] : ((isset($user->lang['MARK_ALL'])) ? $user->lang['MARK_ALL'] : '{ MARK_ALL }')); ?></a> :: <a href="#" onclick="marklist('mcp', 'post', false); return false;"><?php echo ((isset($this->_rootref['L_UNMARK_ALL'])) ? $this->_rootref['L_UNMARK_ALL'] : ((isset($user->lang['UNMARK_ALL'])) ? $user->lang['UNMARK_ALL'] : '{ UNMARK_ALL }')); ?></a></div>
<?php echo (isset($this->_rootref['S_HIDDEN_FIELDS'])) ? $this->_rootref['S_HIDDEN_FIELDS'] : ''; ?>

<?php echo (isset($this->_rootref['S_FORM_TOKEN'])) ? $this->_rootref['S_FORM_TOKEN'] : ''; ?>

</fieldset>

</form>

<?php $this->_tpl_include('mcp_footer.html'); ?>