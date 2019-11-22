<?php

/**
 * Simulação de preditor BHT.
 *
 * Através da entrada dos parâmetros corretos esta função retorna um JSON contendo o passo a passo
 * das etapas de um preditor BHT, contendo a tabela de predição, se a predição foi acertada ou não, etc.
 *
 * @param int $n Número de bits que o histórico vai registrar
 * @param int $m Número de bits (LSB) que serão usados para indexar a tebala de histórico
 * @param array $trace Array contendo todo o trace, cada linha => <endereco_de_PC_em_hexadecima> <T/N> 
 *
 * @return string Retorna um json contendo todos os passos do simulador BHT
 */
function bht($n, $m, $trace) // $m = quantidade de LSBs que serão usados para indexar a tabela de histórico || $n = número de bits de histórico || $trace = 
{
    $json = array(); // array json que será usado no retorno da função



    foreach ($trace as $key => $t) { // foreach que rodará todas as linhas do trace
        $parte = explode(" ", $t); // separa os espaços de cada linha do trace e coloca em $parte
        if (!empty($parte[0])) { // se for um endereço válido
            $parte[0] = hexdec($parte[0]); // primeira posição de $parte recebe o número de PC
            $parte[1] = str_replace("\r", "", $parte[1]); // segunda posição recebe <T/N>
            $trace[$key] = $parte; // $trace na posição dos LSBs do PC recebe $parte
        }
    }
    $entradas = $trace; // entradas do preditor devem ser o trace 

    // Definição de cada uma das posições do array de json
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
        $erros[$index] = 0; // para incializar são 0 erros para aquela linha da tabela de histórico
        $acertos[$index] = 0; // para incializar são 0 acertos para aquela linha da tabela de histórico
        $predicoes[$index] = $contador[str_pad(decbin($n), $n, 0, STR_PAD_LEFT)]; // começa como tomado
    }

    $strHistorico = str_replace("1", "T,", $historico); // conversão de 0 em N e 1 em T para exibição no front-end
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
            /* A LINHA ABAIXO AUMENTA O NÚMERO DE MISS!!! */
            $eTmp = substr(decbin($e), 0, -2); // ignora os dois ultimos bits (shift)
            /* A LINHA ABAIXO ACIMA O NÚMERO DE MISS!!! */
            $lsb = str_pad(substr($eTmp, -1 * $m), $m, 0, STR_PAD_LEFT); // pega os $m bits menos significativos do $e (PC do desvio) 


            array_push($json["lsb"], $lsb);
            if ($contador[$historico[$lsb]] == true) { // se historico >= n predição é = tomado
                $predicao = true;
            } else { // senão predição é não tomado 
                $predicao = false;
            }



            if ($predicao != $real) { // se predição diferente da realidade então aumenta a taxa de miss
                $miss++;
                $erros[$lsb]++; // aumenta o erro naquela posição da tabela
            } else {
                $acertos[$lsb]++; // aumenta os acertos naquela posição da tebala
            }
            $total++;
            if ($real) { // se desvio tomado
                if (bindec($historico[$lsb]) < (pow(2, $n) - 1)) { // se é possível representar o histórico daquela linha + 1 com a quantidade de n bits, então a soma é realizada (verificação de saturação do histórico)
                    $nbin = decbin(bindec($historico[$lsb]) + 1); // soma mais um no histórico
                    $historico[$lsb] = str_pad($nbin, $n, 0, STR_PAD_LEFT); // salva o novo histórico na posição correspondente
                }
            } else { // se desvio não tomado
                if (bindec($historico[$lsb]) > 0) { // se histórico maior que 0 (verificação de saturação de histórico)
                    $nbin = decbin(bindec($historico[$lsb]) - 1); // subtrai um do histórico
                    $historico[$lsb] = str_pad($nbin, $n, 0, STR_PAD_LEFT); // salva o novo histórico na posição correspondente
                }
            }
            $strHistorico = str_replace("1", "T,", $historico); // conversão de 0 em N e 1 em T para exibição no front-end
            $strHistorico = str_replace("0", "N,", $strHistorico);
            $strHistorico = substr_replace($strHistorico, "", -1);

            array_push($json["acertou"], $predicao == $real); // salva no array de json se a predição foi correta
            array_push($json["historico"], $strHistorico); // salva no histórico a atual situação da tabela de histótico
            array_push($json["acertos"], $acertos); // salva no array de json a quantidade de acertos (por linha da tabela)
            array_push($json["erros"], $erros); // salva a quantidade de erros por linha da tabela
            array_push($json["predicao"], $predicoes); // salva qual a predição para aquele momento
            foreach ($historico as $k => $l) {
                $predicoes[$k] = $contador[$historico[$k]];
            }
        }
    }

    $json["miss"] = $miss; // qtd de miss total
    $json["total"] = $total; // qtd de branchs totais
    $json["precisao"] = (($total - $miss) / $total) * 100; // calculo de precisão de predição
    $json["taxamiss"] = ($miss / $total) * 100; // calculo de taxa de miss
    return json_encode($json); // retorna o array $json codificado para json
}
