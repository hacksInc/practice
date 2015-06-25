<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>アニメ「PSYCHO-PASS サイコパス」</title>
<meta name="keywords" content="">
<meta name="description" content="">
<meta name="format-detection" content="telephone=no, email=no">
<link rel="stylesheet" href="/psychopass_portal/css/portal2/reset.css" media="all">
<link rel="stylesheet" href="/psychopass_portal/css/portal2/common.css" media="all">
<link rel="stylesheet" href="/psychopass_portal/css/portal2/exchange.css" media="all">
<script src="/psychopass_portal/js/portal2/jquery.js"></script>
<script src="./js/portal2/jquery.nicescroll.min.js"></script>
<script src="./js/portal2/exchange.js"></script>
<script src="./js/portal2/common.js"></script>
</head>
<body onload="exchange();">

<div id="portalBackground">

<header class="clearfix">
	<img id="headerTitleFrame" src="/psychopass_portal/img/portal/headerTitle.png" width="100%">
	<img id="mark" src="/psychopass_portal/img/portal/psychoMark.png" width="12%">
	<img id="votePtFrame" src="/psychopass_portal/img/portal/votePt.png" width="100%">
	<img id="portalPtFrame" src="/psychopass_portal/img/portal/portalPt.png" width="100%">
	<a onClick="Unity.call('jump_to_game');"><img id="toGame" src="/psychopass_portal/img/portal/toGame.png" width="100%"></a>
	<p id="headerTitle">SPECIAL</p>
	<p id="userNumber">{$app.user.pp_id}</p>
	<p id="votePt">{$app.voting.point|intval}pt</p>
	<p id="portalPt">{$app.user.point}pt</p>
</header>

<div id="background">
	<img src="/psychopass_portal/img/exchange/backgroundFrame.png" width="100%">

	<div id="contentBox">
		<img src="/psychopass_portal/img/exchange/titleBar.png" width="100%">
		<p id="title">投票券交換所</p>
		<img id="topImage" src="/psychopass_portal/img/exchange/topImage.png" width="100%">
		
		<div id="rate">
			<img src="/psychopass_portal/img/exchange/rate.png" width="100%">
			<p id="portalRate">ポータルポイント<br>100POINT</p>
			<p id="ticketRate">投票券<br>1枚</p>
		</div>

		<div id="myPortal">
			<img src="/psychopass_portal/img/exchange/myPortal.png" width="100%">
			<p id="havPortal">所持ポータルポイント</p>
			<p id="myPotalPt"><span>{$app.user.point}</span> POINT</p>
		</div>

		<div id="myTicket">
			<img src="/psychopass_portal/img/exchange/myTicket.png" width="100%">
			<p id="havTicket">所持投票券</p>
			<p id="myTicketNumber"><span>{$app.voting.point|intval}</span> 枚</p>
		</div>

		<div id="trade">
			<img src="/psychopass_portal/img/exchange/tradeFrame1.png" width="100%">
			<img id="tradeSelect" src="/psychopass_portal/img/exchange/tradeSelect.png" width="100%">
			<img id="tradePt" src="/psychopass_portal/img/exchange/tradePt.png" width="100%">
			<img id="tradeArrow" src="/psychopass_portal/img/exchange/tradeArrow.png" width="100%">
			<img id="tradeConfirm" src="/psychopass_portal/img/exchange/tradeConfirm.png" width="100%">
			<img id="tradeFrame2" src="/psychopass_portal/img/exchange/tradeFrame2.png" width="100%">
			<p id="tradeTitle">交換するポイントを入力してください。</p>
			<select name="changePt" id="ptList" onChange="exchange();">
				<option value="1">1</option>
				<option value="2">2</option>
				<option value="3">3</option>
				<option value="4">4</option>
				<option value="5">5</option>
				<option value="10">10</option>
				<option value="15">15</option>
			</select>
			<p id="ppp">POINT</p>
			<p id="confirmPt"><span>0 </span> 枚の投票券と交換します。<br>よろしいですか？</p>
			<img id="ticketImg" src="/psychopass_portal/img/exchange/ticket.png" width="100%">
		</div>

		<div id="select">
			<img id="selectFrame" src="/psychopass_portal/img/exchange/select.png" width="100%">
			<a onClick="Unity.call('{$app.url}/psychopass_portal/exchangeResult.php');"><img id="yes" src="/psychopass_portal/img/exchange/yesBtn.png" width="100%"></a>
			<a onClick="Unity.call('{$app.url}/psychopass_portal/content.php');"><img id="cancel" src="/psychopass_portal/img/exchange/cancelBtn.png" width="100%"></a>
		</div>

	<!-- contentBox --></div>
<!-- background --></div>

<footer class="clearfix">
	<img id="btnFrame" src="/psychopass_portal/img/portal/btnFrame.png">
	<ul id="btn">
		<li><a onClick="Unity.call('{$app.url}/psychopass_portal/index.php');"><img class="footerBtn" src="/psychopass_portal/img/portal/btn1.png" width="100%"></a></li>
		<li><a onClick="Unity.call('{$app.url}/psychopass_portal/news.php');"><img class="footerBtn" src="/psychopass_portal/img/portal/btn2.png" width="100%"></a></li>
		<li><a onClick="Unity.call('{$app.url}/psychopass_portal/content.php');"><img class="footerBtn" src="/psychopass_portal/img/portal/btn3.png" width="100%"></a></li>
		<li><a onClick="Unity.call('{$app.url}/psychopass_portal/info.php');"><img class="footerBtn" src="/psychopass_portal/img/portal/btn4.png" width="100%"></a></li>
		<li><a onClick="Unity.call('{$app.url}/psychopass_portal/mypage.php');"><img class="footerBtn" src="/psychopass_portal/img/portal/btn5.png" width="100%"></a></li>
	</ul>
</footer>


</div><!-- portalBackground -->
<div id="overlay" style="z-index:10;"></div>
</body>
</html>
