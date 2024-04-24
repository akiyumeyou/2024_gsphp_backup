<?php
//$_SESSION使うよ！
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
//※htdocsと同じ階層に「includes」を作成
//include "../../includes/funcs.php";
include "funcs.php";
//sschk();

//1. POSTデータ取得
$user_name      = filter_input( INPUT_POST, "user_name" );
$lid       = filter_input( INPUT_POST, "lid" );
$lpw       = filter_input( INPUT_POST, "lpw" );
$kanri_flg = filter_input( INPUT_POST, "kanri_flg" );
$lpw       = password_hash($lpw, PASSWORD_DEFAULT);   //パスワードハッシュ化

//2. DB接続します
$pdo = db_conn();

//３．データ登録SQL作成
$sql = "INSERT INTO potz_user_table(user_name,lid,lpw,kanri_flg,life_flg, potz_flg)VALUES(:user_name,:lid,:lpw,:kanri_flg,0,0)";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_name', $user_name, PDO::PARAM_STR); //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':lid', $lid, PDO::PARAM_STR); //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':lpw', $lpw, PDO::PARAM_STR); //Integer（数値の場合 PDO::PARAM_INT)::PARAM_STR); //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':kanri_flg', $kanri_flg, PDO::PARAM_INT); //Integer（数値の場合 PDO::PARAM_INT)
$status = $stmt->execute();

//４．データ登録処理後
if ($status == false) {
    // エラー処理
    sql_error($stmt);
} else {
    // 登録成功後、セッションにユーザー情報を保存
    $_SESSION["chk_ssid"] = session_id();
    $_SESSION["kanri_flg"] = $kanri_flg; // 管理者フラグも必要であれば保存
    $_SESSION["user_name"] = $user_name; // ユーザー名をセッションに保存
    $_SESSION["user_id"] = $pdo->lastInsertId(); // 最後に挿入された行のIDを取得して保存

    // ユーザーをメインページなどにリダイレクト
    redirect("index.php"); // 適切なリダイレクト先に変更してください
}

