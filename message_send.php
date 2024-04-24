<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once("funcs.php");
$pdo = db_conn();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $user_name = $_POST['user_name'];
    $content = $_POST['content'];
    $conversation_id = 1;
    $message_type = $_POST['message_type'];

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
    $json = json_encode($messages);

    if (!$stmt->execute()) {
        echo json_encode(['error' => 'Failed to save message']);
        exit;
    }
}

    // メッセージタイプがテキストの場合の処理
if ($message_type == 'text') {
    // データベースからAI設定を取得
    $stmt = $pdo->prepare("SELECT ai_flg, ai_userid FROM P_conversation_table WHERE conversation_id = :conversation_id");
    $stmt->bindValue(':conversation_id', $conversation_id, PDO::PARAM_INT);
    $stmt->execute();
    $ai_data = $stmt->fetch(PDO::FETCH_ASSOC);

    // AIが有効でユーザーIDが一致する場合の処理
    if ($ai_data && $ai_data['ai_flg'] == 1 && $ai_data['ai_userid'] == $user_id) {
        // POSTデータからメッセージ内容を取得
        $message_content = isset($_POST['message_content']) ? $_POST['message_content'] : 'デフォルトのメッセージ';
        $response = null; // 応答の初期化
        
        // AI APIからの応答を取得
        include("ai_api.php");
        $response = get_chat_response($message_content);

        // AIの応答をデータベースに保存
        $stmt = $pdo->prepare("INSERT INTO P_message_table (user_id, user_name, conversation_id, message_type, content, timestamp) VALUES (:user_id, :user_name, :conversation_id, :message_type, :content, CURRENT_TIMESTAMP)");
        $stmt->bindValue(':user_id', 10, PDO::PARAM_INT);
        $stmt->bindValue(':user_name', 'POTZ_AI', PDO::PARAM_STR);
        $stmt->bindValue(':conversation_id', $conversation_id, PDO::PARAM_INT);
        $stmt->bindValue(':message_type', 'text', PDO::PARAM_STR);
        $stmt->bindValue(':content', $response, PDO::PARAM_STR);

        // データベース保存処理の実行とエラーチェック
        if (!$stmt->execute()) {
            echo json_encode(['error' => 'Failed to save AI response: ' . implode(', ', $stmt->errorInfo())]);
            exit;
        }

        // 応答がある場合は成功メッセージとともに応答を返す
        if ($response) {
            echo json_encode(['success' => 'Message and AI response saved', 'ai_response' => $response]);
        } else {
            echo json_encode(['success' => 'Message saved without AI response', 'ai_response' => null]);
        }
    } else {
        // AI条件に一致しない場合の応答
        echo json_encode(['success' => 'No AI response required']);
    }
}
?>

