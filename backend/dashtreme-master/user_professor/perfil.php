<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Perfil Acadêmico - Dashboard</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../assets/css/icons.css" />
    <link rel="stylesheet" href="../assets/css/app-style.css" />
    <link rel="stylesheet" href="../assets/css/sidebar-menu.css" />
    <link rel="stylesheet" href="../css/style.css">
    
</head>

<body class="bg-theme bg-theme1 user_professor_perfil">
    <?php
    require("menu_padrao.php");
    ?>

        <!-- Conteúdo principal -->
        <div class="main-content">
            <div class="perfil-container">
                <div class="perfil-header">Perfil acadêmico (professor)</div>
                <div class="dados-topo">
                    <div class="foto">
                        <img src="../user_adm/imagens/icon_ex1.jpg" alt="avatar" />
                    </div>
                    <div class="dados-pessoais">
                        <p><span>Nome:</span> João da Silva</p>
                        <p><span>Matrícula:</span> 123456</p>
                        <p><span>CPF:</span> 000.000.000-00</p>
                        <p><span>Telefone:</span> (00) 00000-0000</p>
                        <p><span>Email:</span> joao@exemplo.com</p>
                    </div>
                    <div class="dados-profissionais">
                        <p><strong>Titulação:</strong> Mestrado</p>
                        <p><strong>Vínculo:</strong> Efetivo</p>
                        <p><strong>Carga horária:</strong> 20h</p>
                        <p><strong>Disciplinas:</strong> Matemática, Física</p>
                        <p><strong>Ingresso:</strong> 2018</p>
                    </div>
                </div>
                <div class="atuacao">
                    <h5 class="text-center mb-3">Atuação atual</h5>
                    <div class="table-responsive">
                        <table class="table-mobile-responsive">
                            <thead>
                                <tr>
                                    <th>Disciplina</th>
                                    <th>Turma</th>
                                    <th>Turno</th>
                                    <th>Dias/horário</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td data-label="Disciplina">Matemática</td>
                                    <td data-label="Turma">3ºA</td>
                                    <td data-label="Turno">Manhã</td>
                                    <td data-label="Dias/horário">Segunda, Quarta - 08h às 09h</td>
                                </tr>
                                <tr>
                                    <td data-label="Disciplina">Física</td>
                                    <td data-label="Turma">2ºB</td>
                                    <td data-label="Turno">Tarde</td>
                                    <td data-label="Dias/horário">Terça, Quinta - 14h às 15h</td>
                                </tr>
                            </tbody>
                        </table>
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
</body>

</html>