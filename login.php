<?php
// Web上でのログイン情報をサーバー側に一時的に保存するため
session_start();

//入力チェック(受信確認処理追加)
if(
  !isset($_POST["email"]) || $_POST["email"] =="" ||
  !isset($_POST["pass"]) || $_POST["pass"] ==""
){
  exit('ParamError');
}
$email = $_POST['email'];

//データベース接続
try {
  $pdo = new PDO('mysql:dbname=to_do_app;host=localhost;charset=utf8','root','root');
  // echo "接続OK！";
} catch (PDOException $e) {
  echo 'DB接続エラー！: ' . $e->getMessage();
}

$sql = "SELECT * FROM users WHERE email = :email";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':email', $email);
$stmt->execute();
$member = $stmt->fetch();
//指定したハッシュがパスワードにマッチしているかチェック
if (password_verify($_POST['pass'], $member['pass'])) {
  //DBのユーザー情報をセッションに保存
  $_SESSION['id'] = $member['id'];
  $_SESSION['name'] = $member['name'];
  $msg = 'ログインしました。';
  $link = '<a href="after_login.php">ホーム</a>';
  header("Location: calender.php");
  exit;
} else {
  $msg = 'メールアドレスもしくはパスワードが間違っています。';
  $link = '<a href="index.html">ログイン画面へ</a>';
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/logout.css">
  <title>ログインエラー</title>
</head>
<body>
  <p><?= $msg?></p>
  <span><?= $link ?></span>
</body>
</html>