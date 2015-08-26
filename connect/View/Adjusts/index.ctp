<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>2015夏ビアガーデン</title>
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
</head>
<body>
<script>
function statusChangeCallback(response) {
  console.log('statusChangeCallback');
  console.log(response);
  
  if (response.status === 'connected') {
  
    testAPI();
  
  } else if (response.status === 'not_authorized') {
  
    document.getElementById('status').innerHTML = 'ログインしてください。';
    $('#calender').hide();
  
  } else {

    document.getElementById('status').innerHTML = 'ログインしてください。';
    $('#calender').hide();
  }

}

function checkLoginState() {
  FB.getLoginStatus(function(response) {
    statusChangeCallback(response);
  });
}

window.fbAsyncInit = function() {
  FB.init({
    appId      : '1447592058902749',
    cookie     : true,
    xfbml      : true,
    version    : 'v2.2'
  });

  FB.getLoginStatus(function(response) {
    statusChangeCallback(response);
  });

};

(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/ja_JP/sdk.js#xfbml=1&version=v2.4";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

function testAPI() {
  console.log('Welcome!  Fetching your information.... ');
  FB.api('/me', function(response) {
    console.log('Successful login for: ' + response.name);
    document.getElementById('status').innerHTML =
    'Thanks for logging in, ' + response.name + '!';
    $('#calender').show();
  });
}
</script>

<div id="loginButton">
  <fb:login-button scope="public_profile" onlogin="checkLoginState();" data-size="xlarge" data-auto-logout-link="true" >
  </fb:login-button>
</div>

<div id="status">
</div>
<p>参加可能な日をクリックしてね！</p>
<p>（デフォルトは参加不可になっています）</p>
<table id="calender" style="display:none;">
  <tr>
    <th>日</th>
    <th>月</th>
    <th>火</th>
    <th>水</th>
    <th>木</th>
    <th>金</th>
    <th>土</th>
  </tr>
  <tr>
  <?php for($i=-5;$i<=1;$i++) : ?>
    <?php
      if($i==1){
        echo "<td>".$i."</td>";
      } else{
        echo "<td></td>";
      }
    ?>
  <?php endfor; ?>
  </tr>
  <tr>
  <?php for($i=2;$i<=8;$i++) : ?>
    <td><?php echo $i; ?></td>
  <?php endfor; ?>
  </tr>
  <tr>
  <?php for($i=9;$i<=15;$i++) : ?>
    <td><?php echo $i; ?></td>
  <?php endfor; ?>
  </tr>
  <tr>
  <?php for($i=16;$i<=22;$i++) : ?>
    <?php if($i > 17 && $i < 22) {
      echo '<td onClick="click('.$i.');">'.$i.'</td>';
    } else {
      echo '<td>'.$i.'</td>';
    }  
    ?>
  <?php endfor; ?>
  </tr>  
  <tr>
  <?php for($i=23;$i<=29;$i++) : ?>
    <td><?php echo $i; ?></td>
  <?php endfor; ?>
  </tr>
  <tr>
  <?php for($i=30;$i<=36;$i++) : ?>
    <?php
      if($i>31){
        echo "<td></td>";
      } else{
        echo "<td>".$i."</td>";    
      }
    ?>
  <?php endfor; ?>
  </tr>
</table>


</body>
</html>