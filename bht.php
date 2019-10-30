<?php
//Parametros
$json = array();
$n = $_POST['n'];
$m = $_POST['m'];
$trace = explode("\n", file_get_contents($_FILES['trace']['tmp_name']));



foreach ($trace as $key => $t) {
    $parte = explode(" ", $t);
    $parte[0] = hexdec($parte[0]);
    $parte[1] = str_replace("\r", "", $parte[1]);
    $trace[$key] = $parte;
}
$entradas = $trace;
$json["entradas"] = $entradas;
$json["historico"] = array();
$json["contador"] = array();
$json["predicao"] = array();

/* ----------------------------- INICIALIZÇÃO ----------------------------- */

$historico = array();
$contador = array();

for ($i = 0; $i < pow(2, $m); $i++) { // vão existir 2^m linhas na tabela de histórico
    $index = str_pad(decbin($i), $m, 0, STR_PAD_LEFT); // index = o valor de i em binário (completado com quantos zeros a esquerda forem necessários) 
    $historico[$index] = str_pad("0", $n, 0, STR_PAD_LEFT); // coloca 0 (com quantos zeros forem necessários a esquerda) no conteúdo de cada linha da tabela de histórico
    // VÃO EXISTIR 2^N CONTADORES PARA CADA DESVIO (LINHA DA TABELA)
    for ($j = 0; $j < pow(2, $n); $j++) { // vão existir 2^n 
        $index2 = str_pad(decbin($j), $n, 0, STR_PAD_LEFT); // index2 = o valor de i em binário (completado com quantos zeros a esquerda forem necessários) 
        $contador[$index][$index2] = 2; // inicia em 2 (conforme enunciado)
    }
}
array_push($json["historico"], $historico);
array_push($json["contador"], $contador);



/* ----------------------------- CALCULOS ----------------------------- */
$miss = 0;
foreach ($entradas as $linha) { // foreach para cada desvio
    $desvio = $linha[1]; // desvio recebe a string "t" ou "n"
    $e = $linha[0]; // e recebe o endereço de PC
    $real = ($desvio == "t") ? true : false; // se "t" então foi tomado (real = true) senão real = false
    $lsb = str_pad(substr(decbin($e), -1 * $m), $m, 0, STR_PAD_LEFT); // pega os $m bits menos significativos do $e (PC do desvio) VERIFICAR QUESTÃO COM PROFESSOR
    if ($contador[$lsb][$historico[$lsb]] >= 2) { // se historico >= 2 então predição é = tomado
        //echo "ACHO QUE VAI TOMAR <br/>";
        array_push($json["predicao"], true);
        $predicao = true;
    } else { // senão predição é não tomado 
        //echo "ACHO QUE NÃO VAI TOMAR<br/>";
        array_push($json["predicao"], false);
        $predicao = false;
    }


    if ($predicao != $real) { // se predição diferente da realidade então aumenta a taxa de miss
        $miss++;
    }

    $historico[$lsb] .= ($real) ? "1" : "0"; // se desvio tomado muda a string do historico colocando um 1 no final ou um 0 caso não seja tomado
    $historico[$lsb] = substr($historico[$lsb], 1); // ainda como linha anterior (DUAS LINHAS DEVEM SEMPRE PERMANECEREM JUNTAS)
    if ($real) { // se desvio real foi tomado
        if ($contador[$lsb][$historico[$lsb]] < 3) // se contador < 3 aumenta contador
            $contador[$lsb][$historico[$lsb]] += 1;
    } else {
        if ($contador[$lsb][$historico[$lsb]] > 0) // se contador maior que 0 diminui contador
            $contador[$lsb][$historico[$lsb]] -= 1;
    }
    array_push($json["historico"], $historico);
    array_push($json["contador"], $contador);
}
$json["miss"] = $miss;
//echo "taxa de miss = " . $miss;
echo json_encode($json);
