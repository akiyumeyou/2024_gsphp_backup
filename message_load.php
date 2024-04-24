<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);


require_once("funcs.php");
$pdo = db_conn();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    try {
        $stmt = $pdo->prepare("SELECT * FROM P_message_table ORDER BY timestamp DESC");
        $stmt->execute();
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($messages);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to load messages: ' . $e->getMessage()]);
        exit;
    }
}
?>
