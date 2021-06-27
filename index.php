<?php

#消してもいいところ:ここから
$appname = "Twitterの名前に拡張子を追加するやつ";

require_once "C:/htdocs/path/pathconfig.php";
require_once "C:/htdocs/applist.php";
require_once crow_config;
require_once crow_autoload;
require_once crow_twitteroauth;

echo(crows_app_base(0,$appname));
#消してもいいところ:ここまで


#消しちゃあだめ:ここから
use Abraham\TwitterOAuth\TwitterOAuth;
use Abraham\TwitterOAuth\TwitterOAuthException;
#消しちゃあだめ:ここまで


login_crows_app(); #消してもいいところ


#消しちゃあだめ:ここから
if(!empty($_SESSION['twAccessToken'])){

  $objTwitterConection = new TwitterOAuth
  (
    $sTwitterConsumerKey,
    $sTwitterConsumerSecret,
    $_SESSION['twAccessToken']['oauth_token'], #アクセストークン
    $_SESSION['twAccessToken']['oauth_token_secret'] #アクセストークンシークレット
  );


  $objTwUserInfo = $objTwitterConection->get("account/verify_credentials");

}
#消しちゃあだめ:ここまで

#消してもいいところ:ここから
elseif(empty($_SESSION['twAccessToken']['screen_name'])){
    echo(crows_app_base(1,$appname));

    echo("<br><br><br>");
    
    echo("<h2>".$appname."を利用するには<a href='../../login/login.php'>ログイン</a>してください。<h2>");

    echo("<br><br><br>");
    echo(crows_app_base(2,$appname));
    exit;
}


$screen_name = $_SESSION['twAccessToken']['screen_name'];



echo(crows_app_base(1,$appname));
#消してもいいところ:ここまで
?>

<?php #消してもいいところ:ここから?>
<br><br>
<h1><?=$appname?></h1>
<br>
<br>
<?php #消してもいいところ:ここまで?>


<?php

#消しちゃあだめ:ここから

#jsonファイルのパス
$file = "./extension_list.json";

#ファイルの内容を取得する
$list = file_get_contents($file);

#jsonをデコードする
$list = json_decode($list,true);

#変えない部分
$i=0;

#加工しやすいように数字にする
foreach ($list as $value) {
  $extension_num_list[$i] = $value;
  $i=$i+1;
}

#拡張子のリストをシャッフルする
shuffle($extension_num_list);

?>

<?php #消しちゃあだめ:ここから ?>

<form action="./index.php?tweet=True" method="POST">
<br>
<label>追加する文字列: 
<input type="text" name="emoji"  value="🤔"></label>
<br><br>
<button type="submit" name="name" value="<?php echo($extension_num_list[0]); #ランダムな拡張子 ?>"><h4>名前を変更してツイートする</h4></button>
</form>
<form action="./index.php" method="POST">
<button type="submit" name="name" value="<?php echo($extension_num_list[0]); #ランダムな拡張子 ?>">名前を変更する</button>
</form>

<?php

if(!empty($_POST["emoji"])){

  #$_POST["emoji"]に何か文字列がある場合
  $add_emoji = $_POST["emoji"];

}
else{

  #$_POST["emoji"]に何もない場合
  $add_emoji = "🤔";

}

#ユーザー情報を取得する
$info = json_decode(json_encode($objTwUserInfo), true);

#絵文字を消去する
$after=preg_replace('/[\xF0-\xF7][\x80-\xBF][\x80-\xBF][\x80-\xBF]/','/[\xF0-\xF7][\x80-\xBF][\x80-\xBF][\x80-\xBF]/',$info["name"]);

#拡張子を削除する
$after=(explode(".",$after));

#$afterとランダムな拡張子と$_POST["emoji"]を足す
$name_changed = $after[0].".".$extension_num_list[0].$add_emoji;

echo("<br><br><h2>プレビュー:</h2><br>");
echo("<h2>".$name_changed."</h2>");

echo("<br><br><h2>ログ:</h2><br>");

if(!empty($_POST["name"])){ 
  
  #$_POST["name"]が空でない場合

  #名前を変更する$name_changedは先ほど設定したもの
  $profile_request = $objTwitterConection->post("account/update_profile", ["name" => "{$name_changed}"]);

  echo("<br><h3>名前を変更しました。</h3>");

  #URL:index.php?tweet=Trueのとき
  if($_GET["tweet"] == True){

    echo("<div class='add_blank'>ツイートしました。</div>");

    $result = $objTwitterConection->post("statuses/update",array("status" => "名前を変更しました:{$name_changed} via Crow's APP"));

  }
}
else{

  #$_POST["name"]が空の場合

  echo("<div class='add_blank'>ログはまだ何もないようです...</div>");

}
#消しちゃあだめ:ここまで

echo(crows_app_base(2,$appname)); #消してもいいところ

?>
