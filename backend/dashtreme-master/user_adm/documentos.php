<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentos Institucionais - Dashboard Acadêmico</title>
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
            max-width: 800px;
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

        .btn-primary {
            background-color: #1abc9c;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 10px 20px;
        }

        .btn-primary:hover {
            background-color: #16a085;
        }

        .btn-danger {
            background-color: #e74c3c;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 10px 20px;
        }

        .btn-danger:hover {
            background-color: #c0392b;
        }

        .btn-secondary {
            background-color: #7f8c8d;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 10px 20px;
        }

        .btn-secondary:hover {
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

        .documentos-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        .documentos-table th {
            background-color: rgba(113, 175, 254, 0.3);
            color: #ffffff;
            padding: 12px;
            text-align: left;
        }

        .documentos-table td {
            padding: 12px;
            border-bottom: 1px solid rgba(113, 175, 254, 0.2);
        }

        .documentos-table tr:hover {
            background-color: rgba(113, 175, 254, 0.1);
        }

        .action-btn {
            padding: 5px 10px;
            margin-right: 5px;
            border-radius: 4px;
            font-size: 0.8rem;
        }

        .file-input-container {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }

        .file-input-button {
            background-color: rgba(113, 175, 254, 0.3);
            color: #ffffff;
            padding: 8px 15px;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .file-input-button:hover {
            background-color: rgba(113, 175, 254, 0.4);
        }

        .select-file-text {
            color: #ffffff;
        }

        .file-input {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .file-name {
            margin-left: 10px;
            color: #ecf0f1;
            font-size: 0.9rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 70%;
        }

        .navbar {
            background-color: rgba(0, 0, 0, 0.2) !important;
            backdrop-filter: blur(10px);
        }
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
                                <h4 class="page-title"><i class="zmdi zmdi-file mr-2"></i> Documentos Institucionais
                                </h4>
                            </div>

                            <!-- Formulário para upload de documentos -->
                            <div class="form-container">
                                <form id="form-documento">
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
                                            <tbody>
                                                <tr>
                                                    <td>Regulamento</td>
                                                    <td>Regulamento Geral da Instituição</td>
                                                    <td>Documento com as normas gerais da instituição</td>
                                                    <td>15/03/2023</td>
                                                    <td>
                                                        <button class="btn btn-sm action-btn btn-primary"><i
                                                                class="zmdi zmdi-download"></i></button>
                                                        <button class="btn btn-sm action-btn btn-secondary"><i
                                                                class="zmdi zmdi-edit"></i></button>
                                                        <button class="btn btn-sm action-btn btn-danger"><i
                                                                class="zmdi zmdi-delete"></i></button>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Norma</td>
                                                    <td>Normas de Conduta Discente</td>
                                                    <td>Regras de comportamento para alunos</td>
                                                    <td>10/02/2023</td>
                                                    <td>
                                                        <button class="btn btn-sm action-btn btn-primary"><i
                                                                class="zmdi zmdi-download"></i></button>
                                                        <button class="btn btn-sm action-btn btn-secondary"><i
                                                                class="zmdi zmdi-edit"></i></button>
                                                        <button class="btn btn-sm action-btn btn-danger"><i
                                                                class="zmdi zmdi-delete"></i></button>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Portaria</td>
                                                    <td>Portaria de Designação de Coordenadores</td>
                                                    <td>Designação dos coordenadores de curso para 2023</td>
                                                    <td>05/01/2023</td>
                                                    <td>
                                                        <button class="btn btn-sm action-btn btn-primary"><i
                                                                class="zmdi zmdi-download"></i></button>
                                                        <button class="btn btn-sm action-btn btn-secondary"><i
                                                                class="zmdi zmdi-edit"></i></button>
                                                        <button class="btn btn-sm action-btn btn-danger"><i
                                                                class="zmdi zmdi-delete"></i></button>
                                                    </td>
                                                </tr>
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

            // Validação e envio do formulário
            $('#form-documento').submit(function (e) {
                e.preventDefault();

                if (validarFormulario()) {
                    const documento = {
                        tipo: $('#tipo-documento option:selected').text(),
                        tipoValor: $('#tipo-documento').val(),
                        titulo: $('#titulo-documento').val(),
                        descricao: $('#descricao-documento').val(),
                        dataVigencia: $('#data-vigencia').val(),
                        arquivo: $('#arquivo-documento').val().split('\\').pop()
                    };

                    console.log('Dados do documento:', documento);
                    alert('Documento enviado com sucesso!');

                    // Aqui você adicionaria o documento à tabela 
                    adicionarDocumentoNaTabela(documento);
                    limparFormulario();
                }
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

            // Função para adicionar documento na tabela 
            function adicionarDocumentoNaTabela(documento) {
                const dataFormatada = documento.dataVigencia ?
                    new Date(documento.dataVigencia).toLocaleDateString('pt-BR') :
                    new Date().toLocaleDateString('pt-BR');

                const newRow = `
                    <tr>
                        <td>${documento.tipo}</td>
                        <td>${documento.titulo}</td>
                        <td>${documento.descricao || '-'}</td>
                        <td>${dataFormatada}</td>
                        <td>
                            <button class="btn btn-sm action-btn btn-primary"><i class="zmdi zmdi-download"></i></button>
                            <button class="btn btn-sm action-btn btn-secondary"><i class="zmdi zmdi-edit"></i></button>
                            <button class="btn btn-sm action-btn btn-danger"><i class="zmdi zmdi-delete"></i></button>
                        </td>
                    </tr>
                `;

                $('.documentos-table tbody').prepend(newRow);
            }

            // Eventos para os botões de ação na tabela
            $(document).on('click', '.action-btn.btn-primary', function () {
                const row = $(this).closest('tr');
                const titulo = row.find('td:eq(1)').text();
                alert(`Download do documento: ${titulo}`);
            });

            $(document).on('click', '.action-btn.btn-secondary', function () {
                const row = $(this).closest('tr');
                const titulo = row.find('td:eq(1)').text();
                alert(`Editar documento: ${titulo}`);
            });

            $(document).on('click', '.action-btn.btn-danger', function () {
                if (confirm('Tem certeza que deseja excluir este documento?')) {
                    $(this).closest('tr').remove();
                    alert('Documento excluído com sucesso!');
                }
            });
        });
    </script>
</body>

</html>