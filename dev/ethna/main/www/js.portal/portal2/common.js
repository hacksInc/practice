$(function() {

	// スクロールを抑止する関数
	function preventScroll(event) {

	  // li要素だけは、タップイベントに反応したいので、抑止しない。
	  if (event.touches[0].target.tagName.toLowerCase() == "li") {return;}
	  if (event.touches[0].target.tagName.toLowerCase() == "a") {return;}
	  //if (event.touches[0].target.tagName.toLowerCase() == "img") {return;}
	  if (event.touches[0].target.tagName.toLowerCase() == "select") {return;}
	  if (event.touches[0].target.tagName.toLowerCase() == "input") {return;}
	  if (event.touches[0].target.className == "changeText") {return;}
	  if (event.touches[0].target.className == "backLink") {return;}
	  if (event.touches[0].target.className == "backLink2") {return;}
	  if (event.touches[0].target.className == "backBtn") {return;}
	  if (event.touches[0].target.className == "backBtn2") {return;}
	  if (event.touches[0].target.className == "footerBtn") {return;}
	  if (event.touches[0].target.className == "onePaper") {return;}
	  if (event.touches[0].target.className == "theme") {return;}
	  if (event.touches[0].target.className == "shere") {return;}
	  if (event.touches[0].target.id == "overlay") {return;}
	  if (event.touches[0].target.id == "resultBackText") {return;}
	  if (event.touches[0].target.id == "errorBackText") {return;}
	  if (event.touches[0].target.id == "voting_banner") {return;}
	  if (event.touches[0].target.id == "toGame") {return;}
	  if (event.touches[0].target.id == "yes") {return;}
	  if (event.touches[0].target.id == "cancel") {return;}
	  if (event.touches[0].target.id == "vote_btn") {return;}
	  if (event.touches[0].target.id == "ranking_btn") {return;}
	  if (event.touches[0].target.id == "backBtn") {return;}
	  if (event.touches[0].target.id == "kogami") {return;}
	  if (event.touches[0].target.id == "tsunemori") {return;}
	  if (event.touches[0].target.id == "kagari") {return;}
	  if (event.touches[0].target.id == "ginoza") {return;}
	  if (event.touches[0].target.id == "kunizuka") {return;}
	  if (event.touches[0].target.id == "masaoka") {return;}
	  if (event.touches[0].target.id == "karanomori") {return;}


	  // preventDefaultでブラウザ標準動作を抑止する。
	  event.preventDefault();
	}

	// タッチイベントの初期化
	document.addEventListener("touchstart", preventScroll, false);
	document.addEventListener("touchmove", preventScroll, false);
	document.addEventListener("touchend", preventScroll, false); 
	// ジェスチャーイベントの初期化
	document.addEventListener("gesturestart", preventScroll, false);
	document.addEventListener("gesturechange", preventScroll, false);
	document.addEventListener("gestureend", preventScroll, false);

});

function scroll(){
	$(".scroll").niceScroll();
	return false;
}