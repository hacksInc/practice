$(function(){

    // スマホ時、メニューの表示、非表示
    var menu = $('.menu'),
        body = $(document.body),
        layer = $('.layer'),
        menuWidth = menu.width();

    $('#toggle').on('click', function(){
        body.toggleClass('open');

        if(body.hasClass('open')){
            layer.show();
            // body全体をmenuのwidth分、右から左に動かす
            body.animate({'right' : menuWidth }, 300);
            // menuをwidth分、右から左に動かす
            menu.animate({'right' : 0 }, 300);
        } else {
            layer.hide();
            menu.animate({'right' : -menuWidth }, 300);
            body.animate({'right' : 0 }, 300);
        }

    });

    // レイヤー（menu以外の部分)をクリックしても同様に元に戻す処理を行う
    layer.on('click', function(){
        menu.animate({'right' : -menuWidth }, 300);
        body.animate({'right' : 0 }, 300).removeClass('open');
        layer.hide();
    });
});
