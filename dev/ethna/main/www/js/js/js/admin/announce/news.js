$(function(){
	tinymce.init({
		selector: "textarea.tinymce",
		menubar: false,
		statusbar : false,
		plugins: ["textcolor", "code"],
		toolbar: "bold | forecolor | code",
		forced_root_block : false,
		min_height: 150,
		content_css: "/css/admin/announce/tinymce-inverse.css"
	});

	tinymce.init({
		selector: "input.tinymce",
		menubar: false,
		statusbar : false,
		plugins: ["textcolor", "code"],
		toolbar: "bold | forecolor | code",
		forced_root_block : false,
		min_height: 40,
		content_css: "/css/admin/announce/tinymce-inverse.css"
	});
	
	$('.admin-announce-news-content-datetimepicker').click(function(){
		$('.admin-announce-news-content-tinymce-body').css('display', 'none');
		$('.admin-announce-news-content-tinymce-body-dummy').css('display', 'block');
	});
	
	$('.admin-announce-news-content-datetimepicker').datetimepicker({
		timeText: '時刻',
		hourText: '時',
		minuteText: '分',
		secondText: '秒',
		closeText: '確定',
		dateFormat: 'yy-mm-dd',
		timeFormat: 'HH:mm:ss',
		onClose: function(datetimeText, datepickerInstance) {
			$('.admin-announce-news-content-tinymce-body-dummy').css('display', 'none');
			$('.admin-announce-news-content-tinymce-body').css('display', 'block');
		}			
	});
});
