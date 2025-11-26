<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <link rel="icon" href="../assets/images/favicon.ico" type="image/x-icon">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentos Institucionais - SAS</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/app-style.css">
    <link rel="stylesheet" href="../assets/css/icons.css">
    <link rel="stylesheet" href="../assets/css/sidebar-menu.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="bg-theme bg-theme1 user_adm_documentos">
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
                                <h4 class="page-title"><i class="zmdi zmdi-file mr-2"></i> Documentos Institucionais
                                </h4>
                            </div>

                            <!-- Formulário para upload de documentos -->
                            <div class="form-container">
                                <form id="form-documento" enctype="multipart/form-data">
                                    <h5 class="section-title">ADICIONAR NOVO DOCUMENTO</h5>

                                    <div class="form-group">
                                        <div class="bold-title">Tipo de Documento</div>
                                        <select id="tipo-documento" class="form-control" required>
                                            <option value="">Selecione o tipo de documento</option>
                                            <option value="norma">Norma</option>
                                            <option value="regulamento">Regulamento</option>
                                            <option value="portaria">Portaria</option>
                                            <option value="resolucao">Resolução</option>
                                            <option value="edital">Edital</option>
                                            <option value="outro">Outro</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <div class="bold-title">Título do Documento</div>
                                        <input type="text" id="titulo-documento" class="form-control"
                                            placeholder="Título do documento" required>
                                    </div>

                                    <div class="form-group">
                                        <div class="bold-title">Descrição</div>
                                        <textarea id="descricao-documento" class="form-control" rows="3"
                                            placeholder="Descrição do documento"></textarea>
                                    </div>

                                    <div class="form-group">
                                        <div class="bold-title">Arquivo</div>
                                        <div class="file-input-container">
                                            <label class="file-input-button">
                                                <i class="zmdi zmdi-cloud-upload"></i>
                                                <span class="select-file-text">Selecione o arquivo</span>
                                                <span class="file-name" id="file-name">Nenhum arquivo
                                                    selecionado</span>
                                                <input type="file" id="arquivo-documento" class="file-input" required
                                                    accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx">
                                            </label>
                                        </div>
                                        <small class="text-white">Formatos aceitos: PDF, DOC, DOCX, XLS, XLSX, PPT,
                                            PPTX</small>
                                    </div>

                                    <div class="form-group">
                                        <div class="bold-title">Data de Vigência</div>
                                        <input type="date" id="data-vigencia" class="form-control">
                                    </div>

                                    <div class="btn-group">
                                        <button type="submit" class="btn-primary" id="btn-enviar">Enviar
                                            Documento</button>
                                        <button type="reset" class="btn-secondary" id="btn-limpar">Limpar</button>
                                    </div>
                                </form>

                                <!-- Lista de documentos existentes -->
                                <div class="mt-5">
                                    <h5 class="section-title">DOCUMENTOS CADASTRADOS</h5>

                                    <div class="table-responsive">
                                        <table class="documentos-table">
                                            <thead>
                                                <tr>
                                                    <th>Tipo</th>
                                                    <th>Título</th>
                                                    <th>Descrição</th>
                                                    <th>Data</th>
                                                    <th>Ações</th>
                                                </tr>
                                            </thead>
                                            <tbody id="lista-documentos">
                                            </tbody>
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
    <script>
        $(document).ready(function () {
            // Mostrar nome do arquivo selecionado
            $('#arquivo-documento').change(function () {
                var fileName = $(this).val().split('\\').pop();
                $('#file-name').text(fileName || 'Nenhum arquivo selecionado');
            });

            // Carregar lista inicial
            function carregarDocumentos() {
                $.getJSON('../includes/ajax/documentos/listar.php')
                    .done(function(res){
                        if (res.success) {
                            const tbody = $('#lista-documentos');
                            tbody.empty();
                            (res.data || []).forEach(function(doc){
                                const data = doc.Data_Vigencia ? new Date(doc.Data_Vigencia).toLocaleDateString('pt-BR') : new Date(doc.Criado_Em).toLocaleDateString('pt-BR');
                                const tr = `
                                    <tr data-id="${doc.ID_Documento}">
                                        <td>${(doc.Tipo || '').charAt(0).toUpperCase() + (doc.Tipo || '').slice(1)}</td>
                                        <td>${doc.Titulo || ''}</td>
                                        <td>${doc.Descricao || '-'}</td>
                                        <td>${data}</td>
                                        <td>
                                            <button class="btn btn-sm action-btn btn-primary btn-download"><i class="zmdi zmdi-download"></i></button>
                                            <button class="btn btn-sm action-btn btn-danger btn-excluir"><i class="zmdi zmdi-delete"></i></button>
                                        </td>
                                    </tr>`;
                                tbody.append(tr);
                            });
                        } else {
                            alert(res.message || 'Falha ao listar documentos');
                        }
                    })
                    .fail(function(xhr){ alert('Erro ao listar: ' + (xhr.responseText || xhr.statusText)); });
            }
            carregarDocumentos();

            // Validação e envio do formulário
            $('#form-documento').submit(function (e) {
                e.preventDefault();
                if (!validarFormulario()) return;

                const fd = new FormData();
                fd.append('tipo', $('#tipo-documento').val());
                fd.append('titulo', $('#titulo-documento').val());
                fd.append('descricao', $('#descricao-documento').val());
                fd.append('data_vigencia', $('#data-vigencia').val());
                const file = $('#arquivo-documento')[0].files[0];
                fd.append('arquivo', file);

                $.ajax({
                    url: '../includes/ajax/documentos/upload.php',
                    method: 'POST',
                    data: fd,
                    processData: false,
                    contentType: false
                }).done(function(res){
                    try { res = typeof res === 'string' ? JSON.parse(res) : res; } catch(e) {}
                    if (res && res.success) {
                        alert('Documento enviado com sucesso!');
                        limparFormulario();
                        carregarDocumentos();
                    } else {
                        alert((res && res.message) || 'Falha ao enviar');
                    }
                }).fail(function(xhr){
                    alert('Erro ao enviar: ' + (xhr.responseText || xhr.statusText));
                });
            });

            // Botão Limpar
            $('#btn-limpar').click(function () {
                limparFormulario();
            });

            // Validação do formulário
            function validarFormulario() {
                if ($('#tipo-documento').val() === '') {
                    alert('Por favor, selecione o tipo de documento');
                    return false;
                }
                if ($('#titulo-documento').val() === '') {
                    alert('Por favor, informe o título do documento');
                    return false;
                }
                if ($('#arquivo-documento').val() === '') {
                    alert('Por favor, selecione um arquivo');
                    return false;
                }
                return true;
            }

            // Limpar formulário
            function limparFormulario() {
                $('#form-documento')[0].reset();
                $('#file-name').text('Nenhum arquivo selecionado');
            }

            // Eventos de ações na tabela
            $(document).on('click', '.btn-download', function(){
                const id = $(this).closest('tr').data('id');
                window.location = '../includes/ajax/documentos/download.php?id=' + id;
            });

            $(document).on('click', '.btn-excluir', function(){
                const id = $(this).closest('tr').data('id');
                if (!confirm('Tem certeza que deseja excluir este documento?')) return;
                $.post('../includes/ajax/documentos/delete.php', { id })
                    .done(function(res){
                        try { res = typeof res === 'string' ? JSON.parse(res) : res; } catch(e) {}
                        if (res && res.success) {
                            carregarDocumentos();
                        } else {
                            alert((res && res.message) || 'Falha ao excluir');
                        }
                    }).fail(function(xhr){
                        alert('Erro ao excluir: ' + (xhr.responseText || xhr.statusText));
                    });
            });
        });
    </script>
</body>

</html>