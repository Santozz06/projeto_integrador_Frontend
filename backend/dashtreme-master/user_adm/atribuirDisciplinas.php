<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <link rel="icon" href="../assets/images/favicon.ico" type="image/x-icon">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atribuir Disciplinas a Professores</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/app-style.css">
    <link rel="stylesheet" href="../assets/css/icons.css">
    <link rel="stylesheet" href="../assets/css/sidebar-menu.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="bg-theme bg-theme1 user_adm_atribuirDisciplinas sas-dashboard-bg">
    <?php require("menu_padrão.php"); ?>

    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="page-title"><i class="zmdi zmdi-assignment mr-2"></i> Atribuir Disciplinas a
                            Professores</h4>
                    </div>

                    <div id="alert-placeholder"></div>

                    <div class="filter-section">
                        <div class="row">
                            <div class="col-md-4">
                                <label>Ano Letivo</label>
                                <select id="ano" class="form-control">
                                    <option value="">Selecione...</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label>Professor</label>
                                <select id="prof" class="form-control">
                                    <option value="">Selecione...</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-body">
                            <h5 class="mb-3">Atribuir Disciplina Cadastrada</h5>
                            <form id="formAtribuir" class="row g-3">
                                <div class="col-md-9">
                                    <label>Disciplina</label>
                                    <select class="form-control" id="selDisciplina" required>
                                        <option value="">Selecione uma disciplina...</option>
                                    </select>
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-salvar btn-atribuir">Atribuir</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <h5 class="mt-4">Disciplinas Atribuídas</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="tblDisciplinas">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Carga Horária</th>
                                    <th>Etapa</th>
                                    <th>Ano</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/jquery.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/plugins/simplebar/js/simplebar.js"></script>
    <script src="../assets/js/sidebar-menu.js"></script>
    <script src="../assets/js/app-script.js"></script>
    <script>
        function showAlert(type, msg) {
            const html = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${msg}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>`;
            $('#alert-placeholder').html(html);
            setTimeout(() => $('.alert').alert('close'), 4000);
        }

        function carregarAnos() {
            $.getJSON('../includes/ajax/shared/academico/listar_anos_letivos.php', function (resp) {
                if (resp.success) {
                    const $ano = $('#ano');
                    $ano.empty().append('<option value="">Selecione...</option>');
                    resp.data.forEach(a => $ano.append(`<option value="${a}">${a}</option>`));
                }
            });
        }

        function carregarProfessores() {
            $.getJSON('../includes/ajax/admin/professores/listar_professores.php', function (resp) {
                if (resp.success) {
                    const $prof = $('#prof');
                    $prof.empty().append('<option value="">Selecione...</option>');
                    resp.data.forEach(p => $prof.append(`<option value="${p.ID_Professor}">${p.Nome_Completo}${p.Matricula ? ' - ' + p.Matricula : ''}</option>`));
                }
            });
        }

        function carregarTabela() {
            const ano = $('#ano').val();
            const professor_id = $('#prof').val();
            if (!professor_id) { $('#tblDisciplinas tbody').empty(); return; }
            $.getJSON('../includes/ajax/disciplinas/listar_por_professor.php', { ano, professor_id }, function (resp) {
                const $tb = $('#tblDisciplinas tbody');
                $tb.empty();
                if (resp.success && resp.data.length) {
                    resp.data.forEach(d => {
                        const row = `<tr>
                        <td>${d.Nome_Disciplina}</td>
                        <td>${d.Carga_Horaria ?? ''}</td>
                        <td>${d.Etapa ?? ''}</td>
                        <td>${d.Ano_Letivo ?? ''}</td>
                        <td>
                            <button class="btn btn-sm btn-excluir" data-id="${d.ID_Disciplina}" onclick="desatribuir(${d.ID_Disciplina})">Desatribuir</button>
                        </td>
                    </tr>`;
                        $tb.append(row);
                    });
                } else {
                    $tb.append('<tr><td colspan="5" class="text-center">Nenhuma disciplina atribuída</td></tr>');
                }
            });
        }

        function desatribuir(id) {
            if (!confirm('Deseja remover a atribuição desta disciplina?')) return;
            $.ajax({
                url: '../includes/ajax/disciplinas/desatribuir_disciplina.php',
                method: 'POST',
                dataType: 'json',
                data: { id_disciplina: id, id_professor: $('#prof').val(), ano_letivo: $('#ano').val() }
            }).done(function (resp) {
                if (resp.success) {
                    showAlert('success', 'Atribuição removida com sucesso');
                    carregarDisponiveis();
                    carregarTabela();
                } else {
                    showAlert('danger', resp.message || 'Falha ao desatribuir');
                }
            }).fail(function (xhr) {
                const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Erro no servidor ao desatribuir';
                showAlert('danger', msg);
            });
        }

        $(function () {
            carregarAnos();
            carregarProfessores();
            $('#ano, #prof').on('change', function () {
                carregarTabela();
                carregarDisponiveis();
            });

            function carregarDisponiveis() {
                const ano = $('#ano').val();
                const professor_id = $('#prof').val();
                $.getJSON('../includes/ajax/disciplinas/listar_disponiveis.php', { ano, professor_id }, function (resp) {
                    const $sel = $('#selDisciplina');
                    $sel.empty().append('<option value="">Selecione uma disciplina...</option>');
                    if (resp.success && resp.data.length) {
                        resp.data.forEach(d => {
                            const label = `${d.Nome_Disciplina}${d.Etapa ? ' - ' + d.Etapa : ''}${d.Ano_Letivo ? ' (' + d.Ano_Letivo + ')' : ''}`;
                            $sel.append(`<option value="${d.ID_Disciplina}">${label}</option>`);
                        });
                    }
                });
            }

            $('#formAtribuir').on('submit', function (e) {
                e.preventDefault();
                const ano = $('#ano').val();
                const professor_id = $('#prof').val();
                const id_disciplina = $('#selDisciplina').val();
                if (!ano || !professor_id || !id_disciplina) {
                    showAlert('danger', 'Selecione Ano, Professor e Disciplina.');
                    return;
                }
                $.post('../includes/ajax/disciplinas/atribuir_disciplina.php', { ano_letivo: ano, id_professor: professor_id, id_disciplina })
                    .done(function (resp) {
                        if (resp.success) {
                            showAlert('success', 'Disciplina atribuída com sucesso');
                            $('#selDisciplina').val('');
                            carregarTabela();
                            carregarDisponiveis();
                        } else {
                            showAlert('danger', resp.message || 'Falha ao atribuir');
                        }
                    })
                    .fail(function (xhr) {
                        const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Erro no servidor ao atribuir';
                        showAlert('danger', msg);
                    });
            });

            carregarTabela();
            carregarDisponiveis();
        });
    </script>

    <div class="overlay toggle-menu"></div>

</body>

</html>