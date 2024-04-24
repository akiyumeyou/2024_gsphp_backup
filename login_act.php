<?php
//最初にSESSIONを開始！！ココ大事！！
session_start();
ini_set('display_errors', "On");
error_reporting(E_ALL);

//POST値
$lid = $_POST["lid"]; //lid
$lpw = $_POST["lpw"]; //lpw

//1.  DB接続します
include("funcs.php");
$pdo = db_conn();

//2. データ登録SQL作成
//* PasswordがHash化→条件はlidのみ！！
$stmt = $pdo->prepare("SELECT * FROM potz_user_table WHERE lid = :lid AND life_flg=0 ");
$stmt->bindValue(':lid', $lid, PDO::PARAM_STR);

$status = $stmt->execute();

//3. SQL実行時にエラーがある場合STOP
if($status==false){
    sql_error($stmt);
}

//4. 抽出データ数を取得
$val = $stmt->fetch();         //1レコードだけ取得する方法
//$count = $stmt->fetchColumn(); //SELECT COUNT(*)で使用可能()


//5.該当１レコードがあればSESSIONに値を代入
//入力したPasswordと暗号化されたPasswordを比較！[戻り値：true,false]
$pw = password_verify($lpw, $val["lpw"]); //$lpw = password_hash($lpw, PASSWORD_DEFAULT);   //パスワードハッシュ化
if($pw){ 
  //Login成功時
  $_SESSION["chk_ssid"]  = session_id();
  $_SESSION["kanri_flg"] = $val['kanri_flg'];
  $_SESSION["user_name"]      = $val['user_name'];
  $_SESSION["user_id"]      = $val['user_id'];
  //Login成功時（select.phpへ）
  // $file_name = $_GET['file_name'] ;
  // redirect("$file_name");
  redirect("index.php");
}else{
  //Login失敗時(login.phpへ)
  ini_set('display_errors', "On");
error_reporting(E_ALL);
  redirect("login.php");

}

exit();


