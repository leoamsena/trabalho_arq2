<?php

//Parametros
$entradas = array((0xb77a8a3a) => "t", (0xb77be7ab) => "n", (0xb77b55a0) => "t");
$n = 2;
$m = 3;


/* ----------------------------- INICIALIZÇÃO ----------------------------- */

$historico = array();
$contador = array();

for ($i = 0; $i < pow(2, $m); $i++) { // vão existir 2^m linhas na tabela de histórico
    $index = str_pad(decbin($i), $m, 0, STR_PAD_LEFT); // index = o valor de i em binário (completado com quantos zeros a esquerda forem necessários) 
    $historico[$index] = str_pad("0", $n, 0, STR_PAD_LEFT); // coloca 0 (com quantos zeros forem necessários a esquerda) no conteúdo de cada linha da tabela de histórico
}
// VÃO EXISTIR 2^N CONTADORES PARA CADA DESVIO????? OU SÃO 2^N NO TOTAL (CASO SEJA TOTAL DESVIOS ANTERIORES INTERFEREM NOS PRÓXIMOS)
for ($i = 0; $i < pow(2, $n); $i++) { // vão existir 2^n 
    $index = str_pad(decbin($i), $n, 0, STR_PAD_LEFT); // index = o valor de i em binário (completado com quantos zeros a esquerda forem necessários) 
    $contador[$index] = 2; // inicia em 2 (conforme enunciado)
}


/* ----------------------------- CALCULOS ----------------------------- */
$miss = 0;
foreach ($entradas as $e => $desvio) { // foreach para cada desvio
    $real = ($desvio == "t") ? true : false; // se "t" então foi tomado (real = true) senão real = false
    $lsb = str_pad(substr(decbin($e), -1 * $m), $m, 0, STR_PAD_LEFT); // pega os $m bits menos significativos do $e (PC do desvio)
    if ($contador[$historico[$lsb]] >= 2) { // se historico >= 2 então predição é = tomado
        echo "ACHO QUE VAI TOMAR <br/>";
        $predicao = true;
    } else { // senão predição é não tomado 
        echo "ACHO QUE NÃO VAI TOMAR<br/>";
        $predicao = false;
    }


    if ($predicao != $real) { // se predição diferente da realidade então aumenta a taxa de miss
        $miss++;
    }

    $historico[$lsb] .= ($real) ? "1" : "0"; // se desvio tomado muda a string do historico colocando um 1 no final ou um 0 caso não seja tomado
    $historico[$lsb] = substr($historico[$lsb], 1); // ainda como linha anterior (DUAS LINHAS DEVEM SEMPRE PERMANECEREM JUNTAS)
    if ($real) { // se desvio real foi tomado
        if ($contador[$historico[$lsb]] < 3) // se contador < 3 aumenta contador
            $contador[$historico[$lsb]] += 1;
    } else {
        if ($contador[$historico[$lsb]] > 0) // se contador maior que 0 diminui contador
            $contador[$historico[$lsb]] -= 1;
    }
}
echo "taxa de miss = " . $miss;
