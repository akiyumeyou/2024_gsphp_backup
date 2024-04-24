<?php
//1. POSTデータ取得
$name        = $_POST["name"];
$email       = $_POST["email"];
$date_option = $_POST["date_option"];
$naiyou      = $_POST["naiyou"];
$age         = $_POST["age"]; //追加されています

//2. DB接続します
include("funcs.php");
$pdo = db_conn();

//３．データ登録SQL作成
$stmt = $pdo->prepare("INSERT INTO gs_an_table(name,email,age,date_option,naiyou,indate)VALUES(:name,:email,:age,:date_option,:naiyou,sysdate())");
$stmt->bindValue(':name', $name, PDO::PARAM_STR);      //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':email', $email, PDO::PARAM_STR);    //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':age', $age, PDO::PARAM_INT);        //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':date_option', $date_option, PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':naiyou', $naiyou, PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$status = $stmt->execute(); //実行


//４．データ登録処理後
if($status==false){
  sql_error($stmt);
}else{
  redirect("index.php");
}
?>
