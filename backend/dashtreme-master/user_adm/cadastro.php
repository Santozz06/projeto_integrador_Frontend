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

// dados para selects
$estados = $localidadeCRUD->listarEstados();
$paises = $localidadeCRUD->listarPaises();
$orgaos_expedidores = $localidadeCRUD->listarOrgaosExpedidores();

// excluir aluno/servidor
try {
    if (isset($_GET['excluirAluno']) && ctype_digit($_GET['excluirAluno'])) {
        $id = (int) $_GET['excluirAluno'];
    // checa se existe aluno
        $aluno = $usuarioCRUD->buscarAlunoCompleto($id);
        if ($aluno) {
            // marca como inativo
            $usuarioCRUD->excluir($id);
            header('Location: cadastro.php?sucesso=' . urlencode('Aluno excluído com sucesso.'));
            exit;
        } else {
            header('Location: cadastro.php?erro=' . urlencode('Aluno não encontrado.'));
            exit;
        }
    }

    if (isset($_GET['excluirServidor']) && ctype_digit($_GET['excluirServidor'])) {
        $id = (int) $_GET['excluirServidor'];
    // checa se existe servidor
        $prof = $usuarioCRUD->buscarProfessorCompleto($id);
        if ($prof) {
            // marca como inativo
            $usuarioCRUD->excluir($id);
            header('Location: cadastro.php?sucesso=' . urlencode('Servidor excluído com sucesso.'));
            exit;
        } else {
            header('Location: cadastro.php?erro=' . urlencode('Servidor não encontrado.'));
            exit;
        }
    }
} catch (Exception $e) {
    error_log('Erro ao excluir: ' . $e->getMessage());
    header('Location: cadastro.php?erro=' . urlencode('Erro ao excluir: ' . $e->getMessage()));
    exit;
}

// editar aluno
$municipios_aluno = [];
if (isset($_GET['editarAluno']) && !empty($_GET['editarAluno'])) {
    $id_aluno_edicao = $_GET['editarAluno'];
    $aluno_para_edicao = $usuarioCRUD->buscarAlunoCompleto($id_aluno_edicao);

    // municipios pelo UF_Endereco
    if ($aluno_para_edicao && isset($aluno_para_edicao['UF_Endereco']) && $aluno_para_edicao['UF_Endereco']) {
        $municipios_aluno = $localidadeCRUD->listarMunicipiosPorEstado($aluno_para_edicao['UF_Endereco']);
    }

    // derivar logradouro se faltar
    if ($aluno_para_edicao && (!isset($aluno_para_edicao['Logradouro']) || $aluno_para_edicao['Logradouro'] === '') && !empty($aluno_para_edicao['Endereco'])) {
        $partesEndereco = explode(',', (string)$aluno_para_edicao['Endereco']);
        $aluno_para_edicao['Logradouro'] = trim($partesEndereco[0]);
    }
}

// editar servidor
$municipios_servidor = [];
if (isset($_GET['editarServidor']) && !empty($_GET['editarServidor'])) {
    $id_servidor_edicao = $_GET['editarServidor'];
    $servidor_para_edicao = $usuarioCRUD->buscarProfessorCompleto($id_servidor_edicao);

    // municipios pelo UF_Endereco
    if ($servidor_para_edicao && isset($servidor_para_edicao['UF_Endereco']) && $servidor_para_edicao['UF_Endereco']) {
        $municipios_servidor = $localidadeCRUD->listarMunicipiosPorEstado($servidor_para_edicao['UF_Endereco']);
    }

    // derivar logradouro se faltar
    if ($servidor_para_edicao && (!isset($servidor_para_edicao['Logradouro']) || $servidor_para_edicao['Logradouro'] === '') && !empty($servidor_para_edicao['Endereco'])) {
        $partesEndereco = explode(',', (string)$servidor_para_edicao['Endereco']);
        $servidor_para_edicao['Logradouro'] = trim($partesEndereco[0]);
    }
}

// processa formulário
// Funções auxiliares para AJAX (renderização das linhas de tabela)
function renderLinhasAlunos(array $alunos, $editId = null): string {
    $html = '';
    foreach ($alunos as $al) {
        $highlight = ($editId && (int)$editId === (int)$al['ID_Usuario']) ? ' class="table-success"' : '';
        $html .= '<tr'.$highlight.'>'
            .'<td>'.htmlspecialchars($al['Nome_Completo']).'</td>'
            .'<td>'.htmlspecialchars($al['Email']).'</td>'
            .'<td>'.htmlspecialchars($al['Matricula'] ?? 'N/A').'</td>'
            .'<td>'.htmlspecialchars($al['Telefone'] ?? 'N/A').'</td>'
            .'<td>'
            .'<a href="?editarAluno='.$al['ID_Usuario'].'" class="btn btn-sm btn-primary">Editar</a> '
            .'<a href="?excluirAluno='.$al['ID_Usuario'].'" class="btn btn-sm btn-danger" onclick="return confirm(\'Deseja realmente excluir este aluno?\');">Excluir</a>'
            .'</td>'
            .'</tr>';
    }
    return $html;
}
function renderLinhasServidores(array $servidores, $editId = null): string {
    $html='';
    foreach ($servidores as $s) {
        $highlight = ($editId && (int)$editId === (int)$s['ID_Usuario']) ? ' class="table-success"' : '';
        $html .= '<tr'.$highlight.'>'
            .'<td>'.htmlspecialchars($s['Nome_Completo']).'</td>'
            .'<td>'.htmlspecialchars($s['Email']).'</td>'
            .'<td>'.htmlspecialchars($s['Formacao_Academica'] ?? 'N/A').'</td>'
            .'<td>'.htmlspecialchars($s['Matricula'] ?? 'N/A').'</td>'
            .'<td>'.htmlspecialchars($s['Telefone'] ?? 'N/A').'</td>'
            .'<td>'
            .'<a href="?editarServidor='.$s['ID_Usuario'].'" class="btn btn-sm btn-primary">Editar</a> '
            .'<a href="?excluirServidor='.$s['ID_Usuario'].'" class="btn btn-sm btn-danger" onclick="return confirm(\'Deseja realmente excluir este servidor?\');">Excluir</a>'
            .'</td>'
            .'</tr>';
    }
    return $html;
}

$isAjax = ($_SERVER['REQUEST_METHOD'] === 'POST') && isset($_POST['ajax']) && $_POST['ajax'] === '1';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if ($_POST['tipo'] === 'aluno') {
            $email = $_POST['email'];
            $id_aluno = $_POST['id_aluno'] ?? null;

            // email duplicado?
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
                // telefones
                'Telefone' => $_POST['telefone'] ?? '',
                'Telefone_Fixo' => $_POST['telefone'] ?? '',
                'Celular' => $_POST['celular'] ?? '',
                'CEP' => $_POST['cep'] ?? '',
                // endereco completo
                'Endereco' => $_POST['logradouro'] . ', ' . $_POST['numero'] . ' - ' . $_POST['bairro'],
                'Logradouro' => $_POST['logradouro'] ?? '',
                'Numero' => $_POST['numero'] ?? '',
                'Complemento' => $_POST['complemento'] ?? '',
                'Bairro' => $_POST['bairro'] ?? '',
                'UF_Endereco' => $_POST['ufEndereco'] ?? null,
                'Municipio_Endereco' => $_POST['municipio'] ?? null,
                'Data_Expedicao' => $_POST['dataExpedicao'] ?? null,
                'Possui_Necessidades_Especiais' => (isset($_POST['nee']) && $_POST['nee'] === 'sim') ? 1 : 0
            ];

            // nacionalidade / naturalidade / filiacao
            if (isset($_POST['nacionalidade']) && $_POST['nacionalidade'] !== '') {
                $dadosUsuario['Nacionalidade'] = $_POST['nacionalidade'];
            }

            // filiacao
            if (isset($_POST['filiacao']) && trim($_POST['filiacao']) !== '') {
                $dadosUsuario['Filiacao'] = trim($_POST['filiacao']);
            }

            // naturalidade
            if (!empty($_POST['naturalidade'])) {
                $municipioId = $_POST['naturalidade'];
                $municipio = $localidadeCRUD->buscarMunicipio($municipioId);
                $naturalidadeTexto = $municipio && !empty($municipio['nome']) ? $municipio['nome'] : '';

                // busca sigla UF
                $ufSigla = '';
                if (!empty($_POST['ufNaturalidade'])) {
                    try {
                        $stmtUf = $pdo->prepare("SELECT uf FROM estados WHERE codigo_uf = ?");
                        $stmtUf->execute([$_POST['ufNaturalidade']]);
                        $rowUf = $stmtUf->fetch(PDO::FETCH_ASSOC);
                        if ($rowUf && !empty($rowUf['uf'])) {
                            $ufSigla = $rowUf['uf'];
                        }
                    } catch (Exception $e) {
                    }
                }

                if ($naturalidadeTexto !== '') {
                    $dadosUsuario['Naturalidade'] = $ufSigla ? ($naturalidadeTexto . '/' . $ufSigla) : $naturalidadeTexto;
                }
            }

            // senha aluno
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

            // atualização ou novo
            if ($id_aluno) {
                // update
                $usuarioCRUD->atualizarAluno($id_aluno, $dadosUsuario, $matricula);
                // salva NEE
                $usuarioCRUD->salvarNeeAluno((int)$id_aluno, [
                    'possui' => (isset($_POST['nee']) && $_POST['nee'] === 'sim'),
                    'aee' => $_POST['aee'] ?? 0,
                    'salaAee' => $_POST['salaAee'] ?? 0,
                    'monitor' => $_POST['monitor'] ?? 0,
                    'interprete' => $_POST['interprete'] ?? 0,
                    'materialAdaptado' => $_POST['materialAdaptado'] ?? 0,
                    'tecnologiaAssistiva' => $_POST['tecnologiaAssistiva'] ?? 0,
                    'outras' => $_POST['outrasNecessidades'] ?? ''
                ]);
                $sucesso = "Aluno atualizado com sucesso!";
                if ($isAjax) {
                    // Recarrega listagem
                    $listaAlunos = $usuarioCRUD->listarAlunos(1, $limite_por_pagina);
                    // Ao salvar, não manter modo edição: não enviar id para destaque
                    echo json_encode([
                        'success' => true,
                        'tipo' => 'aluno',
                        'id' => (int)$id_aluno, // ainda retornado se precisar em futuro, mas não usado para highlight
                        'mensagem' => $sucesso,
                        'tabela' => renderLinhasAlunos($listaAlunos, null)
                    ]);
                    exit;
                }
                header("Location: cadastro.php?editarAluno=" . $id_aluno . "&sucesso=" . urlencode($sucesso));
                exit;
            } else {
                // create
                $idAluno = $usuarioCRUD->cadastrarAluno($dadosUsuario, $matricula);
                // salva NEE
                $usuarioCRUD->salvarNeeAluno((int)$idAluno, [
                    'possui' => (isset($_POST['nee']) && $_POST['nee'] === 'sim'),
                    'aee' => $_POST['aee'] ?? 0,
                    'salaAee' => $_POST['salaAee'] ?? 0,
                    'monitor' => $_POST['monitor'] ?? 0,
                    'interprete' => $_POST['interprete'] ?? 0,
                    'materialAdaptado' => $_POST['materialAdaptado'] ?? 0,
                    'tecnologiaAssistiva' => $_POST['tecnologiaAssistiva'] ?? 0,
                    'outras' => $_POST['outrasNecessidades'] ?? ''
                ]);
                $sucesso = "Aluno cadastrado com sucesso! Matrícula: " . $matricula;
                if ($isAjax) {
                    $listaAlunos = $usuarioCRUD->listarAlunos(1, $limite_por_pagina);
                    // Após cadastro novo não destacar linha (sai do modo edição imediatamente)
                    echo json_encode([
                        'success' => true,
                        'tipo' => 'aluno',
                        'id' => (int)$idAluno,
                        'mensagem' => $sucesso,
                        'tabela' => renderLinhasAlunos($listaAlunos, null)
                    ]);
                    exit;
                }
                header("Location: cadastro.php?editarAluno=" . $idAluno . "&sucesso=" . urlencode($sucesso));
                exit;
            }


        } elseif ($_POST['tipo'] === 'servidor') {
            $email_servidor = $_POST['emailServidor'];
            $id_servidor = $_POST['id_servidor'] ?? null;

            // email duplicado?
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
                'RG' => $_POST['rgServidor'] ?? '',
                'Raca_Etnia' => $_POST['racaCorServidor'],
                'Estado_Civil' => $_POST['estadoCivilServidor'],
                'Nacionalidade' => $_POST['nacionalidadeServidor'],
                'Filiacao' => $_POST['filiacaoServidor'],
                'Orgao_Exp' => $_POST['orgaoExpedidorServidor'],
                'UF_Exp' => $_POST['ufDocumentoServidor'],
                'Telefone' => $_POST['telefoneServidor'] ?? '',
                'Telefone_Fixo' => $_POST['telefoneServidor'] ?? '',
                'Celular' => $_POST['celularServidor'] ?? '',
                'CEP' => $_POST['cepServidor'] ?? '',
                'Endereco' => $_POST['logradouroServidor'] . ', ' . $_POST['numeroServidor'] . ' - ' . $_POST['bairroServidor'],
                'Logradouro' => $_POST['logradouroServidor'] ?? '',
                'Numero' => $_POST['numeroServidor'] ?? '',
                'Complemento' => $_POST['complementoServidor'] ?? '',
                'Bairro' => $_POST['bairroServidor'] ?? '',
                'UF_Endereco' => $_POST['ufEnderecoServidor'] ?? null,
                'Municipio_Endereco' => $_POST['municipioServidor'] ?? null
            ];

            // Naturalidade (Servidor): converte selecionados em texto "Cidade/UF"
            if (!empty($_POST['naturalidadeServidor'])) {
                $municipioId = $_POST['naturalidadeServidor'];
                $municipio = $localidadeCRUD->buscarMunicipio($municipioId);
                $naturalidadeTexto = $municipio && !empty($municipio['nome']) ? $municipio['nome'] : '';

                $ufSigla = '';
                if (!empty($_POST['ufNaturalidadeServidor'])) {
                    try {
                        $stmtUf = $pdo->prepare("SELECT uf FROM estados WHERE codigo_uf = ?");
                        $stmtUf->execute([$_POST['ufNaturalidadeServidor']]);
                        $rowUf = $stmtUf->fetch(PDO::FETCH_ASSOC);
                        if ($rowUf && !empty($rowUf['uf'])) {
                            $ufSigla = $rowUf['uf'];
                        }
                    } catch (Exception $e) {
                    }
                }

                if ($naturalidadeTexto !== '') {
                    $dadosUsuario['Naturalidade'] = $ufSigla ? ($naturalidadeTexto . '/' . $ufSigla) : $naturalidadeTexto;
                }
            }

            // manter naturalidade se não vier
            if (!isset($dadosUsuario['Naturalidade'])) {
                $dadosUsuario['Naturalidade'] = $servidor_para_edicao['Naturalidade'] ?? '';
            }

            // senha servidor
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
                    $dadosUsuario['Senha'] = $senhaServidor;
                }
            }

            if ($id_servidor) {
                // update
                $usuarioCRUD->atualizarProfessor(
                    $id_servidor,
                    $dadosUsuario,
                    $_POST['formacaoAcademica'],
                    $_POST['dataAdmissao'],
                    null,
                    $_POST['matriculaServidor'] ?? null
                );
                $sucesso = "Servidor atualizado com sucesso!";
                if ($isAjax) {
                    $listaServ = $usuarioCRUD->listarProfessores(1, $limite_por_pagina);
                    // Não destacar linha após salvar atualização
                    echo json_encode([
                        'success' => true,
                        'tipo' => 'servidor',
                        'id' => (int)$id_servidor,
                        'mensagem' => $sucesso,
                        'tabela' => renderLinhasServidores($listaServ, null)
                    ]);
                    exit;
                }
                header("Location: cadastro.php?editarServidor=" . $id_servidor . "&sucesso=" . urlencode($sucesso));
                exit;
            } else {
                // create
                $idProfessor = $usuarioCRUD->cadastrarProfessor(
                    $dadosUsuario,
                    $_POST['formacaoAcademica'],
                    $_POST['dataAdmissao'],
                    null,
                    $_POST['matriculaServidor'] ?? null
                );
                $sucesso = "Servidor cadastrado com sucesso!";
                if ($isAjax) {
                    $listaServ = $usuarioCRUD->listarProfessores(1, $limite_por_pagina);
                    // Não destacar linha após cadastro
                    echo json_encode([
                        'success' => true,
                        'tipo' => 'servidor',
                        'id' => (int)$idProfessor,
                        'mensagem' => $sucesso,
                        'tabela' => renderLinhasServidores($listaServ, null)
                    ]);
                    exit;
                }
                header("Location: cadastro.php?editarServidor=" . $idProfessor . "&sucesso=" . urlencode($sucesso));
                exit;
            }
        }

    } catch (Exception $e) {
        $msg = $e->getMessage();
        $friendly = '';
        // Tratamento para duplicidades 
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
        if ($isAjax) {
            echo json_encode([
                'success' => false,
                'mensagem' => $erro
            ]);
            exit;
        }
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


// Determinar aba ativa (padrao aluno, muda se edição/parametros de servidor presentes)
$abaAtiva = 'aluno';
if (isset($_GET['editarServidor']) || isset($_GET['pagina_servidores']) || (isset($_POST['tipo']) && $_POST['tipo']==='servidor')) {
    $abaAtiva = 'servidor';
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Cadastro - SAS" />
    <meta name="author" content="" />
    <title>Cadastro - SAS</title>
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
    <link href="../css/style.css" rel="stylesheet" />

    <!-- Select2 CSS -->
    <link href="../assets/plugins/select2/css/select2.min.css" rel="stylesheet" />
    <link href="../assets/plugins/select2/css/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    
</head>

<body class="bg-theme bg-theme1 user_adm_cadastro">

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
                            <a class="nav-link <?= $abaAtiva==='aluno' ? 'active' : '' ?>" data-toggle="tab" href="#aluno" role="tab">
                                <i class="zmdi zmdi-accounts-alt mr-1"></i> Aluno
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $abaAtiva==='servidor' ? 'active' : '' ?>" data-toggle="tab" href="#servidor" role="tab">
                                <i class="zmdi zmdi-account-box mr-1"></i> Servidor
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content pt-3">
                        <!-- Aba Aluno -->
                        <div class="tab-pane fade show <?= $abaAtiva==='aluno' ? 'active' : '' ?>" id="aluno" role="tabpanel">
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
                                                <small class="form-text text-white">Obrigatória no cadastro. Em edição, preencha para alterar.</small>
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
                                                <input type="text" class="form-control" name="cpf" id="cpf"
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
                                                <input type="text" class="form-control" name="cep" id="cep"
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
                                                <input type="text" class="form-control" name="telefone" id="telefone"
                                                    placeholder="(00) 0000-0000"
                                                    value="<?= htmlspecialchars($aluno_para_edicao['Telefone'] ?? '') ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Celular</label>
                                                <input type="text" class="form-control" name="celular" id="celular"
                                                    placeholder="(00) 00000-0000" required
                                                    value="<?= htmlspecialchars($aluno_para_edicao['Celular'] ?? '') ?>">
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
                                        <?php if (!empty($aluno_para_edicao['ID_Usuario'])): ?>
                                        <button type="button" class="btn btn-info px-5" id="btnVincularAluno"
                                            onclick="verificarEEnviarParaVinculos('aluno')">Vincular</button>
                                        <?php endif; ?>
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
                                        <tr class="<?= (isset($aluno_para_edicao['ID_Usuario']) && $aluno_para_edicao['ID_Usuario']==$aluno['ID_Usuario']) ? 'table-success' : '' ?>">
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
                        <div class="tab-pane fade show <?= $abaAtiva==='servidor' ? 'active' : '' ?>" id="servidor" role="tabpanel">
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
                                                <input type="text" class="form-control" name="matriculaServidor" required
                                                    value="<?= htmlspecialchars($servidor_para_edicao['Matricula'] ?? '') ?>">
                                                <small class="form-text text-white">Obrigatória. Utilize o padrão interno da instituição.</small>
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
                                                <small class="form-text text-white">Obrigatória no cadastro. Em edição, preencha para alterar.</small>
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
                                                <input type="text" class="form-control" name="cpfServidor" id="cpfServidor" required
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
                                                <input type="text" class="form-control" name="cepServidor" id="cepServidor"
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
                                                            <?= ($servidor_para_edicao['UF_Endereco'] ?? '') == $estado['id'] ? 'selected' : '' ?>>
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
                                                                <?= ($servidor_para_edicao['Municipio_Endereco'] ?? '') == $municipio['id'] ? 'selected' : '' ?>>
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
                                                <input type="text" class="form-control" name="telefoneServidor" id="telefoneServidor"
                                                    value="<?= htmlspecialchars($servidor_para_edicao['Telefone'] ?? '') ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Celular</label>
                                                <input type="text" class="form-control" name="celularServidor" id="celularServidor" required
                                                    value="<?= htmlspecialchars($servidor_para_edicao['Celular'] ?? '') ?>">
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
                                    
                                </div>
                                <!-- Botões -->
                                <div class="form-group row mt-3">
                                    <div class="col-sm-12 text-right">
                                        <button type="submit" class="btn btn-Salvar px-5">Salvar</button>
                                        <?php if (!empty($servidor_para_edicao['ID_Usuario'])): ?>
                                        <button type="button" class="btn btn-info px-5" id="btnVincularServidor"
                                            onclick="verificarEEnviarParaVinculos('servidor')">Vincular</button>
                                        <?php endif; ?>
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
                                        <tr class="<?= (isset($servidor_para_edicao['ID_Usuario']) && $servidor_para_edicao['ID_Usuario']==$servidor['ID_Usuario']) ? 'table-success' : '' ?>">
                                            <td><?= htmlspecialchars($servidor['Nome_Completo']) ?></td>
                                            <td><?= htmlspecialchars($servidor['Email']) ?></td>
                                            <td><?= htmlspecialchars($servidor['Formacao_Academica'] ?? 'N/A') ?></td>
                                            <td><?= htmlspecialchars($servidor['Matricula'] ?? '—') ?></td>
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
    <!-- loader scripts removed: jquery.loading-indicator.js not present -->
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

            // Carrega mapeamento de UFs (sigla -> codigo_uf) via endpoint existente
            let ufMapPromise = fetch('../includes/ajax/listar_estados.php')
                .then(r => r.json())
                .then(j => {
                    if (!j || !j.success) throw new Error('Falha ao listar estados');
                    const map = {};
                    (j.data || []).forEach(e => { if (e.uf) map[e.uf] = e.id; });
                    return map;
                })
                .catch(err => {
                    console.error('Erro ao carregar estados:', err);
                    return {};
                });

            // Consulta ViaCEP e preenche campos
            async function preencherEnderecoPorCEP(cep, contexto) {
                const somenteDigitos = (cep || '').replace(/\D/g, '');
                if (somenteDigitos.length !== 8) return; // CEP inválido/incompleto
                try {
                    const resp = await fetch(`https://viacep.com.br/ws/${somenteDigitos}/json/`);
                    if (!resp.ok) throw new Error('Resposta inválida do ViaCEP');
                    const data = await resp.json();
                    if (data.erro) throw new Error('CEP não encontrado');

                    // Preenche logradouro e bairro quando disponíveis
                    if (contexto.$logradouro && data.logradouro) contexto.$logradouro.val(data.logradouro);
                    if (contexto.$bairro && data.bairro) contexto.$bairro.val(data.bairro);
                    if (contexto.$complemento && data.complemento) contexto.$complemento.val(data.complemento);

                    // Seleciona UF no select (mapeando sigla -> codigo_uf)
                    const ufMap = await ufMapPromise;
                    const codigoUF = ufMap[data.uf] || null;
                    if (codigoUF && contexto.$uf) {
                        contexto.$uf.val(String(codigoUF)).trigger('change');

                        // Após carregar municípios (assíncrono), selecionar pelo IBGE
                        const tentarSelecionarMunicipio = () => {
                            if (!contexto.$municipio) return;
                            // Aguarda options carregarem
                            setTimeout(() => {
                                if (data.ibge) {
                                    contexto.$municipio.val(String(data.ibge)).trigger('change');
                                } else if (data.localidade) {
                                    // fallback por nome se ibge indisponível
                                    const nomeCidade = (data.localidade || '').toLowerCase();
                                    let escolhido = null;
                                    contexto.$municipio.find('option').each(function(){
                                        if (($(this).text() || '').toLowerCase() === nomeCidade) {
                                            escolhido = $(this).val();
                                        }
                                    });
                                    if (escolhido) contexto.$municipio.val(escolhido).trigger('change');
                                }
                            }, 400);
                        };
                        tentarSelecionarMunicipio();
                    }
                } catch (e) {
                    console.warn('Não foi possível preencher endereço pelo CEP:', e.message || e);
                }
            }

            // Amarra CEP -> auto-preenchimento (Aluno)
            $('#cep').on('blur', function(){
                preencherEnderecoPorCEP(this.value, {
                    $uf: $('#ufEndereco'),
                    $municipio: $('#municipio'),
                    $logradouro: $('input[name="logradouro"]'),
                    $bairro: $('input[name="bairro"]'),
                    $complemento: $('input[name="complemento"]')
                });
            });
            // Também quando completar 9 chars (com hífen) durante digitação
            $('#cep').on('input', function(){
                const raw = this.value.replace(/\D/g, '');
                if (raw.length === 8) {
                    preencherEnderecoPorCEP(this.value, {
                        $uf: $('#ufEndereco'),
                        $municipio: $('#municipio'),
                        $logradouro: $('input[name="logradouro"]'),
                        $bairro: $('input[name="bairro"]'),
                        $complemento: $('input[name="complemento"]')
                    });
                }
            });

            // Amarra CEP -> auto-preenchimento (Servidor)
            $('#cepServidor').on('blur', function(){
                preencherEnderecoPorCEP(this.value, {
                    $uf: $('#ufEnderecoServidor'),
                    $municipio: $('#municipioServidor'),
                    $logradouro: $('input[name="logradouroServidor"]'),
                    $bairro: $('input[name="bairroServidor"]'),
                    $complemento: $('input[name="complementoServidor"]')
                });
            });
            $('#cepServidor').on('input', function(){
                const raw = this.value.replace(/\D/g, '');
                if (raw.length === 8) {
                    preencherEnderecoPorCEP(this.value, {
                        $uf: $('#ufEnderecoServidor'),
                        $municipio: $('#municipioServidor'),
                        $logradouro: $('input[name="logradouroServidor"]'),
                        $bairro: $('input[name="bairroServidor"]'),
                        $complemento: $('input[name="complementoServidor"]')
                    });
                }
            });

            // === CPF: máscara e validação ===
            function aplicarMascaraCPF(input) {
                let v = (input.value || '').replace(/\D/g, '');
                if (v.length > 11) v = v.substring(0,11);
                if (v.length > 9) input.value = v.replace(/(\d{3})(\d{3})(\d{3})(\d{0,2})/, '$1.$2.$3-$4');
                else if (v.length > 6) input.value = v.replace(/(\d{3})(\d{3})(\d{0,3})/, '$1.$2.$3');
                else if (v.length > 3) input.value = v.replace(/(\d{3})(\d{0,3})/, '$1.$2');
                else input.value = v;
            }

            function validarCPFValor(cpfStr) {
                const cpf = (cpfStr || '').replace(/\D/g, '');
                if (!cpf || cpf.length !== 11) return false;
                if (/^(\d)\1{10}$/.test(cpf)) return false; // repetidos
                let soma = 0;
                for (let i = 0; i < 9; i++) soma += parseInt(cpf.charAt(i)) * (10 - i);
                let resto = (soma * 10) % 11;
                if (resto === 10 || resto === 11) resto = 0;
                if (resto !== parseInt(cpf.charAt(9))) return false;
                soma = 0;
                for (let i = 0; i < 10; i++) soma += parseInt(cpf.charAt(i)) * (11 - i);
                resto = (soma * 10) % 11;
                if (resto === 10 || resto === 11) resto = 0;
                if (resto !== parseInt(cpf.charAt(10))) return false;
                return true;
            }

            // Liga máscaras CPF e remove estado inválido durante digitação
            $('#cpf, #cpfServidor').on('input', function(){
                aplicarMascaraCPF(this);
                $(this).removeClass('is-invalid');
            });

            // Valida CPF ao sair do campo (sem alert, só marca visualmente)
            $('#cpf').on('blur', function(){
                const v = (this.value || '').trim();
                if (!v) { $(this).removeClass('is-invalid'); return; }
                const ok = validarCPFValor(v);
                $(this).toggleClass('is-invalid', !ok);
            });
            $('#cpfServidor').on('blur', function(){
                const v = (this.value || '').trim();
                if (!v) { $(this).removeClass('is-invalid'); return; }
                const ok = validarCPFValor(v);
                $(this).toggleClass('is-invalid', !ok);
            });

            // === Máscaras de Telefone/Celular (BR) ===
            function aplicarMascaraTelefone(input) {
                // Mantém apenas dígitos
                let val = (input.value || '').replace(/\D/g, '');
                // Limita a 11 dígitos (DDD + número)
                if (val.length > 11) val = val.substring(0, 11);

                // Monta a máscara progressivamente
                if (val.length > 0) {
                    // DDD
                    if (val.length <= 2) {
                        val = '(' + val;
                    } else {
                        val = '(' + val.substring(0, 2) + ') ' + val.substring(2);
                    }

                    // Hífen conforme 10 (fixo) ou 11 (celular) dígitos
                    const digitos = input.value.replace(/\D/g, '');
                    const total = digitos.length;
                    // Posição do hífen: 10 dígitos => 4-4 | 11 dígitos => 5-4
                    if (total >= 7) {
                        // Remove tudo que não é dígito para recálculo
                        const soDigitos = val.replace(/\D/g, '');
                        const ddd = soDigitos.substring(0, 2);
                        const restante = soDigitos.substring(2);
                        if (total === 11) {
                            // (00) 00000-0000
                            const parte1 = restante.substring(0, 5);
                            const parte2 = restante.substring(5);
                            val = `(${ddd}) ${parte1}` + (parte2 ? `-${parte2}` : '');
                        } else {
                            // (00) 0000-0000 (ou montagem parcial)
                            const parte1 = restante.substring(0, 4);
                            const parte2 = restante.substring(4);
                            val = `(${ddd}) ${parte1}` + (parte2 ? `-${parte2}` : '');
                        }
                    }
                }

                input.value = val;
            }

            function validarTelefoneCampo($input, obrigatorioCelular = false) {
                const raw = ($input.val() || '').replace(/\D/g, '');
                if (!raw) return true; // vazio permitido em campos não obrigatórios
                if (obrigatorioCelular) {
                    // Celular deve ter 11 dígitos no Brasil
                    return raw.length === 11;
                }
                // Telefone pode ser fixo (10) ou celular (11)
                return raw.length === 10 || raw.length === 11;
            }

            // Liga máscaras
            $('#telefone, #celular, #telefoneServidor, #celularServidor').on('input', function () {
                aplicarMascaraTelefone(this);
            });

            // Validação ao enviar formulários
            $('#formAluno').on('submit', function (e) {
                // CEP precisa ter 8 dígitos
                const cepRaw = ($('#cep').val() || '').replace(/\D/g, '');
                if (cepRaw.length !== 8) {
                    e.preventDefault();
                    alert('CEP do aluno inválido. Use o formato 00000-000.');
                    $('#cep').focus();
                    return false;
                }
                // CPF precisa ser válido
                const $cpf = $('#cpf');
                if ($cpf.length && !validarCPFValor($cpf.val())) {
                    e.preventDefault();
                    alert('CPF do aluno inválido. Verifique e tente novamente.');
                    $cpf.focus();
                    return false;
                }
                const $tel = $('#telefone');
                const $cel = $('#celular');
                // valida somente se preenchidos; celular é required no HTML, mas reforçamos regra de 11 dígitos
                if ($cel.length && !validarTelefoneCampo($cel, true)) {
                    e.preventDefault();
                    alert('Celular do aluno inválido. Use o formato (00) 00000-0000.');
                    $cel.focus();
                    return false;
                }
                if ($tel.length && $tel.val().trim() !== '' && !validarTelefoneCampo($tel, false)) {
                    e.preventDefault();
                    alert('Telefone do aluno inválido. Use (00) 0000-0000 ou (00) 00000-0000.');
                    $tel.focus();
                    return false;
                }
            });

            $('#formServidor').on('submit', function (e) {
                // CEP precisa ter 8 dígitos
                const cepRaw = ($('#cepServidor').val() || '').replace(/\D/g, '');
                if (cepRaw.length !== 8) {
                    e.preventDefault();
                    alert('CEP do servidor inválido. Use o formato 00000-000.');
                    $('#cepServidor').focus();
                    return false;
                }
                // CPF precisa ser válido
                const $cpf = $('#cpfServidor');
                if ($cpf.length && !validarCPFValor($cpf.val())) {
                    e.preventDefault();
                    alert('CPF do servidor inválido. Verifique e tente novamente.');
                    $cpf.focus();
                    return false;
                }
                const $tel = $('#telefoneServidor');
                const $cel = $('#celularServidor');
                if ($cel.length && !validarTelefoneCampo($cel, true)) {
                    e.preventDefault();
                    alert('Celular do servidor inválido. Use o formato (00) 00000-0000.');
                    $cel.focus();
                    return false;
                }
                if ($tel.length && $tel.val().trim() !== '' && !validarTelefoneCampo($tel, false)) {
                    e.preventDefault();
                    alert('Telefone do servidor inválido. Use (00) 0000-0000 ou (00) 00000-0000.');
                    $tel.focus();
                    return false;
                }
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
                        'formacaoAcademica', 'dataAdmissao'
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