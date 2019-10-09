<?php

require_once(__DIR__ . '/config.php');

$score = $_SESSION['knockDownCount'] * $_SESSION['correctAnswerConut'];

?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">    
    <title>クイズRPG</title>
    <link rel="stylesheet" href="css/style.css">
  </head>
<body>
  <div id="contents">
    <div class="score-area">
      <p>倒したモンスター数：<?php echo $_SESSION['knockDownCount']; ?></p>
      <p>正解数：<?php echo $_SESSION['correctAnswerConut']; ?></p>
      <p>スコア：<?php echo $score; ?></p>
    </div>
    <div class="link-container">
      <a href="index.php" class="btn"><?php $_SESSION = array(); ?>スタート画面へ</a>
    </div>
  </div>
  
</body>
</html>