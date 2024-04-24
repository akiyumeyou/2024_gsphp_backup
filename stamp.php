<?php
//0. SESSION開始！！
session_start();

//１．関数群の読み込み
include("funcs.php");

ini_set('display_errors', 1);
error_reporting(E_ALL);

//LOGINチェック → funcs.phpへ関数化しましょう！
sschk();

?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>スタンプ</title>
        <link href="css/stamp.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+JP&display=swap" rel="stylesheet">
  
    </head>
    <body>
   
 <header class="header">
    <?php include("inc/menu.php");?>

</header>
      <!-- Main[Start] -->
<form method="POST" action="stamp_save.php" enctype="multipart/form-data"><!-- enctype="" -->
    <div class="button">
        <fieldset>
            <legend>スタンプ作成</legend>
            <label>作成者：<?= htmlspecialchars($_SESSION["user_name"], ENT_QUOTES); ?></label><br>
            <input type="hidden" name="user_id" value="<?= htmlspecialchars($_SESSION["user_id"], ENT_QUOTES); ?>">
            <input type="hidden" name="user_name" value="<?= htmlspecialchars($_SESSION["user_name"], ENT_QUOTES); ?>">
            <label for="text-overlay">テキスト:</label>
            <input type="text" id="text-overlay" name="text_overlay">
            <label for="text-color">テキスト色:</label>
            <select name=text_color id="text-color">
                <option value="white">白</option>
                <option value="black">黒</option>
                <option value="pink">ピンク</option>
                <option value="green">緑</option>
                <option value="navy">紺</option>
            </select>
            <label for="text-position">テキスト位置:</label>
            <select name=text_position id="text-position">
                <option value="top">上</option>
                <option value="center">中央</option>
                <option value="bottom">下</option>
            </select>
            <label for="text-size">テキストサイズ:</label>
            <select name=text_size id="text-size">
                <option value="36">小</option>
                <option value="48">中</option>
                <option value="62">大</option>
            </select>
            <button type="button" id="apply-text">テキスト反映</button>
            <div id="image-container" style="position: relative; width: 320px; height: 480px; margin: auto;">
                <canvas id="preview-canvas" width="200" height="360"></canvas>
            </div>
            <label for="image">画像:</label>
            <div id="drop-area">
      <p>画像をドラッグ＆ドロップまたは<span class="file-input-label">クリック</span>して選択</p>
         <input type="file" id="image" name="image" hidden><br><br>
         <div id="file-name"></div>
        </fieldset>
  </div>
    <input type="submit" value="保存">
</form>

<!-- <script src="js/stamp.js"></script> -->
<script>
    // テキストのY位置を計算する関数
function calculateYPosition(canvas, position, size) {
    const height = canvas.height;
    switch (position) {
        case "top":
            return 20 + parseInt(size);  // テキストサイズを考慮したオフセットを追加
        case "center":
            return (height / 2) + (parseInt(size) / 2);  // 中央に配置するための調整
        case "bottom":
            return height - 20;  // 下部に配置し、オフセットを引く
        default:
            return 20 + parseInt(size);
    }
}
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('preview-canvas');
    const ctx = canvas.getContext('2d');
    const imageInput = document.getElementById('image');
    const dropArea = document.getElementById('drop-area');
    const fileNameContainer = document.getElementById('file-name');
    let img = null;

    // 画像ファイルが選択された時の処理
    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(event) {
                img = new Image();
                img.onload = function() {
                    // 画像を320x440ピクセルにリサイズして描画
                    canvas.width = 320;
                    canvas.height = 440;
                    ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                };
                img.src = event.target.result;
            };
            reader.readAsDataURL(file);
            fileNameContainer.textContent = "選択されたファイル: " + file.name;
        } else {
            fileNameContainer.textContent = "選択されたファイルは画像ではありません。";
        }
    });

    // ドラッグアンドドロップイベントの設定
    ['dragover', 'dragenter', 'dragleave', 'dragend', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, function(e) {
            e.preventDefault();
            if (['dragover', 'dragenter'].includes(eventName)) {
                dropArea.classList.add('active');
            } else {
                dropArea.classList.remove('active');
            }

            if (eventName === 'drop' && e.dataTransfer.files.length) {
                imageInput.files = e.dataTransfer.files;
                const event = new Event('change');
                imageInput.dispatchEvent(event);
            }
        }, false);
    });

    // クリックでファイル選択
    dropArea.addEventListener('click', function() {
        imageInput.click();
    });

    // テキスト反映ボタンが押された時の処理
    document.getElementById('apply-text').addEventListener('click', function() {
        if (!img) {
            alert("先に画像を選択してください。");
            return;
        }
    const text = document.getElementById('text-overlay').value;
    const color = document.getElementById('text-color').value;
    const position = document.getElementById('text-position').value;
    const size = document.getElementById('text-size').value;

    ctx.clearRect(0, 0, canvas.width, canvas.height); // 以前の描画をクリア
    ctx.drawImage(img, 0, 0, canvas.width, canvas.height); // 画像を再描画
    ctx.font = `${size}px Arial`;
    ctx.fillStyle = color;
    ctx.textAlign = 'center';

    const yPos = calculateYPosition(canvas, position, size);
    ctx.fillText(text, canvas.width / 2, yPos);

    });
});


</script>






<?php $date = date('Y年m月d日 H:i:s'); ?>
<?php echo $date; ?>
        </form>
        <!-- Main[End] -->
 <footer class="footer">
<?php include("inc/foot.html"); ?>
</footer>
</body>
</html>