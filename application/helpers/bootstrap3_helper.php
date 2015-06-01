<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function _font_icon($kind, $tag, $name) {
	return _tag($tag, NULL, array('class' => $kind.' '.$kind.'-'.$name, 'aria-hidden' => 'true'));
}
function glyphicon($name, $tag = NULL) {
	if (is_null($tag)) $tag = 'span';
	return _font_icon('glyphicon', $tag, $name);
}
function fa($name, $tag = NULL) {
	if (is_null($tag)) $tag = 'span';
	return _font_icon('fa', $tag, $name);
}
function i($name, $tag = NULL, $icon_kind = NULL) {
	if (is_null($tag)) $tag = 'span';
	if (is_null($icon_kind)) $icon_kind = 'fa';
	return _font_icon($icon_kind, $tag, $name);
}
function caret() {
	return _tag('span', NULL, array('class' => 'caret'));
}
function bootstrap_icon_text($icon_name, $text, $icon_kind = NULL, $hidden_size = NULL, $icon_tag = NULL) {
	if (is_null($icon_kind)) $icon_kind = 'fa';
	//if (is_null($hidden_size)) $hidden_size = 'xs';
	return $icon_kind($icon_name, $icon_tag).(empty($hidden_size) ? ' '.$text : ' <span class="hidden-'.$hidden_size.'">'.$text.'</span>');
}
function _label_badge($kind, $text, $name = NULL, $class = NULL) {
	if (is_null($name)) $name = 'default';
	if (is_null($class)) $class = '';
	return _tag('span', $text, array('class' => $kind.' '.$kind.'-'.$name.(empty($class) ? '':' '.$class)));
}
function e($text, $name = NULL, $class = NULL) {
	return _label_badge('label', $text, $name, $class);
}
function b($text, $name = NULL, $class = NULL) {
	return _label_badge('badge', $text, $name, $class);
}
function bootstrap_icon_label($icon_name, $text, $name = NULL, $icon_kind = NULL, $icon_tag = NULL, $class = NULL) {
	if (is_null($icon_kind)) $icon_kind = 'fa';
	return e($icon_kind($icon_name, $icon_tag).' '.$text, $name, $class);
}
function bootstrap_icon_badge($icon_name, $text, $name = NULL, $icon_kind = NULL, $icon_tag = NULL, $class = NULL) {
	if (is_null($icon_kind)) $icon_kind = 'fa';
	return b($icon_kind($icon_name, $icon_tag).' '.$text, $name, $class);
}
function bootstrap_icon_kind_by_extension($extension) {
	$CI =& get_instance();
	$CI->load->library('attachment_manager');
	$_group_key = $CI->attachment_manager->get_file_group_key_by_extension($extension);

	switch($extension) {
		case 'doc':
		case 'docx':
			$_icon_kind = 'file-word-o';
			break;
		case 'xls':
		case 'xlsx':
			$_icon_kind = 'file-excel-o';
			break;
		case 'ppt';
		case 'pptx':
			$_icon_kind = 'file-powerpoint-o';
			break;
		case 'txt':
		case 'text':
			$_icon_kind = 'file-text-o';
			break;
		case 'pdf':
			$_icon_kind = 'file-pdf-o';
			break;
		default:
			switch($_group_key) {
				case 'image':
					$_icon_kind = 'file-image-o';
					break;
				case 'video':
					$_icon_kind = 'file-video-o';
					break;
				case 'audio':
					$_icon_kind = 'file-audio-o';
					break;
				case 'compression':
					$_icon_kind = 'file-archive-o';
					break;
				case 'code':
					$_icon_kind = 'file-code-o';
					break;
				default:
					$_icon_kind = 'file-o';
			}
	}
	return $_icon_kind;
}

// bootstrap_[basic|horizontal]_[input_type]_[data_type]
// label.class=sr-only input-group-addon disable-help-block
// input.disabled input.readonly input.required
// class, placeholder, id

function bootstrap_alert($message, $level = NULL, $close = NULL) {
	if (is_null($level)) $level = 'info';
	if (is_null($close)) $close = FALSE;

	$_s = '<div class="alert alert-'.$level.'" role="alert">'."\n";
	if ($close) $_s .= button_tag(_tag('span', '&times;', array('aria-hidden' => 'true')), array('type' => 'button', 'class' => 'close', 'data-dismiss' => 'alert', 'aria-label' => 'Close'));
	$_s .= $message;
	$_s .= '</div>'."\n";

	return $_s;
}
function bootstrap_image($src, $attributes = NULL) {
	if (!is_array($attributes)) $attributes = array();
/*	if (isset($attributes['class'])) {
		$attributes['class'] .= ' img-responsive';
	} else {
		$attributes['class'] = 'img-responsive';
	}*/
	return image_tag($src, $attributes);
}
function bootstrap_image_anchor($href, $src, $image_attributes = NULL, $anchor_attributes = NULL, $query_data = NULL) {
	return anchor_tag($href, bootstrap_image($src, $image_attributes), $anchor_attributes, $query_data);
}

function bootstrap_display_flash_message() {
	$CI =& get_instance();
	$_messages = array();

	if (isset($CI->session)) {
		foreach(array('success', 'info', 'warning', 'danger') as $_level) {
			$_message = $CI->session->flashdata($_level);
			if (!empty($_message)) $_messages[$_level] = $_message;
		}
	}
	$_messages = array_merge($_messages, $CI->_get_alert_messages());
	$_s = '<div id="alert_message">';
	if (count($_messages) > 0) {
		foreach($_messages as $_k => $_v)
		$_s .= '<div class="alert alert-'.$_k.'" role="alert">'."\n";
		$_s .= button_tag(_tag('span', '&times;', array('aria-hidden' => 'true')), array('type' => 'button', 'class' => 'close', 'data-dismiss' => 'alert', 'aria-label' => 'Close'));
		$_s .= h($_v);
		$_s .= '</div>'."\n";
	}
	$_s .= '</div>';
	return $_s;
}

function bootstrap_display_message() {
	return  bootstrap_display_flash_message();
}

function bootstrap_default_input($kind, $input, $label, $field, $attributes = NULL, $value = NULL, $column_size = NULL) {
	if (!function_exists('form_'.$input)) return FALSE;
	if (is_null($column_size)) $column_size = 2;

	$_default_attributes = array('id' => get_field_id($field), 'class' => array('form-control'), 'placeholder' => $label);
	$_sr_only = FALSE;
	$_pre_input_group_addon = FALSE;
	$_post_input_group_addon = FALSE;
	$_disable_help_block = TRUE;
	$_required = FALSE;
	$_form_group_class = '';
	if (is_null($attributes)) {
		$attributes = $_default_attributes;
	} else if (is_array($attributes)) {
		// remove sr-only
		$_sr_only = element('sr-only', $attributes);
		$attributes = array_diff_key($attributes, array('sr-only' => TRUE));
		// remove pre-input-group-addon
		$_pre_input_group_addon = element('pre-input-group-addon', $attributes);
		$attributes = array_diff_key($attributes, array('pre-input-group-addon' => TRUE));
		// remove post-input-group-addon
		$_post_input_group_addon = element('post-input-group-addon', $attributes);
		$attributes = array_diff_key($attributes, array('post-input-group-addon' => TRUE));
		// remove disable-help-block
		$_disable_help_block = element('disable-help-block', $attributes);
		$attributes = array_diff_key($attributes, array('disable-help-block' => TRUE));
		// required
		$_required = element('required', $attributes);
		// remove form-group-class
		$_form_group_class = element('form-group-class', $attributes);
		$attributes = array_diff_key($attributes, array('form-group-class' => TRUE));

		$attributes = array_merge($_default_attributes, $attributes);
	}

	$_s = '<div class="form-group'.(is_error_field($field) ? ' has-error':'').(empty($_required) ? '':' required').$_form_group_class.'">'."\n";
	if ($kind == 'horizontal') {
		$_left_column_size = $column_size;
		$_right_column_size = 12 - $column_size;
		$_s .= form_label($label, get_field_id($field), array('class' => 'control-label'.($_sr_only ? ' sr-only':' col-sm-'.$_left_column_size)))."\n";
		$_s .= '<div class="'.($_sr_only ? 'col-sm-12':' col-sm-'.$_right_column_size).'">'."\n";
	} else {
		$_s .= form_label($label, get_field_id($field), array('class' => 'control-label'.($_sr_only ? ' sr-only':'')))."\n";
	}

	if ($_pre_input_group_addon || $_post_input_group_addon) $_s .= '<div class="input-group">';
	if ($_pre_input_group_addon) $_s .= '<span class="input-group-addon">'.$_pre_input_group_addon.'</span>';
	$_s .= call_user_func_array('form_'.$input, array($field, $value, field_attributes($attributes)))."\n";
	if ($_post_input_group_addon) $_s .= '<span class="input-group-addon">'.$_post_input_group_addon.'</span>';
	if ($_pre_input_group_addon || $_post_input_group_addon) $_s .= '</div>';
	if (!$_disable_help_block) $_s .= form_error($field, '<span class="help-block">', '</span>');

	if ($kind == 'horizontal') {
		$_s .= '</div>'."\n";
	}
	$_s .= '</div>'."\n";
	return $_s;
}
function bootstrap_default_phone($kind, $label, $field, $calling_code_items, $attributes = NULL, $calling_code_id = NULL, $phone_body = NULL, $column_size = NUL) {
	if (is_null($column_size)) $column_size = 2;
	$_telecom_number_size = 3;
	$_telecom_number_body = 12 - $column_size - $_telecom_number_size;

	$_default_attributes = array('id' => get_field_id($field), 'class' => array('form-control'), 'placeholder' => $label);
	$_sr_only = FALSE;
	$_pre_input_group_addon = FALSE;
	$_post_input_group_addon = FALSE;
	$_disable_help_block = TRUE;
	$_required = FALSE;
	$_form_group_class = '';
	if (is_null($attributes)) {
		$attributes = $_default_attributes;
	} else if (is_array($attributes)) {
		// remove sr-only
		$_sr_only = element('sr-only', $attributes);
		$attributes = array_diff_key($attributes, array('sr-only' => TRUE));
		// remove pre-input-group-addon
		$_pre_input_group_addon = element('pre-input-group-addon', $attributes);
		$attributes = array_diff_key($attributes, array('pre-input-group-addon' => TRUE));
		// remove post-input-group-addon
		$_post_input_group_addon = element('post-input-group-addon', $attributes);
		$attributes = array_diff_key($attributes, array('post-input-group-addon' => TRUE));
		// remove disable-help-block
		$_disable_help_block = element('disable-help-block', $attributes);
		$attributes = array_diff_key($attributes, array('disable-help-block' => TRUE));
		// required
		$_required = element('required', $attributes);
		// remove form-group-class
		$_form_group_class = element('form-group-class', $attributes);
		$attributes = array_diff_key($attributes, array('form-group-class' => TRUE));

		$attributes = array_merge($_default_attributes, $attributes);
	}

	$calling_code_id_attributes = $attributes;
	$phone_body_attributes = $attributes;

	if (($pos = strpos($field, '[')) > 0) {
		$_field = substr($field, 0, $pos);
		$_index = substr($field, $pos, strlen($field) - $_field);

		$_indexes = array();
		if (preg_match_all('/\[(.+)\]/U', $_index, $matches)) {
			foreach($matches[1] as $match) {
				$_indexes[] = $match;
			}
		}
		if (count($_indexes) > 1) return FALSE;

		$field_calling_code_id = $_field.'['.$_indexes[0].'_calling_code_id]';
		$field_phone_body = $_field.'['.$_indexes[0].'_phone_body]';

	} else {
		$field_calling_code_id = $field.'_calling_code_id';
		$field_phone_body = $field.'_phone_body';
	}

	$_s = '<div class="form-group'.(is_error_field($field) ? ' has-error':'').(empty($_required) ? '':' required').$_form_group_class.'">'."\n";
	if ($kind == 'horizontal') {
		$_left_column_size = $column_size;
		$_right_column_size = 12 - $column_size;
		$_s .= form_label($label, get_field_id($field), array('class' => 'control-label'.($_sr_only ? ' sr-only':' col-sm-'.$_left_column_size)))."\n";
#		$_s .= '<div class="'.($_sr_only ? 'col-sm-12':' col-sm-'.$_right_column_size).'">'."\n";
	} else {
		$_s .= form_label($label, get_field_id($field), array('class' => 'control-label'.($_sr_only ? ' sr-only':'')))."\n";
	}

	if ($_pre_input_group_addon || $_post_input_group_addon) $_s .= '<div class="input-group">';
	if ($_pre_input_group_addon) $_s .= '<span class="input-group-addon">'.$_pre_input_group_addon.'</span>';
	$_s .= '<div class="'.($_sr_only ? 'col-xs-3':'col-xs-3 col-sm-'.$_telecom_number_size).'">'."\n";
	$_s .= form_dropdown($field_calling_code_id, $calling_code_items, $calling_code_id, field_attributes($calling_code_id_attributes));
	$_s .= form_error($field_calling_code_id, '<span class="help-inline">', '</span>');
	$_s .= '</div>';
	$_s .= '<div class="'.($_sr_only ? 'col-xs-9':'col-xs-9 col-sm-'.$_telecom_number_body).'">'."\n";
	$_s .= form_input($field_phone_body, $phone_body, field_attributes($phone_body_attributes));
	$_s .= form_error($field_phone_body, '<span class="help-inline">', '</span>');
	$_s .= '</div>';
	if ($_post_input_group_addon) $_s .= '<span class="input-group-addon">'.$_post_input_group_addon.'</span>';
	if ($_pre_input_group_addon || $_post_input_group_addon) $_s .= '</div>';
	if (!$_disable_help_block) $_s .= form_error($field, '<span class="help-block">', '</span>');

	if ($kind == 'horizontal') {
#		$_s .= '</div>'."\n";
	}
	$_s .= '</div>'."\n";
	return $_s;
}
function bootstrap_default_static($kind, $label, $value, $attributes = NULL, $column_size = NULL) {
	if (is_null($column_size)) $column_size = 2;

	$_default_attributes = array('class' => array('form-control-static'));
	$_sr_only = FALSE;
	if (is_null($attributes)) {
		$attributes = $_default_attributes;
	} else if (is_array($attributes)) {
		// remove sr-only
		$_sr_only = element('sr-only', $attributes);
		$attributes = array_diff_key($attributes, array('sr-only' => TRUE));

		$attributes = array_merge($_default_attributes, $attributes);
	}

	$_s = '<div class="form-group">'."\n";
	if ($kind == 'horizontal') {
		$_left_column_size = $column_size;
		$_right_column_size = 12 - $column_size;
		$_s .= form_label($label, NULL, array('class' => 'control-label'.($_sr_only ? ' sr-only':' col-sm-'.$_left_column_size)))."\n";
		$_s .= '<div class="'.($_sr_only ? 'col-sm-12':' col-sm-'.$_right_column_size).'">'."\n";
	} else {
		$_s .= form_label($label, NULL, array('class' => 'control-label'.($_sr_only ? ' sr-only':'')))."\n";
	}

	$_s .= '<p '.field_attributes($attributes).'>'.$value.'</p>'."\n";

	if ($kind == 'horizontal') {
		$_s .= '</div>'."\n";
	}
	$_s .= '</div>'."\n";
	return $_s;
}
function bootstrap_default_static_textarea($kind, $label, $value, $attributes = NULL, $column_size = NULL) {
	if (is_null($column_size)) $column_size = 2;

	$_default_attributes = array('class' => array('form-control-static panel-body'));
	$_sr_only = FALSE;
	if (is_null($attributes)) {
		$attributes = $_default_attributes;
	} else if (is_array($attributes)) {
		// remove sr-only
		$_sr_only = element('sr-only', $attributes);
		$attributes = array_diff_key($attributes, array('sr-only' => TRUE));

		$attributes = array_merge($_default_attributes, $attributes);
	}

	$_s = '<div class="form-group">'."\n";
	if ($kind == 'horizontal') {
		$_left_column_size = $column_size;
		$_right_column_size = 12 - $column_size;
		$_s .= form_label($label, NULL, array('class' => 'control-label'.($_sr_only ? ' sr-only':' col-sm-'.$_left_column_size)))."\n";
		$_s .= '<div class="'.($_sr_only ? 'col-sm-12':' col-sm-'.$_right_column_size).'">'."\n";
	} else {
		$_s .= form_label($label, NULL, array('class' => 'control-label'.($_sr_only ? ' sr-only':'')))."\n";
	}

	$_s .= '<div class="panel panel-default"><div'.field_attributes($attributes).'>'.$value.'</div></div>'."\n";

	if ($kind == 'horizontal') {
		$_s .= '</div>'."\n";
	}
	$_s .= '</div>'."\n";
	return $_s;
}
function bootstrap_default_checkbox($kind, $label, $field, $attributes = NULL, $value = NULL, $column_size = NULL) {
	if (is_null($column_size)) $column_size = 2;

	$_default_attributes = array('id' => get_field_id($field));
	$_sr_only = FALSE;
	$_disable_help_block = TRUE;
	if (is_null($attributes)) {
		$attributes = $_default_attributes;
	} else if (is_array($attributes)) {
		// remove sr-only
		$_sr_only = element('sr-only', $attributes);
		$attributes = array_diff_key($attributes, array('sr-only' => TRUE));
		// remove disable-help-block
		$_disable_help_block = element('disable-help-block', $attributes);
		$attributes = array_diff_key($attributes, array('disable-help-block' => TRUE));

		$attributes = array_merge($_default_attributes, $attributes);
	}

	$_s = '<div class="form-group'.(is_error_field($field) ? ' has-error':'').'">'."\n";
	if ($kind == 'horizontal') {
		$_left_column_size = $column_size;
		$_right_column_size = 12 - $column_size;
		$_s .= '<div class="col-sm-offset-'.$_left_column_size.' col-sm-'.$_right_column_size.'">'."\n";
		$_s .= '<div class="checkbox">'."\n";
		$_c = form_checkbox($field, $value, is_null($value), field_attributes($attributes)).' '.$label."\n";
		$_s .= form_label($_c, get_field_id($field), array('class' => 'control-label'.($_sr_only ? ' sr-only':'')))."\n";
		if (!$_disable_help_block) $_s .= form_error($field, '<span class="help-block">', '</span>');
		$_s .= '</div>'."\n";
		$_s .= '</div>'."\n";

	} else {
		$_s .= '<div class="checkbox">'."\n";
		$_c = form_checkbox($field, $value, is_null($value), field_attributes($attributes)).' '.$label."\n";
		$_s .= form_label($_c, get_field_id($field), array('class' => ''.($_sr_only ? ' sr-only':'')))."\n";
		if (!$_disable_help_block) $_s .= form_error($field, '<span class="help-block">', '</span>');
		$_s .= '</div>'."\n";
	}
	$_s .= '</div>'."\n";
	return $_s;
}
function bootstrap_default_checkbox_multi($kind, $label, $field, $items, $attributes = NULL, $values = NULL, $style = NULL, $column_size = NULL) {
	if (is_null($column_size)) $column_size = 2;
	if (is_string($items)) $_items = array($items);
	else if (is_array($items)) $_items = $items;
	else if (is_null($items)) $_items = array();
	else return FALSE;

	$_sr_only = FALSE;
	$_required = FALSE;
	$_disable_help_block = TRUE;
	if (is_array($attributes)) {
		// remove sr-only
		$_sr_only = element('sr-only', $attributes);
		$attributes = array_diff_key($attributes, array('sr-only' => TRUE));
		// required
		$_required = element('required', $attributes);
		// remove disable-help-block
		$_disable_help_block = element('disable-help-block', $attributes);
		$attributes = array_diff_key($attributes, array('disable-help-block' => TRUE));
	}

	$_s = '<div class="form-group'.(is_error_field($field) ? ' has-error':'').(empty($_required) ? '':' required').'">'."\n";
	if ($kind == 'horizontal' || $kind == 'horizontal_inline') {
		$_left_column_size = $column_size;
		$_right_column_size = 12 - $column_size;
		$_s .= form_label($label, NULL, array('class' => 'control-label'.($_sr_only ? ' sr-only':' col-sm-'.$_left_column_size)))."\n";

		$_s .= '<div class="col-sm-'.$_right_column_size.'">'."\n";
		foreach($items as $_k => $_v) {
			$_s .= ($kind == 'horizontal' ? '<div class="checkbox"><label>' : '<label class="checkbox-inline">')."\n";
			$_id = get_field_id($field).'_'.boolean($_k);
			if (is_null($attributes) || (!is_array($attributes) && !is_string($attributes))) {
				$attributes = array('id' => $_id);
			} else if (is_array($attributes)) {
				$attributes = array_merge(array('id' => $_id), $attributes);
			}

			//$_s .= form_checkbox($field, $_k, $values, field_attributes($attributes)).' '.$_v."\n";
			$_s .= form_checkbox($field, $_k, in_array($_k, $values), field_attributes($attributes)).' '.$_v."\n";

			$_s .= ($kind == 'horizontal' ? '</label></div>' : '</label>')."\n";
		}
		if (!$_disable_help_block) $_s .= form_error($field, '<span class="help-block">', '</span>');
		$_s .= '</div>'."\n";
	} else {
		$_s .= form_label($label, NULL, array('class' => 'control-label'.($_sr_only ? ' sr-only':'')))."\n";
		foreach($items as $_k => $_v) {
			$_s .= '<div class="checkbox"><label>'."\n";
			$_id = get_field_id($field).'_'.boolean($_k);
			if (is_null($attributes) || (!is_array($attributes) && !is_string($attributes))) {
				$attributes = array('id' => $_id);
			} else if (is_array($attributes)) {
				$attributes = array_merge(array('id' => $_id), $attributes);
			}

			//$_s .= form_checkbox($field, $_k, $values, field_attributes($attributes)).' '.$_v."\n";
			$_s .= form_checkbox($field, $_k, in_array($_k, $values), field_attributes($attributes)).' '.$_v."\n";

			$_s .= '</label></div>'."\n";
		}
		if (!$_disable_help_block) $_s .= form_error($field, '<span class="help-block">', '</span>');
	}
	$_s .= '</div>'."\n";
	return $_s;
}
function bootstrap_default_radio_select($kind, $label, $field, $items, $attributes = NULL, $value = NULL, $style = NULL, $column_size = NULL) {
	if (is_null($column_size)) $column_size = 2;
	if (is_string($items)) $_items = array($items);
	else if (is_array($items)) $_items = $items;
	else if (is_null($items)) $_items = array();
	else return FALSE;

	$_sr_only = FALSE;
	$_required = FALSE;
	$_disable_help_block = TRUE;
	if (is_array($attributes)) {
		// remove sr-only
		$_sr_only = element('sr-only', $attributes);
		$attributes = array_diff_key($attributes, array('sr-only' => TRUE));
		// required
		$_required = element('required', $attributes);
		// remove disable-help-block
		$_disable_help_block = element('disable-help-block', $attributes);
		$attributes = array_diff_key($attributes, array('disable-help-block' => TRUE));
	}

	$_s = '<div class="form-group'.(is_error_field($field) ? ' has-error':'').(empty($_required) ? '':' required').'">'."\n";
	if ($kind == 'horizontal' || $kind == 'horizontal_inline') {
		$_left_column_size = $column_size;
		$_right_column_size = 12 - $column_size;
		$_s .= form_label($label, NULL, array('class' => 'control-label'.($_sr_only ? ' sr-only':' col-sm-'.$_left_column_size)))."\n";

		$_s .= '<div class="col-sm-'.$_right_column_size.'">'."\n";
		foreach($items as $_k => $_v) {
			$_s .= ($kind == 'horizontal' ? '<div class="radio"><label>' : '<label class="radio-inline">')."\n";
			$_id = get_field_id($field).'_'.boolean($_k);
			if (is_null($attributes) || (!is_array($attributes) && !is_string($attributes))) {
				$attributes = array('id' => $_id);
			} else if (is_array($attributes)) {
				$attributes = array_merge(array('id' => $_id), $attributes);
			}
			$_s .= form_radio($field, $_k, $value, field_attributes($attributes)).' '.$_v."\n";
			$_s .= ($kind == 'horizontal' ? '</label></div>' : '</label>')."\n";
		}
		if (!$_disable_help_block) $_s .= form_error($field, '<span class="help-block">', '</span>');
		$_s .= '</div>'."\n";
	} else {
		$_s .= form_label($label, NULL, array('class' => 'control-label'.($_sr_only ? ' sr-only':'')))."\n";
		$_s .= '<div>'."\n";
		foreach($items as $_k => $_v) {
			$_s .= ($kind == 'basic' ? '<div class="radio"><label>' : '<label class="radio-inline">')."\n";
			$_id = get_field_id($field).'_'.boolean($_k);
			if (is_null($attributes) || (!is_array($attributes) && !is_string($attributes))) {
				$attributes = array('id' => $_id);
			} else if (is_array($attributes)) {
				$attributes = array_merge(array('id' => $_id), $attributes);
			}
			$_s .= form_radio($field, $_k, $value, field_attributes($attributes)).' '.$_v."\n";
			$_s .= ($kind == 'basic' ? '</label></div>' : '</label>')."\n";
		}
		$_s .= '</div>'."\n";
		if (!$_disable_help_block) $_s .= form_error($field, '<span class="help-block">', '</span>');
	}
	$_s .= '</div>'."\n";
	return $_s;
}
function bootstrap_default_select($kind, $label, $field, $items, $attributes = NULL, $value = NULL, $style = NULL, $column_size = NULL) {
	if (is_null($column_size)) $column_size = 2;
	if (is_string($items)) $_items = array($items);
	else if (is_array($items)) $_items = $items;
	else if (is_null($items)) $_items = array();
	else return FALSE;

	$_default_attributes = array('id' => get_field_id($field), 'class' => array('form-control'));
	$_sr_only = FALSE;
	$_required = FALSE;
	$_pre_input_group_addon = FALSE;
	$_post_input_group_addon = FALSE;
	$_disable_help_block = TRUE;
	if (is_null($attributes)) {
		$attributes = $_default_attributes;
	} else if (is_array($attributes)) {
		// remove sr-only
		$_sr_only = element('sr-only', $attributes);
		$attributes = array_diff_key($attributes, array('sr-only' => TRUE));

		// remove pre-input-group-addon
		$_pre_input_group_addon = element('pre-input-group-addon', $attributes);
		$attributes = array_diff_key($attributes, array('pre-input-group-addon' => TRUE));
		// remove post-input-group-addon
		$_post_input_group_addon = element('post-input-group-addon', $attributes);
		$attributes = array_diff_key($attributes, array('post-input-group-addon' => TRUE));
		// remove disable-help-block
		$_disable_help_block = element('disable-help-block', $attributes);
		$attributes = array_diff_key($attributes, array('disable-help-block' => TRUE));
		// required
		$_required = element('required', $attributes);

		$attributes = array_merge($_default_attributes, $attributes);
	}

	$_s = '<div class="form-group'.(is_error_field($field) ? ' has-error':'').(empty($_required) ? '':' required').'">'."\n";
	if ($kind == 'horizontal') {
		$_left_column_size = $column_size;
		$_right_column_size = 12 - $column_size;
		$_s .= form_label($label, get_field_id($field), array('class' => 'control-label'.($_sr_only ? ' sr-only':' col-sm-'.$_left_column_size)))."\n";
		$_s .= '<div class="'.($_sr_only ? 'col-sm-12':' col-sm-'.$_right_column_size).'">'."\n";
	} else {
		$_s .= form_label($label, get_field_id($field), array('class' => 'control-label'.($_sr_only ? ' sr-only':'')))."\n";
	}

	if ($_pre_input_group_addon || $_post_input_group_addon) $_s .= '<div class="input-group">';
	if ($_pre_input_group_addon) $_s .= '<span class="input-group-addon">'.$_pre_input_group_addon.'</span>';
	$_s .= form_dropdown($field, $items, $value, field_attributes($attributes))."\n";
	if ($_post_input_group_addon) $_s .= '<span class="input-group-addon">'.$_post_input_group_addon.'</span>';
	if ($_pre_input_group_addon || $_post_input_group_addon) $_s .= '</div>';
	if (!$_disable_help_block) $_s .= form_error($field, '<span class="help-block">', '</span>');

	if ($kind == 'horizontal') {
		$_s .= '</div>'."\n";
	}
	$_s .= '</div>'."\n";

	return $_s;
}
function bootstrap_default_group($kind, $text, $column_size = NULL) {
	$_s = bootstrap_default_group_start($kind, $column_size);
	$_s .= $text;
	$_s .= bootstrap_default_group_end($kind, $column_size);
	return $_s;
}
function bootstrap_default_group_start($kind, $column_size = NULL) {
	if (is_null($column_size)) $column_size = 2;

	$_s = '<div class="form-group">'."\n";
	if ($kind == 'horizontal') {
		$_left_column_size = $column_size;
		$_right_column_size = 12 - $column_size;
		$_s .= '<div class="col-sm-offset-'.$_left_column_size.' col-sm-'.$_right_column_size.'">'."\n";
	}
	return $_s;
}
function bootstrap_default_group_end($kind, $column_size = NULL) {
	if (is_null($column_size)) $column_size = 2;

	$_s = '';
	if ($kind == 'horizontal') {
		$_s .= '</div>'."\n";
	}
	$_s .= '</div>'."\n";
	return $_s;
}

function bootstrap_default_file($kind, $label, $field, $attributes = NULL, $value = NULL, $column_size = NULL) {
	if (is_null($column_size)) $column_size = 2;

	$_default_attributes = array('id' => get_field_id($field), 'class' => array('form-control'), 'placeholder' => $label);
	$_sr_only = FALSE;
	$_required = FALSE;
	$_disable_help_block = TRUE;
	$_button_attributes = array();
	$_data_target = NULL;
	if (is_null($attributes)) {
		$attributes = $_default_attributes;
	} else if (is_array($attributes)) {
		// remove sr-only
		$_sr_only = element('sr-only', $attributes);
		$attributes = array_diff_key($attributes, array('sr-only' => TRUE));

		// remove disable-help-block
		$_disable_help_block = element('disable-help-block', $attributes);
		$attributes = array_diff_key($attributes, array('disable-help-block' => TRUE));

		// remove data-action
		$_data_action = element('data-action', $attributes);
		if ($_data_action = 'ajax-file') {
			$_button_attributes['data-action'] = $attributes['data-action'];
			$attributes = array_diff_key($attributes, array('data-action' => TRUE));
		}

		// remove data-target
		$_data_target = element('data-target', $attributes);
		if ($_data_target = 'data-target') {
			//$_button_attributes['data-target'] = $attributes['data-target'];
			$_data_target = $attributes['data-target'];
			$attributes = array_diff_key($attributes, array('data-target' => TRUE));
		}
		// required
		$_required = element('required', $attributes);

		$attributes = array_merge($_default_attributes, $attributes);
	}

	$_s = '<div class="form-group'.(is_error_field($field) ? ' has-error':'').(empty($_required) ? '':' required').'">'."\n";
	if ($kind == 'horizontal') {
		$_left_column_size = $column_size;
		$_right_column_size = 12 - $column_size;
		$_s .= form_label($label, get_field_id($field), array('class' => 'control-label'.($_sr_only ? ' sr-only':' col-sm-'.$_left_column_size)))."\n";
		$_s .= '<div class="'.($_sr_only ? 'col-sm-12':' col-sm-'.$_right_column_size).'">'."\n";
	} else {
		$_s .= form_label($label, get_field_id($field), array('class' => 'control-label'.($_sr_only ? ' sr-only':'')))."\n";
	}

	if (!empty($_data_target)) $_s .= '<div class="input-group">';
	$_s .= form_file($field, $value, field_attributes($attributes))."\n";
	if (!empty($_data_target)) $_s .= '<span class="input-group-btn"><a href="'.$_data_target.'" class="btn btn-default" role="button"'.field_attributes($_button_attributes).'>'.bootstrap_icon_text('upload', l('upload')).'</a></span></div>';

	if (!$_disable_help_block) $_s .= form_error($field, '<span class="help-block">', '</span>');

	if ($kind == 'horizontal') {
		$_s .= '</div>'."\n";
	}
	$_s .= '</div>'."\n";
	return $_s;
}

/* basic */
function bootstrap_basic_input($label, $field, $attributes = NULL, $value = NULL) {
	return bootstrap_default_input('basic', 'input', $label, $field, $attributes, $value);
}
function bootstrap_basic_password($label, $field, $attributes = NULL, $value = NULL) {
	return bootstrap_default_input('basic', 'password', $label, $field, $attributes, $value);
}
function bootstrap_basic_email($label, $field, $attributes = NULL, $value = NULL) {
	return bootstrap_default_input('basic', 'email', $label, $field, $attributes, $value);
}
function bootstrap_basic_textarea($label, $field, $attributes = NULL, $value = NULL) {
	return bootstrap_default_input('basic', 'textarea', $label, $field, $attributes, $value);
}
function bootstrap_basic_checkbox($label, $field, $attributes = NULL, $value = NULL) {
	return bootstrap_default_checkbox('basic', $label, $field, $attributes, $value);
}
function bootstrap_basic_checkbox_multi($label, $field, $items, $attributes = NULL, $value = NULL) {
//	return bootstrap_default_checkbox_multi('basic', $label, $field, $attributes, $value);
}
function bootstrap_basic_inline_checkbox_multi($label, $field, $attributes = NULL, $value = NULL) {
//	return bootstrap_default_checkbox_multi('basic_inline', $label, $field, $items, $attributes, $value);
}
function bootstrap_basic_radio_select($label, $field, $items, $attributes = NULL, $value = NULL) {
	return bootstrap_default_radio_select('basic', $label, $field, $items, $attributes, $value);
}
function bootstrap_basic_inline_radio_select($label, $field, $items, $attributes = NULL, $value = NULL) {
	return bootstrap_default_radio_select('basic_inline', $label, $field, $items, $attributes, $value);
}
function bootstrap_basic_file($label, $field, $attributes = NULL, $value = NULL) {
	return bootstrap_default_file('basic', $label, $field, $attributes, $value);
}
function bootstrap_basic_select($label, $field, $items, $attributes = NULL, $value = NULL) {
	return bootstrap_default_select('basic', $label, $field, $items, $attributes, $value);
}
function bootstrap_basic_select_multi($label, $field, $items, $attributes = NULL, $value = NULL) {
//
}
function bootstrap_basic_phone($lable, $field, $calling_code_items, $attributes = NULL, $calling_code_id = NULL, $phone_body = NULL) {
	return bootstrap_default_phone('basic', $label, $field, $calling_code_items, $attributes, $calling_code_id, $phone_body);
}
function bootstrap_basic_static($label, $value, $attributes = NULL) {
	return bootstrap_default_static('basic', $label, $value, $attributes);
}
function bootstrap_basic_static_textarea($label, $value, $attributes = NULL) {
	return bootstrap_default_static_textarea('basic', $label, $value, $attributes);
}
function bootstrap_basic_group($text, $column_size = NULL) {
	return bootstrap_default_group('basic', $text, $column_size);
}
function bootstrap_basic_group_start($column_size = NULL) {
	return bootstrap_default_group_start('basic', $column_size);
}
function bootstrap_basic_group_end($column_size = NULL) {
	return bootstrap_default_group_end('basic', $column_size);
}

/* horizontal */
function bootstrap_horizontal_input($label, $field, $attributes = NULL, $value = NULL, $column_size = NULL) {
	return bootstrap_default_input('horizontal', 'input', $label, $field, $attributes, $value, $column_size);
}
function bootstrap_horizontal_password($label, $field, $attributes = NULL, $value = NULL, $column_size = NULL) {
	return bootstrap_default_input('horizontal', 'password', $label, $field, $attributes, $value, $column_size);
}
function bootstrap_horizontal_email($label, $field, $attributes = NULL, $value = NULL, $column_size = NULL) {
	return bootstrap_default_input('horizontal', 'email', $label, $field, $attributes, $value, $column_size);
}
function bootstrap_horizontal_textarea($label, $field, $attributes = NULL, $value = NULL, $column_size = NULL) {
	return bootstrap_default_input('horizontal', 'textarea', $label, $field, $attributes, $value, $column_size);
}
function bootstrap_horizontal_checkbox($label, $field, $attributes = NULL, $value = NULL, $column_size = NULL) {
	return bootstrap_default_checkbox('horizontal', $label, $field, $attributes, $value, $column_size);
}
function bootstrap_horizontal_checkbox_multi($label, $field, $items, $attributes = NULL, $value = NULL, $column_size = NULL) {
//	return bootstrap_default_checkbox_multi('horizontal', $label, $field, $items, $attributes, $value, $column_size);
}
function bootstrap_horizontal_inline_checkbox_multi($label, $field, $items, $attributes = NULL, $value = NULL, $column_size = NULL) {
	return bootstrap_default_checkbox_multi('horizontal_inline', $label, $field, $items, $attributes, $value, $column_size);
}
function bootstrap_horizontal_radio_select($label, $field, $items, $attributes = NULL, $value = NULL, $column_size = NULL) {
	return bootstrap_default_radio_select('horizontal', $label, $field, $items, $attributes, $value, $column_size);
}
function bootstrap_horizontal_inline_radio_select($label, $field, $items, $attributes = NULL, $value = NULL, $column_size = NULL) {
	return bootstrap_default_radio_select('horizontal_inline', $label, $field, $items, $attributes, $value, $column_size);
}
function bootstrap_horizontal_file($label, $field, $attributes = NULL, $value = NULL, $column_size = NULL) {
	return bootstrap_default_file('horizontal', $label, $field, $attributes, $value, $column_size);
}
function bootstrap_horizontal_select($label, $field, $items, $attributes = NULL, $value = NULL, $column_size = NULL) {
	return bootstrap_default_select('horizontal', $label, $field, $items, $attributes, $value, $column_size);
}
function bootstrap_horizontal_select_multi($label, $field, $items, $attributes = NULL, $value = NULL, $column_size = NULL) {
//
}
function bootstrap_horizontal_phone($label, $field, $calling_code_items, $attributes = NULL, $calling_code_id = NULL, $phone_body = NULL, $column_size = NULL) {
	return bootstrap_default_phone('horizontal', $label, $field, $calling_code_items, $attributes, $calling_code_id, $phone_body, $column_size);
}
function bootstrap_horizontal_static($label, $value, $attributes = NULL, $column_size = NULL) {
	return bootstrap_default_static('horizontal', $label, $value, $attributes, $column_size);
}
function bootstrap_horizontal_static_textarea($label, $value, $attributes = NULL, $column_size = NULL) {
	return bootstrap_default_static_textarea('horizontal', $label, $value, $attributes, $column_size);
}
function bootstrap_horizontal_group($text, $column_size = NULL) {
	return bootstrap_default_group('horizontal', $text, $column_size);
}
function bootstrap_horizontal_group_start($column_size = NULL) {
	return bootstrap_default_group_start('horizontal', $column_size);
}
function bootstrap_horizontal_group_end($column_size = NULL) {
	return bootstrap_default_group_end('horizontal', $column_size);
}

function bootstrap_horizontal_korean_cellphone($label, $field, $korea_telecom_numbers, $attributes = NULL, $korean_telecom_number = NULL, $phone_body = NULL, $column_size = NULL) {
	if (is_null($column_size)) $column_size = 2;

	$_left_column_size = $column_size;
	$_right_column_size = 12 - $column_size;
	$_telecom_number_size = 3;
	$_telecom_number_body = 12 - $column_size - $_telecom_number_size;
	$_sr_only = FALSE;
	$_required = FALSE;
	$_disable_help_block = FALSE;

	$_korea_telecom_numbers = array();
	foreach ($korea_telecom_numbers as $korea_telecom_number) {
		$_korea_telecom_numbers[$korea_telecom_number] = $korea_telecom_number;
	}

	if (($pos = strpos($field, '[')) > 0) {
		$_field = substr($field, 0, $pos);
		$_index = substr($field, $pos, strlen($field) - $_field);

		$_indexes = array();
		if (preg_match_all('/\[(.+)\]/U', $_index, $matches)) {
			foreach($matches[1] as $match) {
				$_indexes[] = $match;
			}
		}
		if (count($_indexes) > 1) return FALSE;

		$field_telecom_number = $_field.'['.$_indexes[0].'_telecom_number]';
		$field_phone_body = $_field.'['.$_indexes[0].'_phone_body]';

	} else {
		$field_telecom_number = $field.'_telecom_number';
		$field_phone_body = $field.'_phone_body';
	}

	$_default_attributes = array('id' => get_field_id($field), 'class' => array('form-control'), 'placeholder' => '9999-9999');

	if (is_array($attributes)) {
		// remove sr-only
		$_sr_only = element('sr-only', $attributes);
		$attributes = array_diff_key($attributes, array('sr-only' => TRUE));
		// remove disable-help-block
		$_disable_help_block = element('disable-help-block', $attributes);
		$attributes = array_diff_key($attributes, array('disable-help-block' => TRUE));
		// required
		$_required = element('required', $attributes);

		$attributes = array_merge($_default_attributes, $attributes);
	} else if (is_null($attributes) || !is_array($attributes) || !is_string($attributes)) {
		$attributes = $_default_attributes;
	}

	//$_telecom_number_attributes = $attributes;
	$_telecom_number_attributes['class'] = 'form-control';
	$_s = '<div class="form-group'.(is_error_field($field_telecom_number) || is_error_field($field_phone_body) ? ' has-error':'').(empty($_required) ? '':' required').'">'."\n";
	$_s .= form_label($label, get_field_id($field), array('class' => 'control-label'.($_sr_only ? ' sr-only':' col-xs-12 col-sm-'.$_left_column_size)))."\n";
	$_s .= '<div class="'.($_sr_only ? 'col-xs-3':'col-xs-3 col-sm-'.$_telecom_number_size).'">'."\n";
	$_s .= form_dropdown($field_telecom_number, $_korea_telecom_numbers, $korean_telecom_number, field_attributes($_telecom_number_attributes))."\n";
	$_s .= '</div>';
	$_s .= '<div class="'.($_sr_only ? 'col-xs-9':'col-xs-9 col-sm-'.$_telecom_number_body).'">'."\n";
	$_s .= call_user_func_array('form_input', array($field_phone_body, $phone_body, field_attributes($attributes)))."\n";
	$_s .= form_error($field_phone_body, '<span class="help-block">', '</span>');
	$_s .= '</div>';
	$_s .= '</div>'."\n";
	return $_s;
}
function bootstrap_default_korean_post_code($kind, $label, $field, $attributes = NULL, $value = NULL, $column_size = NULL) {
	if (is_null($column_size)) $column_size = 2;
	$input = 'input';

	$_default_attributes = array('id' => get_field_id($field), 'class' => array('form-control'), 'placeholder' => $label, 'button-label' => l('find post code'));
	$_sr_only = FALSE;
	$_required = FALSE;
	$_input_group_addon = FALSE;
	$_disable_help_block = TRUE;
	$_data_post_code = NULL;
	$_data_address = NULL;
	if (is_null($attributes)) {
		$attributes = $_default_attributes;
	} else if (is_array($attributes)) {
		// remove sr-only
		$_sr_only = element('sr-only', $attributes);
		$attributes = array_diff_key($attributes, array('sr-only' => TRUE));
		// remove input-group-addon
		$_input_group_addon = element('input-group-addon', $attributes);
		$attributes = array_diff_key($attributes, array('input-group-addon' => TRUE));
		// remove disable-help-block
		$_disable_help_block = element('disable-help-block', $attributes);
		$attributes = array_diff_key($attributes, array('disable-help-block' => TRUE));
		// required
		$_required = element('required', $attributes);

		$_data_post_code = element('data-post-code', $attributes);
		$attributes = array_diff_key($attributes, array('data-post-code' => TRUE));

		$_data_address = element('data-address', $attributes);
		$attributes = array_diff_key($attributes, array('data-address' => TRUE));

		$attributes = array_merge($_default_attributes, $attributes);
	}

	$_s = '<div class="form-group'.(is_error_field($field) ? ' has-error':'').(empty($_required) ? '':' required').'">'."\n";
	if ($kind == 'horizontal') {
		$_left_column_size = $column_size;
		$_right_column_size = 12 - $column_size - 5;
		$_s .= form_label($label, get_field_id($field), array('class' => 'control-label'.($_sr_only ? ' sr-only':' col-xs-12 col-sm-'.$_left_column_size)))."\n";
		$_s .= '<div class="'.($_sr_only ? 'col-xs-6 col-sm-7':'col-xs-6 col-sm-'.$_right_column_size).'">'."\n";
		if ($_input_group_addon) $_s .= '<div class="input-group"><span class="input-group-addon">'.$_input_group_addon.'</span>';
		$_s .= call_user_func_array('form_'.$input, array($field, $value, field_attributes($attributes)))."\n";
		if ($_input_group_addon) $_s .= '</div>';
		if (!$_disable_help_block) $_s .= form_error($field, '<span class="help-block">', '</span>');
		$_s .= '</div>'."\n";
		$_s .= '<div class="col-xs-6 col-sm-5 text-left">'."\n";

		$_button_attributes = array('type' => 'button', 'class' => 'btn btn-default', 'data-action' => 'daum_postcode');
		if (!empty($_data_post_code)) $_button_attributes['data-post-code'] = $_data_post_code;
		if (!empty($_data_address)) $_button_attributes['data-address'] = $_data_address;
		$_s .= button_tag(bootstrap_icon_text('search', $attributes['button-label']), $_button_attributes);
//		$_s .= button_tag(bootstrap_icon_text('search', $attributes['button-label']), array('type' => 'button', 'class' => 'btn btn-default', 'data-action' => 'daum_postcode'/*, 'onclick' => 'showDaumPostcode();return false;'*/));
#		$_s .= '<a href="#" class="btn btn-default" onclick="showDaumPostcode();return false;" >'.$attributes['button-label'].'</a>'."\n";
		$_s .= '</div>';
	} else {
		$_s .= form_label($label, get_field_id($field), array('class' => 'control-label'.($_sr_only ? ' sr-only':'')))."\n";
		if ($_input_group_addon) $_s .= '<div class="input-group"><span class="input-group-addon">'.$_input_group_addon.'</span>';
		$_s .= call_user_func_array('form_'.$input, array($field, $value, field_attributes($attributes)))."\n";
		if ($_input_group_addon) $_s .= '</div>';
		if (!$_disable_help_block) $_s .= form_error($field, '<span class="help-block">', '</span>');
		$_s .= '<a href="#" class="btn btn-default" onclick="showDaumPostcode();return false;" >'.$attributes['button-label'].'</a>'."\n";
	}
	$_s .= '</div>'."\n";
	
	return $_s;
}
function bootstrap_basic_korean_post_code($label, $field, $attributes = NULL, $value = NULL) {
	return bootstrap_default_korean_post_code('horizontal', $label, $field, $attributes, $value);
}
function bootstrap_horizontal_korean_post_code($label, $field, $attributes = NULL, $value = NULL, $column_size = NULL) {
	return bootstrap_default_korean_post_code('horizontal', $label, $field, $attributes, $value, $column_size);
}

// pagination
function bootstrap_pagination($pages) {
	$_s = '<nav>';

	if (count($pages->pages) > 0) {
		$_s .= '<ul class="pagination">'."\n";
		foreach($pages->pages as $page) {
			$_s .= isset($page->uri) ? '<li '.($page->kind == 'here'? 'class="active"':'').'>'.anchor_tag($page->uri, $page->text, NULL, $page->params).'</li>' : '<li class="disabled"><a href="#">'.$page->text.'</a></li>';
		}
		$_s .= '</ul>';
	}
	$_s .= '</nav>'."\n";
	return $_s;
}
function bootstrap_pager($pages) {
	$_s = '<nav>';
	if (isset($pages->prev_page) && isset($pages->next_page)) {
		$_s .= '<ul class="pager">'."\n";
		$_s .= '<li class="previous'.($pages->prev_page->kind == 'enabled' ? '':' disabled').'">'.($pages->prev_page->kind == 'enabled' ? anchor_tag($pages->prev_page->uri, $pages->prev_page->text, NULL, $pages->prev_page->params) : anchor_tag('#', $pages->prev_page->text)).'</li>';
		$_s .= '<li class="next'.($pages->next_page->kind == 'enabled' ? '':' disabled').'">'.($pages->next_page->kind == 'enabled' ? anchor_tag($pages->next_page->uri, $pages->next_page->text, NULL, $pages->next_page->params) : anchor_tag('#', $pages->next_page->text)).'</li>';
		$_s .= '</ul>';
	}
	$_s .= '</nav>'."\n";
	return $_s;
}
function bootstrap_pagination_pager($pages, $size = NULL) {
	if (is_null($size)) $size = 'xs';
	$_s = '<div class="pagination hidden-'.$size.'">'."\n";
	$_s .= bootstrap_pagination($pages)."\n";
	$_s .= '</div><div class="pager visible-'.$size.'">'."\n";
	$_s .= bootstrap_pager($pages)."\n";
	$_s .= '</div>'."\n";
	return $_s;
}

// modal
function bootstrap_modal_size($width) {
	$_size = NULL;
	if ($width > 870) {	// modal-lg
		$_size = 'modal-lg';
	} else if ($width < 270) {	// modal-sm
		$_size = 'modal-sm';
	}
	return $_size;
}
function bootstrap_modal_content_size($width) {
	$_width = $width;
	if ($_width > 870) {	// modal-lg
		$_width = 870;
	} else if ($_width > 570) {
		$_width = 570;
	} else if ($_width > 270) {	// modal-sm
		$_width = 270;
	}

	return $_width;
}
function bootstrap_responsive_ratio($width, $height) {
	return 'embed-responsive-'.(($height == 0 ? 0 : $width / $height) > (16 / 9) ? '16by9' : '4by3');
}
/* EOF */
