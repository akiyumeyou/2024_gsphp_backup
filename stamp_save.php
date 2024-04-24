<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

include("funcs.php");
$pdo = db_conn();

$user_id = $_POST['user_id'];
$text_overlay = $_POST["text_overlay"] ?? 'Default Text';
$text_color = $_POST["text_color"] ?? 'black';
$text_position = $_POST["text_position"] ?? 'center';
$text_size = $_POST["text_size"] ?? 40;

function convertColor($colorName) {
    switch ($colorName) {
        case "black": return [0, 0, 0];
        case "white": return [255, 255, 255];
        case "pink": return [255, 192, 203];
        case "green": return [0, 128, 0];
        case "navy": return [0, 0, 128];
        default: return [0, 0, 0];
    }
}

function calculateYPosition($imageHeight, $position, $fontSize) {
    switch ($position) {
        case "top":
            return 20 + $fontSize;
        case "center":
            return $imageHeight / 2;
        case "bottom":
            return $imageHeight - $fontSize - 20;
        default:
            return $imageHeight / 2;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
    $uploadDir = 'stdata/';
    $uploadedFileName = basename($_FILES['image']['name']);
    $uploadedFile = $uploadDir . $uploadedFileName;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadedFile)) {
        $imageData = file_get_contents($uploadedFile);
        $image = imagecreatefromstring($imageData);
        if (!$image) {
            die("画像ファイルを読み込めませんでした。");
        }

        $width = 320;
        $height = 440;
        $resizedImage = imagecreatetruecolor($width, $height);
        if (!$resizedImage) {
            die("リサイズ用イメージの作成に失敗しました。");
        }

        if (!imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $width, $height, imagesx($image), imagesy($image))) {
            die("画像のリサイズに失敗しました。");
        }

        list($r, $g, $b) = convertColor($text_color);
        $color = imagecolorallocate($resizedImage, $r, $g, $b);
        $fontPath = 'font/static/NotoSansJP-ExtraBold.ttf';
        $x = $width / 2;
        $y = calculateYPosition($height, $text_position, $text_size);

        list($left, , $right) = imageftbbox($text_size, 0, $fontPath, $text_overlay);
        $textWidth = $right - $left;
        $x -= $textWidth / 2;

        if (!imagettftext($resizedImage, $text_size, 0, $x, $y, $color, $fontPath, $text_overlay)) {
            die("テキストの追加に失敗しました。");
        }

        if (!imagejpeg($resizedImage, $uploadedFile)) {
            die("画像の保存に失敗しました。");
        }

        imagedestroy($image);
        imagedestroy($resizedImage);

        $stmt = $pdo->prepare("INSERT INTO P_stamp_table(user_id, image) VALUES (:user_id, :newfile)");
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':newfile', $uploadedFile, PDO::PARAM_STR);
        $status = $stmt->execute();
        
        if ($status === false) {
            sql_error($stmt);
        } else {
            redirect("stamp.php");
        }
    } else {
        echo "ファイルのアップロードに失敗しました。";
    }
} else {
    echo "適切なデータがPOSTされていません。";
}

exit();
?>
