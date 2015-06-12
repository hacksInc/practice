$(function(){
	'use strict';
	
	var url = location.href;
	var getParameter = url.split('?');
	var parameters;
	var parameterArray = [];
	var characterName;
	//パラメータをkeyに持つ、キャラクターごとの壁紙枚数
	var paperNums = {kogami: 1, tsunemori: 2, ginoza: 2, masaoka: 1, kagari: 1, kunizuka: 2, karanomori: 2, togane: 1, hinakawa: 1, shimotsuki: 1, other: 5}; 
	var owl = $('div#owl-carousel');
	var themeColumnNum = 1;
	var sosialButtonSize = 50;
	var sosialButtonMargin = 20;
	var userAgent = navigator.userAgent;
	var phoneKind;
	var sd = $( "input#php_server_domain" ).val();
	
	if ( ( userAgent.search(/iPhone/) > -1 ) || ( userAgent.search(/iPad/) > -1) ) {
		phoneKind = 'iphone';
	} else if( userAgent.search(/Android/) > -1 ) {
		phoneKind = 'android';
	}
	
	if ( getParameter[1] !== undefined ) {
		parameters = getParameter[1].split('&');
		
		for ( var i = 0; i < parameters.length; i++ ) {
			var temp = parameters[i].split('=');
			parameterArray.push(temp[0]);
			parameterArray[temp[0]] = temp[1];
		}
		
		if ( parameterArray['character'] === undefined ) {
			movePage('wallpaperList.html');
		} else {
			characterName = parameterArray['character'];
			if ( !(characterName in paperNums) ) {
				movePage('wallpaperList.html');
			}
		}
	} else {
		movePage('wallpaperList.html');
	}
	
	for ( var i = 1; i <= paperNums[characterName]; i++ ) {
		owl.append( '<a class="link"><div id="wallpaper' + i + '" class="onePaper">img/wallpaper/' + phoneKind + '/' + characterName + '/wallpaper' + i + '.jpg</div></a>' );
//		owl.append( '<a class="link"><div id="wallpaper' + i + '" class="onePaper">img/wallpaper/' + characterName + '/wallpaper' + i + '.jpg</div></a>' );
//		owl.append( '<a onClick="Unity.call(\'https://' + sd + '/psychopass_portal/img/wallpaper/' + characterName + '/wallpaper' + i + '.jpg\');"><div id="wallpaper' + i + '" class="onePaper">./img/wallpaper/' + characterName + '/wallpaper' + i + '.jpg</div></a>' );
		$('#wallpaper' + i).css({
			'background': 'no-repeat center top url(./img/wallpaper/list/' + phoneKind + '/' + characterName + '/wallpaper' + i + '.jpg)',
//			'background': 'no-repeat center top url(./img/wallpaper/list/' + characterName + '/wallpaper' + i + '.jpg)',
			'-webkit-background-size': 'cover',
			'-moz-background-size': 'cover',
			'-o-background-size': 'cover',
			'background-size': 'cover'
		});
	}
	
	owl.owlCarousel({
		items: themeColumnNum,
		dots: false,
		margin: 15,
		stagePadding: 60
	});
	
	$('div#nextButton').click(function() {
		owl.trigger('next.owl.carousel');
	});
	
	$('div#prevButton').click(function() {
		owl.trigger('prev.owl.carousel', [300]);
	});
	
	if ( paperNums[characterName] <= 1 ) {
		$('div#nextButton').remove();
		$('div#prevButton').remove();
	}

	$('div.owl-item').css({
		'height': $(document).height() - sosialButtonSize - sosialButtonMargin
	});
	
	$('div#owl-carousel a.link').on('click', function(event){
		var clickWallpaper = $('div.onePaper', $(this));
		//タップされた壁紙のパス
		var clickWallpaperPath = clickWallpaper.html();
		movePage(clickWallpaperPath);
		//alert(clickWallpaperPath);
	});
	
	function movePage(url) {
//		$(location).attr( 'href', url );
//		$(location).attr( 'href', "unity:https://" + sd + "/psychopass_portal/" + url );
//		alert("https://" + sd + "/psychopass_portal/" + url);
		Unity.call("https://" + sd + "/psychopass_portal/" + url);
//		Unity.call("https://" + sd + "/psychopass_portal/img/wallpaper.php");
	}
});
