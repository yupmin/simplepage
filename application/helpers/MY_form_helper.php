<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
function form_input($data = NULL, $value = NULL, $extra = NULL) {
	if ((is_array($data) && isset($data['name']))) {
		$name = is_array($data) && isset($data['name']) ? $data['name'] : $data;
		if (isset($data['type']) && $data['type'] != 'password')
			$value = set_value($name, $value, FALSE);
	} else if (is_string($data)){
		$value = set_value($data, $value, FALSE);
	}

	$defaults = array('type' => 'text', 'name' => (( ! is_array($data)) ? $data : ''), 'value' => $value);

	return '<input '._parse_form_attributes($data, $defaults).$extra.' />';
}
function form_password($data = NULL, $value = NULL, $extra = NULL) {
	if (!is_array($data))
		$data = array('name' => $data);

	$data['type'] = 'password';
	return form_input($data, $value, $extra);
}
function form_file($data = NULL, $value = NULL, $extra = NULL) {
	if (!is_array($data))
		$data = array('name' => $data);

	$data['type'] = 'file';
	return form_input($data, $value, $extra);
}
function form_email($data = NULL, $value = NULL, $extra = NULL) {
	if (!is_array($data))
		$data = array('name' => $data);

	$data['type'] = 'email';
	return form_input($data, $value, $extra);
}
function form_search($data = NULL, $value = NULL, $extra = NULL) {
	if (!is_array($data))
		$data = array('name' => $data);

	$data['type'] = 'search';
	return form_input($data, $value, $extra);
}
function form_date($data = NULL, $value = NULL, $extra = NULL) {
	if (!is_array($data))
		$data = array('name' => $data);

	$data['type'] = 'date';
	return form_input($data, $value, $extra);
}
function form_datetime($data = NULL, $value = NULL, $extra = NULL) {
	if (!is_array($data))
		$data = array('name' => $data);

	$data['type'] = 'datetime';
	return form_input($data, $value, $extra);
}
function form_textarea($data = NULL, $value = NULL, $extra = NULL) {
	$value = set_value($data, $value, FALSE);

//	$defaults = array('name' => (( ! is_array($data)) ? $data : ''), 'cols' => '40', 'rows' => '10');
	$defaults = array('name' => (( ! is_array($data)) ? $data : ''));

	if ( ! is_array($data) OR ! isset($data['value']))
		$val = $value;
	else {
		$val = $data['value'];
		unset($data['value']); // textareas don't use the value attribute
	}

	$name = (is_array($data)) ? $data['name'] : $data;
	return "<textarea "._parse_form_attributes($data, $defaults).$extra.">".form_prep($val, $name)."</textarea>";
}
function form_radio($data = NULL, $value = NULL, $checked = NULL, $extra = NULL) {
	if ( ! is_array($data)) {
		$data = array('name' => $data);
	}

	$data['type'] = 'radio';
	return form_checkbox($data, $value, $checked, $extra);
}
function form_checkbox($data = NULL, $value = NULL, $checked = NULL, $extra = NULL) {
	$defaults = array('type' => 'checkbox', 'name' => ( ! is_array($data) ? $data : ''), 'value' => $value);

	// customize -->
	$_field_name = NULL;
	if (is_string($data) && ($pos = strpos($data, '[')) > 0) {
		$_field_name = $data;
	} else if (is_array($data) && isset($data['name'])) {
		$_field_name = $data['name'];
	}
	if (!empty($_field_name) && ($pos = strpos($_field_name, '[')) > 0) {
		$_name = substr($_field_name, 0, $pos);
		$_index = substr($_field_name, $pos, strlen($_field_name) - $_name);
		$_values = NULL;
		if (isset($_POST[$_name])) {
			$_values = $_POST[$_name];
			if (preg_match_all('/\[(.+)\]/U', $_index, $matches)) {
				foreach($matches[1] as $_index) {
					if (isset($_value[$_index])) {
						$_values = $_values[$_index];
					}
				}
			}
		}

		if (is_array($_values)) {
			if (array_key_exists($_index, $_values)) {
				$checked = $_values[$_index] == $value;
			}
		}
	} // <--

	if (is_array($data) && array_key_exists('checked', $data))
	{
		$checked = $data['checked'];

		if ($checked == FALSE)
		{
			unset($data['checked']);
		}
		else
		{
			$data['checked'] = 'checked';
		}
	}
	// customize
	else if (is_array($data) && $value == $checked) {
		$data['checked'] = 'checked';
	}

	if ($checked === TRUE)
	{
		$defaults['checked'] = 'checked';
	}
	else
	{
		unset($defaults['checked']);
	}

	return '<input '._parse_form_attributes($data, $defaults).$extra." />\n";
}
function form_dropdown($data = NULL, $options = NULL, $selected = NULL, $extra = NULL) {
	if (is_null($options)) $options = array();
	if (is_null($selected)) $selected = array();

	$defaults = array();

	if (is_array($data))
	{
		if (isset($data['selected']))
		{
			$selected = $data['selected'];
			unset($data['selected']); // select tags don't have a selected attribute
		}

		if (isset($data['options']))
		{
			$options = $data['options'];
			unset($data['options']); // select tags don't use an options attribute
		}
	}
	else
	{
		$defaults = array('name' => $data);
	}

	// customize -->
	if (($pos = strpos($data, '[')) > 0) {
		$_name = substr($data, 0, $pos);
		$_index = substr($data, $pos, strlen($data) - $_name);
		if (isset($_POST[$_name])) {
			$selected = $_POST[$_name];
			if (preg_match_all('/\[(.+)\]/U', $_index, $matches)) {
				foreach($matches[1] as $match) {
					if (isset($selected[$match]))
						$selected = $selected[$match];
				}
			}
			$selected = array($selected);
		}
	} // <--

	is_array($selected) OR $selected = array($selected);
	is_array($options) OR $options = array($options);

	// If no selected state was submitted we will attempt to set it automatically
	if (empty($selected))
	{
		if (is_array($data))
		{
			if (isset($data['name'], $_POST[$data['name']]))
			{
				$selected = array($_POST[$data['name']]);
			}
		}
		elseif (isset($_POST[$data]))
		{
			$selected = array($_POST[$data]);
		}
	}

	$extra = _attributes_to_string($extra);

	$multiple = (count($selected) > 1 && strpos($extra, 'multiple') === FALSE) ? ' multiple="multiple"' : '';

	$form = '<select '.rtrim(_parse_form_attributes($data, $defaults)).$extra.$multiple.">\n";

	foreach ($options as $key => $val)
	{
		$key = (string) $key;

		if (is_array($val))
		{
			if (empty($val))
			{
				continue;
			}

			$form .= '<optgroup label="'.$key."\">\n";

			foreach ($val as $optgroup_key => $optgroup_val)
			{
				$sel = in_array($optgroup_key, $selected) ? ' selected="selected"' : '';
				$form .= '<option value="'.html_escape($optgroup_key).'"'.$sel.'>'
					.(string) $optgroup_val."</option>\n";
			}

			$form .= "</optgroup>\n";
		}
		else
		{
			$form .= '<option value="'.html_escape($key).'"'
				.(in_array($key, $selected) ? ' selected="selected"' : '').'>'
				.(string) $val."</option>\n";
		}
	}

	return $form."</select>\n";
}
/* EOF */