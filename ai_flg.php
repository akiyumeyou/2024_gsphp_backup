<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

$pdo = db_conn();  // DB接続

try {
    $stmt = $pdo->prepare("SELECT ai_flg FROM P_conversation_table WHERE conversation_id = 1");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $ai_flg = $result ? $result['ai_flg'] : 0;  // ai_flgがない場合はデフォルトで0に
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    $ai_flg = 0;  // エラー発生時にもデフォルト値を設定
}
?>
