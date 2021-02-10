<?php
session_start();
$_SESSION = array();//セッションの中身をすべて削除
session_destroy();//セッションを破壊
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/logout.css">
  <title>ログアウト完了</title>
</head>
<body>
  <p>ログアウトしました。</p>
  <a href="index.html">ログイン画面へ</a>
</body>
</html>