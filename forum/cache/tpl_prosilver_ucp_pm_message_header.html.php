<?php if (!defined('IN_PHPBB')) exit; ?><h2><?php echo ((isset($this->_rootref['L_TITLE'])) ? $this->_rootref['L_TITLE'] : ((isset($user->lang['TITLE'])) ? $user->lang['TITLE'] : '{ TITLE }')); if ($this->_rootref['CUR_FOLDER_NAME']) {  ?>: <?php echo (isset($this->_rootref['CUR_FOLDER_NAME'])) ? $this->_rootref['CUR_FOLDER_NAME'] : ''; } ?></h2>

<div class="panel clearfix pm-panel-header<?php if ($this->_rootref['S_VIEW_MESSAGE']) {  ?> pm<?php } ?>">
	<div class="inner"><span class="corners-top"><span></span></span>

	<?php if ($this->_rootref['FOLDER_STATUS'] && $this->_rootref['FOLDER_MAX_MESSAGES'] != 0) {  ?><p><?php echo (isset($this->_rootref['FOLDER_STATUS'])) ? $this->_rootref['FOLDER_STATUS'] : ''; ?></p><?php } if ($this->_rootref['U_POST_REPLY_PM'] || $this->_rootref['U_POST_NEW_TOPIC'] || $this->_rootref['U_FORWARD_PM']) {  ?>

		<div class="buttons">
			<?php if ($this->_rootref['U_POST_REPLY_PM']) {  ?><div class="pmreply-icon clearfix"><a title="<?php echo ((isset($this->_rootref['L_POST_REPLY_PM'])) ? $this->_rootref['L_POST_REPLY_PM'] : ((isset($user->lang['POST_REPLY_PM'])) ? $user->lang['POST_REPLY_PM'] : '{ POST_REPLY_PM }')); ?>" href="<?php echo (isset($this->_rootref['U_POST_REPLY_PM'])) ? $this->_rootref['U_POST_REPLY_PM'] : ''; ?>"><span></span><?php echo ((isset($this->_rootref['L_POST_REPLY_PM'])) ? $this->_rootref['L_POST_REPLY_PM'] : ((isset($user->lang['POST_REPLY_PM'])) ? $user->lang['POST_REPLY_PM'] : '{ POST_REPLY_PM }')); ?></a></div>
			<?php } else if ($this->_rootref['U_POST_NEW_TOPIC']) {  ?><div class="newpm-icon"><a href="<?php echo (isset($this->_rootref['U_POST_NEW_TOPIC'])) ? $this->_rootref['U_POST_NEW_TOPIC'] : ''; ?>" accesskey="n" title="<?php echo ((isset($this->_rootref['L_UCP_PM_COMPOSE'])) ? $this->_rootref['L_UCP_PM_COMPOSE'] : ((isset($user->lang['UCP_PM_COMPOSE'])) ? $user->lang['UCP_PM_COMPOSE'] : '{ UCP_PM_COMPOSE }')); ?>"><span></span><?php echo ((isset($this->_rootref['L_UCP_PM_COMPOSE'])) ? $this->_rootref['L_UCP_PM_COMPOSE'] : ((isset($user->lang['UCP_PM_COMPOSE'])) ? $user->lang['UCP_PM_COMPOSE'] : '{ UCP_PM_COMPOSE }')); ?></a></div><?php } if ($this->_rootref['U_FORWARD_PM']) {  ?><div class="forwardpm-icon"><a title="<?php echo ((isset($this->_rootref['L_POST_FORWARD_PM'])) ? $this->_rootref['L_POST_FORWARD_PM'] : ((isset($user->lang['POST_FORWARD_PM'])) ? $user->lang['POST_FORWARD_PM'] : '{ POST_FORWARD_PM }')); ?>" href="<?php echo (isset($this->_rootref['U_FORWARD_PM'])) ? $this->_rootref['U_FORWARD_PM'] : ''; ?>"><span></span><?php echo ((isset($this->_rootref['L_FORWARD_PM'])) ? $this->_rootref['L_FORWARD_PM'] : ((isset($user->lang['FORWARD_PM'])) ? $user->lang['FORWARD_PM'] : '{ FORWARD_PM }')); ?></a></div><?php } ?>

		</div>

		<?php if ($this->_rootref['U_POST_REPLY_PM'] && $this->_rootref['S_PM_RECIPIENTS'] > (1)) {  ?>

			<div class="reply-all"><a title="<?php echo ((isset($this->_rootref['L_REPLY_TO_ALL'])) ? $this->_rootref['L_REPLY_TO_ALL'] : ((isset($user->lang['REPLY_TO_ALL'])) ? $user->lang['REPLY_TO_ALL'] : '{ REPLY_TO_ALL }')); ?>" href="<?php echo (isset($this->_rootref['U_POST_REPLY_ALL'])) ? $this->_rootref['U_POST_REPLY_ALL'] : ''; ?>">&raquo; <?php echo ((isset($this->_rootref['L_REPLY_TO_ALL'])) ? $this->_rootref['L_REPLY_TO_ALL'] : ((isset($user->lang['REPLY_TO_ALL'])) ? $user->lang['REPLY_TO_ALL'] : '{ REPLY_TO_ALL }')); ?></a></div>
		<?php } } if ($this->_rootref['TOTAL_MESSAGES'] || $this->_rootref['S_VIEW_MESSAGE']) {  ?>

	<ul class="linklist pm-return-to">
		<li class="rightside pagination">
			<?php if ($this->_rootref['S_VIEW_MESSAGE']) {  ?><a class="<?php echo (isset($this->_rootref['S_CONTENT_FLOW_BEGIN'])) ? $this->_rootref['S_CONTENT_FLOW_BEGIN'] : ''; ?>" href="<?php echo (isset($this->_rootref['U_CURRENT_FOLDER'])) ? $this->_rootref['U_CURRENT_FOLDER'] : ''; ?>"><?php echo ((isset($this->_rootref['L_RETURN_TO'])) ? $this->_rootref['L_RETURN_TO'] : ((isset($user->lang['RETURN_TO'])) ? $user->lang['RETURN_TO'] : '{ RETURN_TO }')); ?> <?php echo (isset($this->_rootref['CUR_FOLDER_NAME'])) ? $this->_rootref['CUR_FOLDER_NAME'] : ''; ?></a><?php } if ($this->_rootref['FOLDER_CUR_MESSAGES'] != 0) {  if ($this->_rootref['TOTAL_MESSAGES']) {  echo (isset($this->_rootref['TOTAL_MESSAGES'])) ? $this->_rootref['TOTAL_MESSAGES'] : ''; } if ($this->_rootref['PAGE_NUMBER']) {  if ($this->_rootref['PAGINATION']) {  ?> &bull; <a href="#" onclick="jumpto(); return false;" title="<?php echo ((isset($this->_rootref['L_JUMP_TO_PAGE'])) ? $this->_rootref['L_JUMP_TO_PAGE'] : ((isset($user->lang['JUMP_TO_PAGE'])) ? $user->lang['JUMP_TO_PAGE'] : '{ JUMP_TO_PAGE }')); ?>"><?php echo (isset($this->_rootref['PAGE_NUMBER'])) ? $this->_rootref['PAGE_NUMBER'] : ''; ?></a> &bull; <span><?php echo (isset($this->_rootref['PAGINATION'])) ? $this->_rootref['PAGINATION'] : ''; ?></span><?php } else { ?> &bull; <?php echo (isset($this->_rootref['PAGE_NUMBER'])) ? $this->_rootref['PAGE_NUMBER'] : ''; } } } ?>

		</li>
	</ul>
		<?php } ?>

	</div>
</div>

<form id="viewfolder" method="post" action="<?php echo (isset($this->_rootref['S_PM_ACTION'])) ? $this->_rootref['S_PM_ACTION'] : ''; ?>">