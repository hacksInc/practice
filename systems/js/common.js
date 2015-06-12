$(function(){
	$(document)

	
})
// ページの読み込みが完全に完了したら以下の処理を実行
window.onload = function() {

  // 「#top-bar」を固定/解除するための基準となる値を取得し、変数「topbar」に代入
  var topbar = $("#top-bar").offset().top - $("#header_bar").height();

  // 「#bottom-bar」を固定/解除するための基準となる値を取得し、
  // 変数「bottombar」に代入
  var bottombar = $("#related-posts").offset().top - $(window).height() + 24
    + $("#top-bar").height();

  // 画面がスクロールされたら以下の処理を実行
  $(window).scroll(function() {

    // 「#top-bar」上にScrollTopの位置の値を表示
    $("#top-bar").text(
      "「#top-bar」scrollTop: " + $(this).scrollTop()
    );

    // ScrollTopの位置が「topbar」よりも値が大きければ、「#top-bar」を固定し、
    // 記事部分のブロック要素の位置を「#top-bar」の高さ分だけ下げる
    if($(window).scrollTop() > topbar) {

      $("#top-bar").css({"position": "fixed", "top": "38px"});
      $("#entry").css({"position": "relative", "top": $("#top-bar").height() + "px"});

    // 小さければ、「#top-bar」の固定を解除し、
    // 記事部分のブロック要素の位置を元に戻す
    } else {

      $("#top-bar").css("position", "static");
      $("#entry").css({"position": "relative", "top": 0});

    }

    // ScrollTopの位置が「bottombar」よりも値が小さければ、
    // 「#bottom-bar」を固定
    if($(window).scrollTop() < bottombar) {
      $("#bottom-bar").addClass("fixed-bottom");
    // 大きければ、「#bottom-bar」の固定を解除
    } else {
      $("#bottom-bar").removeClass("fixed-bottom");
    }

  });
