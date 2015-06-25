$(function(){

	$(".scroll").niceScroll({
		autohidemode: "scroll"
	})
});

/*
// バーのcolor
cursorcolor: "#424242",
// バーのmin_opacity
cursoropacitymin: 0,
// バーのmax_opacity
cursoropacitymax: 1,
// バーの幅
cursorwidth: "5px",
// バーのborder
cursorborder: "1px solid #fff",
// バーのborder-raidus
cursorborderradius: "5px",
// スクロールバー自体のz-index
zindex: "auto" | <number>,
// スクロールのスピード
scrollspeed: 60,
// マウスホイールのスピード（px）
mousescrollstep: 40,
// PC上でタッチデバイスのようなスクロールにするか
touchbehavior: false,
// スクロールを加速
hwacceleration: true,
// ボックスのコンテンツのズームを可能にする
boxzoom: false,
// ダブルクリックでズーム（boxzoomがtrueの時のみ）
dblclickzoom: true,
タッチデバイスでズーム（boxzoomがtrueの時のみ）
gesturezoom: true,
// grab icon表示（touchbehaviorがtrueの時のみ）
grabcursorenabled: true
// 自動でバーを消すかどうか
autohidemode: true,
			// true | // スクロールしていない時、自動で消える
			// "cursor" | // バーだけ消す（レールは残る）
			// false | // 何も消さない
			// "leave" | // Boxコンテンツから離れた時、消える
			// "hidden" | // スクロール時も全部消えたまま
			// "scroll", // スクロールしている時だけ出現
// レールのbackground
background: "",
// 読み込んだ時にリサイズする
iframeautoresize: true,
// バーのmin_height(px)
cursorminheight: 32,
// レールのpadding
railpadding: { top: 0, right: 0, left: 0, bottom: 0 },
// カーソルでドラッグしたときのスピード
cursordragspeed: 0.3,
preservenativescrolling: true, // you can scroll native scrollable areas with mouse, bubbling mouse wheel event
railoffset: false, // you can add offset top/left for rail position
bouncescroll: false, // (only hw accell) enable scroll bouncing at the end of content as mobile-like
spacebarenabled: true, // enable page down scrolling when space bar has pressed
disableoutline: true, // for chrome browser, disable outline (orange highlight) when selecting a div with nicescroll
horizrailenabled: true, // nicescroll can manage horizontal scroll
railalign: right, // alignment of vertical rail
railvalign: bottom, // alignment of horizontal rail
enabletranslate3d: true, // nicescroll can use css translate to scroll content
enablemousewheel: true, // nicescroll can manage mouse wheel events
enablekeyboard: true, // nicescroll can manage keyboard events
smoothscroll: true, // scroll with ease movement
sensitiverail: true, // click on rail make a scroll
enablemouselockapi: true, // can use mouse caption lock API (same issue on object dragging)
cursorfixedheight: false, // set fixed height for cursor in pixel
hidecursordelay: 400, // set the delay in microseconds to fading out scrollbars
directionlockdeadzone: 6, // dead zone in pixels for direction lock activation
nativeparentscrolling: true, // detect bottom of content and let parent to scroll, as native scroll does
enablescrollonselection: true, // enable auto-scrolling of content when selection text
rtlmode: "auto", // horizontal div scrolling starts at left side
cursordragontouch: false, // drag cursor in touch / touchbehavior mode also
oneaxismousemode: "auto", // it permits horizontal scrolling with mousewheel on horizontal only content, if false (vertical-only) mousewheel don't scroll horizontally, if value is auto detects two-axis mouse
scriptpath: "" // define custom path for boxmode icons ("" => same script path)
preventmultitouchscrolling: true // prevent scrolling on multitouch events

*/