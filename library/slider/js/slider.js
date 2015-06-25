$(function(){

	$('.slider').bxSlider({
		auto: true,  // 自動再生
		infiniteLoop: true,  // 無限ループ
		speed: 1000, // スライドスピード（ミリ秒）
		pager: false, // ページャ
		slideWidth: 980, // 画像のサイズ
		minSlides: 1, // スライダーエリアに表示する最小の要素数を指定する
		maxSlides: 1, // スライダーエリアに表示する最大の要素数を指定する
		moveSlides: 1, // 1回の遷移でスライドさせる要素の数を指定する
		pause: 4000, // 停止時間の指定（ミリ秒）
	});
});

/************** オプション一覧 ****************
// 下記は全てデフォルト値

// スライドモード指定 (horizontal, vartivcal, fade)
mode: 'horizontal',
// 無限ループ
infiniteLoop: true,
// 一回のスライドにかかる時間を指定（ミリ秒）
speed: 500,
// 自動遷移を有効にする
auto: false,
// 自動遷移をロード時にスタート。falseにした場合コントロールから明示的にスタートする必要がある
autoStart: true,
// 停止時間の指定（ミリ秒）
pause: 4000,
// 「前へ」「次へ」の表示オプション。最初と最後のページで「前へ」もしくは「次へ」を非表示にする 。無限ループ時は無効。
hideControlOnEnd: true,
// ページャを表示する
pager: true,
// 'full'：スライダー要素の数だけページャが表示。'short'：現在の要素の順番と全体の要素数を表示
pagerType: 'full',
// マウスオーバー時に自動遷移を停止する
autoHover: false,
// 自動遷移を開始するまでの待ち時間を指定する。最初の要素だけ長く表示するなど、初回の遷移開始を遅らせることができる
autoDelay: 0,
// スライダーエリアに表示する最小の要素数を指定する
minSlides: 1,
// スライダーエリアに表示する最大の要素数を指定する
maxSlides: 1,
// 1回の遷移でスライドさせる要素の数を指定する
moveSlides: 0,
// スライドさせる要素の大きさ（幅）を指定する。デフォルトでは100%になり、スライドエリアと同じサイズになる
slideWidth: 0,
// スライド要素間のマージンを指定する
slideMargin: 0,
// スライド開始ポイントの指定。0がスタートで任意の数値を指定
startSlide: 0,
// スライド開始ポイントをランダムにする
randomStart: false,
// レスポンシブ対応
responsive: true,
// スライダー対象となる子要素を指定する。セレクタ指定はjQueryのセレクタルールを利用する
slideSelector: '',
// イージング指定。イージングを利用するには別途プラグインが必要。
easing: null,
// 画像下部に説明文の表示。画像のtitleタグのテキストを表示
captions: false,
// tickerモード。スライダーが一定の速度でずっと流れる
ticker: false,
// tickerモード時に、マウスオーバーされたらアニメーションを一時停止する。ただし、useCSSがtrueの場合利用できない
tickerHover: false,
// スライド要素の高さに合わせてスライダーエリアの高さを調整する
adaptiveHeight: true,
// スライダーエリアの高さの調整のアニメーション時間を調整する
adaptiveHeightSpeed: 500,
// 動画をレスポンシブに対応させる。これを有効にするには別途ライブラリが必要になる
video: false,
// スライダーのアニメーションにCSSアニメーションを利用する
useCSS: true,
// 事前に読み込んでおく画像の種類を指定する。'visible', 'all'のどちらかを指定する。カルーセルにする場合、内部で子要素の画像すべての指定に強制される
preloadImages: 'visible',
//　タッチデバイスに対応する trueにすることで、スワイプでもスライドするようになる
touchEnabled: true,
// スワイプ判定のための距離を指定する
swipeThreshold: 50,
// スワイプの動きと画像のスライドを対応させる。スワイプ動作に画像が連動するようなスライドをさせる
oneToOneTouch: true,
// X軸方向へのデフォルトのスワイプ操作の動きを制御する。画像をスワイプしても画面はスワイプしないようにする
preventDefaultSwipeX: true,
// Y軸方向へのデフォルトのスワイプ操作の動きを制御する
preventDefaultSwipeY: false,
// 'short'を指定した際の、現在の要素の順番と全体の要素数の区切り文字を指定
pagerShortSeparator: ' / ',
// ページャを構築する要素を指定する。セレクタはjQueryのセレクタを指定。任意のDOMに対してページャを配置できる
pagerSelector: null,
// ページャ作成用のメソッドを定義する。ページャの各要素のDOMを返すメソッドを渡せる
buildPager: null,
// 指定した別のスライダー要素をページャとして利用する。ページャと画像のマッピングはdata-slide-indexカスタム属性を利用して行われる
pagerCustom: null,
// コントロールを表示する
controls: true,
// 「次へ」ボタンのテキストを指定する
nextText: 'Next',
// 「前へ」ボタンのテキストを指定する
prevText: 'Prev',
// 「次へ」ボタンとして利用する要素のセレクタを指定する
nextSelector: null,
// 「前へ」ボタンとして利用する要素のセレクタを指定する
prevSelector: null,
// 自動遷移のコントロールを表示する。autoプロパティがtrueの場合にのみ有効になる
autoControls: false,
// 「スタート」ボタンのテキストを指定する
startText: 'Start',
// 「ストップ」ボタンのテキストを指定する
stopText: 'Stop',
// 「スタート」「ストップ」のどちらか片方のボタンだけ表示。再生中は「ストップ」、停止中は「スタート」ボタンだけ表示される
autoControlsCombine: false,
// 自動遷移のコントロールを構築する要素を指定する
autoControlsSelector: null,

// 自動遷移の方向を指定する。'next', 'prev'のどちらかを指定
autoDirection: 'next',
// スライダーがロード完了した際のコールバックを指定する
onSliderLoad: function() {},
 // スライドが開始する前に実行したいコールバックを指定する
onSlideBefore: function() {},
// スライドが完了した後に実行したいコールバックを指定する
onSlideAfter: function() {},
// 「次へ」のスライドが行われる前に実行したいコールバックを指定する。onSlideBeforeの後に実行される
onSlideNext: function() {},
// 「前へ」のスライドが行われる前に実行したいコールバックを指定する。onSlideBeforeの後に実行される
onSlidePrev: function() {}

*****************************************/