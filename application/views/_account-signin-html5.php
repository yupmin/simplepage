<div class="page-header">
<h1><?php echo l('sign in'); ?></h1>
</div>

<?php echo bootstrap_display_message(); ?>

<?php echo form_open(url_for($self->this_base_uri.'/signin', $self->return_get_values), array('class'=>'form-horizontal'), $self->form_post_values); ?>
<?php echo bootstrap_horizontal_input(l('userid'), 'account[userid]', array('required' => NULL, 'autocomplete' => 'off', 'autocapitalize' => 'off'), $account_userid); ?>
<?php echo bootstrap_horizontal_password(l('password'), 'account[password]', array('required' => NULL)); ?>
<?php echo bootstrap_horizontal_group(button_tag(bootstrap_icon_text('sign-in', l('sign in')), array('type' => 'submit', 'class' => 'btn btn-default'))); ?>
<?php echo form_close(); ?>