<?php
//入力チェック(受信確認処理追加)
if(
  !isset($_POST["name"]) || $_POST["name"] =="" ||
  !isset($_POST["email"]) || $_POST["email"] =="" ||
  !isset($_POST["pass"]) || $_POST["pass"] ==""
){
  exit('ParamError');
}


//フォームからの値をそれぞれ変数に代入
$name = $_POST['name'];
$email = $_POST['email'];
//パスワードはハッシュ化する
$pass = password_hash($_POST['pass'], PASSWORD_BCRYPT);

//データベース接続
try {
  $pdo = new PDO('mysql:dbname=to_do_app;host=localhost;charset=utf8','root','root');
  echo "接続OK！";
} catch (PDOException $e) {
  echo 'DB接続エラー！: ' . $e->getMessage();
}

//フォームに入力されたmailとpassがすでに登録されていないかチェック
$sql = "SELECT * FROM users WHERE email = :email AND pass = :pass";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':email', $mail);
$stmt->bindValue(':pass', $pass);
$stmt->execute();
$member = $stmt->fetch();
if ($member['email'] == $email) {
    $msg = '同じメールアドレスが存在します。';
    $link = '<a href="signup.php">戻る</a>';
} else {
    //登録されていなければinsert 
    $sql = "INSERT INTO users(name, email, pass) VALUES (:name, :email, :pass)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':name', $name);
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':pass', $pass);
    $stmt->execute();
    $msg = '会員登録が完了しました';
    $link = '<a href="index.html">ログインページ</a>';
}
?>

<h1><?php echo $msg; ?></h1><!--メッセージの出力-->
<?php echo $link; ?>