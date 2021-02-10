<?php
//共通に使う関数を記述
// DB接続文
function db_conect(){
  try {
    $pdo = new PDO('mysql:dbname=to_do_app;host=localhost;charset=utf8','root','root');
    return $pdo;
  } catch (PDOException $e) {
    echo 'DB接続エラー！: ' . $e->getMessage();
  }
}

//SQLエラー
function sql_error($stmt){
  //execute（SQL実行時にエラーがある場合）
  $error = $stmt->errorInfo();
  exit("SQLError:".$error[2]);
}

?>