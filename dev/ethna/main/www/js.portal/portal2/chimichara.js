$(function(){
	'use strict';

	var folNum;
	var imgNum;
	var select;
	var charaArray;
	var set = $('div#portalBackground');
	var themeName = $('input#php_theme_name').val();
	var flg = $('#chimiFlg').val();

	// 各テーマのちみキャラのフォルダ数と各フォルダ内のimg数
	var charaNums =
	{
	'kogami':{'folder':8,'folder1':16,'folder2':1,'folder3':6,'folder4':8,'folder5':2,'folder6':10,'folder7':1,'folder8':1},
	'tsunemori':{'folder':9,'folder1':19,'folder2':6,'folder3':6,'folder4':11,'folder5':13,'folder6':14,'folder7':13,'folder8':1,'folder9':1},
	'ginoza':{'folder':4,'folder1':16,'folder2':9,'folder3':1,'folder4':1},
	'kagari':{'folder':6,'folder1':17,'folder2':13,'folder3':9,'folder4':7,'folder5':12,'folder6':17},
	'karanomori':{'folder':1,'folder1':13},
	'kunizuka':{'folder':3,'folder1':17,'folder2':5,'folder3':1},
	'masaoka':{'folder':1,'folder1':18},
	'other':{'folder':2,'folder1':11,'folder2':7},
	'hanako':{'folder':1,'folder1':3},
	'tarou':{'folder':1,'folder1':7}
	};

	if ( flg == 'error' ) {
		// 現在のテーマのちみキャラをランダムで取得
		folNum = Math.floor(Math.random() * charaNums[themeName]['folder']) + 1;
		imgNum = Math.floor(Math.random() * charaNums[themeName]['folder'+ folNum]) + 1;

		// 1体目のちみキャラ出現
		set.append('<img id="error_chara" src="/psychopass_portal/img/chimichara/' + themeName + '/' + folNum + '/' + themeName + '_' + imgNum + '.png" width="100%">');
		set.append('<img id="error_chara_shadow" src="/psychopass_portal/img/portal/chara_shadow.png" width="100%">');
	}
	if ( flg == 'result' ) {
		// 現在のテーマのちみキャラをランダムで取得
		folNum = Math.floor(Math.random() * charaNums[themeName]['folder']) + 1;
		imgNum = Math.floor(Math.random() * charaNums[themeName]['folder'+ folNum]) + 1;

		// 1体目のちみキャラ出現
		set.append('<img id="result_chara" src="/psychopass_portal/img/chimichara/' + themeName + '/' + folNum + '/' + themeName + '_' + imgNum + '.png" width="100%">');
		set.append('<img id="result_chara_shadow" src="/psychopass_portal/img/portal/chara_shadow.png" width="100%">');
	}

	if ( flg == 1 || flg == 2 || flg == 3 ) {

		// 現在のテーマのちみキャラをランダムで取得
		folNum = Math.floor(Math.random() * charaNums[themeName]['folder']) + 1;
		imgNum = Math.floor(Math.random() * charaNums[themeName]['folder'+ folNum]) + 1;

		// 1体目のちみキャラ出現
		set.append('<img id="chara1" src="/psychopass_portal/img/chimichara/' + themeName + '/' + folNum + '/' + themeName + '_' + imgNum + '.png" width="100%">');
		set.append('<img id="chara1_shadow" src="/psychopass_portal/img/portal/chara_shadow.png" width="100%">');
	}

	// 2体目の処理
	if ( flg == 2 || flg == 3 ) {

		// 現在のテーマを削除
		delete charaNums[themeName];

		// テーマ解放有無のリストを取得
		var themeArray = $.parseJSON( $( "input#php_theme_json" ).val() );

		// まだ解放されていないテーマを削除
		for ( var i = 0; i < themeArray.length; ++i ) {

			themeName = themeArray[i].theme_name;
			
			if ( themeArray[i].lock_flg == 1 ) {
				delete charaNums[themeName];
			}
		}

		// 残ったテーマを配列に格納
		charaArray = new Array();
		for ( var i in charaNums ) {
		    charaArray.push({ key:i, val:charaNums[i] });
		}

		// テーマをランダムで選択
		select = Math.floor(Math.random() * charaArray.length);
		themeName = charaArray[select].key;
		
		folNum = Math.floor(Math.random() * charaNums[themeName]['folder']) + 1;
		imgNum = Math.floor(Math.random() * charaNums[themeName]['folder'+ folNum]) + 1;

		// 2体目のちみキャラ出現
		set.append('<img id="chara2" src="/psychopass_portal/img/chimichara/' + themeName + '/' + folNum + '/' + themeName + '_' + imgNum + '.png" width="100%">');
		set.append('<img id="chara2_shadow" src="/psychopass_portal/img/portal/chara_shadow.png" width="100%">');

	}

	// 3体目の処理
	if ( flg == 3 ) {

		// 2体目のテーマを配列から除外
		charaArray.splice(select, 1);

		select = Math.floor(Math.random() * charaArray.length);
		themeName = charaArray[select].key;

		folNum = Math.floor(Math.random() * charaNums[themeName]['folder']) + 1;
		imgNum = Math.floor(Math.random() * charaNums[themeName]['folder'+ folNum]) + 1;

		// 3体目のちみキャラ出現
		set.append('<img id="chara3" src="/psychopass_portal/img/chimichara/' + themeName + '/' + folNum + '/' + themeName + '_' + imgNum + '.png" width="100%">');
		set.append('<img id="chara3_shadow" src="/psychopass_portal/img/portal/chara_shadow.png" width="100%">');
	}


});