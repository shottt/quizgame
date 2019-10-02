<?php

// モンスター達格納用
$monsters = array();
// 性別クラス
class Sex{
  const MAN = 1;
  const WOMAN = 2;
  const OKAMA = 3;
}
// 抽象クラス（生き物クラス）
abstract class Creature{
  protected $name;
  protected $hp;
  protected $attackMin;
  protected $attackMax;
  abstract public function sayCry();
  public function setName($str){
    $this->name = $str;
  }
  public function getName(){
    return $this->name;
  }
  public function setHp($num){
    $this->hp = $num;
  }
  public function getHp(){
    return $this->hp;
  }
  public function attack($targetObj){
    $attackPoint = mt_rand($this->attackMin, $this->attackMax);
    if(!mt_rand(0,9)){ //10分の1の確率でクリティカル
      $attackPoint = $attackPoint * 1.5;
      $attackPoint = (int)$attackPoint;
      History::set($this->getName().'のクリティカルヒット!!');
    }
    $targetObj->setHp($targetObj->getHp()-$attackPoint);
    History::set($attackPoint.'ポイントのダメージ！');
  }
}
// 人クラス
class Human extends Creature{
  protected $sex;
  public function __construct($name, $sex, $hp, $attackMin, $attackMax) {
    $this->name = $name;
    $this->sex = $sex;
    $this->hp = $hp;
    $this->attackMin = $attackMin;
    $this->attackMax = $attackMax;
  }
  public function setSex($num){
    $this->sex = $num;
  }
  public function getSex(){
    return $this->sex;
  }
  public function sayCry(){
    History::set($this->name.'が叫ぶ！');
    switch($this->sex){
      case Sex::MAN :
        History::set('ぐはぁっ！');
        break;
      case Sex::WOMAN :
        History::set('きゃっ！');
        break;
      case Sex::OKAMA :
        History::set('もっと！♡');
        break;
    }
  }
}
// モンスタークラス
class Monster extends Creature{
  // プロパティ
  protected $img;
  // コンストラクタ
  public function __construct($name, $hp, $img, $attackMin, $attackMax) {
    $this->name = $name;
    $this->hp = $hp;
    $this->img = $img;
    $this->attackMin = $attackMin;
    $this->attackMax = $attackMax;
  }
  // ゲッター
  public function getImg(){
    return $this->img;
  }
  public function sayCry(){
    History::set($this->name.'が叫ぶ！');
    History::set('はうっ！');
  }
}
// 魔法を使えるモンスタークラス
class MagicMonster extends Monster{
  private $magicAttack;
  function __construct($name, $hp, $img, $attackMin, $attackMax, $magicAttack) {
    parent::__construct($name, $hp, $img, $attackMin, $attackMax);
    $this->magicAttack = $magicAttack;
  }
  public function getMagicAttack(){
    return $this->magicAttack;
  }
  public function attack($targetObj){
    if(!mt_rand(0,4)){ //5分の1の確率で魔法攻撃
      History::set($this->name.'の魔法攻撃!!');
      $targetObj->setHp( $targetObj->getHp() - $this->magicAttack );
      History::set($this->magicAttack.'ポイントのダメージを受けた！');
    }else{
      parent::attack($targetObj);
    }
  }
}
interface HistoryInterface{
  public static function set($str);
  public static function clear();
}
// 履歴管理クラス（インスタンス化して複数に増殖させる必要性がないクラスなので、staticにする）
class History implements HistoryInterface{
  public static function set($str){
    // セッションhistoryが作られてなければ作る
    if(empty($_SESSION['history'])) $_SESSION['history'] = '';
    // 文字列をセッションhistoryへ格納
    $_SESSION['history'] .= $str.'<br>';
  }
  public static function clear(){
    unset($_SESSION['history']);
  }
}
// クイズクラス
class Quiz{
  private $quizSet = [];

  public function __construct(){
    $this->setQuiz();
    error_log(print_r($this->quizSet,true));

    if(!isset($_SESSION['current_num'])){
      $_SESSION['current_num'] = 0;
    }
  }
  public function checkAnswer() {
    $correctAnswer = $this->quizSet[$_SESSION['current_num']]['a'][0];
    $_SESSION['current_num']++;
    return $correctAnswer;
  }
  public function isFinished(){
     return count($this->quizSet) === $_SESSION['current_num'];
  }
  public function reset(){
    $_SESSION['current_num'] = 0;
  }
  public function getCurrentQuiz(){
    return $this->quizSet[$_SESSION['current_num']];
  }
  private function setQuiz(){
    $this->quizSet[] = [
      'q' => '血液をろ過して老廃物を取り除く、人間にある臓器は何？',
      'a' => ['腎臓', '胃', '膀胱', '膵臓']
    ];
    $this->quizSet[] = [
      'q' => '次のうち、「しょうにん」や「あきんど」と読む職業はどれ？',
      'a' => ['商人', '農民', '武士', '大工']
    ];
    $this->quizSet[] = [
      'q' => '「順序」を英語でいうと何？',
      'a' => ['order', 'ranking', 'number', 'operation']
    ];
    $this->quizSet[] = [
      'q' => '星占いなどに使われる12星座は、次のうち、何によって決められる？',
      'a' => ['誕生日', '出身地', '親の職業', '血液型']
    ];
    $this->quizSet[] = [
      'q' => '俗に「ハガレン」と呼ばれる荒川弘の漫画。この「レン」とは何という言葉の略？',
      'a' => ['錬金術師', '連合艦隊', '連勝記録', '連帯責任']
    ];
    $this->quizSet[] = [
      'q' => '藤原道長の娘・彰子に仕えた。「源氏物語」の作者は誰？',
      'a' => ['紫式部', '赤式部', '紅式部', '赤式部']
    ];
    $this->quizSet[] = [
      'q' => '試合が終了すれば敵味方なし、という意味で試合終了のことを「ノーサイド」というスポーツは何？',
      'a' => ['ラグビー', 'ラクロス', 'ホッケー', 'アメフト']
    ];
    $this->quizSet[] = [
      'q' => '漫画「ピーナッツ」に登場する犬、スヌーピーを買っている少年の名は？',
      'a' => ['チャーリー', 'フランクリン', 'シュローダー', 'ライナス']
    ];
    $this->quizSet[] = [
      'q' => 'サッカーで、試合中にレッドカードがを提示された選手はどうなる？',
      'a' => ['退場になる', '賞金がもらえる', '髪を切られる', '引退になる']
    ];
    $this->quizSet[] = [
      'q' => '全ての素数をかけた時にできる数は偶数、奇数のうちのどちら？',
      'a' => ['偶数', '奇数', 'どちらの場合もある', 'どちらでもない']
    ];
    $this->quizSet[] = [
      'q' => 'スタジオジブリの映画の放映権をを持っているテレビ局はどこ？',
      'a' => ['日本テレビ', 'フジテレビ', 'NHK', 'テレビ朝日']
    ];
    $this->quizSet[] = [
      'q' => 'お酒として飲まれるアルコールの種類は？',
      'a' => ['エタノール', 'ブタノール', 'プロパノール', 'メタノール']
    ];
    $this->quizSet[] = [
      'q' => '次の色の中で、色の鮮やかさを示す色の「彩度」が最も高いのはどれ？',
      'a' => ['赤色', '白色', '灰色', '黒色']
    ];
    $this->quizSet[] = [
      'q' => '垣根を英語で言うと何？',
      'a' => ['fence', 'gate', 'wall', 'ceiling']
    ];
    $this->quizSet[] = [
      'q' => '2007年にインターネットに投稿され、ヒットした初音ミクのボカロ曲「"何"にしてあげる」？',
      'a' => ['みくみく', 'はつはつ', 'はみはみ', 'つみつみ']
    ];
    $this->quizSet[] = [
      'q' => '「yawn」を日本語で言うと何？',
      'a' => ['あくび', 'げっぷ', 'しゃっくり', 'くしゃみ']
    ];
    $this->quizSet[] = [
      'q' => '月見里、さて何と読む？',
      'a' => ['やまなし', 'つきみさと', 'おつきみ', 'やまつき']
    ];
    $this->quizSet[] = [
      'q' => '存在する武道でないものはどれか？',
      'a' => ['鞭道', '太道', '杖道', '忍術']
    ];
    $this->quizSet[] = [
      'q' => '形式的な「フォーマル」に対して、格式ばってない気軽な服装を指す言葉は何？',
      'a' => ['カジュアル', 'ビジュアル', 'セクシャル', 'ルーズ']
    ];
    $this->quizSet[] = [
      'q' => 'サービスや商品に対して不当に高額な金額を要求する店のことを何と言う？',
      'a' => ['ぼったくり', 'ドケチ', '閉店する詐欺', '価格破壊']
    ];
    $this->quizSet[] = [
      'q' => '次の塔のうち、完成したのが最も早かったのはどれ？',
      'a' => ['通天閣', '横浜マリンタワー', '東京タワー', '京都タワー']
    ];
    $this->quizSet[] = [
      'q' => '円錐の頂点と、底面の円上の1点を結んだ線を何という？',
      'a' => ['母線', '子線', '父線', '娘線']
    ];
    $this->quizSet[] = [
      'q' => 'シリコンとは、どんな元素のこと？',
      'a' => ['ケイ素', '炭素', '窒素', 'ヨウ素']
    ];
    $this->quizSet[] = [
      'q' => '木村カエラが2010年6月に結婚を発表した、相手の俳優は誰？',
      'a' => ['瑛太', '小栗旬', '小池徹平', '妻夫木聡']
    ];
    $this->quizSet[] = [
      'q' => '「甲」の部首はどれ？',
      'a' => ['田', '十', '一', '甲']
    ];
    $this->quizSet[] = [
      'q' => 'ロックバンド「スピッツ」のシングルのタイトルとして正しいのは？',
      'a' => ['水色の街', '白色の街', '桃色の街', '黄色の街']
    ];
    $this->quizSet[] = [
      'q' => '嵐のメンバー「二宮和也」の正しい読み方は何？',
      'a' => ['にのみやかずなり', 'にみやかずや', 'にみやかずなり', 'にのみやかずや']
    ];
    $this->quizSet[] = [
      'q' => '「ハムレット」の舞台であるデンマークの城の名は？',
      'a' => ['クロンボ-城', 'シロンボ-城', 'アカンボ-城', 'トロンボ-城']
    ];
    return shuffle($this->quizSet);
  }
}