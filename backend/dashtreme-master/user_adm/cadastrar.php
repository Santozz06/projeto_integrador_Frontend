<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Disciplinas - Dashboard Acadêmico</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/app-style.css">
    <link rel="stylesheet" href="../assets/css/icons.css">
    <link rel="stylesheet" href="../assets/css/sidebar-menu.css">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background: linear-gradient(to right, #2c3e50, #3498db);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #ecf0f1;
        }

        .form-container {
            background: rgba(255, 255, 255, 0.05);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            margin: 40px auto;
        }

        .form-group label {
            color: #71affe;
            font-weight: 600;
        }

        .form-control {
            background-color: rgba(255, 255, 255, 0.15);
            border: 1px solid #71affe;
            color: #fff;
            border-radius: 6px;
        }

        .form-control:focus {
            background-color: rgba(255, 255, 255, 0.2);
            color: #fff;
            border-color: #1abc9c;
            box-shadow: 0 0 0 0.2rem rgba(26, 188, 156, 0.25);
        }

        .btn-salvar {
            background-color: #1abc9c;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 10px 20px;
        }

        .btn-salvar:hover {
            background-color: #16a085;
        }

        .btn-cancelar {
            background-color: #e74c3c;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 10px 20px;
        }

        .btn-cancelar:hover {
            background-color: #c0392b;
        }

        .btn-voltar {
            background-color: #7f8c8d;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 10px 20px;
        }

        .btn-voltar:hover {
            background-color: #616a6b;
        }

        .section-title {
            color: #71affe;
            border-bottom: 2px solid #71affe;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }

        input::placeholder,
        select,
        textarea {
            color: #ecf0f1;
        }

        option {
            color: #e4dfdf;
        }

        select.form-control option {
            background-color: rgba(45, 65, 91, 0.9);
            color: #ecf0f1;
        }

        .form-section {
            margin-bottom: 20px;
        }

        .bold-title {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .navbar {
            background-color: rgba(0, 0, 0, 0.2) !important;
            backdrop-filter: blur(10px);
        }

        /* Modal transparente no estilo padrão do sistema */
        #confirmExclusaoModal .modal-content {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            color: #ecf0f1;
            border: 1px solid rgba(255, 255, 255, 0.15);
        }
        #confirmExclusaoModal .modal-header { border-bottom: 1px solid rgba(255, 255, 255, 0.15); }
        #confirmExclusaoModal .modal-title { color: #71affe; font-weight: 600; }
        #confirmExclusaoModal .btn-primary { background-color: #e74c3c; border: none; }
        #confirmExclusaoModal .btn-primary:hover { background-color: #c0392b; }
        #confirmExclusaoModal .btn-secondary { background-color: #7f8c8d; border: none; }
        #confirmExclusaoModal .btn-secondary:hover { background-color: #616a6b; }

        /* Botões de ação (igual cadastroTurmas.php) */
        .btn-editar {
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 5px 10px;
            font-size: 12px;
        }
        .btn-editar:hover { background-color: #2980b9; }

        .btn-excluir {
            background-color: #e74c3c;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 5px 10px;
            font-size: 12px;
        }
        .btn-excluir:hover { background-color: #c0392b; }
    </style>
</head>

<body class="bg-theme bg-theme1">
    <?php
    require("menu_padrão.php");
    ?>

    <!-- Conteúdo principal -->
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card" style="background-color: transparent; border: none; box-shadow: none;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="page-title"><i class="zmdi zmdi-plus-circle mr-2"></i> Cadastrar
                                    Disciplina</h4>
                            </div>

                            <!-- Formulário de cadastro/edição de disciplina -->
                            <div class="form-container">
                                <form id="form-disciplina">
                                    <input type="hidden" id="id-disciplina" value="">
                                    <!-- Seção Dados da Disciplina -->
                                    <div class="form-section">
                                        <h5 class="section-title">DADOS DA DISCIPLINA</h5>
                                        <div class="form-group">
                                            <div class="bold-title">Disciplina</div>
                                            <input type="text" id="nome-disciplina" class="form-control"
                                                placeholder="Nome da disciplina">
                                        </div>
                                        <div class="form-group">
                                            <div class="bold-title">Carga horária</div>
                                            <input type="number" id="carga-horaria" class="form-control"
                                                placeholder="Horas totais">
                                        </div>
                                        <div class="form-group">
                                            <div class="bold-title">Ano letivo</div>
                                            <select id="ano-letivo" class="form-control">
                                                <option value="">Selecione o ano letivo</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <div class="bold-title">Etapa/série</div>
                                            <select id="etapa-serie" class="form-control">
                                                <option value="">Selecione a etapa/série</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Botões -->
                                    <div class="btn-group">
                                        <button type="submit" class="btn-salvar" id="btn-salvar">Salvar</button>
                                        <button type="button" class="btn-cancelar" id="btn-cancelar">Cancelar</button>
                                    </div>
                                </form>
                            </div>

                            <!-- Listagem de Disciplinas -->
                            <div class="card mt-4" style="background: rgba(255,255,255,0.05); border-radius: 12px;">
                                <div class="card-body">
                                    <h5 class="section-title">DISCIPLINAS CADASTRADAS</h5>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div></div>
                                        <input type="text" id="filtro-disciplinas" class="form-control" placeholder="Filtrar por nome/etapa/ano" style="max-width: 320px; background-color: rgba(255,255,255,0.15); border: 1px solid #71affe; color:#fff;">
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped" style="color:#ecf0f1;">
                                            <thead>
                                                <tr>
                                                    <th>Nome</th>
                                                    <th>Carga Horária</th>
                                                    <th>Ano Letivo</th>
                                                    <th>Etapa/Série</th>
                                                    <th style="width:140px;">Ações</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody-disciplinas"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="overlay toggle-menu"></div>
    </div>

    <!-- Scripts -->
    <script src="../assets/js/jquery.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/plugins/simplebar/js/simplebar.js"></script>
    <script src="../assets/js/sidebar-menu.js"></script>
    <script src="../assets/js/app-script.js"></script>

    <!-- Modal de confirmação de exclusão -->
    <div class="modal fade" id="confirmExclusaoModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar exclusão</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p id="confirmExclusaoTexto">Tem certeza que deseja excluir esta disciplina?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnConfirmarExclusao">Excluir</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            // Validação e envio do formulário
            $('#form-disciplina').submit(function (e) {
                e.preventDefault();

                if (validarFormulario()) {
                    const payload = {
                        nome_disciplina: $('#nome-disciplina').val().trim(),
                        carga_horaria: $('#carga-horaria').val(),
                        ano_letivo: $('#ano-letivo').val(),
                        etapa: $('#etapa-serie').val() || $('#etapa-serie option:selected').text() || null
                    };

                    const id = $('#id-disciplina').val();
                    const url = id
                        ? '../includes/ajax/disciplinas/atualizar_disciplina.php'
                        : '../includes/ajax/disciplinas/criar_disciplina.php';
                    const data = id ? Object.assign({ id_disciplina: id }, payload) : payload;

                    $.post(url, data)
                        .done(function (resp) {
                            if (resp && resp.success) {
                                alert(id ? 'Disciplina atualizada com sucesso!' : 'Disciplina cadastrada com sucesso!');
                                limparFormulario();
                                carregarDisciplinas();
                            } else {
                                alert(resp && resp.message ? resp.message : 'Operação não concluída.');
                            }
                        })
                        .fail(function (xhr) {
                            let msg = 'Erro na operação.';
                            try { msg += '\n' + (xhr.responseJSON?.message || xhr.responseText); } catch (e) { }
                            alert(msg);
                        });
                }
            });

            // Botão Cancelar
            $('#btn-cancelar').click(function () {
                if (confirm('Deseja realmente cancelar? Todos os dados não salvos serão perdidos.')) {
                    limparFormulario();
                }
            });

            // Validação do formulário
            function validarFormulario() {
                if ($('#nome-disciplina').val() === '') {
                    alert('Por favor, informe o nome da disciplina');
                    return false;
                }
                if ($('#carga-horaria').val() === '' || $('#carga-horaria').val() <= 0) {
                    alert('Por favor, informe uma carga horária válida');
                    return false;
                }
                if ($('#etapa-serie').val() === '') {
                    alert('Por favor, selecione a etapa/série');
                    return false;
                }
                return true;
            }

            // Limpar formulário
            function limparFormulario() {
                $('#nome-disciplina').val('');
                $('#carga-horaria').val('');
                $('#ano-letivo').val('');
                $('#etapa-serie').val('');
                $('#id-disciplina').val('');
            }

            // Carregar anos letivos para o select
            function carregarAnosLetivos() {
                $.get('../includes/ajax/listar_anos_letivos.php')
                    .done(function (resp) {
                        if (resp && resp.success && Array.isArray(resp.data)) {
                            const $sel = $('#ano-letivo');
                            $sel.find('option:not(:first)').remove();
                            resp.data.forEach(function (ano) {
                                $sel.append($('<option>', { value: ano, text: ano }));
                            });
                        }
                    });
            }

            carregarAnosLetivos();

            // Carregar etapas ao iniciar e quando mudar o ano
            function carregarEtapas() {
                const ano = $('#ano-letivo').val();
                const url = '../includes/ajax/listar_etapas.php' + (ano ? ('?ano=' + encodeURIComponent(ano)) : '');
                $.get(url)
                    .done(function (resp) {
                        const $sel = $('#etapa-serie');
                        $sel.find('option:not(:first)').remove();
                        if (resp && resp.success && Array.isArray(resp.data)) {
                            resp.data.forEach(function (etapa) {
                                $sel.append($('<option>', { value: etapa, text: etapa }));
                            });
                        }
                    })
                    .fail(function(){ /* silencioso */});
            }

            $('#ano-letivo').on('change', function(){
                $('#etapa-serie').val('');
                carregarEtapas();
            });

            carregarEtapas();

            // Listar Disciplinas (CRUD visual)
            function carregarDisciplinas() {
                $.get('../includes/ajax/disciplinas/listar_disciplinas.php')
                    .done(function (resp) {
                        const $tbody = $('#tbody-disciplinas');
                        $tbody.empty();
                        if (resp && resp.success && Array.isArray(resp.data)) {
                            if (resp.data.length === 0) {
                                $tbody.append('<tr><td colspan="5" class="text-center">Nenhuma disciplina cadastrada.</td></tr>');
                                return;
                            }
                            resp.data.forEach(function (d) {
                                const nome = d.Nome_Disciplina || '';
                                const carga = d.Carga_Horaria != null ? d.Carga_Horaria : '';
                                const ano = d.Ano_Letivo != null ? d.Ano_Letivo : '';
                                const etapa = d.Etapa || '';
                                const id = d.ID_Disciplina;
                                const row = `
                                    <tr data-id="${id}">
                                        <td>${nome}</td>
                                        <td>${carga}</td>
                                        <td>${ano}</td>
                                        <td>${etapa}</td>
                                        <td>
                                            <button type="button" class="btn btn-editar btn-sm" data-acao="editar">Editar</button>
                                            <button type="button" class="btn btn-excluir btn-sm" data-acao="excluir">Excluir</button>
                                        </td>
                                    </tr>`;
                                $tbody.append(row);
                            });
                        } else {
                            $tbody.append('<tr><td colspan="5" class="text-center">Não foi possível carregar as disciplinas.</td></tr>');
                        }
                    })
                    .fail(function () {
                        const $tbody = $('#tbody-disciplinas');
                        $tbody.empty().append('<tr><td colspan="5" class="text-center">Erro ao carregar as disciplinas.</td></tr>');
                    });
            }

            // Delegação de eventos para Editar/Excluir
            $(document).on('click', 'button[data-acao="editar"]', function () {
                const $tr = $(this).closest('tr');
                const id = $tr.data('id');
                const nome = $tr.children().eq(0).text();
                const carga = $tr.children().eq(1).text();
                const ano = $tr.children().eq(2).text();
                const etapa = $tr.children().eq(3).text();

                $('#id-disciplina').val(id);
                $('#nome-disciplina').val(nome);
                $('#carga-horaria').val(carga);
                $('#ano-letivo').val(ano);
                // Garante que a etapa exista na lista; se não existir, adiciona temporariamente
                const $etapa = $('#etapa-serie');
                if (!$etapa.find(`option[value="${etapa}"]`).length && etapa) {
                    $etapa.append($('<option>', { value: etapa, text: etapa }));
                }
                $etapa.val(etapa);

                $('html, body').animate({ scrollTop: $('.form-container').offset().top - 40 }, 400);
            });

            let disciplinaParaExcluir = { id: null, nome: '' };
            $(document).on('click', 'button[data-acao="excluir"]', function () {
                const $tr = $(this).closest('tr');
                disciplinaParaExcluir.id = $tr.data('id');
                disciplinaParaExcluir.nome = $tr.children().eq(0).text();
                $('#confirmExclusaoTexto').text(`Deseja realmente excluir a disciplina "${disciplinaParaExcluir.nome}"?`);
                $('#confirmExclusaoModal').modal('show');
            });

            $('#btnConfirmarExclusao').on('click', function(){
                if (!disciplinaParaExcluir.id) return;
                $.post('../includes/ajax/disciplinas/excluir_disciplina.php', { id_disciplina: disciplinaParaExcluir.id })
                    .done(function (resp) {
                        $('#confirmExclusaoModal').modal('hide');
                        if (resp && resp.success) {
                            alert('Disciplina excluída com sucesso!');
                            carregarDisciplinas();
                            if ($('#id-disciplina').val() == disciplinaParaExcluir.id) { limparFormulario(); }
                        } else {
                            alert(resp && resp.message ? resp.message : 'Não foi possível excluir.');
                        }
                    })
                    .fail(function (xhr) {
                        $('#confirmExclusaoModal').modal('hide');
                        let msg = 'Erro ao excluir.';
                        try { msg += '\n' + (xhr.responseJSON?.message || xhr.responseText); } catch (e) { }
                        alert(msg);
                    });
            });

            // Filtro simples na tabela
            $('#filtro-disciplinas').on('input', function(){
                const termo = $(this).val().toLowerCase();
                $('#tbody-disciplinas tr').each(function(){
                    const tds = $(this).children();
                    const texto = [tds.eq(0).text(), tds.eq(1).text(), tds.eq(2).text(), tds.eq(3).text()].join(' ').toLowerCase();
                    $(this).toggle(texto.indexOf(termo) !== -1);
                });
            });

            carregarDisciplinas();
        });
    </script>

</body>

</html>