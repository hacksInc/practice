$(function() {
	'use strict';
	
	var url = location.href;
	var getParameter = url.split('?');
	var parameters;
	var parameterArray = [];
	
	//userData
	var email;
	var user_name;
	var user_name_en;
	var sex;
	var password;
	
	if ( getParameter[1] !== undefined ) {
		parameters = getParameter[1].split('&');
		
		for ( var i = 0; i < parameters.length; i++ ) {
			var temp = parameters[i].split('=');
			parameterArray.push(temp[0]);
			parameterArray[temp[0]] = temp[1];
		}
		
		if ( parameterArray['email'] === undefined ) {
			movePage('login.html');
		} else {
			email = parameterArray['email'];
			$('p#email').text( email );
		}
	} else {
		movePage('login.html');
	}
	
	$('input#confirmButton').click(function() {
		var confirmSex;
		var signupArea = $('div.signupArea');
		
		user_name = $('input#user_name').val();
		user_name_en = $('input#user_name_en').val();
		sex = $('select#sex').val();
		password = $('input#password').val();
		
		if ( sex == 1 ) {
			confirmSex = '男性';
		} else if ( sex == 2 ) {
			confirmSex = '女性';
		}
		
		if ( user_name == '' ) {
			// alert ('名前を入力して下さい');
			movePage('noname.html');
		} else if ( user_name_en == '' ) {
			// alert ('名前(ローマ字)を入力して下さい');
			movePage('alphabet.html');
		} else if ( confirmSex == undefined ) {
			// alert ('性別を選択して下さい');
			movePage('sex.html');
		} else if ( password == '' || $('input#password2').val() == '' ) {
			// alert ('パスワードを入力して下さい');
			movePage('nopassword.html');
		} else if ( password != $('input#password2').val() ) {
			// alert ('入力されたパスワードが異なります');
			movePage('different.html');
		} else if ( password.match(/[^0-9A-Za-z]+/) ) {
			// alert ('パスワードは半角英数字で入力して下さい');
			movePage('alphanum.html');
		} else {
			$('div#signupForm', signupArea).hide();
			signupArea.addClass('confirm');
			signupArea.removeClass('signupArea');
			$('div#confirmTable').show();
			
			$('p#confirmEmail').text(email);
			$('p#confirmUserName').text(user_name);
			$('p#confirmUserNameEn').text(user_name_en);
			$('p#confirmSex').text(confirmSex);
			$('p#confirmPassword').text(password);
		}
	});
	
	$('input#sendButton').click(function() {
		var sendParameters = {};
		
		sendParameters.email = email;
		sendParameters.user_name = user_name;
		sendParameters.user_name_en = user_name_en;
		sendParameters.sex_kbn = sex;
		sendParameters.password = password;
		
		$.ajax({
			url: "./api/member_regist.php",
			dataType: 'jsonp',
			jsonp: 'callback',
			data: sendParameters,
			type: 'get',
			success: function(data) {
				// alert(data.status);
				if ( data.status == 1 ) {
					//最初の遷移時同様にパラメータ付きurlを開くようにした
					movePage('popupSuccess.html?sex='+sendParameters.sex_kbn+'&pass='+sendParameters.password);
					// movePage('popup.html');
				} else if ( data.status == 2 ) {
					movePage('popupParamErr.html');
				} else if ( data.status == 3 ) {
					//一時テスト用に
					movePage('popupdoubled.html');
					// movePage('popup.html');
				} else {
					movePage('popupError.html');
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				alert(errorThrown);
			}
		});
	});
	
	function movePage(url) {
		// alert("*HTML* "+url);
		$(location).attr( 'href', url );
	}
});