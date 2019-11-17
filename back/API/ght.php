<?php
/* ESTE ARQUIVO SIMPLESMENTE CHAMA E EXIBE A RESPOSTA EM FORMA DE API DOS PARAMATROS PASSADOS POR POST */
include("../ght.php");
$n = $_POST['n'];
$m = $_POST['m'];
$int = $_POST['inte'];
$g = $_POST['g'];
$trace = explode("\n", $_POST['trace']);
$json = ght($n, $m, $g, $trace, $int);
echo $json;
