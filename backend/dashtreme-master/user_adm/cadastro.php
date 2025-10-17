<?php
require_once '../includes/bootstrap.php';

$erro = '';
$sucesso = '';
$aluno_para_edicao = null;
$servidor_para_edicao = null;

require_once '../includes/conexao.php';
require_once '../includes/crud/UsuarioCRUD.php';
require_once '../includes/crud/LocalidadeCRUD.php';

$usuarioCRUD = new UsuarioCRUD($pdo);
$localidadeCRUD = new LocalidadeCRUD($pdo);

// Carregar dados para os selects
$estados = $localidadeCRUD->listarEstados();
$paises = $localidadeCRUD->listarPaises();
$orgaos_expedidores = $localidadeCRUD->listarOrgaosExpedidores();

// Se estiver editando aluno
$municipios_aluno = [];
if (isset($_GET['editarAluno']) && !empty($_GET['editarAluno'])) {
    $id_aluno_edicao = $_GET['editarAluno'];
    $aluno_para_edicao = $usuarioCRUD->buscarAlunoCompleto($id_aluno_edicao);

    // DEBUG: Verificar dados do aluno
    error_log("Dados do aluno carregado: " . print_r($aluno_para_edicao, true));

    // Carregar municípios baseado no UF_Endereco (se existir)
    if ($aluno_para_edicao && isset($aluno_para_edicao['UF_Endereco']) && $aluno_para_edicao['UF_Endereco']) {
        $municipios_aluno = $localidadeCRUD->listarMunicipiosPorEstado($aluno_para_edicao['UF_Endereco']);
    }
}

// Se estiver editando servidor
$municipios_servidor = [];
if (isset($_GET['editarServidor']) && !empty($_GET['editarServidor'])) {
    $id_servidor_edicao = $_GET['editarServidor'];
    $servidor_para_edicao = $usuarioCRUD->buscarProfessorCompleto($id_servidor_edicao);

    // Carregar municípios baseado no UF_Endereco (se existir)
    if ($servidor_para_edicao && isset($servidor_para_edicao['UF_Endereco']) && $servidor_para_edicao['UF_Endereco']) {
        $municipios_servidor = $localidadeCRUD->listarMunicipiosPorEstado($servidor_para_edicao['UF_Endereco']);
    }
}

// PROCESSAR FORMULÁRIO (Criação e Atualização)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if ($_POST['tipo'] === 'aluno') {
            $email = $_POST['email'];
            $id_aluno = $_POST['id_aluno'] ?? null;

            // Verificar se o email já existe (apenas para novos cadastros)
            $email_existente = $usuarioCRUD->emailExiste($email, $id_aluno);
            if ($email_existente) {
                throw new Exception("O email '$email' já está cadastrado para outro usuário.");
            }

            $dadosUsuario = [
                'Login' => $email,
                'Nome_Completo' => $_POST['nomeCompleto'],
                'Email' => $email,
                'Data_Nascimento' => $_POST['dataNascimento'],
                'Sexo' => $_POST['sexo'],
                'CPF' => $_POST['cpf'],
                'Raca_Etnia' => $_POST['racaCor'],
                'Orgao_Exp' => $_POST['orgaoExpedidor'],
                'UF_Exp' => $_POST['ufDocumento'],
                'Telefone' => $_POST['celular'],
                'Endereco' => $_POST['logradouro'] . ', ' . $_POST['numero'] . ' - ' . $_POST['bairro'],
                'Possui_Necessidades_Especiais' => (isset($_POST['nee']) && $_POST['nee'] === 'sim') ? 1 : 0
            ];

            // Senha do aluno: obrigatória no cadastro; em edição só altera se informada e confirmar coincidir
            $senhaAluno = $_POST['senha'] ?? '';
            $confirmarSenhaAluno = $_POST['confirmarSenhaAluno'] ?? '';
            if ($id_aluno) {
                if (!empty($senhaAluno) || !empty($confirmarSenhaAluno)) {
                    if ($senhaAluno !== $confirmarSenhaAluno) {
                        throw new Exception('As senhas do aluno não coincidem.');
                    }
                    $dadosUsuario['Senha'] = $senhaAluno;
                }
            } else {
                if (empty($senhaAluno)) {
                    throw new Exception('A senha é obrigatória para novo cadastro de aluno.');
                }
                if ($senhaAluno !== $confirmarSenhaAluno) {
                    throw new Exception('As senhas do aluno não coincidem.');
                }
                $dadosUsuario['Senha'] = $senhaAluno;
            }

            $matricula = $_POST['matriculaAluno'];

            // Verifica se é uma atualização ou um novo cadastro
            if ($id_aluno) {
                // Atualização
                $usuarioCRUD->atualizarAluno($id_aluno, $dadosUsuario, $matricula);
                $sucesso = "Aluno atualizado com sucesso!";
                // Redireciona mantendo o ID para edição
                header("Location: cadastro.php?editarAluno=" . $id_aluno . "&sucesso=" . urlencode($sucesso));
                exit;
            } else {
                // Novo cadastro
                $idAluno = $usuarioCRUD->cadastrarAluno($dadosUsuario, $matricula);
                $sucesso = "Aluno cadastrado com sucesso! Matrícula: " . $matricula;
                // Redireciona para edição do aluno recém-criado
                header("Location: cadastro.php?editarAluno=" . $idAluno . "&sucesso=" . urlencode($sucesso));
                exit;
            }


        } elseif ($_POST['tipo'] === 'servidor') {
            $email_servidor = $_POST['emailServidor'];
            $id_servidor = $_POST['id_servidor'] ?? null;

            // Verificar se o email já existe
            $email_existente = $usuarioCRUD->emailExiste($email_servidor, $id_servidor);
            if ($email_existente) {
                throw new Exception("O email '$email_servidor' já está cadastrado para outro usuário.");
            }

            $cpf_existente = $usuarioCRUD->cpfExiste($_POST['cpfServidor'], $id_servidor);
            if ($cpf_existente) {
                throw new Exception("O CPF '{$_POST['cpfServidor']}' já está cadastrado para outro usuário.");
            }

            $dadosUsuario = [
                'Login' => $email_servidor,
                'Nome_Completo' => $_POST['nomeCompletoServidor'],
                'Email' => $email_servidor,
                'Data_Nascimento' => $_POST['dataNascimentoServidor'],
                'Sexo' => $_POST['sexoServidor'],
                'CPF' => $_POST['cpfServidor'],
                'Raca_Etnia' => $_POST['racaCorServidor'],
                'Estado_Civil' => $_POST['estadoCivilServidor'],
                'Nacionalidade' => $_POST['nacionalidadeServidor'],
                'Naturalidade' => $_POST['naturalidadeServidor'],
                'Filiacao' => $_POST['filiacaoServidor'],
                'Orgao_Exp' => $_POST['orgaoExpedidorServidor'],
                'UF_Exp' => $_POST['ufDocumentoServidor'],
                'Telefone' => $_POST['celularServidor'],
                'Endereco' => $_POST['logradouroServidor'] . ', ' . $_POST['numeroServidor'] . ' - ' . $_POST['bairroServidor']
            ];

            // Senha: obrigatória para novo cadastro; em edição, só altera se informada e confirmar coincidir
            $senhaServidor = $_POST['senha'] ?? '';
            $confirmarSenhaServidor = $_POST['confirmarSenhaServidor'] ?? '';

            if (!$id_servidor) {
                if (empty($senhaServidor)) {
                    throw new Exception('A senha é obrigatória para novo cadastro de servidor.');
                }
                if ($senhaServidor !== $confirmarSenhaServidor) {
                    throw new Exception('As senhas não coincidem.');
                }
                $dadosUsuario['Senha'] = $senhaServidor;
            } else {
                if (!empty($senhaServidor) || !empty($confirmarSenhaServidor)) {
                    if ($senhaServidor !== $confirmarSenhaServidor) {
                        throw new Exception('As senhas não coincidem.');
                    }
                    $dadosUsuario['Senha'] = $senhaServidor; // atualizar
                }
            }

            if ($id_servidor) {
                // Atualização
                $usuarioCRUD->atualizarProfessor(
                    $id_servidor,
                    $dadosUsuario,
                    $_POST['formacaoAcademica'],
                    $_POST['dataAdmissao'],
                    $_POST['areaAtuacaoServidor'],
                    $_POST['matriculaServidor'] ?? null
                );
                $sucesso = "Servidor atualizado com sucesso!";
                header("Location: cadastro.php?editarServidor=" . $id_servidor . "&sucesso=" . urlencode($sucesso));
                exit;
            } else {
                // Novo cadastro
                $idProfessor = $usuarioCRUD->cadastrarProfessor(
                    $dadosUsuario,
                    $_POST['formacaoAcademica'],
                    $_POST['dataAdmissao'],
                    $_POST['areaAtuacaoServidor'],
                    $_POST['matriculaServidor'] ?? null
                );
                $sucesso = "Servidor cadastrado com sucesso!";
                header("Location: cadastro.php?editarServidor=" . $idProfessor . "&sucesso=" . urlencode($sucesso));
                exit;
            }
        }

    } catch (Exception $e) {
        $msg = $e->getMessage();
        $friendly = '';
        // Tratamento para duplicidades (MySQL 1062) e violação de unique (SQLSTATE 23000)
        if ((method_exists($e, 'getCode') && (int)$e->getCode() === 23000) || stripos($msg, 'Duplicate entry') !== false) {
            if (stripos($msg, 'uniq_email') !== false || stripos($msg, 'Email') !== false) {
                $friendly = 'Este e-mail já está cadastrado. Use outro e-mail ou recupere a senha.';
            } elseif (stripos($msg, 'Matricula') !== false) {
                $friendly = 'Esta matrícula já está cadastrada. Verifique o valor informado.';
            } else {
                $friendly = 'Registro duplicado. Verifique os campos únicos informados.';
            }
        }
        $erro = $friendly ?: ("Erro no cadastro: " . $msg);
        error_log("Erro cadastro: " . $msg);
    }
}

// Exibe mensagens de sucesso ou erro passadas pela URL
if (isset($_GET['sucesso'])) {
    $sucesso = $_GET['sucesso'];
}
if (isset($_GET['erro'])) {
    $erro = $_GET['erro'];
}

$limite_por_pagina = 10;

// Paginação para Alunos
$pagina_alunos = isset($_GET['pagina_alunos']) ? (int) $_GET['pagina_alunos'] : 1;
$total_alunos = $usuarioCRUD->countAlunos();
$total_paginas_alunos = ceil($total_alunos / $limite_por_pagina);
$alunos = $usuarioCRUD->listarAlunos($pagina_alunos, $limite_por_pagina);

// Paginação para Servidores
$pagina_servidores = isset($_GET['pagina_servidores']) ? (int) $_GET['pagina_servidores'] : 1;
$total_servidores = $usuarioCRUD->countProfessores();
$total_paginas_servidores = ceil($total_servidores / $limite_por_pagina);
$servidores = $usuarioCRUD->listarProfessores($pagina_servidores, $limite_por_pagina);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Cadastro - Dashboard Acadêmico" />
    <meta name="author" content="" />
    <title>Cadastro - Dashboard Acadêmico</title>
    <!-- loader-->
    <link href="../assets/css/pace.min.css" rel="stylesheet" />
    <script src="../assets/js/pace.min.js"></script>
    <!--favicon-->
    <link rel="icon" href="../assets/images/favicon.ico" type="image/x-icon">
    <!-- simplebar CSS-->
    <link href="../assets/plugins/simplebar/css/simplebar.css" rel="stylesheet" />
    <!-- Bootstrap core CSS-->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet" />
    <!-- animate CSS-->
    <link href="../assets/css/animate.css" rel="stylesheet" type="text/css" />
    <!-- Icons CSS-->
    <link href="../assets/css/icons.css" rel="stylesheet" type="text/css" />
    <!-- Sidebar CSS-->
    <link href="../assets/css/sidebar-menu.css" rel="stylesheet" />
    <!-- Custom Style-->
    <link href="../assets/css/app-style.css" rel="stylesheet" />

    <!-- Select2 CSS -->
    <link href="../assets/plugins/select2/css/select2.min.css" rel="stylesheet" />
    <link href="../assets/plugins/select2/css/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    <style>
        .form-section {
            margin-bottom: 30px;
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
        }

        .form-section h5 {
            color: #71affa;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .needs-box {
            border: 1px solid #71affa;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
            background-color: transparent;
            color: #212529;
            position: relative;
            z-index: 1;
        }

        .checkbox-label {
            display: block;
            position: relative;
            padding-left: 30px;
            margin-bottom: 12px;
            cursor: pointer;
            user-select: none;
            font-size: 14px;
        }

        .checkbox-label input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }

        .checkmark {
            position: absolute;
            top: 0;
            left: 0;
            height: 20px;
            width: 20px;
            background-color: #eee;
            border-radius: 3px;
        }

        .checkbox-label:hover input~.checkmark {
            background-color: #ccc;
        }

        .checkbox-label input:checked~.checkmark {
            background-color: #2c5f9e;
        }

        .checkmark:after {
            content: "";
            position: absolute;
            display: none;
        }

        .checkbox-label input:checked~.checkmark:after {
            display: block;
        }

        .checkbox-label .checkmark:after {
            left: 7px;
            top: 3px;
            width: 5px;
            height: 10px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        .btn-Salvar {
            background-color: #2ecc71;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 10px 20px;
        }

        .btn-Salvar:hover {
            background-color: #27ae60;
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

        .btn-info {
            background-color: #17a2b8;
            border-color: #17a2b8;
            color: white;
        }

        .btn-info:hover {
            background-color: #138496;
            border-color: #117a8b;
            color: white;
        }

        .btn-info.disabled {
            background-color: #6c757d;
            border-color: #6c757d;
            cursor: not-allowed;
            opacity: 0.65;
        }

        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
            padding: 0.75rem 1.25rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
            border-radius: 0.25rem;
        }

        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
            padding: 0.75rem 1.25rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
            border-radius: 0.25rem;
        }

        .table th {
            background-color: #71affa;
            color: white;
        }

        .nav-tabs .nav-link.active {
            background-color: #71affa;
            color: white;
            border-color: #71affa;
        }

        .nav-tabs .nav-link {
            color: #71affa;
        }

        .select2-container--bootstrap-5 .select2-selection {
            min-height: 38px;
        }
    </style>
</head>

<body class="bg-theme bg-theme1">

    <?php require("menu_padrão.php"); ?>

    <div class="clearfix"></div>

    <div class="content-wrapper">
        <div class="container-fluid">

            <div class="row pt-2 pb-2">
                <div class="col-sm-9">
                    <h4 class="page-title">Cadastro de Alunos e Servidores</h4>
                </div>
            </div>

            <!-- Mensagens -->
            <?php if ($sucesso): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="zmdi zmdi-check-circle mr-2"></i> <?= htmlspecialchars($sucesso) ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <?php if ($erro): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="zmdi zmdi-close-circle mr-2"></i> <?= htmlspecialchars($erro) ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs nav-primary" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#aluno" role="tab">
                                <i class="zmdi zmdi-accounts-alt mr-1"></i> Aluno
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#servidor" role="tab">
                                <i class="zmdi zmdi-account-box mr-1"></i> Servidor
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content pt-3">
                        <!-- Aba Aluno -->
                        <div class="tab-pane fade show active" id="aluno" role="tabpanel">
                            <form id="formAluno" method="POST">
                                <input type="hidden" name="tipo" value="aluno">
                                <input type="hidden" name="id_aluno"
                                    value="<?= $aluno_para_edicao['ID_Usuario'] ?? '' ?>">
                                <!-- Senha agora é capturada pelos campos abaixo; não usamos padrão -->

                                <!-- Dados Pessoais - Aluno -->
                                <div class="form-section">
                                    <h5>Dados Pessoais</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Nome Completo</label>
                                                <input type="text" class="form-control" name="nomeCompleto" required
                                                    value="<?= htmlspecialchars($aluno_para_edicao['Nome_Completo'] ?? '') ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Data de Nascimento</label>
                                                <input type="date" class="form-control" name="dataNascimento" required
                                                    value="<?= htmlspecialchars($aluno_para_edicao['Data_Nascimento'] ?? '') ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Matrícula</label>
                                                <input type="text" class="form-control" name="matriculaAluno" required
                                                    value="<?= htmlspecialchars($aluno_para_edicao['Matricula'] ?? '') ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Sexo</label>
                                                <select class="form-control" name="sexo" required>
                                                    <option value="">Selecione...</option>
                                                    <option <?= ($aluno_para_edicao['Sexo'] ?? '') == 'Masculino' ? 'selected' : '' ?>>Masculino</option>
                                                    <option <?= ($aluno_para_edicao['Sexo'] ?? '') == 'Feminino' ? 'selected' : '' ?>>Feminino</option>
                                                    <option <?= ($aluno_para_edicao['Sexo'] ?? '') == 'Outro' ? 'selected' : '' ?>>Outro</option>
                                                    <option <?= ($aluno_para_edicao['Sexo'] ?? '') == 'Prefiro não informar' ? 'selected' : '' ?>>Prefiro não informar</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Raça/Cor</label>
                                                <select class="form-control" name="racaCor" required>
                                                    <option value="">Selecione...</option>
                                                    <option <?= ($aluno_para_edicao['Raca_Etnia'] ?? '') == 'Branca' ? 'selected' : '' ?>>Branca</option>
                                                    <option <?= ($aluno_para_edicao['Raca_Etnia'] ?? '') == 'Preta' ? 'selected' : '' ?>>Preta</option>
                                                    <option <?= ($aluno_para_edicao['Raca_Etnia'] ?? '') == 'Parda' ? 'selected' : '' ?>>Parda</option>
                                                    <option <?= ($aluno_para_edicao['Raca_Etnia'] ?? '') == 'Amarela' ? 'selected' : '' ?>>Amarela</option>
                                                    <option <?= ($aluno_para_edicao['Raca_Etnia'] ?? '') == 'Indígena' ? 'selected' : '' ?>>Indígena</option>
                                                    <option <?= ($aluno_para_edicao['Raca_Etnia'] ?? '') == 'Prefiro não informar' ? 'selected' : '' ?>>Prefiro não informar</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Senha</label>
                                                <input type="password" class="form-control" name="senha" 
                                                    placeholder="Defina uma senha" <?= empty($aluno_para_edicao) ? 'required' : '' ?>>
                                                <small class="form-text text-muted">Obrigatória no cadastro. Em edição, preencha para alterar.</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Confirmar Senha</label>
                                                <input type="password" class="form-control" name="confirmarSenhaAluno"
                                                    placeholder="Repita a senha" <?= empty($aluno_para_edicao) ? 'required' : '' ?>>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Nacionalidade -->
                                <div class="form-section">
                                    <h5>Nacionalidade</h5>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Nacionalidade</label>
                                                <select class="form-control select2-busca" name="nacionalidade"
                                                    required>
                                                    <option value="">Selecione...</option>
                                                    <?php foreach ($paises as $pais): ?>
                                                        <option value="<?= $pais['nome'] ?>"
                                                            <?= ($aluno_para_edicao['Nacionalidade'] ?? '') == $pais['nome'] ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($pais['nome']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Estado para filtrar naturalidade -->
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Estado de Nascimento</label>
                                                <select class="form-control select2-busca" name="ufNaturalidade"
                                                    id="ufNaturalidade">
                                                    <option value="">Selecione o estado...</option>
                                                    <?php foreach ($estados as $estado): ?>
                                                        <option value="<?= $estado['id'] ?>"
                                                            <?= ($aluno_para_edicao['uf_naturalidade'] ?? '') == $estado['id'] ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($estado['nome']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Naturalidade (Cidade de Nascimento)</label>
                                                <select class="form-control select2-busca" name="naturalidade"
                                                    id="naturalidade" required>
                                                    <option value="">Selecione primeiro o estado...</option>
                                                    <?php if (isset($aluno_para_edicao['naturalidade_id'])): ?>
                                                        <!-- Se estiver editando, mostra a cidade selecionada -->
                                                        <option value="<?= $aluno_para_edicao['naturalidade_id'] ?>"
                                                            selected>
                                                            <?= htmlspecialchars($aluno_para_edicao['Naturalidade'] ?? '') ?>
                                                        </option>
                                                    <?php endif; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Filiação</label>
                                                <input type="text" class="form-control" name="filiacao"
                                                    placeholder="Nome da mãe/pai" required
                                                    value="<?= htmlspecialchars($aluno_para_edicao['Filiacao'] ?? '') ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Documentos -->
                                <div class="form-section">
                                    <h5>Documentos</h5>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>CPF</label>
                                                <input type="text" class="form-control" name="cpf"
                                                    placeholder="000.000.000-00" required
                                                    value="<?= htmlspecialchars($aluno_para_edicao['CPF'] ?? '') ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Data de Expedição</label>
                                                <input type="date" class="form-control" name="dataExpedicao" required
                                                    value="<?= htmlspecialchars($aluno_para_edicao['Data_Expedicao'] ?? '') ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>UF</label>
                                                <select class="form-control select2-busca" name="ufDocumento" required>
                                                    <option value="">Selecione...</option>
                                                    <?php foreach ($estados as $estado): ?>
                                                        <option value="<?= $estado['id'] ?>"
                                                            <?= ($aluno_para_edicao['UF_Exp'] ?? '') == $estado['id'] ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($estado['nome']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Órgão Expedidor</label>
                                                <select class="form-control select2-busca" name="orgaoExpedidor"
                                                    required>
                                                    <option value="">Selecione...</option>
                                                    <?php foreach ($orgaos_expedidores as $orgao): ?>
                                                        <option value="<?= $orgao['sigla'] ?>"
                                                            <?= ($aluno_para_edicao['Orgao_Exp'] ?? '') == $orgao['sigla'] ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($orgao['sigla']) ?> -
                                                            <?= htmlspecialchars($orgao['nome']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Endereço -->
                                <div class="form-section">
                                    <h5>Endereço</h5>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>CEP</label>
                                                <input type="text" class="form-control" name="cep"
                                                    placeholder="00000-000" maxlength="9" required
                                                    value="<?= htmlspecialchars($aluno_para_edicao['CEP'] ?? '') ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label>Logradouro</label>
                                                <input type="text" class="form-control" name="logradouro" required
                                                    value="<?= htmlspecialchars($aluno_para_edicao['Logradouro'] ?? '') ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group">
                                                <label>Nº</label>
                                                <input type="text" class="form-control" name="numero" required
                                                    value="<?= htmlspecialchars($aluno_para_edicao['Numero'] ?? '') ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Complemento</label>
                                                <input type="text" class="form-control" name="complemento"
                                                    value="<?= htmlspecialchars($aluno_para_edicao['Complemento'] ?? '') ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Bairro</label>
                                                <input type="text" class="form-control" name="bairro" required
                                                    value="<?= htmlspecialchars($aluno_para_edicao['Bairro'] ?? '') ?>">
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>UF</label>
                                                <select class="form-control select2-busca" name="ufEndereco"
                                                    id="ufEndereco" required>
                                                    <option value="">Selecione...</option>
                                                    <?php foreach ($estados as $estado): ?>
                                                        <option value="<?= $estado['id'] ?>"
                                                            <?= ($aluno_para_edicao['UF_Endereco'] ?? '') == $estado['id'] ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($estado['nome']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Município</label>
                                                <select class="form-control select2-busca" name="municipio"
                                                    id="municipio" required>
                                                    <option value="">Selecione primeiro o estado...</option>
                                                    <?php if (!empty($municipios_aluno)): ?>
                                                        <?php foreach ($municipios_aluno as $municipio): ?>
                                                            <option value="<?= $municipio['id'] ?>"
                                                                <?= ($aluno_para_edicao['Municipio_Endereco'] ?? '') == $municipio['id'] ? 'selected' : '' ?>>
                                                                <?= htmlspecialchars($municipio['nome']) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Contatos -->
                                <div class="form-section">
                                    <h5>Contatos</h5>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Telefone</label>
                                                <input type="text" class="form-control" name="telefone"
                                                    placeholder="(00) 0000-0000"
                                                    value="<?= htmlspecialchars($aluno_para_edicao['Telefone_Fixo'] ?? '') ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Celular</label>
                                                <input type="text" class="form-control" name="celular"
                                                    placeholder="(00) 00000-0000" required
                                                    value="<?= htmlspecialchars($aluno_para_edicao['Telefone'] ?? '') ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>E-mail</label>
                                                <input type="email" class="form-control" name="email" required
                                                    value="<?= htmlspecialchars($aluno_para_edicao['Email'] ?? '') ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Necessidades Especiais -->
                                <div class="form-section">
                                    <h5>Possui Necessidades Educacionais Especiais (NEE)?</h5>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="nee" id="nee-sim"
                                                    value="sim" <?= ($aluno_para_edicao['Possui_Necessidades_Especiais'] ?? 0) == 1 ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="nee-sim">Sim</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="nee" id="nee-nao"
                                                    value="nao" <?= ($aluno_para_edicao['Possui_Necessidades_Especiais'] ?? 0) == 0 ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="nee-nao">Não</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="needs-box mt-3"
                                        style="<?= ($aluno_para_edicao['Possui_Necessidades_Especiais'] ?? 0) == 1 ? 'display: block;' : 'display: none;' ?>">
                                        <h6>Descrever necessidades:</h6>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label class="checkbox-label">AEE (Atendimento Educacional
                                                    Especializado)
                                                    <input type="checkbox" name="aee" <?= ($aluno_para_edicao['AEE'] ?? 0) == 1 ? 'checked' : '' ?>>
                                                    <span class="checkmark"></span>
                                                </label>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="checkbox-label">Sala de AEE
                                                    <input type="checkbox" name="salaAee"
                                                        <?= ($aluno_para_edicao['Sala_AEE'] ?? 0) == 1 ? 'checked' : '' ?>>
                                                    <span class="checkmark"></span>
                                                </label>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="checkbox-label">Monitor/Estagiário
                                                    <input type="checkbox" name="monitor"
                                                        <?= ($aluno_para_edicao['Monitor'] ?? 0) == 1 ? 'checked' : '' ?>>
                                                    <span class="checkmark"></span>
                                                </label>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="checkbox-label">Intérprete de Libras
                                                    <input type="checkbox" name="interprete"
                                                        <?= ($aluno_para_edicao['Interprete_Libras'] ?? 0) == 1 ? 'checked' : '' ?>>
                                                    <span class="checkmark"></span>
                                                </label>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="checkbox-label">Material adaptado
                                                    <input type="checkbox" name="materialAdaptado"
                                                        <?= ($aluno_para_edicao['Material_Adaptado'] ?? 0) == 1 ? 'checked' : '' ?>>
                                                    <span class="checkmark"></span>
                                                </label>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="checkbox-label">Tecnologia assistiva
                                                    <input type="checkbox" name="tecnologiaAssistiva"
                                                        <?= ($aluno_para_edicao['Tecnologia_Assistiva'] ?? 0) == 1 ? 'checked' : '' ?>>
                                                    <span class="checkmark"></span>
                                                </label>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Outros (especificar)</label>
                                                    <input type="text" class="form-control" name="outrasNecessidades"
                                                        value="<?= htmlspecialchars($aluno_para_edicao['Outras_Necessidades'] ?? '') ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Botões -->
                                <div class="form-group row">
                                    <div class="col-sm-12 text-right">
                                        <button type="submit" class="btn btn-Salvar px-5"
                                            id="btnSalvarAluno">Salvar</button>
                                        <button type="button" class="btn btn-info px-5" id="btnVincularAluno"
                                            onclick="verificarEEnviarParaVinculos('aluno')">Vincular</button>
                                        <a href="cadastro.php" class="btn btn-cancelar px-5">Cancelar</a>
                                    </div>
                                </div>
                            </form>

                            <!-- Listagem de Alunos -->
                            <h5 class="mt-4">Alunos Cadastrados</h5>
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th>Matrícula</th>
                                        <th>Telefone</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($alunos as $aluno): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($aluno['Nome_Completo']) ?></td>
                                            <td><?= htmlspecialchars($aluno['Email']) ?></td>
                                            <td><?= htmlspecialchars($aluno['Matricula'] ?? 'N/A') ?></td>
                                            <td><?= htmlspecialchars($aluno['Telefone'] ?? 'N/A') ?></td>
                                            <td>
                                                <a href="?editarAluno=<?= $aluno['ID_Usuario'] ?>"
                                                    class="btn btn-sm btn-primary">Editar</a>
                                                <a href="?excluirAluno=<?= $aluno['ID_Usuario'] ?>"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Deseja realmente excluir este aluno?');">Excluir</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <!-- Paginação Alunos -->
                            <nav>
                                <ul class="pagination justify-content-center">
                                    <li class="page-item <?= ($pagina_alunos <= 1) ? 'disabled' : '' ?>">
                                        <a class="page-link"
                                            href="?pagina_alunos=<?= $pagina_alunos - 1 ?>">Anterior</a>
                                    </li>
                                    <?php for ($i = 1; $i <= $total_paginas_alunos; $i++): ?>
                                        <li class="page-item <?= ($pagina_alunos == $i) ? 'active' : '' ?>">
                                            <a class="page-link" href="?pagina_alunos=<?= $i ?>"><?= $i ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    <li
                                        class="page-item <?= ($pagina_alunos >= $total_paginas_alunos) ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?pagina_alunos=<?= $pagina_alunos + 1 ?>">Próxima</a>
                                    </li>
                                </ul>
                            </nav>

                        </div> <!-- Fim da aba Aluno -->

                        <!-- Aba Servidor -->
                        <div class="tab-pane fade" id="servidor" role="tabpanel">
                            <form id="formServidor" method="POST" novalidate>
                                <input type="hidden" name="tipo" value="servidor">
                                <input type="hidden" name="id_servidor"
                                    value="<?= $servidor_para_edicao['ID_Usuario'] ?? '' ?>">

                                <!-- Dados Pessoais -->
                                <div class="form-section">
                                    <h5>Dados Pessoais</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Nome Completo</label>
                                                <input type="text" class="form-control" name="nomeCompletoServidor"
                                                    required
                                                    value="<?= htmlspecialchars($servidor_para_edicao['Nome_Completo'] ?? '') ?>">
                                                <div class="invalid-feedback">Por favor, informe o nome completo</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Data de Nascimento</label>
                                                <input type="date" class="form-control" name="dataNascimentoServidor"
                                                    required
                                                    value="<?= htmlspecialchars($servidor_para_edicao['Data_Nascimento'] ?? '') ?>">
                                                <div class="invalid-feedback">Por favor, informe a data de nascimento
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Matrícula</label>
                                                <input type="text" class="form-control" name="matriculaServidor"
                                                    value="<?= htmlspecialchars($servidor_para_edicao['Matricula'] ?? '') ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Sexo</label>
                                                <select class="form-control" name="sexoServidor" required>
                                                    <option value="">Selecione...</option>
                                                    <option <?= ($servidor_para_edicao['Sexo'] ?? '') == 'Masculino' ? 'selected' : '' ?>>Masculino</option>
                                                    <option <?= ($servidor_para_edicao['Sexo'] ?? '') == 'Feminino' ? 'selected' : '' ?>>Feminino</option>
                                                    <option <?= ($servidor_para_edicao['Sexo'] ?? '') == 'Outro' ? 'selected' : '' ?>>Outro</option>
                                                    <option <?= ($servidor_para_edicao['Sexo'] ?? '') == 'Prefiro não informar' ? 'selected' : '' ?>>Prefiro não informar</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Raça/Cor</label>
                                                <select class="form-control" name="racaCorServidor" required>
                                                    <option value="">Selecione...</option>
                                                    <option <?= ($servidor_para_edicao['Raca_Etnia'] ?? '') == 'Branca' ? 'selected' : '' ?>>Branca</option>
                                                    <option <?= ($servidor_para_edicao['Raca_Etnia'] ?? '') == 'Preta' ? 'selected' : '' ?>>Preta</option>
                                                    <option <?= ($servidor_para_edicao['Raca_Etnia'] ?? '') == 'Parda' ? 'selected' : '' ?>>Parda</option>
                                                    <option <?= ($servidor_para_edicao['Raca_Etnia'] ?? '') == 'Amarela' ? 'selected' : '' ?>>Amarela</option>
                                                    <option <?= ($servidor_para_edicao['Raca_Etnia'] ?? '') == 'Indígena' ? 'selected' : '' ?>>Indígena</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Estado Civil</label>
                                                <select class="form-control" name="estadoCivilServidor" required>
                                                    <option value="">Selecione...</option>
                                                    <option <?= ($servidor_para_edicao['Estado_Civil'] ?? '') == 'Solteiro(a)' ? 'selected' : '' ?>>Solteiro(a)</option>
                                                    <option <?= ($servidor_para_edicao['Estado_Civil'] ?? '') == 'Casado(a)' ? 'selected' : '' ?>>Casado(a)</option>
                                                    <option <?= ($servidor_para_edicao['Estado_Civil'] ?? '') == 'Divorciado(a)' ? 'selected' : '' ?>>Divorciado(a)</option>
                                                    <option <?= ($servidor_para_edicao['Estado_Civil'] ?? '') == 'Viúvo(a)' ? 'selected' : '' ?>>Viúvo(a)</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Senha do Servidor (colocada aqui para manter consistência com a aba Aluno) -->
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Senha</label>
                                                <input type="password" class="form-control" name="senha"
                                                    placeholder="Defina uma senha" <?= empty($servidor_para_edicao) ? 'required' : '' ?>>
                                                <small class="form-text text-muted">Obrigatória no cadastro. Em edição, preencha para alterar.</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Confirmar Senha</label>
                                                <input type="password" class="form-control" name="confirmarSenhaServidor"
                                                    placeholder="Repita a senha" <?= empty($servidor_para_edicao) ? 'required' : '' ?>>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Nacionalidade -->
                                <div class="form-section">
                                    <h5>Nacionalidade</h5>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Nacionalidade</label>
                                                <select class="form-control select2-busca" name="nacionalidadeServidor"
                                                    required>
                                                    <option value="">Selecione...</option>
                                                    <?php foreach ($paises as $pais): ?>
                                                        <option value="<?= $pais['nome'] ?>"
                                                            <?= ($servidor_para_edicao['Nacionalidade'] ?? '') == $pais['nome'] ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($pais['nome']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Estado de Nascimento</label>
                                                <select class="form-control select2-busca" name="ufNaturalidadeServidor"
                                                    id="ufNaturalidadeServidor">
                                                    <option value="">Selecione o estado...</option>
                                                    <?php foreach ($estados as $estado): ?>
                                                        <option value="<?= $estado['id'] ?>"
                                                            <?= ($servidor_para_edicao['uf_naturalidade'] ?? '') == $estado['id'] ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($estado['nome']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Naturalidade (Cidade de Nascimento)</label>
                                                <select class="form-control select2-busca" name="naturalidadeServidor"
                                                    id="naturalidadeServidor" required>
                                                    <option value="">Selecione primeiro o estado...</option>
                                                    <?php if (isset($servidor_para_edicao['naturalidade_id'])): ?>
                                                        <option value="<?= $servidor_para_edicao['naturalidade_id'] ?>"
                                                            selected>
                                                            <?= htmlspecialchars($servidor_para_edicao['Naturalidade'] ?? '') ?>
                                                        </option>
                                                    <?php endif; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Filiação</label>
                                                <input type="text" class="form-control" name="filiacaoServidor" required
                                                    value="<?= htmlspecialchars($servidor_para_edicao['Filiacao'] ?? '') ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Documentos -->
                                <div class="form-section">
                                    <h5>Documentos</h5>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>CPF</label>
                                                <input type="text" class="form-control" name="cpfServidor" required
                                                    value="<?= htmlspecialchars($servidor_para_edicao['CPF'] ?? '') ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>RG</label>
                                                <input type="text" class="form-control" name="rgServidor" required
                                                    value="<?= htmlspecialchars($servidor_para_edicao['RG'] ?? '') ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Órgão Expedidor</label>
                                                <select class="form-control select2-busca" name="orgaoExpedidorServidor"
                                                    required>
                                                    <option value="">Selecione...</option>
                                                    <?php foreach ($orgaos_expedidores as $orgao): ?>
                                                        <option value="<?= $orgao['sigla'] ?>"
                                                            <?= ($servidor_para_edicao['Orgao_Exp'] ?? '') == $orgao['sigla'] ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($orgao['sigla']) ?> -
                                                            <?= htmlspecialchars($orgao['nome']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>UF</label>
                                                <select class="form-control select2-busca" name="ufDocumentoServidor"
                                                    required>
                                                    <option value="">Selecione...</option>
                                                    <?php foreach ($estados as $estado): ?>
                                                        <option value="<?= $estado['id'] ?>"
                                                            <?= ($servidor_para_edicao['UF_Exp'] ?? '') == $estado['id'] ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($estado['nome']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Endereço -->
                                <div class="form-section">
                                    <h5>Endereço</h5>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>CEP</label>
                                                <input type="text" class="form-control" name="cepServidor"
                                                    placeholder="00000-000" maxlength="9" required
                                                    value="<?= htmlspecialchars($servidor_para_edicao['CEP'] ?? '') ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label>Logradouro</label>
                                                <input type="text" class="form-control" name="logradouroServidor"
                                                    required
                                                    value="<?= htmlspecialchars($servidor_para_edicao['Logradouro'] ?? '') ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group">
                                                <label>Nº</label>
                                                <input type="text" class="form-control" name="numeroServidor" required
                                                    value="<?= htmlspecialchars($servidor_para_edicao['Numero'] ?? '') ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Complemento</label>
                                                <input type="text" class="form-control" name="complementoServidor"
                                                    value="<?= htmlspecialchars($servidor_para_edicao['Complemento'] ?? '') ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Bairro</label>
                                                <input type="text" class="form-control" name="bairroServidor" required
                                                    value="<?= htmlspecialchars($servidor_para_edicao['Bairro'] ?? '') ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>UF</label>
                                                <select class="form-control select2-busca" name="ufEnderecoServidor"
                                                    id="ufEnderecoServidor" required>
                                                    <option value="">Selecione...</option>
                                                    <?php foreach ($estados as $estado): ?>
                                                        <option value="<?= $estado['id'] ?>"
                                                            <?= ($servidor_para_edicao['estado_id'] ?? '') == $estado['id'] ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($estado['nome']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Município</label>
                                                <select class="form-control select2-busca" name="municipioServidor"
                                                    id="municipioServidor" required>
                                                    <option value="">Selecione primeiro o estado...</option>
                                                    <?php if (!empty($municipios_servidor)): ?>
                                                        <?php foreach ($municipios_servidor as $municipio): ?>
                                                            <option value="<?= $municipio['id'] ?>"
                                                                <?= ($servidor_para_edicao['municipio_id'] ?? '') == $municipio['id'] ? 'selected' : '' ?>>
                                                                <?= htmlspecialchars($municipio['nome']) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Contatos -->
                                <div class="form-section">
                                    <h5>Contatos</h5>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Telefone</label>
                                                <input type="text" class="form-control" name="telefoneServidor"
                                                    value="<?= htmlspecialchars($servidor_para_edicao['Telefone_Fixo'] ?? '') ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Celular</label>
                                                <input type="text" class="form-control" name="celularServidor" required
                                                    value="<?= htmlspecialchars($servidor_para_edicao['Telefone'] ?? '') ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>E-mail</label>
                                                <input type="email" class="form-control" name="emailServidor" required
                                                    value="<?= htmlspecialchars($servidor_para_edicao['Email'] ?? '') ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Dados Profissionais -->
                                <div class="form-section">
                                    <h5>Dados Profissionais</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Formação Acadêmica</label>
                                                <select class="form-control" name="formacaoAcademica" required>
                                                    <option value="">Selecione...</option>
                                                    <option <?= ($servidor_para_edicao['Formacao'] ?? '') == 'Graduação Completa' ? 'selected' : '' ?>>Graduação Completa</option>
                                                    <option <?= ($servidor_para_edicao['Formacao'] ?? '') == 'Pós-Graduação' ? 'selected' : '' ?>>Pós-Graduação</option>
                                                    <option <?= ($servidor_para_edicao['Formacao'] ?? '') == 'Mestrado' ? 'selected' : '' ?>>Mestrado</option>
                                                    <option <?= ($servidor_para_edicao['Formacao'] ?? '') == 'Doutorado' ? 'selected' : '' ?>>Doutorado</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Data de Admissão</label>
                                                <input type="date" class="form-control" name="dataAdmissao" required
                                                    value="<?= htmlspecialchars($servidor_para_edicao['Data_Ingresso'] ?? '') ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Área de Atuação</label>
                                                <input type="text" class="form-control" name="areaAtuacaoServidor"
                                                    required
                                                    value="<?= htmlspecialchars($servidor_para_edicao['Area_Atuacao'] ?? '') ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Botões -->
                                <div class="form-group row mt-3">
                                    <div class="col-sm-12 text-right">
                                        <button type="submit" class="btn btn-Salvar px-5">Salvar</button>
                                        <button type="button" class="btn btn-info px-5" id="btnVincularServidor"
                                            onclick="verificarEEnviarParaVinculos('servidor')">Vincular</button>
                                        <button type="button" class="btn btn-cancelar px-5"
                                            id="btnCancelarServidor">Cancelar</button>
                                    </div>
                                </div>
                            </form>

                            <!-- Listagem de Servidores -->
                            <h5 class="mt-4">Servidores Cadastrados</h5>
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th>Formação</th>
                                        <th>Matrícula</th>
                                        <th>Telefone</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($servidores as $servidor): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($servidor['Nome_Completo']) ?></td>
                                            <td><?= htmlspecialchars($servidor['Email']) ?></td>
                                            <td><?= htmlspecialchars($servidor['Formacao_Academica'] ?? 'N/A') ?></td>
                                            <td><?= htmlspecialchars($servidor['Matricula'] ?? 'N/A') ?></td>
                                            <td><?= htmlspecialchars($servidor['Telefone'] ?? 'N/A') ?></td>
                                            <td>
                                                <a href="?editarServidor=<?= $servidor['ID_Usuario'] ?>"
                                                    class="btn btn-sm btn-primary">Editar</a>
                                                <a href="?excluirServidor=<?= $servidor['ID_Usuario'] ?>"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Deseja realmente excluir este servidor?');">Excluir</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <!-- Paginação Servidores -->
                            <nav>
                                <ul class="pagination justify-content-center">
                                    <li class="page-item <?= ($pagina_servidores <= 1) ? 'disabled' : '' ?>">
                                        <a class="page-link"
                                            href="?pagina_servidores=<?= $pagina_servidores - 1 ?>">Anterior</a>
                                    </li>
                                    <?php for ($i = 1; $i <= $total_paginas_servidores; $i++): ?>
                                        <li class="page-item <?= ($pagina_servidores == $i) ? 'active' : '' ?>">
                                            <a class="page-link" href="?pagina_servidores=<?= $i ?>"><?= $i ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    <li
                                        class="page-item <?= ($pagina_servidores >= $total_paginas_servidores) ? 'disabled' : '' ?>">
                                        <a class="page-link"
                                            href="?pagina_servidores=<?= $pagina_servidores + 1 ?>">Próxima</a>
                                    </li>
                                </ul>
                            </nav>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="../assets/js/jquery.min.js"></script>
    <script src="../assets/js/popper.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>

    <!-- simplebar js -->
    <script src="../assets/plugins/simplebar/js/simplebar.js"></script>
    <!-- sidebar-menu js -->
    <script src="../assets/js/sidebar-menu.js"></script>
    <!-- loader scripts -->
    <script src="../assets/js/jquery.loading-indicator.js"></script>
    <!-- Custom scripts -->
    <script src="../assets/js/app-script.js"></script>

    <!-- Select2 JS -->
    <script src="../assets/plugins/select2/js/select2.min.js"></script>
    <script src="../assets/plugins/select2/js/i18n/pt-BR.js"></script>

    <script>
        // Inicializar Select2 para todos os selects com busca
        $(document).ready(function () {
            $('.select2-busca').select2({
                theme: 'bootstrap-5',
                language: 'pt-BR',
                placeholder: 'Digite para buscar...',
                allowClear: true,
                width: '100%'
            });


            // Naturalidade - Aluno
            $('#ufNaturalidade').on('change', function () {
                const estadoId = this.value;
                const naturalidadeSelect = $('#naturalidade');

                console.log('Estado naturalidade selecionado:', estadoId);

                if (estadoId) {
                    fetch(`../includes/ajax/carregar_municipios.php?estado_id=${estadoId}`)
                        .then(response => response.json())
                        .then(data => {
                            naturalidadeSelect.empty().append('<option value="">Selecione a cidade...</option>');
                            data.forEach(municipio => {
                                naturalidadeSelect.append(`<option value="${municipio.id}">${municipio.nome}</option>`);
                            });
                            naturalidadeSelect.trigger('change');
                        })
                        .catch(error => {
                            console.error('Erro ao carregar municípios para naturalidade:', error);
                            naturalidadeSelect.empty().append('<option value="">Erro ao carregar cidades</option>');
                        });
                } else {
                    naturalidadeSelect.empty().append('<option value="">Selecione primeiro o estado...</option>');
                }
            });

            // Naturalidade - Servidor
            $('#ufNaturalidadeServidor').on('change', function () {
                const estadoId = this.value;
                const naturalidadeSelect = $('#naturalidadeServidor');

                console.log('Estado naturalidade servidor selecionado:', estadoId);

                if (estadoId) {
                    fetch(`../includes/ajax/carregar_municipios.php?estado_id=${estadoId}`)
                        .then(response => response.json())
                        .then(data => {
                            naturalidadeSelect.empty().append('<option value="">Selecione a cidade...</option>');
                            data.forEach(municipio => {
                                naturalidadeSelect.append(`<option value="${municipio.id}">${municipio.nome}</option>`);
                            });
                            naturalidadeSelect.trigger('change');
                        })
                        .catch(error => {
                            console.error('Erro ao carregar municípios para naturalidade servidor:', error);
                            naturalidadeSelect.empty().append('<option value="">Erro ao carregar cidades</option>');
                        });
                } else {
                    naturalidadeSelect.empty().append('<option value="">Selecione primeiro o estado...</option>');
                }
            });

            // Endereço - Município - Aluno
            $('#ufEndereco').on('change', function () {
                const estadoId = this.value;
                const municipioSelect = $('#municipio');

                console.log('Estado endereço selecionado:', estadoId);

                if (estadoId) {
                    municipioSelect.empty().append('<option value="">Carregando...</option>');

                    fetch(`../includes/ajax/carregar_municipios.php?estado_id=${estadoId}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Erro na resposta do servidor');
                            }
                            return response.json();
                        })
                        .then(data => {
                            municipioSelect.empty().append('<option value="">Selecione o município...</option>');
                            if (data && data.length > 0) {
                                data.forEach(municipio => {
                                    municipioSelect.append(`<option value="${municipio.id}">${municipio.nome}</option>`);
                                });
                            } else {
                                municipioSelect.append('<option value="">Nenhum município encontrado</option>');
                            }
                            municipioSelect.trigger('change');
                        })
                        .catch(error => {
                            console.error('Erro ao carregar municípios para endereço:', error);
                            municipioSelect.empty().append('<option value="">Erro ao carregar municípios</option>');
                        });
                } else {
                    municipioSelect.empty().append('<option value="">Selecione primeiro o estado...</option>');
                }
            });

            // Endereço - Município - Servidor
            $('#ufEnderecoServidor').on('change', function () {
                const estadoId = this.value;
                const municipioSelect = $('#municipioServidor');

                console.log('Estado endereço servidor selecionado:', estadoId);

                if (estadoId) {
                    municipioSelect.empty().append('<option value="">Carregando...</option>');

                    fetch(`../includes/ajax/carregar_municipios.php?estado_id=${estadoId}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Erro na resposta do servidor');
                            }
                            return response.json();
                        })
                        .then(data => {
                            municipioSelect.empty().append('<option value="">Selecione o município...</option>');
                            if (data && data.length > 0) {
                                data.forEach(municipio => {
                                    municipioSelect.append(`<option value="${municipio.id}">${municipio.nome}</option>`);
                                });
                            } else {
                                municipioSelect.append('<option value="">Nenhum município encontrado</option>');
                            }
                            municipioSelect.trigger('change');
                        })
                        .catch(error => {
                            console.error('Erro ao carregar municípios para endereço servidor:', error);
                            municipioSelect.empty().append('<option value="">Erro ao carregar municípios</option>');
                        });
                } else {
                    municipioSelect.empty().append('<option value="">Selecione primeiro o estado...</option>');
                }
            });

            // Máscara para CEP - limitar a 8 dígitos
            function aplicarMascaraCEP(input) {
                // Remove tudo que não é número
                let cep = input.value.replace(/\D/g, '');

                // Limita a 8 caracteres
                if (cep.length > 8) {
                    cep = cep.substring(0, 8);
                }

                // Aplica a máscara: 00000-000
                if (cep.length > 5) {
                    cep = cep.substring(0, 5) + '-' + cep.substring(5);
                }

                input.value = cep;
            }

            // Event listeners para os campos CEP com máscara
            $('#cep').on('input', function () {
                aplicarMascaraCEP(this);
            });

            $('#cepServidor').on('input', function () {
                aplicarMascaraCEP(this);
            });

            // Se estiver editando e já tiver um estado selecionado, carrega os municípios para endereço (Aluno)
            <?php if (isset($aluno_para_edicao) && !empty($aluno_para_edicao['UF_Endereco'])): ?>
                (function(){
                    const estadoEnderecoIdAluno = <?= (int)$aluno_para_edicao['UF_Endereco'] ?>;
                    const municipioSelecionadoAluno = <?= isset($aluno_para_edicao['Municipio_Endereco']) ? (int)$aluno_para_edicao['Municipio_Endereco'] : 'null' ?>;
                    fetch(`../includes/ajax/carregar_municipios.php?estado_id=${estadoEnderecoIdAluno}`)
                        .then(r=>r.json())
                        .then(data=>{
                            const $sel = $('#municipio');
                            $sel.empty().append('<option value="">Selecione...</option>');
                            data.forEach(m=>{
                                const sel = (municipioSelecionadoAluno === m.id) ? 'selected' : '';
                                $sel.append(`<option value="${m.id}" ${sel}>${m.nome}</option>`);
                            });
                            $sel.trigger('change');
                        });
                })();
            <?php endif; ?>

            // Se estiver editando servidor e já tiver um estado selecionado, carrega os municípios para endereço (Servidor)
            <?php if (isset($servidor_para_edicao) && !empty($servidor_para_edicao['UF_Endereco'])): ?>
                (function(){
                    const estadoEnderecoIdServ = <?= (int)$servidor_para_edicao['UF_Endereco'] ?>;
                    const municipioSelecionadoServ = <?= isset($servidor_para_edicao['Municipio_Endereco']) ? (int)$servidor_para_edicao['Municipio_Endereco'] : 'null' ?>;
                    fetch(`../includes/ajax/carregar_municipios.php?estado_id=${estadoEnderecoIdServ}`)
                        .then(r=>r.json())
                        .then(data=>{
                            const $sel = $('#municipioServidor');
                            $sel.empty().append('<option value="">Selecione...</option>');
                            data.forEach(m=>{
                                const sel = (municipioSelecionadoServ === m.id) ? 'selected' : '';
                                $sel.append(`<option value="${m.id}" ${sel}>${m.nome}</option>`);
                            });
                            $sel.trigger('change');
                        });
                })();
            <?php endif; ?>

            // Se estiver editando servidor e já tiver um estado de naturalidade, carrega as cidades
            <?php if (isset($servidor_para_edicao) && !empty($servidor_para_edicao['uf_naturalidade'])): ?>
                (function(){
                    const ufNatServ = <?= (int)$servidor_para_edicao['uf_naturalidade'] ?>;
                    const natSelServ = <?= isset($servidor_para_edicao['naturalidade_id']) ? (int)$servidor_para_edicao['naturalidade_id'] : 'null' ?>;
                    fetch(`../includes/ajax/carregar_municipios.php?estado_id=${ufNatServ}`)
                        .then(r=>r.json())
                        .then(data=>{
                            const $sel = $('#naturalidadeServidor');
                            $sel.empty().append('<option value="">Selecione a cidade...</option>');
                            data.forEach(m=>{
                                const sel = (natSelServ === m.id) ? 'selected' : '';
                                $sel.append(`<option value="${m.id}" ${sel}>${m.nome}</option>`);
                            });
                            $sel.trigger('change');
                        });
                })();
            <?php endif; ?>

            // Mostrar/ocultar necessidades especiais
            document.querySelectorAll('input[name="nee"]').forEach(radio => {
                radio.addEventListener('change', function () {
                    const needsBox = this.closest('.form-section').querySelector('.needs-box');
                    if (this.value === 'sim') {
                        needsBox.style.display = 'block';
                    } else {
                        needsBox.style.display = 'none';
                    }
                });
            });
            // === FUNÇÃO PARA VERIFICAR CAMPOS E ENVIAR PARA VÍNCULOS ===
            function verificarEEnviarParaVinculos(tipo) {
                let formId, idUsuario, camposObrigatorios;

                if (tipo === 'aluno') {
                    formId = 'formAluno';
                    idUsuario = document.querySelector('input[name="id_aluno"]').value;
                    camposObrigatorios = [
                        'nomeCompleto', 'dataNascimento', 'matriculaAluno', 'sexo', 'racaCor',
                        'nacionalidade', 'naturalidade', 'filiacao', 'cpf', 'dataExpedicao',
                        'ufDocumento', 'orgaoExpedidor', 'cep', 'logradouro', 'numero', 'bairro',
                        'ufEndereco', 'municipio', 'celular', 'email'
                    ];
                } else {
                    formId = 'formServidor';
                    idUsuario = document.querySelector('input[name="id_servidor"]').value;
                    camposObrigatorios = [
                        'nomeCompletoServidor', 'dataNascimentoServidor', 'sexoServidor', 'racaCorServidor',
                        'estadoCivilServidor', 'nacionalidadeServidor', 'naturalidadeServidor', 'filiacaoServidor',
                        'cpfServidor', 'rgServidor', 'orgaoExpedidorServidor', 'ufDocumentoServidor',
                        'cepServidor', 'logradouroServidor', 'numeroServidor', 'bairroServidor',
                        'ufEnderecoServidor', 'municipioServidor', 'celularServidor', 'emailServidor',
                        'formacaoAcademica', 'dataAdmissao', 'areaAtuacaoServidor'
                    ];
                }

                // Verificar se todos os campos obrigatórios estão preenchidos
                const form = document.getElementById(formId);
                let camposFaltantes = [];
                let formularioValido = true;

                camposObrigatorios.forEach(campo => {
                    const elemento = form.querySelector(`[name="${campo}"]`);
                    if (elemento) {
                        const valor = elemento.value.trim();
                        if (!valor) {
                            camposFaltantes.push(campo);
                            formularioValido = false;

                            // Destacar campo vazio
                            elemento.style.borderColor = '#e74c3c';
                            elemento.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        } else {
                            elemento.style.borderColor = '';
                        }
                    }
                });

                if (!formularioValido) {
                    const mensagem = `Por favor, preencha todos os campos obrigatórios antes de vincular.\n\nCampos faltantes:\n- ${camposFaltantes.join('\n- ')}`;
                    alert(mensagem);
                    return;
                }

                // Verificar se é um usuário existente (tem ID)
                if (!idUsuario) {
                    alert('Por favor, salve o cadastro primeiro antes de vincular.');
                    return;
                }

                // Se tudo estiver ok, redirecionar para a tela de vínculos
                window.location.href = `gerenciarVinculos.php?tipo=${tipo}&id=${idUsuario}`;
            }

            document.addEventListener('input', function (e) {
                if (e.target.style.borderColor === 'rgb(231, 76, 60)') {
                    e.target.style.borderColor = '';
                }
            });
        });
    </script>


</body>

</html>