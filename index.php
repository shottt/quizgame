<?php

require_once(__DIR__ . '/class.php');
require_once(__DIR__ . '/config.php');

// インスタンス生成
$human = new Human('勇者', Sex::MAN, 500, 60, 120);
$monsters[] = new Monster('スライム', 80, 'img/monster01.png', 20, 40 );
$monsters[] = new MagicMonster('ポーン', 110, 'img/monster02.png', 20, 50, mt_rand(40, 70) );
$monsters[] = new Monster('マッチョ', 160, 'img/monster03.png', 30, 50 );
$monsters[] = new MagicMonster('シルフ', 120, 'img/monster04.png', 20, 40, mt_rand(30, 60) );
$monsters[] = new Monster('ドラゴン', 200, 'img/monster05.png', 50, 70 );
$monsters[] = new Monster( 'おばけ', 90, 'img/monster06.png', 10, 30 );
$quiz = new Quiz();

if(!$quiz->isFinished()){
  $data = $quiz->getCurrentQuiz();
}else{
  $quiz->reset();
  $data = $quiz->getCurrentQuiz();
}

shuffle($data['a']);
$_SESSION['correctAnswer'] = $quiz->checkAnswer();

function createMonster(){
  global $monsters;
  $monster =  $monsters[mt_rand(0, 5)];
  History::set('▼ ' . $monster->getName().'が現れた！');
  $_SESSION['monster'] =  $monster;
}
function createHuman(){
  global $human;
  $_SESSION['human'] =  $human;
}
function init(){
  History::clear();
  History::set('>>>>> 初期化します！');
  $_SESSION['knockDownCount'] = 0;
  $_SESSION['correctAnswerConut'] = 0;
  createHuman();
  createMonster();
}
function gameOver(){
  header("Location:finish.php");
}

//1.post送信されていた場合
if(!empty($_POST)){
  $resetFlg = (!empty($_POST['reset'])) ? true : false;
  $startFlg = (!empty($_POST['start'])) ? true : false;
  $attackFlg = (!empty($_POST['attack'])) ? true : false;
  $stayFlg = (!empty($_POST['stay'])) ? true : false;
  error_log('POSTされた！');
  error_log('resetFlg：' . $resetFlg);
  error_log('$startFlg：' . $startFlg);
  error_log('$attackFlg：' . $attackFlg);
  error_log('$stayFlg：' . $stayFlg);

  if($resetFlg){
    $_SESSION = array();
  }else{

    if($startFlg){
      History::set('ゲームスタート！');
      $_SESSION['current_num'] = 0;
      init();
    }else{
      // クイズに正解した場合
      if($attackFlg){
        // モンスターに攻撃を与える
        error_log('attackした！');
        History::set($_SESSION['human']->getName().'の攻撃！');
        $_SESSION['human']->attack($_SESSION['monster']);
        $_SESSION['monster']->sayCry();
        
        // モンスターが攻撃をする(モンスターから攻撃を受ける)
        History::set($_SESSION['monster']->getName().'の攻撃！');
        $_SESSION['monster']->attack($_SESSION['human']);
        $_SESSION['human']->sayCry();
        $_SESSION['correctAnswerConut'] = $_SESSION['correctAnswerConut']+1;

        error_log($_SESSION['human']->getHp());
        error_log($_SESSION['monster']->getHp());
        
        
      }else{ 
        //クイズに不正解の場合
        if($stayFlg){
          // モンスターが攻撃をする(モンスターから攻撃を受ける)
          error_log('attackされた！');
          History::set($_SESSION['monster']->getName().'の攻撃！');
          $_SESSION['monster']->attack($_SESSION['human']);
          $_SESSION['human']->sayCry();

          error_log($_SESSION['human']->getHp());
          error_log($_SESSION['monster']->getHp());
        }
      }
      // 自分のhpが0以下になったらゲームオーバー
      if($_SESSION['human']->getHp() <= 0){
        gameOver();
      }else{
        // モンスターのhpが0以下になったら、別のモンスターを出現させる
        if($_SESSION['monster']->getHp() <= 0){
          History::set($_SESSION['monster']->getName().'を倒した！');
          createMonster();
          $_SESSION['knockDownCount'] = $_SESSION['knockDownCount']+1;
        }
      }
    }
  }
  $_POST = array();
}

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
    <header class="header">
      <h1 class="game-title">クイズRPG</h1>
    </header>
    <div id="contents">
      <?php if(empty($_SESSION['human'])) : ?>
      <div class="start-screen">
        <form method="post" class="start-form">
          <h2 class="catch-copy">クイズに答えて敵を倒そう！！</h2>
          <input type="submit" name="start" value="▶GAME START ?" class="btn">
        </form>
      </div>
      <?php else : ?>
      
      <div class="battle-area">
        <div class="monster">
          <p class="img-container"><img src="<?php echo $_SESSION['monster']->getImg(); ?>" class="blink"></p>
          <span><?php echo $_SESSION['monster']->getName(); ?></span>/<span>HP：<?php echo $_SESSION['monster']->getHp(); ?></span>
          <p>勇者の残りHP：<?php echo $_SESSION['human']->getHp(); ?></p>
        </div>
        
        <div class="log js-auto-scroll">
          <p><?php echo (!empty($_SESSION['history'])) ? $_SESSION['history'] : ''; ?></p>
        </div>
      </div>
      <div class="quiz-area">
        <div class="question">
          <p><?= 'Q. ' . $data['q']; ?></p>
        </div>
        <ol class="answer-list">
          <?php foreach ($data['a'] as $a) : ?>
            <li class="answer js-answer-check"><span class="judge js-judge"></span><?= $a; ?></li>
          <?php endforeach; ?>
        </ol>
      </div>

      <form method="post" class="reset-btn">
        <input type="submit" name="reset" value="▶︎ゲームをリセットする" class="btn">
      </form>
      <?php endif; ?>
      
    </div>
    <script src="jquery-3.4.1.min.js"></script>
    <script src="quiz.js"></script>
  </body>
</html>
