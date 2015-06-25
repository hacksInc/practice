<!-- Le javascript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="/psychopass_game/js/jquery-1.9.1.min.js"></script>
<script src="/psychopass_game/js/jquery-ui-1.10.3.custom.min.js"></script>
<script src="/psychopass_game/js/jquery.cookie.js"></script>
<script src="/psychopass_game/js/bootstrap.min.js"></script>

{*******************************************************************************
 * datepickerについて
 * 
 * Boostrapのdatepickerとjquery-uiのdatepickerの2種類がある。
 * ・年月のみを入力させたい場合
 *   Bootstrapのdatepickerを使用する。
 *   jquery-uiのdatepickerだと、onClose等の処理を記述しないといけないので使用しない。
 * ・年月日時分を入力させたい場合
 *   jquery-uiのdatepickerを使用する。
 *   Bootstrapのdatepickerは、ウィンドウの切り替えや閉じる挙動が使いにくいので使用しない。
 * ・年月日を入力させたい場合：
 *   Boostrapのdatepickerでもjquery-uiのでも可。
 * として使い分ける事。
 * 
 * どちらを使用するかは、このSmartyテンプレートをincludeする際、
 * {include file="admin/common/script.tpl" datepicker="jquery"}
 * または 
 * {include file="admin/common/script.tpl"}
 * で指定する。
 *
 * 両方を同一ページ内で使おうとすると、"datepicker"という関数名がかぶるので、
 * 使用するのはページごとにどちらか1種類だけにする事。
 ******************************************************************************}
{if $datepicker == "jquery"}
	<script src="/psychopass_game/js/i18n/jquery.ui.datepicker-ja.js"></script>
	<script src="/psychopass_game/js/jquery-ui-timepicker-addon.js"></script>
	{literal}
	<script>
		$(function(){
			$('.jquery-ui-datepicker').datepicker({
				dateFormat: 'yy-mm-dd',
			});
			
			$('.jquery-ui-datetimepicker').datetimepicker({
				timeText: '時刻',
				hourText: '時',
				minuteText: '分',
				secondText: '秒',
				closeText: '確定',
				dateFormat: 'yy-mm-dd',
				timeFormat: 'HH:mm:ss'
			});
		});
	</script>
	{/literal}
{else}
	<script src="/psychopass_game/js/bootstrap-datepicker.js"></script>
	<script src="/psychopass_game/js/locales/bootstrap-datepicker.ja.js"></script>
	<script src="/psychopass_game/js/bootstrap-datetimepicker.min.js"></script>{* http://tarruda.github.io/bootstrap-datetimepicker/ *}
	{literal}
	<script>
		$(function(){
			$('.datepicker').datepicker({
				language: 'ja',
				autoclose: true,
			    format: 'yyyy-mm-dd',
				minViewMode: 'days'
			});

			$('.monthpicker').datepicker({
				language: 'ja',
				autoclose: true,
			    format: 'yyyy-mm',
				minViewMode: 'months'
			});

			$.fn.datetimepicker.dates['ja'] = $.fn.datepicker.dates['ja'];
		    $('.datetimepicker').datetimepicker({
				language: 'ja',
			    format: 'yyyy-MM-dd hh:mm:ss'
		    });
	    });
	</script>
	{/literal}
{/if}

<script src="/psychopass_game/js/tablesort.js"></script>
<script src="/psychopass_game/js/editablegrid-2.0.1.js"></script>
<script src="/psychopass_game/js/json2.js"></script>
<script src="/psychopass_game/js/underscore.js"></script>
<script src="/psychopass_game/js/backbone.js"></script>
<script src="/psychopass_game/js/php/is_numeric.js"></script>
<script src="/psychopass_game/js/php/strlen.js"></script>
<script src="/psychopass_game/js/php/strcmp.js"></script>
<script src="/psychopass_game/js/php/strncmp.js"></script>
<script src="/psychopass_game/js/php/strpos.js"></script>
<script src="/psychopass_game/js/php/stripos.js"></script>
<script src="/psychopass_game/js/php/base64_encode.js"></script>
<script src="/psychopass_game/js/php/explode.js"></script>
<script src="/psychopass_game/js/jquery.tablesorter.min.js"></script>
{literal}
<script>
	$(function(){
		$('i.icon-question-sign').each(function(){
			if ($(this).data('original-title')) {
				$(this).tooltip();
			}
		});
		$('i.icon-info-sign').each(function(){
			if ($(this).data('original-title')) {
				$(this).tooltip();
			}
		});
	});
</script>
{/literal}
