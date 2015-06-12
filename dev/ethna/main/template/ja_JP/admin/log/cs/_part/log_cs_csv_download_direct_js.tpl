{literal}
<script>
$(function(){
	$('#formCsvDownloadDirect').submit(function() {
		function enableButton() {
			var timer = setInterval(function() {
				clearInterval(timer);
				$('#btnCsvDownloadDirect').removeAttr('disabled');
			}, 5000);
		}
		
		function disableButton() {
			$('#btnCsvDownloadDirect').attr('disabled', 'disabled');

			var old_uniq = $.cookie('download_uniq');
			var timer = setInterval(function() {
				var new_uniq = $.cookie('download_uniq');
				if (new_uniq && strcmp(new_uniq, old_uniq)) {
					clearInterval(timer);
					enableButton();
				}
			}, 1000);
		}
		
		disableButton();
	});
});
</script>
{/literal}