$(document).ready(function() {
	var marqueeSpeed = '20s';
	
	//twitter記事取得
	$.ajax({
		url: "./api/get_twitter.php",
		dataType : 'jsonp',
		jsonp: 'callback',
		type: 'get',
		success: function(data) {
			if ( data.status == 1 ) {
				//取得成功
				$('div.marquee p').text(data.twitter_txt);
				$('div.marquee p').css({
					'padding-left': '100%',
					'-moz-animation-duration': marqueeSpeed,
					'-moz-animation-name': 'marquee',
					'-moz-animation-iteration-count': 'infinite',
					'-moz-animation-timing-function': 'linear',
					
					'-webkit-animation-duration': marqueeSpeed,
					'-webkit-animation-name': 'marquee',
					'-webkit-animation-iteration-count': 'infinite',
					'-webkit-animation-timing-function': 'linear',
					
					'-ms-animation-duration': marqueeSpeed,
					'-ms-animation-name': 'marquee',
					'-ms-animation-iteration-count': 'infinite',
					'-ms-animation-timing-function': 'linear',
					
					'-o-animation-duration': marqueeSpeed,
					'-o-animation-name': 'marquee',
					'-o-animation-iteration-count': 'infinite',
					'-o-animation-timing-function': 'linear',
				});
				
			} else if ( data.status == 2 ) {
				//Twitterデータ無し
				$('a#twitterArea').remove();
			} else if ( data.status == 99 ) {
				//その他エラー
				movePage('unknown.html');
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
		//	alert(errorThrown);
		}
	});
	
	$("input#loginButton").click(function() {
		var loginId = $('input#login_id').val();
		var loginPassword = $('input#password').val();
		var param = {};
	
		if ( loginId == '' ) {
			//alert('IDを入力して下さい');
			movePage('popupNoId.html');
		} else if ( loginPassword == '' ) {
			//alert('パスワードを入力して下さい');
			movePage('popupNoPass.html');
		} else {
			param.login_id = loginId;
			param.password = loginPassword;
			
			$.ajax({
				url: "./api/login.php",
				dataType : 'jsonp',
				jsonp: 'callback',
				data: param,
				type: 'get',
				success: function(data) {
					if ( data.status == 1 ) {
						//alert('ログイン成功');
						
						
						movePage('index.html');
					} else if ( data.status == 2 ) {
						movePage('popupInputError.html');
					} else if ( data.status == 3 ) {
						movePage('notRegistered.html');
					} else if ( data.status == 99 ) {
						movePage('popup.html');
					}
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
					alert(errorThrown);
				}
			});
		}
	});
	
	$('input#newMember').click(function() {
		movePage('mail.html');
	});
	
	function movePage(url) {
		$(location).attr( 'href', url );
	}
});
