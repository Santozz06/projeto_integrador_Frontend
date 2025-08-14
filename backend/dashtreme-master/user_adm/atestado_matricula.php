<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atestado de Matrícula - Dashboard Acadêmico</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/app-style.css">
    <link rel="stylesheet" href="../assets/css/icons.css">
    <link rel="stylesheet" href="../assets/css/sidebar-menu.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .filter-section {
            background-color: transparent !important;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid rgba(0, 0, 0, 0.1);
            box-shadow: none !important;
        }

        .student-list {
            max-height: 500px;
            overflow-y: auto;
            background-color: transparent !important;
            border-radius: 8px;
            border: none !important;
            padding: 0 !important;
        }

        .student-card {
            cursor: pointer;
            transition: all 0.2s ease;
            background-color: transparent !important;
            margin-bottom: 8px;
            border-radius: 4px;
            padding: 12px;
            border: none !important;
            box-shadow: none !important;
            border-left: 3px solid transparent;
            /* Preparando para o estado selecionado */
        }

        /* Contraste no hover - versão sutil */
        .student-card:hover {
            background-color: rgba(0, 0, 0, 0.03) !important;
            /* Cinza muito claro */
            border-left: 3px solid rgba(33, 150, 243, 0.3);
            /* Azul claro */
        }

        /* Contraste quando selecionado - versão mais forte */
        .student-card.selected {
            background-color: rgba(33, 150, 243, 0.08) !important;
            border-left: 3px solid #2196F3;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        /* Melhor contraste para texto */
        .student-card h6 {
            margin-bottom: 4px;
            color: #333;
            font-weight: 500;
        }

        .student-card small {
            color: #555;

        }

        /* Contraste extra no hover */
        .student-card:hover h6 {
            color: #2196F3;
        }

        .student-card.selected h6 {
            color: #1565C0;
        }

        .btn-generate {
            margin-top: 20px;
        }

        .no-print {
            display: block;
        }

        .btn-custom-print {
            background-color: #3498db !important;
            color: white !important;
            border: none !important;
        }

        .btn-custom-print:hover {
            background-color: #2980b9 !important;
        }

        @media print {
            .no-print {
                display: none !important;
            }
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
            <div class="card">
                <div class="card-body no-print">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="page-title"><i class="zmdi zmdi-assignment-account mr-2"></i> Atestado de
                            Matrícula</h4>
                    </div>

                    <!-- Filtros -->
                    <div class="filter-section">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="ano-letivo">Ano Letivo</label>
                                    <select class="form-control" id="ano-letivo">
                                        <option>2025</option>
                                        <option>2024</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="serie">Série</label>
                                    <select class="form-control" id="serie">
                                        <option value="">Todas</option>
                                        <option>1º Ano</option>
                                        <option>2º Ano</option>
                                        <option>3º Ano</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="turma">Turma</label>
                                    <select class="form-control" id="turma">
                                        <option value="">Selecione uma turma</option>
                                        <option>Turma A</option>
                                        <option>Turma B</option>
                                        <option>Turma C</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lista de Alunos -->
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Alunos Matriculados</h5>
                            <div class="card student-list">
                                <div class="card-body">
                                    <div id="alunos-container">
                                        <p class="text-white">Selecione uma turma para visualizar os alunos.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botão de Gerar -->
                    <div class="text-center btn-generate">
                        <button id="gerar-atestado" class="btn btn-custom-print" disabled>
                            <i class="zmdi zmdi-file-text mr-2"></i> Gerar Atestado
                        </button>
                    </div>
                </div>

                <!-- Container para o PDF (inicialmente oculto) -->
                <div id="pdf-container" style="display:none;"></div>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="botaoSair.js"></script>
    <script>
        $(document).ready(function () {
            // Dados de exemplo 
            const alunosPorTurma = {
                "Turma A": [
                    { id: 1, nome: "Ana Silva", matricula: "20250001", curso: "Ensino Médio Integrado ao Técnico em Informática", nivel: "TÉCNICO", modalidade: "PRESENCIAL", turno: "MATUTINO" },
                    { id: 2, nome: "Bruno Oliveira", matricula: "20250002", curso: "Ensino Médio Integrado ao Técnico em Informática", nivel: "TÉCNICO", modalidade: "PRESENCIAL", turno: "MATUTINO" }
                ],
                "Turma B": [
                    { id: 3, nome: "Carlos Souza", matricula: "20250003", curso: "Ensino Médio Regular", nivel: "MÉDIO", modalidade: "PRESENCIAL", turno: "VESPERTINO" },
                    { id: 4, nome: "Daniela Costa", matricula: "20250004", curso: "Ensino Médio Regular", nivel: "MÉDIO", modalidade: "PRESENCIAL", turno: "VESPERTINO" }
                ]
            };

            // Variável para armazenar aluno selecionado
            let alunoSelecionado = null;

            // Carrega alunos quando uma turma é selecionada
            $('#turma').change(function () {
                const turmaSelecionada = $(this).val();
                const alunosContainer = $('#alunos-container');

                if (turmaSelecionada && alunosPorTurma[turmaSelecionada]) {
                    alunosContainer.empty();

                    alunosPorTurma[turmaSelecionada].forEach(aluno => {
                        alunosContainer.append(`
                            <div class="student-card p-3 mb-2 border rounded" data-id="${aluno.id}">
                                <h6>${aluno.nome}</h6>
                                <small class="text-white">Matrícula: ${aluno.matricula}</small>
                            </div>
                        `);
                    });

                    // Adiciona evento de clique nos cards de aluno
                    $('.student-card').click(function () {
                        $('.student-card').removeClass('selected');
                        $(this).addClass('selected');

                        const alunoId = $(this).data('id');
                        alunoSelecionado = alunosPorTurma[turmaSelecionada].find(a => a.id == alunoId);

                        $('#gerar-atestado').prop('disabled', false);
                    });
                } else {
                    alunosContainer.html('<p class="text-muted">Nenhum aluno encontrado para esta turma.</p>');
                    $('#gerar-atestado').prop('disabled', true);
                }
            });

            // Gera o atestado em PDF
            $('#gerar-atestado').click(function () {
                if (!alunoSelecionado) return;

                const dataAtual = new Date();
                const dia = String(dataAtual.getDate()).padStart(2, '0');
                const mes = String(dataAtual.getMonth() + 1).padStart(2, '0');
                const ano = dataAtual.getFullYear();
                const dataFormatada = `${dia}/${mes}/${ano}`;

                // Cria o conteúdo do PDF
                const pdfContent = `
                    <!DOCTYPE html>
                    <html lang="pt-br">
                    <head>
                        <meta charset="UTF-8">
                        <title>Atestado de Matrícula</title>
                        <style>
                            body {
                               font-family: sans-serif;
                                margin: 40px 60px;
                                font-size: 16px;
                                color: #000;
                                line-height: 1.5;
                            }
                            .cabecalho { text-align: center; font-size: 14px; margin-bottom: 30px; line-height: 1.4; }
                            .titulo { text-align: center; font-weight: bold; font-size: 20px; margin: 30px 0; text-transform: uppercase; }
                            .texto { text-align: justify; line-height: 1.8; margin-bottom: 20px; }
                            .dados { margin: 20px 0; line-height: 1.8; }
                            .rodape { margin-top: 40px; text-align: right; }
                            .autenticidade { margin-top: 30px; font-size: 14px; text-align: center; }
                            .codigo { font-weight: bold; margin-top: 10px; text-align: center; }
                            strong { font-weight: bold; }
                        </style>
                    </head>
                    <body>
                        <div id="doc">
                            <div class="cabecalho">
                                República Federativa do Brasil<br>
                                Ministério da Educação<br>
                                Secretaria de Educação Profissional e Tecnológica<br>
                                Instituto Federal de Educação, Ciência e Tecnologia<br>
                                Campus Parobé
                            </div>

                            <div class="titulo">ATESTADO DE MATRÍCULA</div>

                            <div class="texto">
                                Atestamos, para os fins que se fizerem necessários, que o(a) estudante <strong>${alunoSelecionado.nome}</strong> possui vínculo regular de matrícula nesta Instituição de Ensino no curso de <strong>${alunoSelecionado.curso}</strong>, de nível <strong>${alunoSelecionado.nivel}</strong>, modalidade <strong>${alunoSelecionado.modalidade}</strong>, no turno <strong>${alunoSelecionado.turno}</strong>, conforme registro acadêmico atualizado.
                            </div>

                            <div class="dados">
                                Matrícula nº: <strong>${alunoSelecionado.matricula}</strong><br>
                                Período Letivo: <strong>${$('#ano-letivo').val()}</strong><br>
                                Duração: <strong>13/02/${$('#ano-letivo').val()} a 12/12/${$('#ano-letivo').val()}</strong>
                            </div>

                            <div class="rodape">
                                <strong>Parobé - RS, ${dataFormatada}</strong>
                            </div>

                            <div class="autenticidade">
                                Para verificar a autenticidade deste documento, acesse:<br>
                                <a href="http://meusite.com/autenticacao" target="_blank">http://meusite.com/autenticacao</a>
                            </div>

                            <div class="codigo">
                                Código de verificação: <span>${gerarCodigoVerificacao()}</span>
                            </div>
                        </div>
                    </body>
                    </html>
                `;

                // Insere o conteúdo no container oculto
                const pdfContainer = $('#pdf-container');
                pdfContainer.html(pdfContent);

                // Gera o PDF
                const element = pdfContainer.find('#doc')[0];

                html2pdf().set({
                    margin: 10,
                    filename: `atestado_matricula_${alunoSelecionado.matricula}.pdf`,
                    image: { type: 'jpeg', quality: 0.98 },
                    html2canvas: { scale: 2 },
                    jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
                }).from(element).save();
            });

            // Função para gerar código de verificação aleatório
            function gerarCodigoVerificacao() {
                const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ0123456789';
                let result = '';
                for (let i = 0; i < 12; i++) {
                    if (i > 0 && i % 4 === 0) result += '-';
                    result += chars.charAt(Math.floor(Math.random() * chars.length));
                }
                return result;
            }
        });
    </script>
</body>

</html>