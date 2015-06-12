$(function(){

    // スマホ時、メニューの表示、非表示
    var menu = $('.sp_menu'),
        body = $(document.body),
        layer = $('.layer'),
        menuWidth = menu.width();

    $('#toggle').on('click', function(){
        body.toggleClass('open');

        if(body.hasClass('open')){
            $(".layer").show();
            // body全体をmenuのwidth分、右から左に動かす
            body.animate({'right' : menuWidth }, 300);
            // menuをwidth分、右から左に動かす
            menu.animate({'right' : 0 }, 300);
        } else {
            $(".layer").hide();
            // 元に戻す処理
            $('.sp_menulist').slideUp();
            $('.toggle2').css('transform', 'rotate(0deg)');
            menu.animate({'right' : -menuWidth }, 300);
            body.animate({'right' : 0 }, 300);
        }

    });

    // レイヤー（menu以外の部分)をクリックしても同様に元に戻す処理を行う
    layer.on('click', function(){
        $('.sp_menulist').slideUp();
        $('.toggle2').css('transform', 'rotate(0deg)');
        menu.animate({'right' : -menuWidth }, 300);
        body.animate({'right' : 0 }, 300).removeClass('open');
        layer.hide();
    });

    // スマホmenuの下層の隠れてる部分の表示非表示
    $('.sp_menu p').on('click', function(){

        // クリックされたら一旦全部閉じる
        $('.sp_menulist').slideUp();
        $('.toggle2').css('transform', 'rotate(0deg)');

        // もしクリックした要素がactive状態（開いてる状態）だったらactiveを消す
        if ( $(this).hasClass('active') ) {

            $(this).removeClass('active');

        } else {
            // もしactiveなかったら（開いてない状態）
            // 一旦他のactive全部消す
            $('.sp_menu p').removeClass('active');
            // クリックされた要素のみactive追加
            $(this).addClass('active');
            // クリックされた要素のみ×マークにする
            $(this).children('.toggle2').css('transform', 'rotate(45deg)');
            // クリックされた要素の下層メニューを表示
            $(this).parent('li').children('.sp_menulist').slideDown();
        }
    });

    // PC時、menuの表示、非表示
	$(".menulist").hover(function(){
		$(this).children('.menulist ul').slideToggle();
	});
});
