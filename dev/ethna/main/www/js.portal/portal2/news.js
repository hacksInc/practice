$(function(){
	'use strict';
	
	var newsArea = $('article#contents');
	var lastFlg = false;
	var prevLastNewsId = '';
	var loadPosition = 100;
	var loadFlg = false;
	var pageNo = 1;
	var requestNum = 10;
	
	loadNewsList();
		
	$('article#contents').bind("scroll", function() {
		var contents = $('article#contents');
		var displayHeight = contents.get(0).offsetHeight;
		var contentsHeight = contents.get(0).scrollHeight;
		var scrollPosition = contents.scrollTop();
		
		if ( !lastFlg && !loadFlg && contentsHeight - scrollPosition <= displayHeight + loadPosition ) {
			loadNewsList();
		}
	});
	
	function loadNewsList() {
		var param = {};
		
		loadFlg = true;
		
		param.page_no = pageNo;
		param.request_num = requestNum;
		
		$.ajax({
			url: "./api/get_news.php",
			dataType: 'jsonp',
			jsonp: 'callback',
			data: param,
			type: 'get',
			success: function(data) {
				if ( data.status == 1 ) {
					var newsArray = data.news;
					var appendNewsData = '';
					
					for ( var i = 0; i < newsArray.length; ++i ) {
						var newImage = '';
						
						if ( newsArray[i].new_flag == 1 ) {
							newImage = '<img class="newImage" src="img/news/new.png" width="30" height="13">';
						}
						
						appendNewsData += '<section class="oneNews list">\n';
						appendNewsData += '<h2 class="newsTitle"><a href="newsDetail.html?id=' + newsArray[i].id + '">' + newsArray[i].news_title + '</a>' + newImage + '</h2>\n';
						appendNewsData += '<p class="date">' + dateFormat(newsArray[i].disp_date) + '</p>\n';
						appendNewsData += '</section>\n';
						appendNewsData += '<hr>\n';
					}
					
					newsArea.append( appendNewsData );
				} else if ( data.status == 2 ) {
					movePage('pointGet.html');
				} else if ( data.status == 4 ) {
					lastFlg = true;
				} else if ( data.status == 90 ) {
					movePage('sessionError.html');
				} else if ( data.status == 99 ) {
					movePage('unknown.html');
				}
				pageNo++;
				loadFlg = false;
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
			//	alert(errorThrown);
			}
		});
	}
	
	function movePage(url) {
		$(location).attr( 'href', url );
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
