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
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="bg-theme bg-theme1 user_aluno_atestado_frequencia">
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
                                <h4 class="mb-0"><i class="zmdi zmdi-assignment mr-2"></i> Atestado de frequência</h4>
                            </div>
                            <div class="card-body">
                                <a href="ensino.php" class="btn btn-primary btn-voltar-custom">
                                    <i class="zmdi zmdi-arrow-left mr-1"></i> VOLTAR
                                </a>

                                <h5 class="mb-4">Selecione um ano para emitir o atestado de frequência</h5>

                                <div id="lista-anos">
                                    <div class="text-muted">Carregando anos disponíveis…</div>
                                </div>

                                <div class="mt-4 pt-3 border-top">
                                    <p class="text-faint">Selecione um ano escolar acima para imprimir seu atestado
                                        de frequência.</p>
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
        (function() {
            function badgeClass(status) {
                if (!status) return 'badge-warning';
                var s = ('' + status).toLowerCase();
                if (s === 'matriculado' || s === 'ativa' || s === 'ativo') return 'badge-info';
                if (s === 'aprovado' || s === 'concluido') return 'badge-success';
                return 'badge-warning';
            }

            function renderAnos(anos) {
                var $container = $('#lista-anos');
                $container.empty();
                if (!Array.isArray(anos) || anos.length === 0) {
                    $container.append('<div class="text-muted">Nenhum ano letivo encontrado para seu usuário.</div>');
                    return;
                }
                anos.forEach(function(item){
                    var ano = item.ano || '';
                    var serie = item.serie ? (' - ' + item.serie) : '';
                    var status = item.status || '';
                    var bc = badgeClass(status);
                    var html = [
                        '<a class="year-option" href="frequencia_detalhes.php?ano=' + encodeURIComponent(ano) + '">',
                        '  <span class="year-title">' + ano + serie + '</span>',
                        '  <span class="badge ' + bc + ' status-badge year-status">' + (status || '') + '</span>',
                        '</a>'
                    ].join('');
                    $container.append(html);
                });
            }

            $(function(){
                $.ajax({
                    url: '../includes/ajax/aluno/anos_matriculas.php',
                    method: 'GET',
                    dataType: 'json',
                    cache: false
                }).done(function(resp){
                    if (resp && resp.success && Array.isArray(resp.anos)) {
                        renderAnos(resp.anos);
                    } else {
                        $('#lista-anos').html('<div class="text-warning">Nao foi possivel carregar os anos (resposta invalida).</div>');
                    }
                }).fail(function(xhr){
                    var msg = 'Falha ao carregar anos';
                    if (xhr && xhr.responseJSON && xhr.responseJSON.message) msg += ': ' + xhr.responseJSON.message;
                    $('#lista-anos').html('<div class="text-danger">' + msg + '</div>');
                });
            });
        })();
    </script>
    
</body>

</html>