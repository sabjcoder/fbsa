<?php
require_once "vadersentiment.php";

$textToTest = "you are good.";

$sentimenter = new SentimentIntensityAnalyzer();
$result = $sentimenter->getSentiment($textToTest);

print_r($result);
?>