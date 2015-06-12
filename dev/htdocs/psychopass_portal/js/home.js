$(document).ready(function() {
	var marqueeSpeed = '20s';
	var topicsData = '';
	var point_get = parseInt( $("input#php_point_get").val() );
	
    var obj = $('.slider').bxSlider({ // 自動再生
		auto: true,
	    infiniteLoop: true,
	    responsive: true,
	    speed: 1500,
	    pager: false,
	    displaySlideQty: 1,
	    minSlides: 1,
	    maxSlides: 1,
	    moveSlides: 1,
	    slideMargin: 10,
	    pause: 2500,
	    onSlideAfter: function() { // 自動再生
	       obj.startAuto();
	    }
	 });
	
//	var twitter_txt = ;
	$('div.marquee p').text( $("#php_twitter_txt").val() );
	$('div.marquee p').css({
		'padding-left': '100%',
		'-moz-animation-duration': marqueeSpeed,
		'-moz-animation-name': 'marquee',
		'-moz-animation-iteration-count': 'infinite',
		'-moz-animation-timing-function': 'linear',
		
		'-webkit-animation-duration': marqueeSpeed,
		'-webkit-animation-name': 'marquee',
		'-webkit-animation-iteration-count': 'infinite',
		'-webkit-animation-timing-function': 'linear',
		
		'-ms-animation-duration': marqueeSpeed,
		'-ms-animation-name': 'marquee',
		'-ms-animation-iteration-count': 'infinite',
		'-ms-animation-timing-function': 'linear',
		
		'-o-animation-duration': marqueeSpeed,
		'-o-animation-name': 'marquee',
		'-o-animation-iteration-count': 'infinite',
		'-o-animation-timing-function': 'linear',
	});
	
	topicsData += '<section class="oneNews">';
	topicsData += '<h2>';
	topicsData += $("#php_news_title").val();
	topicsData += '</h2>';
	topicsData += '<p>';
	topicsData += nl2br($("#php_news_text").val());
	topicsData += '</p>';
	topicsData += '</section>';
	
	$('article#topicsText').append(topicsData);
	
	if ( point_get == 1 ) {
		$("body").append('<div id="modal-overlay"></div>');
		$("#modal-overlay").fadeIn("slow");
		
		$("div#modal-content").fadeIn("slow");
		
		//[#modal-overlay]、または[#modal-close]をクリックしたら…
		$("#modal-overlay").unbind().click(function(){

		    //[#modal-content]と[#modal-overlay]をフェードアウトした後に…
		    $("#modal-content,#modal-overlay").fadeOut("slow",function(){
      	 		//[#modal-overlay]を削除する
    	    	$('#modal-overlay').remove();
    	    	$('#modal-content').remove();
	    	});
   		});
	}

	
	function movePage(url) {
		$(location).attr( 'href', url );
	}
	
	function nl2br(text) {
		text = text.replace(/\r\n/g, "<br />");
		text = text.replace(/(\n|\r)/g, "<br />");
		return text;
	}

	function dateFormat(text) {
		var returnText = '';
		
		for ( var i = 0; i < text.length; i++ ) {
			returnText += text.charAt(i);
			switch (i) {
				case 3:
				case 5:
					returnText += '-';
			}
		}
		
		return returnText;
	}
});
