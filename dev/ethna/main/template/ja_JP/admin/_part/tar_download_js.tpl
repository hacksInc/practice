{literal}
<script>
$(function(){
	var target = '{/literal}{$app.target}{literal}';
	
	$('#rsync-button').click(function() {
		if (!window.confirm('よろしいですか？')) {
			return;
		}
		
		$('.rsync-makuo-button').attr('disabled', 'disabled');
		$('#rsync-time').html('反映中');
		$('#rsync-user').html('');
		
		$.post('/psychopass_game/admin/api/rsync', {'target': target})
			.done(function(data) {
				$('#rsync-time').html(data.time);
				$('#rsync-user').html(data.user);
			})
			.fail(function(data) {
				$('#rsync-time').html('error');
				$('#rsync-user').html('');
			})
			.always(function(data) {
				$('.rsync-makuo-button').removeAttr('disabled');
			});
	});

	$('#makuo-button').click(function() {
		if (!window.confirm('よろしいですか？')) {
			return;
		}

		$('.rsync-makuo-button').attr('disabled', 'disabled');
		$('#makuo-time').html('反映中');
		$('#makuo-user').html('');

		$.post('/psychopass_game/admin/api/makuo', {'target': target})
			.done(function(data) {
				$('#makuo-time').html(data.time);
				$('#makuo-user').html(data.user);
			})
			.fail(function(data) {
				$('#makuo-time').html('error');
				$('#makuo-user').html('');
			})
			.always(function(data) {
				$('.rsync-makuo-button').removeAttr('disabled');
			});
	});
	
	$('#svn-button').click(function() {
		var path     = $('#svn-path-input').val();
		var revision = $('#svn-revision-input').val();
		
		if (!window.confirm('よろしいですか？')) {
			return;
		}

		$('#svn-button').attr('disabled', 'disabled');
		$('#svn-time').html('反映中');
		$('#svn-user').html('');
		
		var request_data = {'target': target, 'path': path};
		if (strlen(revision) > 0) {
			request_data.revision = revision;
		}

		$.post('/admin/api/svn/checkout', request_data)
			.done(function(data) {
				$('#svn-time').html(data.time);
				$('#svn-user').html(data.user);
			})
			.fail(function(data) {
				$('#svn-time').html('error');
				$('#svn-user').html('');
			})
			.always(function(data) {
				$('#svn-button').removeAttr('disabled');
			});
	});
	
	$('#download-form').submit(function() {
		function checkExitCode(new_uniq) {
			$.ajax({
				url: '/psychopass_game/admin/api/tar/exit',
				type: 'POST',
				data: {
					download_uniq: new_uniq
				},					
				cache: false,
			})
			.done(function( data ) {
				if (strcmp(data, '0') !== 0) {
					alert('Error ' + data);
				}
			})
			.fail(function( data ) {
				alert('Error');
			})
			.always(function( data ) {
				$('#download-btn').removeAttr('disabled');
			});
		}
		
		function waitEndOfCommunication() {
			$('#download-btn').attr('disabled', 'disabled');
		
			var old_uniq = $.cookie('download_uniq');
			var timer = null;
			timer = setInterval(function() {
				var new_uniq = $.cookie('download_uniq');
				if (new_uniq && strcmp(new_uniq, old_uniq)) {
					clearInterval(timer);
					checkExitCode(new_uniq);
				}
			}, 1000);
		}
		
		waitEndOfCommunication();
	});
});
</script>
{/literal}
