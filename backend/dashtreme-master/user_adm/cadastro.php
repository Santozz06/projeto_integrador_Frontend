<?php
require_once '../includes/bootstrap.php';

$erro = '';
$sucesso = '';
$aluno_para_edicao = null;

require_once '../includes/conexao.php';
require_once '../includes/crud/UsuarioCRUD.php';
require_once '../includes/crud/LocalidadeCRUD.php';

$usuarioCRUD = new UsuarioCRUD($pdo);
$localidadeCRUD = new LocalidadeCRUD($pdo);

// Carregar dados para os selects
$estados = $localidadeCRUD->listarEstados();
$paises = $localidadeCRUD->listarPaises();
$orgaos_expedidores = $localidadeCRUD->listarOrgaosExpedidores();

// Se estiver editando, carrega municípios do estado do aluno
$municipios = [];
if (isset($_GET['editarAluno']) && !empty($_GET['editarAluno'])) {
    $id_aluno_edicao = $_GET['editarAluno'];
    $aluno_para_edicao = $usuarioCRUD->buscarAlunoCompleto($id_aluno_edicao);

    if ($aluno_para_edicao && $aluno_para_edicao['estado_id']) {
        $municipios = $localidadeCRUD->listarMunicipiosPorEstado($aluno_para_edicao['estado_id']);
    }
}

// PROCESSAR FORMULÁRIO (Criação e Atualização)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if ($_POST['tipo'] === 'aluno') {
            $email = $_POST['email'];
            $id_aluno = $_POST['id_aluno'] ?? null;

            // Verificar se o email já existe (apenas para novos cadastros)
            if (!$id_aluno) {
                $email_existente = $usuarioCRUD->verificarEmailExistente($email);
                if ($email_existente) {
                    throw new Exception("O email '$email' já está cadastrado no sistema.");
                }
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

            // Se não houver senha no POST, não a inclui nos dados a serem atualizados
            if (!empty($_POST['senha'])) {
                $dadosUsuario['Senha'] = $_POST['senha'];
            }

            $matricula = $_POST['matriculaAluno'];

            // Verifica se é uma atualização ou um novo cadastro
            if ($id_aluno) {
                // Atualização
                $usuarioCRUD->atualizarAluno($id_aluno, $dadosUsuario, $matricula);
                $sucesso = "Aluno atualizado com sucesso!";
            } else {
                // Novo cadastro
                $idAluno = $usuarioCRUD->cadastrarAluno($dadosUsuario, $matricula);
                $sucesso = "Aluno cadastrado com sucesso! Matrícula: " . $matricula;
            }

            // Redireciona para a mesma página para limpar o formulário e mostrar a mensagem
            header("Location: cadastro.php?sucesso=" . urlencode($sucesso));
            exit;

        } elseif ($_POST['tipo'] === 'servidor') {
            $email_servidor = $_POST['emailServidor'];

            // Verificar se o email já existe
            $email_existente = $usuarioCRUD->verificarEmailExistente($email_servidor);
            if ($email_existente) {
                throw new Exception("O email '$email_servidor' já está cadastrado no sistema.");
            }

            $dadosUsuario = [
                'Login' => $email_servidor,
                'Nome_Completo' => $_POST['nomeCompletoServidor'],
                'Email' => $email_servidor,
                'Senha' => $_POST['senha'],
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

            $idProfessor = $usuarioCRUD->cadastrarProfessor(
                $dadosUsuario,
                $_POST['formacaoAcademica'],
                $_POST['dataAdmissao']
            );
            $sucesso = "Professor cadastrado com sucesso!";

            header("Location: cadastro.php?sucesso=" . urlencode($sucesso));
            exit;
        }

    } catch (Exception $e) {
        $erro = "Erro no cadastro: " . $e->getMessage();
        error_log("Erro cadastro: " . $e->getMessage());
    }
}

// Exibe mensagens de sucesso ou erro passadas pela URL
if (isset($_GET['sucesso'])) {
    $sucesso = $_GET['sucesso'];
}
if (isset($_GET['erro'])) {
    $erro = $_GET['erro'];
}

// Listagem de alunos e servidores
$alunos = $usuarioCRUD->listarAlunos();
$servidores = $usuarioCRUD->listarProfessores();
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
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
            background-color: #e9ecef;
            color: #222;
            border: 1px solid #ccc;
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

                                <!-- Deixa o campo senha em branco no modo de edição -->
                                <input type="hidden" name="senha" value="<?= $aluno_para_edicao ? '' : 'senha123' ?>">

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
                                </div>

                                <!-- Nacionalidade -->
                                <div class="form-section">
                                    <h5>Nacionalidade</h5>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Nacionalidade</label>
                                                <select class="form-control" name="nacionalidade" required>
                                                    <option value="" disabled selected>Selecione...</option>
                                                    <option value="Brasileiro(a)"
                                                        <?= ($aluno_para_edicao['Nacionalidade'] ?? '') == 'Brasileiro(a)' ? 'selected' : '' ?>>Brasileiro(a)</option>
                                                    <option value="Argentino(a)" <?= ($aluno_para_edicao['Nacionalidade'] ?? '') == 'Argentino(a)' ? 'selected' : '' ?>>Argentino(a)
                                                    </option>
                                                    <option value="Uruguaio(a)" <?= ($aluno_para_edicao['Nacionalidade'] ?? '') == 'Uruguaio(a)' ? 'selected' : '' ?>>Uruguaio(a)</option>
                                                    <option value="Chileno(a)" <?= ($aluno_para_edicao['Nacionalidade'] ?? '') == 'Chileno(a)' ? 'selected' : '' ?>>Chileno(a)</option>
                                                    <option value="Americano(a)" <?= ($aluno_para_edicao['Nacionalidade'] ?? '') == 'Americano(a)' ? 'selected' : '' ?>>Americano(a)
                                                    </option>
                                                    <option value="Canadense" <?= ($aluno_para_edicao['Nacionalidade'] ?? '') == 'Canadense' ? 'selected' : '' ?>>Canadense</option>
                                                    <option value="Espanhol(a)" <?= ($aluno_para_edicao['Nacionalidade'] ?? '') == 'Espanhol(a)' ? 'selected' : '' ?>>Espanhol(a)</option>
                                                    <option value="Português(a)" <?= ($aluno_para_edicao['Nacionalidade'] ?? '') == 'Português(a)' ? 'selected' : '' ?>>Português(a)
                                                    </option>
                                                    <option value="Italiano(a)" <?= ($aluno_para_edicao['Nacionalidade'] ?? '') == 'Italiano(a)' ? 'selected' : '' ?>>Italiano(a)</option>
                                                    <option value="Alemão(ã)" <?= ($aluno_para_edicao['Nacionalidade'] ?? '') == 'Alemão(ã)' ? 'selected' : '' ?>>Alemão(ã)</option>
                                                    <option value="Francês(a)" <?= ($aluno_para_edicao['Nacionalidade'] ?? '') == 'Francês(a)' ? 'selected' : '' ?>>Francês(a)</option>
                                                    <option value="Japonês(a)" <?= ($aluno_para_edicao['Nacionalidade'] ?? '') == 'Japonês(a)' ? 'selected' : '' ?>>Japonês(a)</option>
                                                    <option value="Chinês(a)" <?= ($aluno_para_edicao['Nacionalidade'] ?? '') == 'Chinês(a)' ? 'selected' : '' ?>>Chinês(a)</option>
                                                    <option value="Outra" <?= ($aluno_para_edicao['Nacionalidade'] ?? '') == 'Outra' ? 'selected' : '' ?>>Outra nacionalidade</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Naturalidade</label>
                                                <input type="text" class="form-control" name="naturalidade" required
                                                    value="<?= htmlspecialchars($aluno_para_edicao['Naturalidade'] ?? '') ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
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
                                                <select class="form-control" name="ufDocumento" required>
                                                    <option value="">Selecione...</option>
                                                    <option <?= ($aluno_para_edicao['UF_Exp'] ?? '') == 'SP' ? 'selected' : '' ?>>SP</option>
                                                    <!-- outros estados -->
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Órgão Expedidor</label>
                                                <input type="text" class="form-control" name="orgaoExpedidor"
                                                    placeholder="SSP" required
                                                    value="<?= htmlspecialchars($aluno_para_edicao['Orgao_Exp'] ?? '') ?>">
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
                                                    placeholder="00000-000" required
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
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Município</label>
                                                <input type="text" class="form-control" name="municipio" required
                                                    value="<?= htmlspecialchars($aluno_para_edicao['Municipio'] ?? '') ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>UF</label>
                                                <select class="form-control" name="ufEndereco" required>
                                                    <option value="">Selecione...</option>
                                                    <option <?= ($aluno_para_edicao['UF_Endereco'] ?? '') == 'SP' ? 'selected' : '' ?>>SP</option>
                                                    <!-- outros estados -->
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
                        </div>

                       <div class="tab-pane fade" id="servidor" role="tabpanel">
                            <form id="formServidor" method="POST" action="cadastro.php" novalidate>
                                <input type="hidden" name="tipo" value="servidor">

                                <!-- Dados Pessoais -->
                                <div class="form-section">
                                    <h5>Dados Pessoais</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Nome Completo</label>
                                                <input type="text" class="form-control" name="nomeCompletoServidor"
                                                    required>
                                                <div class="invalid-feedback">Por favor, informe o nome completo
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Data de Nascimento</label>
                                                <input type="date" class="form-control" name="dataNascimentoServidor"
                                                    required>
                                                <div class="invalid-feedback">Por favor, informe a data de
                                                    nascimento</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Sexo</label>
                                                <select class="form-control" name="sexoServidor" required>
                                                    <option value="">Selecione...</option>
                                                    <option>Masculino</option>
                                                    <option>Feminino</option>
                                                    <option>Outro</option>
                                                    <option>Prefiro não informar</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Raça/Cor</label>
                                                <select class="form-control" name="racaCorServidor" required>
                                                    <option value="">Selecione...</option>
                                                    <option>Branca</option>
                                                    <option>Preta</option>
                                                    <option>Parda</option>
                                                    <option>Amarela</option>
                                                    <option>Indígena</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Estado Civil</label>
                                                <select class="form-control" name="estadoCivilServidor" required>
                                                    <option value="">Selecione...</option>
                                                    <option>Solteiro(a)</option>
                                                    <option>Casado(a)</option>
                                                    <option>Divorciado(a)</option>
                                                    <option>Viúvo(a)</option>
                                                </select>
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
                                                <select class="form-control" name="nacionalidadeServidor" required>
                                                    <option value="">Selecione...</option>
                                                    <option>Brasileiro(a)</option>
                                                    <option>Argentino(a)</option>
                                                    <option>Uruguaio(a)</option>
                                                    <option>Chileno(a)</option>
                                                    <option>Americano(a)</option>
                                                    <option>Canadense</option>
                                                    <option>Outra</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Naturalidade</label>
                                                <input type="text" class="form-control" name="naturalidadeServidor" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Filiação</label>
                                                <input type="text" class="form-control" name="filiacaoServidor" required>
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
                                                <input type="text" class="form-control" name="cpfServidor" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>RG</label>
                                                <input type="text" class="form-control" name="rgServidor" required>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Órgão Expedidor</label>
                                                <input type="text" class="form-control" name="orgaoExpedidorServidor" required>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>UF</label>
                                                <select class="form-control" name="ufDocumentoServidor" required>
                                                    <option value="">Selecione...</option>
                                                    <option>SP</option>
                                                    <option>RJ</option>
                                                    <option>MG</option>
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
                                                <input type="text" class="form-control" name="cepServidor" required>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label>Logradouro</label>
                                                <input type="text" class="form-control" name="logradouroServidor" required>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group">
                                                <label>Nº</label>
                                                <input type="text" class="form-control" name="numeroServidor" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Complemento</label>
                                                <input type="text" class="form-control" name="complementoServidor">
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
                                                <input type="text" class="form-control" name="telefoneServidor">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Celular</label>
                                                <input type="text" class="form-control" name="celularServidor" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>E-mail</label>
                                                <input type="email" class="form-control" name="emailServidor" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Dados Profissionais -->
                                <div class="form-section">
                                    <h5>Dados Profissionais</h5>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Cargo/Função</label>
                                                <input type="text" class="form-control" name="cargoFuncaoServidor" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Matrícula</label>
                                                <input type="text" class="form-control" name="matriculaServidor" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Data de Admissão</label>
                                                <input type="date" class="form-control" name="dataAdmissaoServidor" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Formação Acadêmica</label>
                                                <select class="form-control" name="formacaoAcademicaServidor" required>
                                                    <option value="">Selecione...</option>
                                                    <option>Graduação Completa</option>
                                                    <option>Pós-Graduação</option>
                                                    <option>Mestrado</option>
                                                    <option>Doutorado</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Área de Atuação</label>
                                                <input type="text" class="form-control" name="areaAtuacaoServidor" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row mt-3">
                                    <div class="col-sm-12 text-right">
                                        <button type="submit" class="btn btn-Salvar px-5">Salvar</button>
                                        <button type="button" class="btn btn-cancelar px-5"
                                            id="btnCancelarServidor">Cancelar</button>
                                    </div>
                                </div>
                            </form>
                        </div> <!-- Fim da aba Servidor -->
                    </div> <!-- Fim do tab-content -->
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

    <script>
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

        // Limpar formulários
        document.getElementById('btnCancelarAluno')?.addEventListener('click', function () {
            document.getElementById('formAluno').reset();
        });
    </script>

</body>

</html>