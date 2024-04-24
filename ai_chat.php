<?php

$pdo = db_conn();

// ユーザーIDとメッセージをPOSTリクエストから取得
$user_id = $_POST['user_id'];
$message = $_POST['message'];

// ai_flgとai_useridをデータベースから取得
$stmt = $pdo->prepare("SELECT ai_flg, ai_userid FROM P_conversation_table WHERE conversation_id = 1");
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result && $result['ai_flg'] == 1 && $result['ai_userid'] == $user_id) {
    // 条件に一致する場合、応答メッセージを返す
    echo "AI Response: こちらはAIからの返信です。";  // 応答内容は適宜調整
} else {
    // 条件に一致しない場合、何もしないか、別の処理を行う
    echo "通常の処理または無反応。";
}
?>
