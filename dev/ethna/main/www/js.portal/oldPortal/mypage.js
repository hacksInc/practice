$(document).ready(function() {
	$.ajax({
		url: "./api/get_member.php",
		dataType : 'jsonp',
		jsonp: 'callback',
		type: 'get',
		success: function(data) {
			if ( data.status == 1 ) {
				$('p#name').text(data.user_name);
				$('th#nameEn').text(data.user_name_en);
				$('td#id').text('ID : ' + data.citizen_id);
				$('td#codeName').text('CODE NAME : ' + data.code_name);
				$('td#loginCount').text('NUMBER OF LOGINS : ' + data.login_cnt);
				if ( data.sex == 2 ) {
					setProfileImg('hanako');
				} else {
					setProfileImg('taro');
				}
			} else if ( data.status == 2 ) {
				movePage('paramErr.html');
			} else if ( data.status == 90 ) {
//				movePage('sessionError.html');
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			alert(errorThrown);
		}
	});
	
	function movePage(url) {
//		$(location).attr( 'href', url );
	}
});