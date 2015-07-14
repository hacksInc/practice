
//***********************************//
//***********  header.js  ***********//
//***********************************//

	//スティッキーヘッダー
	var $window = $(window), //ウィンドウを指定
	$content = $(".menu"), //#content部分
	$chenge = $(".change"), //#change部分
	topContent = $content.offset().top; //#contentの位置を取得
	 
	var sticky = false;
	 
	 $window.on("scroll", function () {
	      if ($window.scrollTop() > topContent) {　//scroll位置が#contentの上にある場合
	           if ( sticky === false ){
	                $chenge.slideDown();　//#change部分が上がる。
	                sticky = true;
	           }
	      } else {
	           if ( sticky === true ){　//scroll位置が下にある場合
	                $chenge.slideUp();//#change部分が降りてくる。
	                sticky = false;
	           }
	      }
	 });
	 $window.trigger("scroll");



//***********************************//
//*************  menu.js  ***********//
//***********************************//

	//メニューバーをホバーしたらサブウィンドウが出現する
	$(function(){
		$(".menu_bar li").hover(function(){
			$(".menu_list",this).show();
		},

		function(){
			$(".menu_list",this).hide();
		});
	});


//***********************************//
//********  new_projects.js  ********//
//***********************************//

	$(function() {
		$('.tab_menu li').click(function() {

			var num = $('.tab_menu li').index(this);

			$('.tab_menu li').removeClass('on');
			$(this).addClass('on');

			$('.new_project').removeClass('select');
			$('.new_project').eq(num).addClass('select');

			$('.navi').removeClass('act');
			$('.navi').eq(num).addClass('act');

		});
	});

	$(function(){
	    $('.new_project').totemticker({
	        row_height: '55px',
	        speed: 800,
	        interval: 3000,
	        max_items: 5,
	        mousestop: true,
	    });
	});


//***********************************//
//********** facebook.js  ***********//
//***********************************//
(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/ja_JP/sdk.js#xfbml=1&version=v2.0";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

