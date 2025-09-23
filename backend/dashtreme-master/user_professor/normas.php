<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Normas - Dashboard Acadêmico</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/app-style.css">
    <link rel="stylesheet" href="../assets/css/icons.css">
    <link rel="stylesheet" href="../assets/css/sidebar-menu.css">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background: linear-gradient(to right, #2c3e50, #3498db);
            color: #ecf0f1;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }

        .main-content {
            margin-left: 240px;
            padding: 20px;
        }

        .container-normas {
            max-width: 1000px;
            margin: 60px auto;
            background: rgba(255, 255, 255, 0.1);
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.4);
            text-align: center;
        }

        h2 {
            color: #ffffff;
            font-size: 32px;
            margin-bottom: 10px;
            font-weight: bold;
        }

        h4 {
            color: #f1f1f1;
            font-size: 18px;
            margin-bottom: 40px;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 40px;
            margin-bottom: 30px;
        }

        .norma-item {
            flex: 1 1 250px;
            background-color: rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: transform 0.2s ease;
        }

        .norma-item:hover {
            transform: scale(1.03);
        }

        .norma-item i {
            font-size: 48px;
            color: #ffffff;
        }

        .norma-item p {
            margin-top: 12px;
            font-weight: 600;
            color: #ffffff;
            font-size: 16px;
        }

        .download-btn i {
            font-size: 28px;
            color: #ffffff;
            margin-top: 10px;
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .download-btn i:hover {
            color: #1abc9c;
        }

        .actions {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 30px;
        }

        .btn-normas {
            padding: 12px 24px;
            background-color: transparent;
            border: 2px solid #ffffff;
            color: #ffffff;
            border-radius: 10px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-normas:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .alert-success {
            background-color: #27ae60;
            padding: 12px;
            border-radius: 6px;
            color: white;
            text-align: center;
            margin-top: 20px;
            display: none;
        }

        @media (max-width: 768px) {
            .norma-item {
                flex: 1 1 100%;
            }

            .btn-normas {
                width: 100%;
            }

            .main-content {
                margin-left: 0;
                padding: 10px;
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
    require("menu_padrao.php");
    ?>

        <div class="main-content">
            <div class="container-normas">
                <h2>Normas da Instituição</h2>
                <h4>Consulte aqui as diretrizes acadêmicas e administrativas.</h4>

                <div class="row">
                    <div class="col-md-3 norma-item">
                        <i class="zmdi zmdi-collection-folder-image"></i>
                        <p>Normas Acadêmicas</p>
                        <a href="../user_professor/ArquivosParaExemplos/normas_academicas.pdf" class="download-btn"
                            download>
                            <i class="zmdi zmdi-download"></i>
                        </a>
                    </div>
                    <div class="col-md-3 norma-item">
                        <i class="zmdi zmdi-file-text"></i>
                        <p>Avaliações e Recuperações</p>
                        <a href="../user_professor/ArquivosParaExemplos/avaliacoes_recuperacoes.pdf"
                            class="download-btn" download>
                            <i class="zmdi zmdi-download"></i>
                        </a>
                    </div>
                    <div class="col-md-3 norma-item">
                        <i class="zmdi zmdi-time"></i>
                        <p>Frequência e Pontualidade</p>
                        <a href="../user_professor/ArquivosParaExemplos/frequencia_pontualidade.pdf"
                            class="download-btn" download>
                            <i class="zmdi zmdi-download"></i>
                        </a>
                    </div>
                </div>
                

                <div class="actions">
                    <button class="btn-normas btn-baixar-todas">Baixar todas as normas em PDF</button>
                    <button class="btn-normas" onclick="atualizarNormas()">Atualizar Normas</button>
                </div>

                <div class="alert-success" id="alertaSucesso">Normas atualizadas com sucesso!</div>
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
        // Caminhos dos PDFs
        const pdfs = {
            normas: '../user_professor/ArquivosParaExemplos/normas_academicas.pdf',
            avaliacoes: '../user_professor/ArquivosParaExemplos/avaliacoes_recuperacoes.pdf',
            frequencia: '../user_professor/ArquivosParaExemplos/frequencia_pontualidade.pdf'
        };

        // Simula o download de um arquivo
        function baixarPDF(caminho, nomeArquivo) {
            const link = document.createElement('a');
            link.href = caminho;
            link.download = nomeArquivo;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // Clique em cada botão individual
        document.querySelectorAll('.download-btn').forEach((btn, index) => {
            btn.addEventListener('click', () => {
                const keys = Object.keys(pdfs);
                const key = keys[index];
                baixarPDF(pdfs[key], `${key}.pdf`);
            });
        });

        // Botão "Baixar todas as normas"
        function baixarTodasNormas() {
            for (const key in pdfs) {
                baixarPDF(pdfs[key], `${key}.pdf`);
            }
        }
        // Atribui função ao botão
        document.querySelector('.btn-baixar-todas').addEventListener('click', baixarTodasNormas);

        function atualizarNormas() {
            const alerta = document.getElementById('alertaSucesso');
            alerta.style.display = 'block';
            setTimeout(() => {
                alerta.style.display = 'none';
            }, 3000);
        }
    </script>

</body>

</html>