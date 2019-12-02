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
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</head>

<body class="container">
    <div class="d-flex h6 words-style mt-6">
    <div class = "esquerda">
        <div class="row-3">
            <button class="btn btn-info botao" onclick="javascript:prev()">
            <img src="../assets/img/anterior.svg" width="20"/>
            </button>
        </div>
        <div class="row-6">
            <button class="btn btn-info botao" onclick="javascript:next()">
            <img src="../assets/img/proxima.svg" width="20"/>
            </button>
        </div>
        <div class="row-8">
            <button class="btn btn-info botao" onclick="javascript:fast()">Fast</button>
        </div>
        <div class="row-10">
            <button class="btn btn-info botao" onclick="javascript:start()">Reset</button>
        </div>
    </div>
    </div>
    
    <div class="d-flex big-opa words-style mt-3">
        <div class="row">
            Preditor Local
        </div>
    </div>
    </br>
    <div class="d-flex h3  words-style mt-4">
            <div class="col-2">
                    Instrução:
                    <input type="text" value="" class="form-control" id="inpInstrucao" disabled />
            </div>
                <div class="col-3">
                    Index:
                    <input type="text" value="" class="form-control" id="inpIndex" disabled />
                </div>
                <div class="col-2">
                    Branch foi:
                    <input type="text" value="" class="form-control" id="inpReal" disabled />
                </div>
            </div>
            </div>
        
     <div class="d-flex h3 justify-content-center words-style mt-4">
            <div class="col">
                <table class="table table-bordered ml-2" id="tabelaHist">
                    <thead>
                        <tr>
                            <th scope="col" id="thIndex">Index</th>
                            <th scope="col">Historico</th>
                            <th scope="col">Predição</th>
                            <th scope="col">Acertos</th>
                            <th scope="col">Erros</th>
                            <th scope="col">Precisão</th>

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
        </div>
    </div>
     <div id="final">
         <!-- Trigger the modal with a button -->
            <button type="button" class="btn btn-info btn-lg mb-5" data-toggle="modal" data-target="#myModal">
                Ver dados finais
            </button>
            
            <!-- Modal -->
            <div id="myModal" class="modal fade" role="dialog">
              <div class="modal-dialog">
            
                <!-- Modal content-->
                <div class="modal-content">
                  <div class="modal-header">
                    <h4 class="modal-title">Informações finais</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                  </div>
                  <div class="modal-body">
                    <p>
                        <div class="col-11 mb-5 ml-3" id="final">
                          <div class="row mb-2">
                            Número de miss:<br />
                            <input type="text" class="form-control" id="nmiss" disabled />
                          </div>
                          <div class="row mb-2">
                            Número de branchs:<br />
                            <input type="text" class="form-control" id="ntotal" disabled />
                          </div>
                          
                          <div class="row mb-2">
                            Precisão:<br />
                            <input type="text" class="form-control" id="precisao" disabled />
                          </div>
                          <div class="row mb-2">
                            Taxa de miss:<br />
                            <input type="text" class="form-control" id="tmiss" disabled />
                          </div>
                        </div>
                        </p>
                  </div>
                </div>
            
              </div>
            </div>
            </div>
      </div
            </br>
</body>
<script src="../assets/js/bootstrap.js"></script>
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
            string += "<td>" + bits + "</td><td>" + array[bits] + "</td><td>" + ((predicao[bits] == true) ? "T" : "N") + "</td><td>" + acertos[bits] + "</td><td>" + erros[bits] + "</td>" + "<td>" + ((acertos[bits] + erros[bits] != 0) ? ((acertos[bits] / (erros[bits] + acertos[bits])) * 100).toFixed(2) : (100)) + "</td>";
            string += "</tr>";
        }

        $("#thistorico").html(string);
    }
</script>

</html>