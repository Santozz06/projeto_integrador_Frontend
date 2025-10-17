<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atribuir Disciplinas a Professores</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/app-style.css">
    <link rel="stylesheet" href="../assets/css/icons.css">
    <link rel="stylesheet" href="../assets/css/sidebar-menu.css">
    <style>
        .filter-section { background: transparent; border-radius: 8px; padding: 16px; margin-bottom: 16px; }
        .card { background-color: transparent; }
        .table th { background-color: #71affa; color: #fff; }
    </style>
</head>

<body class="bg-theme bg-theme1">
<?php require("menu_padrão.php"); ?>

<div class="content-wrapper">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="page-title"><i class="zmdi zmdi-assignment mr-2"></i> Atribuir Disciplinas a Professores</h4>
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

                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-3">Nova Disciplina</h5>
                        <form id="formDisciplina" class="row g-3">
                            <div class="col-md-6">
                                <label>Nome da Disciplina</label>
                                <input type="text" class="form-control" name="nome" required>
                            </div>
                            <div class="col-md-3">
                                <label>Carga Horária (h/semana)</label>
                                <input type="number" class="form-control" name="carga" min="0" step="1" required>
                            </div>
                            <div class="col-md-3">
                                <label>Etapa (opcional)</label>
                                <input type="text" class="form-control" name="etapa">
                            </div>
                            <div class="col-12 text-right mt-3">
                                <button type="submit" class="btn btn-Salvar">Salvar</button>
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
        setTimeout(()=> $('.alert').alert('close'), 4000);
    }

    function carregarAnos() {
        $.getJSON('../includes/ajax/listar_anos_letivos.php', function(resp){
            if (resp.success) {
                const $ano = $('#ano');
                $ano.empty().append('<option value="">Selecione...</option>');
                resp.data.forEach(a=> $ano.append(`<option value="${a}">${a}</option>`));
            }
        });
    }

    function carregarProfessores() {
        $.getJSON('../includes/ajax/listar_professores.php', function(resp){
            if (resp.success) {
                const $prof = $('#prof');
                $prof.empty().append('<option value="">Selecione...</option>');
                resp.data.forEach(p=> $prof.append(`<option value="${p.ID_Professor}">${p.Nome_Completo}${p.Matricula? ' - '+p.Matricula: ''}</option>`));
            }
        });
    }

    function carregarTabela() {
        const ano = $('#ano').val();
        const professor_id = $('#prof').val();
        if (!professor_id) { $('#tblDisciplinas tbody').empty(); return; }
        $.getJSON('../includes/ajax/disciplinas/listar_disciplinas.php', { ano, professor_id }, function(resp){
            const $tb = $('#tblDisciplinas tbody');
            $tb.empty();
            if (resp.success && resp.data.length) {
                resp.data.forEach(d=>{
                    const row = `<tr>
                        <td>${d.Nome_Disciplina}</td>
                        <td>${d.Carga_Horaria ?? ''}</td>
                        <td>${d.Etapa ?? ''}</td>
                        <td>${d.Ano_Letivo ?? ''}</td>
                        <td>
                            <button class="btn btn-sm btn-danger" data-id="${d.ID_Disciplina}" onclick="excluir(${d.ID_Disciplina})">Excluir</button>
                        </td>
                    </tr>`;
                    $tb.append(row);
                });
            } else {
                $tb.append('<tr><td colspan="5" class="text-center">Nenhuma disciplina atribuída</td></tr>');
            }
        });
    }

    function excluir(id) {
        if (!confirm('Deseja realmente excluir esta disciplina?')) return;
        $.ajax({
            url: '../includes/ajax/disciplinas/excluir_disciplina.php',
            method: 'POST',
            dataType: 'json',
            data: { id_disciplina: id }
        }).done(function(resp){
            if (resp.success) {
                showAlert('success', 'Disciplina excluída com sucesso');
                carregarTabela();
            } else {
                showAlert('danger', resp.message || 'Falha ao excluir');
            }
        }).fail(function(xhr){
            const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Erro no servidor ao excluir';
            showAlert('danger', msg);
        });
    }

    $(function(){
        carregarAnos();
        carregarProfessores();
        $('#ano, #prof').on('change', carregarTabela);

        $('#formDisciplina').on('submit', function(e){
            e.preventDefault();
            const ano = $('#ano').val();
            const professor_id = $('#prof').val();
            const nome = $(this).find('[name="nome"]').val().trim();
            const carga = $(this).find('[name="carga"]').val();
            const etapa = $(this).find('[name="etapa"]').val().trim();

            if (!ano || !professor_id || !nome || !carga) {
                showAlert('danger', 'Preencha Ano, Professor, Nome e Carga Horária.');
                return;
            }

            $.ajax({
                url: '../includes/ajax/disciplinas/criar_disciplina.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    ano_letivo: ano,
                    id_professor: professor_id,
                    nome_disciplina: nome,
                    carga_horaria: carga,
                    etapa: etapa
                }
            }).done(function(resp){
                if (resp.success) {
                    showAlert('success', resp.message || 'Disciplina atribuída com sucesso');
                    $('#formDisciplina')[0].reset();
                    carregarTabela();
                } else {
                    showAlert('danger', resp.message || 'Falha ao salvar disciplina');
                }
            }).fail(function(xhr){
                const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Erro no servidor ao salvar';
                showAlert('danger', msg);
            });
        });
    });
</script>

</body>
</html>
