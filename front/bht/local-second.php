<?php
include("../../back/bht.php");
$n = $_POST['n'];
$m = $_POST['m'];
$trace = explode("\n", file_get_contents($_FILES['trace']['tmp_name']));
$json = bht($n, $m, $trace);

?>
<!DOCTYPE html>
<html>

<head>
    <title>Preditores</title>
    <meta charset="utf-8" />
    <link href="https://fonts.googleapis.com/css?family=Montserrat&display=swap" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="../assets/css/estilo.css" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous" />
</head>

<body class="container">
    <div class="d-flex justify-content-start words-style mt-5"></div>
    <div class="d-flex big-opa justify-content-end words-style mt-5">
        <div class="row">
            Preditor Local
        </div>
    </div>
    <div class="d-flex h2 justify-content-center words-style mt-5">
        <div class="row mt-3">
            <div class="col">
                Instrução: <br />
                <input type="text" value="" class="form-control" id="inpInstrucao" disabled />
                Index: <br />
                <input type="text" value="" class="form-control" id="inpIndex" disabled />
                Branch foi: <br />
                <input type="text" value="" class="form-control" id="inpReal" disabled />
            </div>
            <div class="col">
                <table class="table table-bordered" id="tabelaHist">
                    <thead>
                        <tr>
                            <th scope="col" id="thIndex">Index</th>
                            <th scope="col">Historico</th>
                            <th scope="col">Predição</th>
                            <th scope="col">Acertos</th>
                            <th scope="col">Erros</th>

                        </tr>
                    </thead>
                    <tbody id="thistorico">
                        <tr>
                            <td>XXXXXXX</td>
                            <td>XXXXXXX</td>
                        </tr>
                        <tr>
                            <td>XXXXXXX</td>
                            <td>XXXXXXX</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="col">
                <button class="btn btn-primary" onclick="javascript:prev()">Prev</button>
                <button class="btn btn-primary" onclick="javascript:next()">Next</button>
                <button class="btn btn-primary" onclick="javascript:fast()">Fast</button>
                <button class="btn btn-primary" onclick="javascript:start()">Reset</button>
            </div>
            <div class="col" id="final">
                Número de miss:<br />
                <input type="text" class="form-control" id="nmiss" disabled />
                Número de branchs:<br />
                <input type="text" class="form-control" id="ntotal" disabled />
                Precisão:<br />
                <input type="text" class="form-control" id="precisao" disabled />
                Taxa de miss:<br />
                <input type="text" class="form-control" id="tmiss" disabled />
            </div>



        </div>
    </div>
</body>
<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
<script src="../assets/js/sortTableFinal.js"></script>
<script type="text/javascript">
    var json = <?= $json ?>;
    var tabelaHistorico = "";
    var int = -1;
    var max = json.historico.length - 1;
    next();
    preencheFinal(json.miss, json.total, json.precisao, json.taxamiss);

    function start() {
        int = 0;
        atualiza();
    }

    function fast() {
        int = max;
        atualiza();
    }

    function prev() {
        if (int > 0) {
            int--;
            atualiza();
        }

    }

    function atualiza() {
        preencheHistorico(json.historico[int], json.predicao[int], json.lsb[int - 1], json.acertos[int], json.erros[int], json.acertou[int - 1], json.entradas[int - 1]);
        sortTable("tabelaHist");
        if (int == max) { // se final preenche tabela final
            $("#final").show();
        } else {
            $("#final").hide();
        }
    }

    function preencheFinal(miss, total, precisao, taxamiss) {
        $("#ntotal").val(total);
        $("#nmiss").val(miss);
        $("#precisao").val(precisao.toFixed(2) + "%");
        $("#tmiss").val(taxamiss.toFixed(2) + "%");

    }

    function next() {
        if (int < max) {
            int++;
            atualiza();
        }
    }


    function preencheHistorico(array, predicao, lsb, acertos, erros, acertou, entrada) {

        var string = "";
        $("#inpInstrucao").val((entrada != null) ? (entrada[0]).toString(16) : "");
        $("#inpIndex").val((lsb != null) ? lsb : "");
        $("#inpReal").val((entrada != null) ? entrada[1] : "");

        for (var bits in array) {
            //console.log("BITS = " + bits + " predicao[bits] = " + predicao[bits]);
            classe = (acertou) ? "A" : "E";
            string += (bits == lsb) ? "<tr class='select" + classe + "'>" : "<tr>";
            string += "<td>" + bits + "</td><td>" + array[bits] + "</td><td>" + ((predicao[bits] == true) ? "T" : "N") + "</td><td>" + acertos[bits] + "</td><td>" + erros[bits] + "</td>";
            string += "</tr>";
        }

        $("#thistorico").html(string);
    }
</script>

</html>