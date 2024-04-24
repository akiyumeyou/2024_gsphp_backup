<?php
require_once("funcs.php"); // DB接続関数の呼び出し

ini_set('display_errors', 1);
error_reporting(E_ALL);

// POSTデータの検証
if (!isset($_POST["message_id"]) || !is_numeric($_POST["message_id"])) {
    echo "Invalid input";
    exit;
}

$messageno = $_POST["message_id"];
$conversation_id = 1; // conversation_idを指定

try {
    $pdo = db_conn(); // DB接続
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 更新操作
    $sql = "UPDATE P_conversation_table SET kidoku = ? WHERE conversation_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$messageno, $conversation_id]);

    // 成功した場合、メッセージ番号を返す
    echo $messageno;
} catch (PDOException $e) {
    // エラー発生時の処理
    echo "Database error: " . $e->getMessage();
}
?>


