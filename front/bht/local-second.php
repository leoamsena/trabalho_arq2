<?php
include("../../back/bht.php");
$n = $_POST['n'];
$m = $_POST['m'];
$trace = explode("\n", file_get_contents($_FILES['trace']['tmp_name']));
$json = bht($n, $m, $trace);
?>

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
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">Index</th>
                            <th scope="col">Historico</th>
                            <th scope="col">Predição</th>
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
                <button class="btn btn-primary" onclick="javascript:atualiza()">Next</button>
            </div>




        </div>
    </div>
</body>
<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
<script type="text/javascript">
    var json = <?= $json ?>;
    var tabelaHistorico = "";
    var int = -1;
    var max = json.historico.length;
    atualiza();

    function atualiza() {
        if (int < max) {
            int++;
            preencheHistorico(json.historico[int], json.predicao[int], json.lsb[int - 1]);
        }
    }

    function preencheHistorico(array, predicao, lsb) {
        console.log(array);
        var string = "";
        for (var bits in array) {
            string += (bits == lsb) ? "<tr class='selectL'>" : "<tr>";
            string += "<td>" + bits + "</td><td>" + array[bits] + "</td><td>" + ((predicao) ? "T" : "N") + "</td>";
            string += "</tr>";
        }

        $("#thistorico").html(string);
    }
</script>

</html>