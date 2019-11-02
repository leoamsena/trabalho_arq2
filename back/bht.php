<?php
function bht($n, $m, $trace)
{
    //Parametros
    $json = array();
    //$n = $_POST['n'];
    //$m = $_POST['m'];
    //$trace = explode("\n", file_get_contents($_FILES['trace']['tmp_name']));



    foreach ($trace as $key => $t) {
        $parte = explode(" ", $t);
        if (!empty($parte[0])) {
            $parte[0] = hexdec($parte[0]);
            $parte[1] = str_replace("\r", "", $parte[1]);
            $trace[$key] = $parte;
        }
    }
    $entradas = $trace;
    $json["entradas"] = $entradas;
    $json["historico"] = array();
    $json["contador"] = array();
    $json["predicao"] = array();
    $json["lsb"] = array();
    $json["acertos"] = array();
    $json["erros"] = array();
    $json["acertou"] = array();


    /* ----------------------------- INICIALIZÇÃO ----------------------------- */

    $historico = array();
    $contador = array();
    $erros = array();
    $acertos = array();
    $predicoes = array();

    // VÃO EXISTIR 2^N CONTADORES
    for ($j = 0; $j < pow(2, $n); $j++) { // vão existir 2^n 
        $index2 = str_pad(decbin($j), $n, 0, STR_PAD_LEFT); // index2 = o valor de i em binário (completado com quantos zeros a esquerda forem necessários) 
        $contador[$index2] = ($j >= $n) ? true : false; // se index2 >= 2 então predição é tomado, senão é não tomado
    }

    for ($i = 0; $i < pow(2, $m); $i++) { // vão existir 2^m linhas na tabela de histórico
        $index = str_pad(decbin($i), $m, 0, STR_PAD_LEFT); // index = o valor de i em binário (completado com quantos zeros a esquerda forem necessários) 
        $historico[$index] = str_pad(decbin($n), $n, 0, STR_PAD_LEFT); // coloca N (com quantos zeros forem necessários a esquerda) no conteúdo de cada linha da tabela de histórico
        $erros[$index] = 0;
        $acertos[$index] = 0;
        $predicoes[$index] = $contador[str_pad(decbin($n), $n, 0, STR_PAD_LEFT)]; // começa como tomado
    }

    $strHistorico = str_replace("1", "T,", $historico);
    $strHistorico = str_replace("0", "N,", $strHistorico);
    $strHistorico = substr_replace($strHistorico, "", -1);
    array_push($json["historico"], $strHistorico);

    array_push($json["acertos"], $acertos);
    array_push($json["erros"], $erros);
    array_push($json["contador"], $contador);
    array_push($json["predicao"], $predicoes);


    /* ----------------------------- CALCULOS ----------------------------- */
    $total = 0;
    $miss = 0;
    $interacao = -1;

    foreach ($entradas as $linha) { // foreach para cada desvio
        $interacao++;
        if (is_array($linha)) {
            $desvio = strtoupper($linha[1]); // desvio recebe a string "t" ou "n"
            $e = $linha[0]; // e recebe o endereço de PC
            $real = ($desvio == "T") ? true : false; // se "t" então foi tomado (real = true) senão real = false
            $lsb = str_pad(substr(decbin($e), -1 * $m), $m, 0, STR_PAD_LEFT); // pega os $m bits menos significativos do $e (PC do desvio) VERIFICAR QUESTÃO COM PROFESSOR
            array_push($json["lsb"], $lsb);
            if ($contador[$historico[$lsb]] == true) { // se historico >= n predição é = tomado
                $predicao = true;
            } else { // senão predição é não tomado 
                //echo "HISTORICO[LSB] = " . $historico[$lsb] . " N TOMADO <br/>";
                $predicao = false;
            }



            if ($predicao != $real) { // se predição diferente da realidade então aumenta a taxa de miss
                $miss++;
                $erros[$lsb]++;
            } else {
                $acertos[$lsb]++;
            }
            $total++;
            if ($real) { // se desvio tomado
                if (bindec($historico[$lsb]) < (pow(2, $n) - 1)) {
                    $nbin = decbin(bindec($historico[$lsb]) + 1);
                    $historico[$lsb] = str_pad($nbin, $n, 0, STR_PAD_LEFT);
                }
            } else {
                if (bindec($historico[$lsb]) > 0) {
                    //echo "ERA = " . $historico[$lsb] . "<br/>";
                    $nbin = decbin(bindec($historico[$lsb]) - 1);
                    $historico[$lsb] = str_pad($nbin, $n, 0, STR_PAD_LEFT);
                    //echo "AGORA = " . $historico[$lsb] . "<br/>";
                }
            }
            // $historico[$lsb] .= ($real) ? "1" : "0"; // se desvio tomado muda a string do historico colocando um 1 no final ou um 0 caso não seja tomado
            //$historico[$lsb] = substr($historico[$lsb], 1); // ainda como linha anterior (DUAS LINHAS DEVEM SEMPRE PERMANECEREM JUNTAS)
            $strHistorico = str_replace("1", "T,", $historico);
            $strHistorico = str_replace("0", "N,", $strHistorico);
            $strHistorico = substr_replace($strHistorico, "", -1);
            array_push($json["acertou"], $predicao == $real);
            array_push($json["historico"], $strHistorico);
            array_push($json["acertos"], $acertos);
            array_push($json["erros"], $erros);
            array_push($json["predicao"], $predicoes);
            foreach ($historico as $k => $l) {
                $predicoes[$k] = $contador[$historico[$k]];
            }
        }
    }

    $json["miss"] = $miss;
    $json["total"] = $total;
    $json["precisao"] = (($total - $miss) / $total) * 100;
    $json["taxamiss"] = ($miss / $total) * 100;
    return json_encode($json);
}
