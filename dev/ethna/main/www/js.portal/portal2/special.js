$(function(){
	'use strict';
	
	var owl = $('div#owl-carousel');
	var themeColumnNum = 1;
	var themeIdArray = [];
	var themePointArray = [];
	
	var themeArray = $.parseJSON( $( "input#php_theme_json" ).val() );
	var userPoint = parseInt( $("input#php_user_point").val() );
	var ustr = $( "input#php_ustr" ).val();
	var sd = $( "input#php_server_domain" ).val();
	
	for ( var i = 0; i < themeArray.length; ++i ) {
		var themeName = themeArray[i].theme_name;
		var character = $('div#' + themeName);
		
		//キャラクター名を呼び出して、IDを取得できるように
		themeIdArray.push(themeName);
		themeIdArray[themeName] = themeArray[i].theme_id;
		themePointArray[themeName] = themeArray[i].use_point;
		
		if ( themeArray[i].lock_flg != 1 ) {
			character.removeClass ( 'lock' );
			if ( themeArray[i].selected_flg != 1 ) {
				character.addClass ( 'deselected' );
			}
		}
	}
	
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
			// キャラクター名からテーマIDを取得
//			movePage( 'get_theme.php?theme_id=' + themeIdArray[clickCharacterName] );
			$("body").append('<p id="modal-overlay"></p>');
			$("#modal-overlay").fadeIn("slow");
			
//			centeringModalSyncer();
			
//			if ( true ) {
			if ( userPoint >= parseInt( themePointArray[clickCharacterName] ) ) {
				$("body").append('<div id="modal-content"><p>このテーマはまだ開放されていません。</p><p>開放には' + themePointArray[clickCharacterName] + 'pt必要です。</p><p>ポイントを消費して開放しますか？</p><div style="text-align:center;"><p><a id="modal-openpage" class="button-link">開放する</a></p></div><p>※ポップアップ以外の箇所をタッチすると元の画面に戻ります</p></div>');
			} else {
				$("body").append('<div id="modal-content"><p>このテーマはまだ開放されていません。</p><p>開放には' + themePointArray[clickCharacterName] + 'pt必要です。</p><p>ポイントが不足しているため、このテーマを開放できません。</p><p>※ポップアップ以外の箇所をタッチすると元の画面に戻ります</p></div>');
			}
			
			$("div#modal-content").fadeIn("slow");
			
			// リンク押したら処理に遷移
			$("#modal-openpage").unbind().click(function(){
				movePage( 'getTheme.php?theme_id=' + themeIdArray[clickCharacterName] );
			});
			//[#modal-overlay]、または[#modal-close]をクリックしたら…
			$("#modal-overlay").unbind().click(function(){

			    //[#modal-content]と[#modal-overlay]をフェードアウトした後に…
			    $("#modal-content,#modal-overlay").fadeOut("slow",function(){
    	  	 		//[#modal-overlay]を削除する
    		    	$('#modal-overlay').remove();
    		    	$('#modal-content').remove();
		    	});
   			});

		} else if ( clickCharacter.hasClass('deselected') ) {
			//テーマ設定
			movePage( 'changeTheme.php?theme_id=' + themeIdArray[clickCharacterName] + '&ustr=' + ustr );
		}
	});
	
	function movePage(url) {
//		$(location).attr( 'href', "unity:https://" + sd + "/psychopass_portal/" + url );
		Unity.call( "https://" + sd + "/psychopass_portal/" + url );
	}
	
	function centeringModalSyncer(){
		//画面(ウィンドウ)の幅を取得し、変数[w]に格納
		var w = $(window).width();

		//画面(ウィンドウ)の高さを取得し、変数[h]に格納
		var h = window.innerHeight;

		//コンテンツ(#modal-content)の幅を取得し、変数[cw]に格納
		var cw = $("#modal-content").outerWidth({margin:true});

		//コンテンツ(#modal-content)の高さを取得し、変数[ch]に格納
		var ch = $("#modal-content").outerHeight({margin:true});

		//コンテンツ(#modal-content)を真ん中に配置するのに、左端から何ピクセル離せばいいか？を計算して、変数[pxleft]に格納
		var pxleft = ((w - cw)/2);

		//コンテンツ(#modal-content)を真ん中に配置するのに、上部から何ピクセル離せばいいか？を計算して、変数[pxtop]に格納
		//var pxtop = ((h - ch)/2);

		//[#modal-content]のCSSに[left]の値(pxleft)を設定
		$("#modal-content").css({"left": pxleft + "px"});

		//[#modal-content]のCSSに[top]の値(pxtop)を設定
		$("#modal-content").css({"top": pxtop + "px"});
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
