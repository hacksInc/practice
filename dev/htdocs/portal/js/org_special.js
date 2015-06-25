$(function(){
	'use strict';
	
	var owl = $('div#owl-carousel');
	var themeColumnNum = 1;
	var themeIdArray = [];
	
	$.ajax({
		url: "./api/get_theme_info.php",
		dataType : 'jsonp',
		jsonp: 'callback',
		type: 'get',
		success: function(data) {
			if ( data.status == 1 ) {
				var themeArray = data.theme;
			
			//デバッグ用に追加
			//alert(data);	
					
				for ( var i = 0; i < themeArray.length; ++i ) {
					var themeName = themeArray[i].theme_name;
					var character = $('div#' + themeName);
					//デバッグ用に追加
					//alert(themeName);
					
					//キャラクター名を呼び出して、IDを取得できるように
					themeIdArray.push(themeName);
					themeIdArray[themeName] = themeArray[i].theme_id;
					
					if ( themeArray[i].lock_flg != 1 ) {
						character.removeClass ( 'lock' );
						if ( themeArray[i].selected_flg != 1 ) {
							character.addClass ( 'deselected' );
						}
					}
				}
			} else if ( data.status == 2 ) {
				movePage('error.html');
			} else if ( data.status == 3 ) {
				movePage('error.html');
			} else if ( data.status == 90 ) {
				movePage('sessionError.html');
			} else if ( data.status == 99 ) {
				movePage('error.html');
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			alert(errorThrown);
		}
	});
	
	$.ajax({
		url: "./api/get_member.php",
		dataType : 'jsonp',
		jsonp: 'callback',
		type: 'get',
		success: function(data) {
			if ( data.status == 1 ) {
				$('td#point').text(data.point.replace( /(\d)(?=(\d\d\d)+(?!\d))/g, '$1,' )).append(' <span>pt</span>');
			} else if ( data.status == 2 ) {
				movePage('menberError.html');
			} else if ( data.status == 90 ) {
				movePage('sessionError.html');
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			alert(errorThrown);
		}
	});
	
	owl.owlCarousel({
		items: themeColumnNum,
		dots: false
	});
	
	$('div#nextButton').click(function() {
		owl.trigger('next.owl.carousel');
	});
	
	$('div#prevButton').click(function() {
		owl.trigger('prev.owl.carousel', [300]);
	});

	$('div.owl-item').css({
		'height': $(document).height() - 200
	});
	
	$('div#owl-carousel a.link').on('click', function(event){
		var clickCharacter = $('div.oneTheme', $(this));
		//タップされたキャラクター名取得(kogamiなど)
		var clickCharacterName = clickCharacter.html();
		var param = {};
		
		if ( clickCharacter.hasClass('lock') ) {
			//購入処理
			
			//キャラクター名からテーマIDを取得
			param.theme_id = themeIdArray[clickCharacterName];
			
			//デバッグ用に追加
			//alert(param.theme_id);
			$.ajax({
				url: "./api/get_theme.php",
				dataType : 'jsonp',
				jsonp: 'callback',
				data: param,
				type: 'get',
				success: function(data) {
					if ( data.status == 1 ) {
						//テーマゲット
						movePage('special.html');
					} else if ( data.status == 2 ) {
						//パラメーターエラー
						movePage('paramError.html');
					} else if ( data.status == 3 ) {
						//ポイント不足
						movePage('pointShortage.html');
					} else if ( data.status == 90 ) {
						//セッションエラー
						movePage('sessionError.html');
					} else if ( data.status == 99 ) {
						//その他エラー
						movePage('unknown.html');
					}
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
					alert(errorThrown);
				}
			});
		} else if ( clickCharacter.hasClass('deselected') ) {
			//テーマ設定
			movePage(clickCharacterName + '.html');
		}
	});
	
	function movePage(url) {
		$(location).attr( 'href', url );
	}
});

$(window).load(function(){
	'use strict';
	
	var documentHeight = $(document).height();
	var themeNote = $('div#themeNote');
	var themeNoteMarginL = parseInt(themeNote.css('width')) / 2;
	var sosialButtonSize = 50;
	var sosialButtonMargin = 20;
	var owlItem = $('.owl-item');
	var owlItemOffset = owlItem.offset();
	
	themeNote.css({
		'margin-left': -themeNoteMarginL,
		'bottom': sosialButtonSize + sosialButtonMargin
	});
	
	owlItem.css({
		'height': documentHeight - owlItemOffset.top
	});
});
