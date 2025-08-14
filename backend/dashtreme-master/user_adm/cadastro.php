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

    <link rel="stylesheet" href="style.css">

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
        }

        .checkbox-label {
            display: block;
            position: relative;
            padding-left: 30px;
            margin-bottom: 12px;
            cursor: pointer;
            user-select: none;
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

        .is-invalid {
            border-color: #dc3545 !important;
        }

        .invalid-feedback {
            display: none;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 80%;
            color: #dc3545;
        }

        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
            padding: 0.75rem 1.25rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
            border-radius: 0.25rem;
            display: none;
        }

        .form-section h5 {
            color: #71affa;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .btn-Salvar {
            background-color: #1abc9c;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 10px 20px;
        }

        .btn-Salvar:hover {
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

    <div class="clearfix"></div>

    <div class="content-wrapper">
        <div class="container-fluid">

            <div class="row pt-2 pb-2">
                <div class="col-sm-9">
                    <h4 class="page-title">Cadastro de Alunos e Servidores</h4>
                </div>
            </div>

            <!-- Mensagem de sucesso -->
            <div class="alert-success" id="successMessage">
                <i class="zmdi zmdi-check-circle mr-2"></i> Cadastro realizado com sucesso!
            </div>

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
                            <form id="formAluno">
                                <!-- Dados Pessoais - Aluno -->
                                <div class="form-section">
                                    <h5>Dados Pessoais</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Nome Completo</label>
                                                <input type="text" class="form-control" id="nomeCompleto" required>
                                                <div class="invalid-feedback">Por favor, informe o nome completo
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Data de Nascimento</label>
                                                <input type="date" class="form-control" id="dataNascimento" required>
                                                <div class="invalid-feedback">Por favor, informe a data de
                                                    nascimento</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Matrícula</label>
                                                <input type="text" class="form-control" id="matriculaAluno" required>
                                                <div class="invalid-feedback">Por favor, informe a matrícula</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Sexo</label>
                                                <select class="form-control" id="sexo" required>
                                                    <option value="">Selecione...</option>
                                                    <option>Masculino</option>
                                                    <option>Feminino</option>
                                                    <option>Outro</option>
                                                    <option>Prefiro não informar</option>
                                                </select>
                                                <div class="invalid-feedback">Por favor, selecione o sexo</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Raça/Cor</label>
                                                <select class="form-control" id="racaCor" required>
                                                    <option value="">Selecione...</option>
                                                    <option>Branca</option>
                                                    <option>Preta</option>
                                                    <option>Parda</option>
                                                    <option>Amarela</option>
                                                    <option>Indígena</option>
                                                    <option>Prefiro não informar</option>
                                                </select>
                                                <div class="invalid-feedback">Por favor, selecione a raça/cor</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Estado Civil</label>
                                                <select class="form-control" id="estadoCivil" required>
                                                    <option value="">Selecione...</option>
                                                    <option>Solteiro(a)</option>
                                                    <option>Casado(a)</option>
                                                    <option>Divorciado(a)</option>
                                                    <option>Viúvo(a)</option>
                                                </select>
                                                <div class="invalid-feedback">Por favor, selecione o estado civil
                                                </div>
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
                                                <select class="form-control" id="nacionalidade" required>
                                                    <option value="" disabled selected>Selecione...</option>
                                                    <option value="Brasileiro(a)">Brasileiro(a)</option>
                                                    <option value="Argentino(a)">Argentino(a)</option>
                                                    <option value="Uruguaio(a)">Uruguaio(a)</option>
                                                    <option value="Chileno(a)">Chileno(a)</option>
                                                    <option value="Americano(a)">Americano(a)</option>
                                                    <option value="Canadense">Canadense</option>
                                                    <option value="Espanhol(a)">Espanhol(a)</option>
                                                    <option value="Português(a)">Português(a)</option>
                                                    <option value="Italiano(a)">Italiano(a)</option>
                                                    <option value="Alemão(ã)">Alemão(ã)</option>
                                                    <option value="Francês(a)">Francês(a)</option>
                                                    <option value="Japonês(a)">Japonês(a)</option>
                                                    <option value="Chinês(a)">Chinês(a)</option>
                                                    <option value="Outra">Outra nacionalidade</option>
                                                </select>
                                                <div class="invalid-feedback">Por favor, selecione a nacionalidade
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Naturalidade</label>
                                                <input type="text" class="form-control" id="naturalidade" required>
                                                <div class="invalid-feedback">Por favor, informe a naturalidade
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Filiação</label>
                                                <input type="text" class="form-control" id="filiacao"
                                                    placeholder="Nome da mãe/pai" required>
                                                <div class="invalid-feedback">Por favor, informe a filiação</div>
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
                                                <input type="text" class="form-control" id="cpf"
                                                    placeholder="000.000.000-00" required>
                                                <div class="invalid-feedback">Por favor, informe o CPF</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Data de Expedição</label>
                                                <input type="date" class="form-control" id="dataExpedicao" required>
                                                <div class="invalid-feedback">Por favor, informe a data de
                                                    expedição
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>UF</label>
                                                <select class="form-control" id="ufDocumento" required>
                                                    <option value="">Selecione...</option>
                                                    <option>SP</option>
                                                    <!-- outros estados -->
                                                </select>
                                                <div class="invalid-feedback">Por favor, selecione a UF
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Órgão Expedidor</label>
                                                <input type="text" class="form-control" id="orgaoExpedidor"
                                                    placeholder="SSP" required>
                                                <div class="invalid-feedback">Por favor, informe o órgão
                                                    expedidor
                                                </div>
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
                                                <input type="text" class="form-control" id="cep" placeholder="00000-000"
                                                    required>
                                                <div class="invalid-feedback">Por favor, informe o CEP</div>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label>Logradouro</label>
                                                <input type="text" class="form-control" id="logradouro" required>
                                                <div class="invalid-feedback">Por favor, informe o
                                                    logradouro</div>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group">
                                                <label>Nº</label>
                                                <input type="text" class="form-control" id="numero" required>
                                                <div class="invalid-feedback">Por favor, informe o número
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Complemento</label>
                                                <input type="text" class="form-control" id="complemento">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Bairro</label>
                                                <input type="text" class="form-control" id="bairro" required>
                                                <div class="invalid-feedback">Por favor, informe o bairro
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Município</label>
                                                <input type="text" class="form-control" id="municipio" required>
                                                <div class="invalid-feedback">Por favor, informe o município
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>UF</label>
                                                <select class="form-control" id="ufEndereco" required>
                                                    <option value="">Selecione...</option>
                                                    <option>SP</option>
                                                    <!-- outros estados -->
                                                </select>
                                                <div class="invalid-feedback">Por favor, selecione a UF
                                                </div>
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
                                                <input type="text" class="form-control" id="telefone"
                                                    placeholder="(00) 0000-0000">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Celular</label>
                                                <input type="text" class="form-control" id="celular"
                                                    placeholder="(00) 00000-0000" required>
                                                <div class="invalid-feedback">Por favor, informe o celular
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>E-mail</label>
                                                <input type="email" class="form-control" id="email" required>
                                                <div class="invalid-feedback">Por favor, informe um e-mail
                                                    válido
                                                </div>
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
                                                    value="sim">
                                                <label class="form-check-label" for="nee-sim">Sim</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="nee" id="nee-nao"
                                                    value="nao" checked>
                                                <label class="form-check-label" for="nee-nao">Não</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="needs-box mt-3">
                                        <h6>Descrever necessidades:</h6>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label class="checkbox-label">AEE (Atendimento Educacional
                                                    Especializado)
                                                    <input type="checkbox" id="aee">
                                                    <span class="checkmark"></span>
                                                </label>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="checkbox-label">Sala de AEE
                                                    <input type="checkbox" id="salaAee">
                                                    <span class="checkmark"></span>
                                                </label>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="checkbox-label">Monitor/Estagiário
                                                    <input type="checkbox" id="monitor">
                                                    <span class="checkmark"></span>
                                                </label>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="checkbox-label">Intérprete de Libras
                                                    <input type="checkbox" id="interprete">
                                                    <span class="checkmark"></span>
                                                </label>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="checkbox-label">Material adaptado
                                                    <input type="checkbox" id="materialAdaptado">
                                                    <span class="checkmark"></span>
                                                </label>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="checkbox-label">Tecnologia assistiva
                                                    <input type="checkbox" id="tecnologiaAssistiva">
                                                    <span class="checkmark"></span>
                                                </label>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Outros (especificar)</label>
                                                    <input type="text" class="form-control" id="outrasNecessidades">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Botões -->
                                <div class="form-group row">
                                    <div class="col-sm-12 text-right">
                                        <button type="button" class="btn btn-Salvar px-5" id="btnSalvarEVincular">
                                            <i class="zmdi zmdi-link mr-1"></i> Salvar e Vincular Turma
                                        </button>
                                        <button type="button" class="btn btn-cancelar px-5"
                                            id="btnCancelarAluno">Cancelar</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Aba Servidor -->
                        <div class="tab-pane fade" id="servidor" role="tabpanel">
                            <form id="formServidor">
                                <!-- Dados Pessoais -->
                                <div class="form-section">
                                    <h5>Dados Pessoais</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Nome Completo</label>
                                                <input type="text" class="form-control" id="nomeCompletoServidor"
                                                    required>
                                                <div class="invalid-feedback">Por favor, informe o nome completo
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Data de Nascimento</label>
                                                <input type="date" class="form-control" id="dataNascimentoServidor"
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
                                                <select class="form-control" id="sexoServidor" required>
                                                    <option value="">Selecione...</option>
                                                    <option>Masculino</option>
                                                    <option>Feminino</option>
                                                    <option>Outro</option>
                                                    <option>Prefiro não informar</option>
                                                </select>
                                                <div class="invalid-feedback">Por favor, selecione o sexo</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Raça/Cor</label>
                                                <select class="form-control" id="racaCorServidor" required>
                                                    <option value="">Selecione...</option>
                                                    <option>Branca</option>
                                                    <option>Preta</option>
                                                    <option>Parda</option>
                                                    <option>Amarela</option>
                                                    <option>Indígena</option>
                                                    <option>Prefiro não informar</option>
                                                </select>
                                                <div class="invalid-feedback">Por favor, selecione a raça/cor</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Estado Civil</label>
                                                <select class="form-control" id="estadoCivilServidor" required>
                                                    <option value="">Selecione...</option>
                                                    <option>Solteiro(a)</option>
                                                    <option>Casado(a)</option>
                                                    <option>Divorciado(a)</option>
                                                    <option>Viúvo(a)</option>
                                                </select>
                                                <div class="invalid-feedback">Por favor, selecione o estado civil
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Nacionalidade-->
                                <div class="form-section">
                                    <h5>Nacionalidade</h5>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Nacionalidade</label>
                                                <select class="form-control" id="nacionalidadeServidor" required>
                                                    <option value="" disabled selected>Selecione...</option>
                                                    <option value="Brasileiro(a)">Brasileiro(a)</option>
                                                    <option value="Argentino(a)">Argentino(a)</option>
                                                    <option value="Uruguaio(a)">Uruguaio(a)</option>
                                                    <option value="Chileno(a)">Chileno(a)</option>
                                                    <option value="Americano(a)">Americano(a)</option>
                                                    <option value="Canadense">Canadense</option>
                                                    <option value="Espanhol(a)">Espanhol(a)</option>
                                                    <option value="Português(a)">Português(a)</option>
                                                    <option value="Italiano(a)">Italiano(a)</option>
                                                    <option value="Alemão(ã)">Alemão(ã)</option>
                                                    <option value="Francês(a)">Francês(a)</option>
                                                    <option value="Japonês(a)">Japonês(a)</option>
                                                    <option value="Chinês(a)">Chinês(a)</option>
                                                    <option value="Outra">Outra nacionalidade</option>
                                                </select>
                                                <div class="invalid-feedback">Por favor, selecione a nacionalidade
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Naturalidade</label>
                                                <input type="text" class="form-control" id="naturalidadeServidor"
                                                    required>
                                                <div class="invalid-feedback">Por favor, informe a naturalidade
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Filiação</label>
                                                <input type="text" class="form-control" id="filiacaoServidor"
                                                    placeholder="Nome da mãe/pai" required>
                                                <div class="invalid-feedback">Por favor, informe a filiação</div>
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
                                                <input type="text" class="form-control" id="cpfServidor"
                                                    placeholder="000.000.000-00" required>
                                                <div class="invalid-feedback">Por favor, informe o CPF</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>RG</label>
                                                <input type="text" class="form-control" id="rgServidor" required>
                                                <div class="invalid-feedback">Por favor, informe o RG</div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Órgão Expedidor</label>
                                                <input type="text" class="form-control" id="orgaoExpedidorServidor"
                                                    placeholder="SSP" required>
                                                <div class="invalid-feedback">Por favor, informe o órgão expedidor
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>UF</label>
                                                <select class="form-control" id="ufDocumentoServidor" required>
                                                    <option value="">Selecione...</option>
                                                    <option>SP</option>
                                                </select>
                                                <div class="invalid-feedback">Por favor, selecione a UF</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Título de Eleitor</label>
                                                <input type="text" class="form-control" id="tituloEleitor">
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
                                                <input type="text" class="form-control" id="cepServidor"
                                                    placeholder="00000-000" required>
                                                <div class="invalid-feedback">Por favor, informe o CEP</div>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label>Logradouro</label>
                                                <input type="text" class="form-control" id="logradouroServidor"
                                                    required>
                                                <div class="invalid-feedback">Por favor, informe o logradouro</div>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group">
                                                <label>Nº</label>
                                                <input type="text" class="form-control" id="numeroServidor" required>
                                                <div class="invalid-feedback">Por favor, informe o número</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Complemento</label>
                                                <input type="text" class="form-control" id="complementoServidor">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Bairro</label>
                                                <input type="text" class="form-control" id="bairroServidor" required>
                                                <div class="invalid-feedback">Por favor, informe o bairro</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Município</label>
                                                <input type="text" class="form-control" id="municipioServidor" required>
                                                <div class="invalid-feedback">Por favor, informe o município</div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>UF</label>
                                                <select class="form-control" id="ufEnderecoServidor" required>
                                                    <option value="">Selecione...</option>
                                                    <option>SP</option>
                                                    <!-- outros estados -->
                                                </select>
                                                <div class="invalid-feedback">Por favor, selecione a UF</div>
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
                                                <input type="text" class="form-control" id="telefoneServidor"
                                                    placeholder="(00) 0000-0000">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Celular</label>
                                                <input type="text" class="form-control" id="celularServidor"
                                                    placeholder="(00) 00000-0000" required>
                                                <div class="invalid-feedback">Por favor, informe o celular</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>E-mail</label>
                                                <input type="email" class="form-control" id="emailServidor" required>
                                                <div class="invalid-feedback">Por favor, informe um e-mail válido
                                                </div>
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
                                                <input type="text" class="form-control" id="cargoFuncao" required>
                                                <div class="invalid-feedback">Por favor, informe o cargo/função
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Matrícula</label>
                                                <input type="text" class="form-control" id="matriculaServidor" required>
                                                <div class="invalid-feedback">Por favor, informe a matrícula</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Data de Admissão</label>
                                                <input type="date" class="form-control" id="dataAdmissao" required>
                                                <div class="invalid-feedback">Por favor, informe a data de admissão
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Formação Acadêmica</label>
                                                <select class="form-control" id="formacaoAcademica" required>
                                                    <option value="">Selecione...</option>
                                                    <option>Ensino Médio Completo</option>
                                                    <option>Graduação Incompleta</option>
                                                    <option>Graduação Completa</option>
                                                    <option>Pós-Graduação</option>
                                                    <option>Mestrado</option>
                                                    <option>Doutorado</option>
                                                </select>
                                                <div class="invalid-feedback">Por favor, selecione a formação
                                                    acadêmica</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Área de Atuação</label>
                                                <input type="text" class="form-control" id="areaAtuacao" required>
                                                <div class="invalid-feedback">Por favor, informe a área de atuação
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Botões -->
                                <div class="form-group row">
                                    <div class="col-sm-12 text-right">
                                        <button type="submit" class="btn btn-Salvar px-5"><i
                                                class="zmdi zmdi-save mr-1"></i> Salvar</button>
                                        <button type="button" class="btn btn-cancelar px-5"
                                            id="btnCancelarServidor">Cancelar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!--Overlay-->
    <div class="overlay toggle-menu"></div>

    <a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>

    <footer class="footer">
        <div class="container">
            <div class="text-center">
                Copyright © 2023 Dashboard Acadêmico
            </div>
        </div>
    </footer>

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
    <!-- referencia cadastro.js -->
    <script src="../user_adm/cadastro.js"></script>
    <script src="botaoSair.js"></script>

</body>

</html>