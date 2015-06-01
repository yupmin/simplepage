function get_url_ext(url, ext) {
	var old_ext = $.url('fileext', url);
	var path = $.url('path', url);
	var query = $.url('?', url);
	var pos = path.lastIndexOf(old_ext);
	// remove ext
	if (pos) path = path.slice(0, pos);
	if (typeof ext != 'undefined' && ext.length) path += '.' + ext;
	if (typeof query != 'undefined' && query.length) path += '?' + query;

	return path;
}
function is_confirm(by_confirm) {
	if (typeof by_confirm == 'undefined') by_confirm = false;
	if (by_confirm == true.toString()) by_confirm = 'Are you sure?';
	if (!by_confirm) return true;
	return confirm(by_confirm);
}
function get_csrf_token() {
	return typeof csrf_token == 'undefined' ? null : csrf_token;
}

var delay_time = 5000;
var slide_up_time = 200;
function show_alert(type, message){
	var alert_message = $('<div class="alert alert-' + type + '"><button type="button" class="close" data-dismiss="alert">&times;</button>'+message+'</div>');
	alert_message.delay(delay_time).slideUp(slide_up_time, function() { $(this).alert('close'); });
	$('#alert_message').append(alert_message);

	return true;
}
$("#alert_message .alert").delay(delay_time).slideUp(slide_up_time, function() { $(this).alert('close'); });

$('[data-action=search_form]').submit(function(e) {
	var keyword = $(this).find('input[name=keyword]');
	if (!keyword.val().length) { keyword.focus(); return false; }
});

$(function () { $('[data-toggle="tooltip"]').tooltip() });
//$(document).on('mouseover', '[data-toggle=tooltip]', function(e) {
//	console.log(this);
//	$(this).tooltip();
//});

$(document).on('click', '[data-action=cancel]', function(e) {
	e.preventDefault();
	e.stopPropagation();

	var url = $(this).attr('data-url');
	var by_confirm = $(this).attr('data-confirm');
	if (typeof by_confirm == 'undefined') by_confirm = false;
	if (by_confirm == true.toString()) by_confirm = 'Are you sure?';
	if ((by_confirm && confirm(by_confirm)) || !by_confirm) {
		if (url.length)
			history.back();
		else
			$(location).attr('href',url);
	}
});
/*
prifix :
- post_
- post_form_
- ajax_
- ajax_post_
- modal_
- modal_ajax_
- modal_ajax_post_
- ajax_file_
_ ajax_file_post_

tag :
- form
- anchor
- file

o post_anchor : a[data-action=post]
o post_form_anchor : a[data-action=post_form]
o ajax_post_anchor : a[data-action=ajax_post]
o ajax_form : form[data-action=ajax_form]
  ajax_form_anchor : a[data-action=ajax_form]
  ajax_form_button : button[data-action=ajax_form]
o modal_ajax_anchor : a[data-action=modal_ajax]
  modal_ajax_button : button[data-action=modal_ajax]
  modal_ajax_form : form[data-action=modal_ajax_form]
o modal_ajax_form_button : button[data-action=modal_ajax_form]
  modal_ajax_form_anchor : a[data-action=modal_ajax_form]
  ajax_file_anchor : a[data-action=ajax_file]
o ajax_file_post_anchor : a[data-action=ajax_file_post]
*/
$(document).on('click', 'a[data-action=post]', function(e) {
	e.preventDefault();
	e.stopPropagation();

	var this_tag = $(this);
	var href = this_tag.attr('href');
	var token = get_csrf_token();
	var form_id = this_tag.attr('data-form-id');
	if (typeof form_id == 'undefined') form_id = 'my_form';
	var by_confirm = this_tag.attr('data-confirm');
	if (is_confirm(by_confirm)) {
		if ($('#'+form_id)) {
			var form = $('<form />');
			form.hide();
			form.attr('id', form_id);
			form.append($('<input type="hidden" />').attr('name', 'csrf_token').attr('value', token));
			$('body').append(form);
		}
		form.attr({'action' : href, 'method':'post'});
		form.submit();
	}
});
$(document).on('click', 'a[data-action=post_form]', function(e) {
	e.preventDefault();
	e.stopPropagation();

	var this_tag = $(this);
	var href = this_tag.attr('href');
	var form = $(this).closest('form');
	var by_confirm = $(this).attr('data-confirm');
	if (is_confirm(by_confirm)) {
		form.attr('action', href);
		form.submit();
	}
});
$(document).on('click', 'a[data-action=ajax_post]', function(e) {
	e.preventDefault();
	e.stopPropagation();

	var this_tag = $(this);
	var href = this_tag.attr('href');
	var ext = this_tag.attr('data-action-ext');
	if (typeof ext == 'undefined') ext = 'json';
	var token = get_csrf_token();
	var ajax_data = {'csrf_token' : token};
	var old_inner_html;
	var by_confirm = this_tag.attr('data-confirm');
	if (is_confirm(by_confirm)) {
		href = get_url_ext(href, ext);

		this_tag.attr('disabled', true);

		$.post(href, ajax_data, function(response) {
			if (response.error) {
				show_alert('danger', response.message);
			} else {
				if (typeof response.result != 'undefined' && 'target_url' in response.result) {
					$.get(response.result.target_url, null, function(response) {
						$.each(response.result.partial, function(key, value) {
							$('#'+key).html(value);
						});
					}, ext);
				}
				show_alert('success', response.message);
			}
		}, ext);
		this_tag.attr('disabled', false);
	}
});
$(document).on('submit', 'form[data-action=ajax_form]', function(e) {
	e.preventDefault();
	e.stopPropagation();

	var this_form = $(this);
	var action = this_form.attr('action');
	var ext = this_form.attr('data-action-ext');
	if (typeof ext == 'undefined') ext = 'json';
	var ajax_data = this_form.serialize();
	var by_confirm = this_form.attr('data-confirm');
	if (is_confirm(by_confirm)) {
		action = get_url_ext(action, ext);

		this_form.find('[type=submit]').button('loading');

		$.post(action, ajax_data, function(response) {
			if (response.error) {
				if (typeof response.result != 'undefined' && 'partial' in response.result) {
					$.each(response.result.partial, function(key, value) {
						$('#'+key).html(value);
					});
				}
				show_alert('danger', response.message);
			} else {
				if (typeof response.result != 'undefined' && 'target_url' in response.result) {
					$.get(response.result.target_url, null, function(response) {
						$.each(response.result.partial, function(key, value) {
							$('#'+key).html(value);
						});
					}, ext);
				}
				show_alert('success', response.message);
				this_form.trigger('reset');
			}
		}, ext);

		this_form.find('[type=submit]').button('reset');
	}
});
$(document).on('click', 'a[data-action=modal_ajax]', function(e) {
	e.preventDefault();
	e.stopPropagation();

	var this_tag = $(this);
	var ext = this_tag.attr('data-action-ext');
	if (typeof ext == 'undefined') ext = 'json';
	var href = this_tag.attr('href');
	var modal_id = this_tag.attr('data-modal-id');
	if (typeof modal_id == 'undefined') modal_id = 'my_modal';
	var modal_size = this_tag.attr('data-modal-size');
	if (typeof modal_size == 'undefined') modal_size = '';
	var token = get_csrf_token();
	var ajax_data = {'csrf_token' : token};
	var old_inner_html;
	var by_confirm = this_tag.attr('data-confirm');
	if (is_confirm(by_confirm)) {
		href = get_url_ext(href, ext);

		this_tag.button('loading');

		$.get(href, ajax_data, function(response) {
			if (response.error) {
				show_alert('danger', response.message);
			} else {
				if (!$('#'+modal_id).length) {
					var modal = $('<div class="modal fade"tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"></div></div></div>');
					modal.attr('id', modal_id);
					modal.on('hidden.bs.modal', function(e) {
						//console.log(this_tag);
						this_tag.html(old_inner_html);
						this_tag.attr('disabled', false);
					});

					$('body').append(modal);
				}

				if (typeof response.result.partial.modal_form != 'undefined') {
					var modal_form = response.result.partial.modal_form;
					$('#'+modal_id+' .modal-dialog').removeClass('modal-lg').removeClass('modal-sm').addClass(modal_size);
					$('#'+modal_id+' .modal-content').html(modal_form);
					$('#'+modal_id).modal({'show':true, 'keyboard':false, 'backdrop':'static'});
				}
			}
		}, ext);

		this_tag.button('reset');
	}
});
$(document).on('click', 'button[data-action=modal_ajax_form]', function(e) {
	e.preventDefault();
	e.stopPropagation();

	var this_tag = $(this);
	var form = this_tag.closest('form');
	var action = form.attr('action');
	var ext = this_tag.attr('data-action-ext');
	if (typeof ext == 'undefined') ext = 'json';
	var modal_id = this_tag.attr('data-modal-id');
	if (typeof modal_id == 'undefined') modal_id = 'my_modal';
	var ajax_data = form.serialize();
	var by_confirm = this_tag.attr('data-confirm');
	if (is_confirm(by_confirm)) {
		action = get_url_ext(action, ext);

		this_tag.button('loading');

		$.post(action, ajax_data, function(response) {
			if (response.error) {
				if (typeof response.result.partial.modal_form != 'undefined') {
					var modal_form = response.result.partial.modal_form;
					$('#'+modal_id+' .modal-content').html(modal_form);
				}
				show_alert('danger', response.message);
			} else {
				if (typeof response.result != 'undefined' && 'target_url' in response.result) {
					$.get(response.result.target_url, null, function(response) {
						$.each(response.result.partial, function(key, value) {
							$('#'+key).html(value);
						});
					}, ext);
				}
				show_alert('success', response.message);
				$('#'+modal_id).modal('hide');
			}
		}, ext);

		this_tag.button('reset');
	}
});
$(document).on('submit', 'form[data-action=modal_ajax_form]', function(e) {
	e.preventDefault();
	e.stopPropagation();

	var this_form = $(this);
	var action = this_form.attr('action');
	var ext = this_form.attr('data-action-ext');
	if (typeof ext == 'undefined') ext = 'json';
	var modal_id = this_form.attr('data-modal-id');
	if (typeof modal_id == 'undefined') modal_id = 'my_modal';
	var ajax_data = this_form.serialize();
	var by_confirm = this_form.attr('data-confirm');
	if (is_confirm(by_confirm)) {
		action = get_url_ext(action, ext);

		$.post(action, ajax_data, function(response) {
			if (response.error) {
				if (typeof response.result != 'undefined'
					&& typeof response.result.partial != 'undefined'
					&& typeof response.result.partial.modal_form != 'undefined') {
					var modal_form = response.result.partial.modal_form;
					$('#'+modal_id+' .modal-content').html(modal_form);
				}
				show_alert('danger', response.message);
			} else {
				if (typeof response.result != 'undefined' && 'target_url' in response.result) {
					$.get(response.result.target_url, null, function(response) {
						$.each(response.result.partial, function(key, value) {
							$('#'+key).html(value);
						});
					}, ext);
				}
				show_alert('success', response.message);
				$('#'+modal_id).modal('hide');
			}
		}, ext);
	}
});

// reference : https://www.new-bamboo.co.uk/blog/2012/01/10/ridiculously-simple-ajax-uploads-with-formdata/
function supportAjaxUploadWithProgress() {
	return supportFileAPI() && supportAjaxUploadProgressEvents() && supportFormData();

	function supportFileAPI() {
		var fi = document.createElement('INPUT');
		fi.type = 'file';
		return 'files' in fi;
	};

	function supportAjaxUploadProgressEvents() {
		var xhr = new XMLHttpRequest();
		return !! (xhr && ('upload' in xhr) && ('onprogress' in xhr.upload));
	};

	function supportFormData() {
		return !! window.FormData;
	}
}
function getIframeContentJSON(iframe){
	//IE may throw an "access is denied" error when attempting to access contentDocument on the iframe in some cases
	try {
		// iframe.contentWindow.document - for IE<7
		var doc = iframe.contentDocument ? iframe.contentDocument: iframe.contentWindow.document, response;

		var innerHTML = doc.body.innerHTML;
		//plain text response may be wrapped in <pre> tag
		if (innerHTML.slice(0, 5).toLowerCase() == "<pre>" && innerHTML.slice(-6).toLowerCase() == "</pre>") {
			innerHTML = doc.body.firstChild.firstChild.nodeValue;
		}
		response = eval("(" + innerHTML + ")");
	} catch(err){
		response = {success: false};
	}

	return response;
}

// multiple modal
/*$(document).on('hidden.bs.modal', function (e) {
	$(this).removeClass('fv-modal-stack');
	$('body').data('fv_open_modals', $('body').data('fv_open_modals') - 1);
});
$(document).on('shown.bs.modal', function (e) {
	// keep track of the number of open modals
	if (typeof( $('body').data('fv_open_modals') ) == 'undefined') {
		$('body').data('fv_open_modals', 0);
	}

	// if the z-index of this modal has been set, ignore.
	if ($(this).hasClass('fv-modal-stack')) {
		return;
	}

	$(this).addClass('fv-modal-stack');
	$('body').data('fv_open_modals', $('body').data('fv_open_modals') + 1);
	$(this).css('z-index', 1040 + (10 * $('body').data('fv_open_modals')));
	$('.modal-backdrop').not('.fv-modal-stack').css('z-index', 1039 + (10 * $('body').data('fv_open_modals')));
	$('.modal-backdrop').not('.fv-modal-stack').addClass('fv-modal-stack');
});*/

/*$(document).on({
    'show.bs.modal': function () {
        var zIndex = 1040 + (10 * $('.modal:visible').length);
        $(this).css('z-index', zIndex);
        setTimeout(function() {
            $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
        }, 0);
    },
    'hidden.bs.modal': function() {
        if ($('.modal:visible').length > 0) {
            // restore the modal-open class to the body element, so that scrolling works
            // properly after de-stacking a modal.
            setTimeout(function() {
                $(document.body).addClass('modal-open');
            }, 0);
        }
    }
}, '.modal');*/

$(document).on('click', 'a[data-action=ajax_file_post]', function(e) {
	e.preventDefault();
	e.stopPropagation();

	var this_tag = $(this);
	var href = this_tag.attr('href');
	var ext = this_tag.attr('data-action-ext');
	if (typeof ext == 'undefined') ext = 'json';
	var token = get_csrf_token();
	var input_group_div = this_tag.parent().parent();
	var file_input = input_group_div.find('input[type=file]');
	var file_datas = $(file_input).prop('files');

	if (file_datas.length) {
		file_data = file_datas[0];

		if (supportAjaxUploadWithProgress()) {
			//console.log(file_input.attr('name'));
			var form_data = new FormData();
			form_data.append(file_input.attr('name'), file_data);
			form_data.append('csrf_token', token);
			href = get_url_ext(href, ext);
			console.log(form_data);

			// progress start
			var progress_div = input_group_div.parent().find('.progress');
			if (!progress_div.length) {
				var progress_div = $('<div class="progress"><div class="progress-bar" role="progessbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="min-width: 2em;" ">0%</div></div>');
				progress_div.insertBefore(input_group_div);
				var progress_bar = progress_div.find('.progress-bar');
			} else {
				var progress_bar = progress_div.find('.progress-bar');
				progress_bar.attr('aria-valuenow', 0);
				progress_bar.width('0%');
				progress_bar.text('0%');
				progress_div.fadeIn(500);
			}

			// spinngin_span
			var spinning_span = input_group_div.find('span.glyphicon-upload');
			progress_bar.removeClass('progress-bar-danger');
			spinning_span.removeClass('glyphicon-upload').addClass('glyphicon-refresh glyphicon-spin');
			this_tag.button('loading');

			$.ajax({
				type: 'post',
				url: href,
				data: form_data,
				dataType: ext,
				cache: false,
				contentType: false,
				processData: false,
				xhr: function() {
					var xhr = new XMLHttpRequest();
					xhr.upload.addEventListener('progress', function (e) {
						console.log(e);
						if (e.lengthComputable) {
							var progress = Math.ceil((e.loaded / e.total) * 100);
							progress_bar.attr('aria-valuenow', progress);
							progress_bar.width(progress+'%');
							progress_bar.text(progress+'%');
						}
					});
					return xhr;
				},
				success: function(data, textStatus, jqXHR){
					if (data.error) {
						progress_bar.addClass('progress-bar-danger');
						progress_bar.delay(800).text(data.message);
					} else {
						// attachment_list html result.partial
						if (typeof data.result != 'undefined' && 'target_url' in data.result) {
							$.get(data.result.target_url, null, function(response) {
								$.each(response.result.partial, function(key, value) {
									$('#'+key).html(value);
								});
							}, ext);
						}
						progress_div.delay(800).fadeOut(800);
					}
				},
				error: function(jqXHR, textStatus, errorThrown) {
					//console.log(jqXHR, textStatus, errorThrown);
					progress_bar.attr('class', 'progress-bar progress-bar-danger');
					progress_bar.text(textStatus);
				},
				complete: function(jqXHR, textStatus) {
					// stop spinning
					spinning_span.removeClass('glyphicon-refresh glyphicon-spin').addClass('glyphicon-upload');
					file_input.val('').clone(true);
					this_tag.button('reset');
				}
			});
			return;
		}
		// IE 8,9
		var iframe_id = 'ajax_upload_iframe';
		var iframe = $('<iframe width="0" height="0" border="0" src="javascript: false;" style="display: none;"/>');
		iframe.attr('id', iframe_id);
		iframe.attr('name', iframe_id);
		var iframe_form_id = 'ajax_upload_iframe_form';
		var iframe_form = $('<form method="POST" enctype="multipart/form-data" encoding="multipart/form-data" style="display: none;" />');
		iframe_form.attr('id', iframe_form_id);
		iframe_form.attr('action', target);
		iframe_form.attr('target', iframe_id);
		iframe_form.append($('<input />').attr('type', 'hidden').attr('name', 'csrf_token').attr('value', token));
		iframe_form.append(file_data);
//		console.log(iframe_form);
		if (!$('#'+iframe_id).length) {
			$('body').append(iframe);
		}
		if (!$('#'+iframe_form_id).length) {
			$('body').append(iframe_form);
		}
		// progress start
		// Add event...
		var eventHandlerIframe = function () {
			//console.log('123');
			if (iframe.detachEvent)
				iframe.detachEvent("onload", eventHandlerIframe);
			else
				iframe.removeEventListener("load", eventHandlerIframe, false);

			response = getIframeContentJSON(iframe);
			//console.log(response);
		}
		//console.log(iframe.addEventListener);
		//console.log(iframe.attachEvent);
		if (iframe.addEventListener)
			iframe.addEventListener("load", eventHandlerIframe, true);
		if (iframe.attachEvent)
			iframe.attachEvent("onload", eventHandlerIframe);

		iframe_form.submit();
	}
});
/* EOF */