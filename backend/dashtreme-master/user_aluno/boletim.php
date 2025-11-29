<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Ensino - Emitir Boletim</title>
    <link href="../assets/css/pace.min.css" rel="stylesheet" />
    <script src="../assets/js/pace.min.js"></script>
    <link rel="icon" href="../assets/images/favicon.ico" type="image/x-icon">
    <link href="../assets/plugins/simplebar/css/simplebar.css" rel="stylesheet" />
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet" />
    <link href="../assets/css/animate.css" rel="stylesheet" />
    <link href="../assets/css/icons.css" rel="stylesheet" />
    <link href="../assets/css/sidebar-menu.css" rel="stylesheet" />
    <link href="../assets/css/app-style.css" rel="stylesheet" />
    <link href="../css/style.css" rel="stylesheet" />
</head>

<body class="bg-theme bg-theme1 user_aluno_boletim">
    <?php
    require("menu_padrao.php");
    ?>

    <div class="clearfix"></div>

    <!-- Conteúdo da Página -->
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row justify-content-center mt-4">
                <div class="col-lg-10">
                    <div class="card shadow-lg rounded-lg">
                        <div class="card-header">
                            <h4 class="mb-0"><i class="zmdi zmdi-assignment mr-2"></i> Boletim</h4>
                        </div>
                        <div class="card-body">
                            <a href="ensino.php" class="btn btn-primary btn-voltar-custom">
                                <i class="zmdi zmdi-arrow-left mr-1"></i> VOLTAR
                            </a>

                            <h5 class="mb-4">Selecione um ano para emitir o boletim</h5>

                            <div id="lista-anos"></div>

                            <div class="mt-4 pt-3 border-top">
                                <p class="text-faint">Selecione um ano escolar acima para visualizar ou
                                    imprimir seu boletim acadêmico.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    </div>

    <!-- Scripts -->
    <script src="../assets/js/jquery.min.js"></script>
    <script src="../assets/js/popper.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/plugins/simplebar/js/simplebar.js"></script>
    <script src="../assets/js/sidebar-menu.js"></script>
    <script src="../assets/js/app-script.js"></script>
    <script>
        (function () {
            function badgeClass(status) {
                if (status === 'matriculado') return 'badge-info';
                if (status === 'aprovado') return 'badge-success';
                return 'badge-warning';
            }
            fetch('../includes/ajax/aluno/anos_matriculas.php')
                .then(r => r.json())
                .then(resp => {
                    if (!resp.success) { return; }
                    var cont = document.getElementById('lista-anos');
                    cont.innerHTML = '';
                    (resp.anos || []).forEach(function (item) {
                        var serie = item.serie ? ' - ' + item.serie : '';
                        var div = document.createElement('a');
                        div.className = 'year-option';
                        div.href = 'boletim_detalhes.php?ano=' + encodeURIComponent(item.ano);
                        div.innerHTML = '<span class="year-title">' + item.ano + serie + '</span>' +
                            '<span class="badge ' + badgeClass(item.status) + ' status-badge year-status">' + item.status + '</span>';
                        cont.appendChild(div);
                    });
                }).catch(function () { });
        })();
    </script>
    <div class="overlay toggle-menu"></div>
</body>

</html>