<div class="page-header">
	<h1><?php echo l('set up configuration'); ?></h1>
</div>

<?php echo bootstrap_display_message(); ?>

<p class="lead"><?php echo l('1. you have to migrate below.');?></p>
<pre>cd [web_root];
curl -sS https://getcomposer.org/installer | php
php composer.phar install
php index.php migration prepare</pre>
<p class="lead"><?php echo l('2. and please type database information.');?></p>

<?php echo form_open(url_for($self->this_base_uri.'/setup', $self->return_get_values), array('class'=>''), $self->form_post_values); ?>
<?php echo bootstrap_basic_input(l('site name'), 'config[site_name]', array('required' => TRUE), $site_name); ?>
<?php echo bootstrap_basic_input(l('site key'), 'config[site_key]', array('required' => TRUE), $site_key); ?>
<?php echo bootstrap_basic_input(l('service domain'), 'config[service_domain]', array('required' => TRUE), $service_domain); ?>

<?php echo bootstrap_basic_input(l('userid'), 'account[userid]', array('required' => TRUE)); ?>
<?php echo bootstrap_basic_input(l('name'), 'account[name]', array('required' => TRUE)); ?>
<?php echo bootstrap_basic_password(l('password'), 'account[password]', array('required' => TRUE)); ?>
<?php echo bootstrap_basic_password(l('verify password'), 'verify_password', array('required' => TRUE)); ?>
<?php echo bootstrap_basic_group(button_tag(bootstrap_icon_text('sign-in', l('submit')), array('type' => 'submit', 'class' => 'btn btn-default'))); ?>
<?php echo form_close(); ?>