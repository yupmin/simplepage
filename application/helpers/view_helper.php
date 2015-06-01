<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
function get_script_csrf_token() {
	$CI =& get_instance();
	$_csrf_token = $CI->_get_csrf_token();
	if (!empty($_csrf_token)):
?>
<script>
var csrf_token = '<?php echo $_csrf_token;?>';
</script>
<?php
	endif;
}

function get_htmlcode_time($time, $date_format = NULL, $attributes = NULL) {
	if (is_null($date_format)) $date_format = 'date_format2';
	if (!function_exists($date_format)) return FALSE;

	return empty($time) ? '&nbsp' : '<time datetime="' . mdate('%Y-%m-%dT%h:%i%P', $time).'">'.date_format2($time).'</time>';
}