<nav class="navbar navbar-inverse navbar-fixed-top">
<div class="container">
<div class="navbar-header">
	<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
		<span class="sr-only">Toggle navigation</span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
	</button>
	<?php echo anchor_tag('', h($self->site_name), array('class' => 'navbar-brand')); ?>
</div>
<div id="navbar" class="collapse navbar-collapse">
	<ul class="nav navbar-nav">
	</ul>
	<ul class="nav navbar-nav navbar-right">
<?php if (get_account_userid()): ?>
		<li><?php echo anchor_tag('#', bootstrap_icon_text('user', $self->this_account_name.' <span class="caret"></span>'), array('class' => 'dropdown-toggle', 'data-toggle' => 'dropdown', 'role' => 'button', 'aria-expanded' => 'false')); ?>
			<ul class="dropdown-menu" role="menu">
				<li <?php echo $self->this_uri == 'account/edit' ? 'class="active"' : '';?>><?php echo anchor_tag('account/edit', bootstrap_icon_text('user', l('edit account')), NULL, array('return_url' => $self->this_url)); ?></li>
<?php 	if (in_array($self->this_account_user_level, array('administrator', 'system administrator'))): ?>
				<li class="divider"></li>
				<li <?php echo $self->this_container == 'manage' ? 'class="active"' : '';?>><?php echo anchor_tag('manage', bootstrap_icon_text('cog', l('manage')), NULL, array('return_url' => $self->this_url)); ?></li>
<?php 	endif; ?>
				<li class="divider"></li>
				<li><?php echo anchor_tag('account/signout', bootstrap_icon_text('sign-out', l('sign out'))); ?></li>
			</ul>
		</li>
<?php else: ?>
<?php 	if ($self->allow_signin): ?>
		<li <?php echo $self->this_uri == 'account/signin' ? 'class="active"' : '';?>><?php echo anchor_tag('account/signin', bootstrap_icon_text('sign-in', l('sign in')), NULL, array('return_url' => $self->this_url)); ?></li>
<?php 	endif;?>
<?php endif; ?>
	</ul>
</div><!--/.nav-collapse -->
</div>
</nav>