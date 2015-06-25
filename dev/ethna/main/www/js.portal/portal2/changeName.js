

function cancel() {
       $('#errBox').hide();
       $('#overlay').hide();
       return false;

}

function accept(){
	var sd = $( "input#php_server_domain" ).val();
	var nickname = $('#nickname').val();
	var ruby = $('#ruby').val();
	var nickLength = nickname.length;
	var rubyLength = ruby.length;


	if ( nickLength > 10 ) {

		// nickname が10文字以上の場合エラー
    	$('#errBox').show();
        $('#overlay').show();
        $('#errMsg').text('住人登録名（ニックネーム）は9文字以内です。');
        return false;
	}

	    if ( nickname == "") {

	    	// nickname が空の場合エラー出現
            $('#errBox').show();
            $('#overlay').show();
            $('#errMsg').text('住人登録名（ニックネーム）を入力してください。');
            return false;

	    } else {

	    	// nickname の文字判定（新規登録のものを流用）
	        for(var i = 0; i < nickLength; i++) {

	 			var unicode = nickname.charCodeAt(i);

	            if ((+0x0000 <= unicode && unicode <= +0x007f) ||
	                (+0x0391 <= unicode && unicode <= +0x03C9) ||
	                (+0x0410 <= unicode && unicode <= +0x044F) ||
	                (+0x3001 <= unicode && unicode <= +0x3039) ||
	                (+0x3040 <= unicode && unicode <= +0x30FF) ||
	                (+0xFF00 <= unicode && unicode <= +0xFFEF) ||
	                (+0x31F0 <= unicode && unicode <= +0x31FF) ||
	                (+0x3400 <= unicode && unicode <= +0x9FFF) ||
	                (+0xF900 <= unicode && unicode <= +0xFAFF) ||
	                (+0x1B000 <= unicode && unicode <= +0x1B0FF) ||
	                (+0x20000 <= unicode && unicode <= +0x2FA1F)
	            ) {

	            } else {

	            	//上記判定を通らなかったらエラー出現
	                $('#errBox').show();
	                $('#overlay').show();
	                $('#errMsg').text('住人登録名（ニックネーム）を正しく入力してください。');
	                return false;
	            }
	        }
    	}

	    if ( rubyLength > 10 ) {

	    	// nickname が10文字以上の場合エラー
            $('#errBox').show();
            $('#overlay').show();
            $('#errMsg').text('住人登録名（英数字）は9文字以内です。');
            return false;
	    }

	if ( ruby == "") {

		// ruby が空の場合エラー出現
    	$('#errBox').show();
        $('#overlay').show();
        $('#errMsg').text('住人登録名（英数字）を入力してください。');
        return false;

	} else {

	    	// ruby は英数字のみ許容
		if ( ruby.match(/^[a-zA-Z0-9]+$/i)) {

		} else {

			// 上記通らなかったらエラー
			$('#errBox').show();
			$('#overlay').show();
			$('#errMsg').text('住人登録名（英数字）を正しく入力してください。');
			return false;

		}
	}

	Unity.call( "https://" + sd + "/psychopass_portal/changeNameresult.php?nickname=" + nickname + "&ruby=" + ruby );
}
