<?php

require_once(__DIR__ . '/config.php');

$correctAnswer = $_SESSION['correctAnswer'];
$_SESSION['correctAnswer'] = '';

header('Content-Type: application/json; charset=UTF-8');
echo json_encode([
  'correct_answer' => $correctAnswer
]);