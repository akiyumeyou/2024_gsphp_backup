<?php
session_start();

include "funcs.php";
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>USERデータ登録</title>
  <link href="css/style.css" rel="stylesheet">
  <style>div{padding: 10px;font-size:16px;}</style>
</head>
<body>

<!-- Head[Start] -->
<header>
 <?php include("inc/head.html"); ?>
</header>
<!-- Head[End] -->

<?php
// ログイン状態をチェック
if (isset($_SESSION["user_name"])) {
    // ログインしている場合はエラーメッセージを表示
    echo "<p>既にログインしています。会員登録はログオフ状態でのみ可能です。</p>";
} else {
    // ログオフ状態の場合は、通常通り登録フォームを表示
?>
    <!-- Main[Start] -->
    <form method="post" action="user_insert.php">
      <div class="botron">
       <fieldset>
        <legend>ユーザー登録</legend>
         <label>お 名 前 ：<input type="text" required name="user_name"></label><br>
         <label>Login ID：<input type="text" required name="lid"></label><br>
         <label>Login PW:<input class="input_pw" type="password" required name="lpw"></label><br><br>
         <label>会員種別：
          一般<input type="radio" name="kanri_flg" value="0" checked>
          管理者<input type="radio" name="kanri_flg" value="1">
        </label>
        <br>
         <input type="submit" value="送信">
        </fieldset>
      </div>
    </form>
    <!-- Main[End] -->
<?php
}
?>

</body>
</html>
