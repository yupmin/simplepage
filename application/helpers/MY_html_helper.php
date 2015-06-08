<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function _tag($tag, $data, $attributes = NULL) {
	$_attributes = '';
	if (is_array($attributes)) {
		$_attributes = field_attributes($attributes);
	} else if (is_string($attributes)) {
		$_attributes = ' '.$attributes;
	}

	return '<'.$tag.$_attributes.">".$data."</".$tag.">";
}
function button_tag($data, $attributes = NULL) {
	return _tag('button', $data, $attributes);
}
function anchor_tag($href, $text = NULL, $attributes = NULL, $query_data = NULL) {
	if (is_null($attributes)) $attributes = array();

	if (is_array($href)) {
		$href = url_for($href, $query_data);
	} else {
		if (preg_match('/^#.*/', $href) != FALSE) {
			// nothing
		} else if (!preg_match('!^\w+://! i', $href)) {
			$href = url_for($href, $query_data);
		}
	}
	if (strlen($text) == 0) $text = $href;

	$attributes = array_merge(array('href' => $href), $attributes);
	return _tag('a', $text, $attributes);
}
function time_tag($timestamp, $date_format_kind = NULL) {
	if (is_null($date_format_kind)) $date_format_kind = '2';

	return _tag('time', call_user_func('date_string', $timestamp, $date_format_kind), array('datetime' => standard_date('DATE_W3C', $timestamp)));
}
function image_tag($src, $attributes = NULL) {
	if (is_null($attributes)) $attributes = array();
	if (!isset($attributes['alt'])) $attributes['alt'] = '';
	$attributes['src'] = $src;

	return  '<img'.field_attributes($attributes).' />';
}
/* EOF */