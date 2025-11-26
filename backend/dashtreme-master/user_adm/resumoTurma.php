<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <link rel="icon" href="../assets/images/favicon.ico" type="image/x-icon">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resumo por Turma - SAS</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/app-style.css">
    <link rel="stylesheet" href="../assets/css/icons.css">
    <link rel="stylesheet" href="../assets/css/sidebar-menu.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="bg-theme bg-theme1 user_adm_resumoTurma">
    <?php
    require("menu_padrão.php");
    ?>


    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="card" style="background-color: transparent;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
                        <h4 class="page-title"><i class="zmdi zmdi-group-work mr-2"></i> Resumo por Turma</h4>
                        <button id="print-btn" class="btn btn-custom-print">
                            <i class="zmdi zmdi-print mr-2"></i>Imprimir Relatório
                        </button>
                    </div>

                    <div class="filter-section no-print">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="ano-filter">Ano Letivo</label>
                                    <select class="form-control" id="ano-filter">
                                        <option value="">Selecione</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="turma-filter">Turma</label>
                                    <select class="form-control" id="turma-filter" disabled>
                                        <option value="">Selecione o ano primeiro</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button id="btn-carregar" class="btn btn-primary btn-block" disabled>Carregar</button>
                            </div>
                        </div>
                    </div>

                    <!-- Cards de Resumo -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card summary-card students">
                                <div class="card-body text-center">
                                    <div class="summary-value" id="total-alunos">0</div>
                                    <div class="summary-label">Alunos</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card summary-card teachers">
                                <div class="card-body text-center">
                                    <div class="summary-value" id="total-professores">0</div>
                                    <div class="summary-label">Professores</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card summary-card disciplines">
                                <div class="card-body text-center">
                                    <div class="summary-value" id="total-disciplinas">0</div>
                                    <div class="summary-label">Disciplinas</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detalhes por Turma -->
                    <div class="turma-header">
                        <h5><i class="zmdi zmdi-filter mr-2"></i> Turma: Selecione acima</h5>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Disciplina</th>
                                    <th>Professor</th>
                                    <th>Alunos</th>
                                    <th>Aulas Semanais</th>
                                </tr>
                            </thead>
                            <tbody id="detalhes-turma-body">
                                <tr><td colspan="4" class="text-center">Selecione uma turma e clique em Carregar</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="overlay toggle-menu"></div>
    </div>

    <script src="../assets/js/jquery.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/plugins/simplebar/js/simplebar.js"></script>
    <script src="../assets/js/sidebar-menu.js"></script>
    <script src="../assets/js/app-script.js"></script>
   
    <script>
        $(document).ready(function () {
            const $ano = $('#ano-filter');
            const $turma = $('#turma-filter');
            const $btn = $('#btn-carregar');

            function carregarAnos() {
                $.getJSON('../includes/ajax/shared/academico/listar_anos_letivos.php', function (resp) {
                    if (resp.success) {
                        $ano.empty().append('<option value="">Selecione</option>');
                        resp.data.forEach(ano => $ano.append(`<option value="${ano}">${ano}</option>`));
                    }
                });
            }

            function carregarTurmas(ano) {
                $turma.prop('disabled', true).empty().append('<option value="">Carregando...</option>');
                $.getJSON('../includes/ajax/admin/turmas/listar_turmas.php', { ano }, function (resp) {
                    $turma.empty();
                    if (resp.success && resp.data.length) {
                        $turma.append('<option value="">Selecione</option>');
                        resp.data.forEach(t => $turma.append(`<option value="${t.ID_Turma}">${t.Nome_Turma} (${t.Etapa || ''})</option>`));
                        $turma.prop('disabled', false);
                    } else {
                        $turma.append('<option value="">Nenhuma turma encontrada</option>');
                    }
                });
            }

            $ano.on('change', function(){
                const val = $(this).val();
                $btn.prop('disabled', !val);
                if (val) carregarTurmas(val);
            });

            $btn.on('click', function(e){
                e.preventDefault();
                const turmaId = $turma.val();
                if (!turmaId) { alert('Selecione uma turma'); return; }
                carregarResumo(turmaId);
            });

            function carregarResumo(turmaId) {
                // 1) Alunos
                $.getJSON('../includes/ajax/admin/turmas/listar_alunos_por_turma.php', { turma_id: turmaId }, function(resp){
                    if (resp.success) {
                        $('#total-alunos').text(resp.data.length);
                    } else {
                        $('#total-alunos').text('0');
                    }
                });

                // 2) Professores
                $.getJSON('../includes/ajax/admin/professores/listar_professores_por_turma.php', { turma_id: turmaId }, function(resp){
                    if (resp.success) {
                        $('#total-professores').text(resp.data.length);
                    } else {
                        $('#total-professores').text('0');
                    }
                });

                // 3) Disciplinas + detalhes
                $.getJSON('../includes/ajax/shared/academico/listar_disciplinas_por_turma.php', { turma_id: turmaId }, function(resp){
                    const $tbody = $('#detalhes-turma-body');
                    $tbody.empty();
                    if (resp.success && resp.data.length) {
                        $('#total-disciplinas').text(resp.data.length);
                        // Para cada disciplina, alunos = total alunos da turma (já carregado acima). Aqui exibimos apenas a quantidade no momento do carregamento.
                        // Como chamadas são assíncronas, buscamos o texto atual do card.
                        const alunosTxt = $('#total-alunos').text();
                        resp.data.forEach(d => {
                            $tbody.append(`<tr><td>${d.Nome_Disciplina}</td><td>${d.Professor || ''}</td><td>${alunosTxt}</td><td>${d.Carga_Horaria || ''}</td></tr>`);
                        });
                        const turmaText = $turma.find('option:selected').text();
                        $('.turma-header h5').text(`Turma: ${turmaText}`);
                    } else {
                        $('#total-disciplinas').text('0');
                        $tbody.append('<tr><td colspan="4" class="text-center">Nenhuma disciplina encontrada</td></tr>');
                    }
                });
            }

            // Inicialização
            carregarAnos();

            // Impressão
            $('#print-btn').click(function () { window.print(); });
        });
    </script>
</body>

</html>