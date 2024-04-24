<?php
// 開発中エラー確認
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once("funcs.php"); // DB接続関数の呼び出し
$pdo = db_conn();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // エラーモードを例外に設定


header('Content-Type: application/json'); // JSON形式でデータを出力する設定

// POSTリクエストがあった場合はデータを保存する
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id   = $_POST['user_id'];
    $user_name = $_POST['user_name'];
    $content = $_POST['content'];
    $conversation_id = 1;
    $message_type = $_POST['message_type']; // message_typeはPOSTデータから直接取得

    // スタンプの場合、contentを'image_path'で保存
    if ($message_type == 'stamp') {
        $content = 'stdata/' . $content;
    }

    $stmt = $pdo->prepare("INSERT INTO P_message_table (user_id, user_name, conversation_id, message_type, content, timestamp) VALUES (:user_id, :user_name, :conversation_id, :message_type, :content, sysdate())");
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':user_name', $user_name, PDO::PARAM_STR);
    $stmt->bindValue(':conversation_id', $conversation_id, PDO::PARAM_INT);
    $stmt->bindValue(':message_type', $message_type, PDO::PARAM_STR);
    $stmt->bindValue(':content', $content, PDO::PARAM_STR);

    if ($stmt->execute() === false) {
        echo json_encode(['error' => 'Failed to save message']);
        exit;
    }
// メッセージ保存後、AIフラグとユーザーIDをチェック
$stmt = $pdo->prepare("SELECT ai_flg, ai_userid FROM P_conversation_table WHERE conversation_id = :conversation_id");
$stmt->bindValue(':conversation_id', $conversation_id, PDO::PARAM_INT);
$stmt->execute();
$ai_data = $stmt->fetch(PDO::FETCH_ASSOC);

if ($ai_data['ai_flg'] == 1 && $ai_data['ai_userid'] == $user_id) {
    // ai_chat.phpを呼び出してChatGPTからのメッセージを取得
    include("ai_chat.php");
    $response = get_chat_response($content); // ai_chat.phpで定義される関数
    echo json_encode(['success' => 'Message saved', 'ai_response' => $response]);
} else {
    echo json_encode(['success' => 'Message saved']);
}
    exit;
} else {
   // GETリクエストの場合はデータベースからメッセージを読み込む
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    try {
        $stmt = $pdo->prepare("SELECT * FROM P_message_table ORDER BY timestamp DESC");
        $stmt->execute();
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($messages);
    } catch (PDOException $e) {
        // エラーが発生した場合はエラーメッセージを返す
        http_response_code(500); // サーバーエラーを示すステータスコード
        echo json_encode(['error' => 'メッセージの読み込みに失敗しました: ' . $e->getMessage()]);
        exit;
    }
}
};
?>
