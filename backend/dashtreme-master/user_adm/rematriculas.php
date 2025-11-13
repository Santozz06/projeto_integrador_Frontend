<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rematrículas - SAS (Sistema Academico Santos)</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/app-style.css">
    <link rel="stylesheet" href="../assets/css/icons.css">
    <link rel="stylesheet" href="../assets/css/sidebar-menu.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="bg-theme bg-theme1 user_adm_rematriculas">
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
                                <h4 class="page-title"><i class="zmdi zmdi-refresh mr-2"></i> Rematrículas</h4>
                            </div>

                            <!-- Container de busca do aluno -->
                            <div class="form-group">
                                <label for="search-aluno" class="text-white font-weight-bold">Buscar aluno</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="search-aluno"
                                        placeholder="Digite nome ou matrícula...">
                                    <div class="input-group-append">
                                        <button class="btn btn-custom-primary" type="button" id="btn-pesquisar">
                                            <i class="zmdi zmdi-search"></i> Pesquisar
                                        </button>
                                    </div>
                                </div>
                                <small class="text-muted">Digite pelo menos 2 caracteres para pesquisar.</small>
                            </div>

                            <!-- Resultados da pesquisa -->
                            <div id="search-results" class="search-results"></div>

                            <!-- Aluno selecionado -->
                            <div id="selected-student" class="selected-student" style="display: none;">
                                <div class="student-info" id="selected-student-name"></div>
                                <div class="student-details">
                                    Matrícula: <span id="selected-student-matricula"></span> |
                                    Turma Atual: <span id="selected-student-turma"></span> |
                                    Turno Atual: <span id="selected-student-turno"></span>
                                </div>
                            </div>

                            <!-- Formulário de rematrícula -->
                            <div class="form-container">
                                <!-- Seção Rematrícula -->
                                <div class="form-section">
                                    <h5 class="section-title">REMATRÍCULA</h5>
                                    <div class="form-group">
                                        <div class="bold-title">Nome</div>
                                        <input type="text" id="nome-aluno" class="form-control" readonly>
                                    </div>
                                    <div class="form-group">
                                        <div class="bold-title">Matrícula</div>
                                        <input type="text" id="matricula-aluno" class="form-control" readonly>
                                    </div>
                                    <div class="form-group">
                                        <div class="bold-title">Ano letivo</div>
                                        <select id="ano-letivo" class="form-control"></select>
                                    </div>
                                </div>

                                <!-- Seção Etapa/Série -->
                                <div class="form-section">
                                    <h5 class="section-title">ETAPA/SÉRIE</h5>
                                    <div class="form-group">
                                        <div class="bold-title">Turma</div>
                                        <select id="nova-turma" class="form-control">
                                            <option value="">Selecione a nova turma</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Seção Atualização de dados -->
                                <div class="form-section">
                                    <h5 class="section-title">ATUALIZAÇÃO DE DADOS</h5>
                                    <div class="form-group">
                                        <div class="bold-title">Telefone</div>
                                        <input type="tel" id="telefone" class="form-control"
                                            placeholder="(00) 00000-0000">
                                    </div>
                                    <div class="form-group">
                                        <div class="bold-title">Endereço</div>
                                        <input type="text" id="endereco" class="form-control"
                                            placeholder="Rua, número, bairro">
                                    </div>
                                </div>

                                <!-- Seção E-mail -->
                                <div class="form-section">
                                    <h5 class="section-title">E-MAIL</h5>
                                    <div class="form-group">
                                        <div class="bold-title">Dados nova matrícula</div>
                                        <input type="email" id="email" class="form-control"
                                            placeholder="email@exemplo.com">
                                    </div>
                                </div>

                                <!-- Seção Turno -->
                                <div class="form-section">
                                    <h5 class="section-title">TURNO</h5>
                                    <select id="novo-turno" class="form-control">
                                        <option value="">Selecione o novo turno</option>
                                        <option value="manha">Manhã</option>
                                        <option value="tarde">Tarde</option>
                                        <option value="noite">Noite</option>
                                    </select>
                                </div>

                                <!-- Botões -->
                                <div class="btn-group">
                                    <button class="btn-confirmar" id="btn-confirmar">Confirmar</button>
                                    <button class="btn-cancelar" id="btn-cancelar">Cancelar</button>
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
        $(function () {
            let alunoSelecionado = null;

            // Carrega anos letivos e define padrão (ano atual e/ou próximo)
            function carregarAnos() {
                const $ano = $('#ano-letivo');
                $ano.empty().append('<option value="">Carregando anos...</option>');
                fetch('../includes/ajax/listar_anos_letivos.php')
                    .then(r => r.json())
                    .then(resp => {
                        $ano.empty().append('<option value="">Selecione o ano letivo</option>');
                        if (resp.success && resp.data && resp.data.length) {
                            // popula anos existentes (já ordenados desc no backend)
                            resp.data.forEach(a => $ano.append(`<option value="${a}">${a}</option>`));
                            // opcional: oferecer próximo ano, mas NÃO selecionar por padrão
                            const maxAno = Math.max.apply(null, resp.data.map(Number));
                            if (Number.isFinite(maxAno)) {
                                $ano.prepend(`<option value="${maxAno + 1}">${maxAno + 1}</option>`);
                                // Seleciona o maior ano existente por padrão (garante turmas)
                                $ano.val(String(maxAno));
                            }
                        } else {
                            const anoAtual = new Date().getFullYear();
                            $ano.append(`<option value="${anoAtual}">${anoAtual}</option>`).val(String(anoAtual));
                        }
                        atualizarTurmasPorAno();
                    })
                    .catch(() => {
                        const anoAtual = new Date().getFullYear();
                        $ano.empty().append(`<option value="${anoAtual}">${anoAtual}</option>`).val(String(anoAtual));
                        atualizarTurmasPorAno();
                    });
            }

            function atualizarTurmasPorAno() {
                const ano = $('#ano-letivo').val();
                const $turma = $('#nova-turma');
                if (!ano) { $turma.empty().append('<option value="">Selecione o ano primeiro</option>'); return; }
                $turma.empty().append('<option value="">Carregando turmas...</option>');
                fetch(`../includes/ajax/listar_turmas.php?ano=${encodeURIComponent(ano)}`)
                    .then(r => r.json())
                    .then(resp => {
                        $turma.empty().append('<option value="">Selecione a nova turma</option>');
                        if (resp.success && resp.data) {
                            resp.data.forEach(t => {
                                const label = `${t.Nome_Turma}${t.Etapa ? ' ('+t.Etapa+')' : ''}`;
                                $turma.append(`<option value="${t.ID_Turma}">${label}</option>`);
                            });
                        } else {
                            $turma.append('<option value="">Nenhuma turma encontrada</option>');
                        }
                    })
                    .catch(() => $turma.empty().append('<option value="">Erro ao carregar turmas</option>'));
            }

            $('#ano-letivo').on('change', atualizarTurmasPorAno);
            carregarAnos();

            // Botão de pesquisa
            $('#btn-pesquisar').click(function () {
                const termo = $('#search-aluno').val().trim();
                if (termo.length < 2) {
                    alert('Digite pelo menos 2 caracteres para pesquisar');
                    return;
                }
                const $res = $('#search-results');
                $res.empty().append('<div class="text-white">Pesquisando...</div>').show();
                fetch(`../includes/ajax/buscar_alunos.php?q=${encodeURIComponent(termo)}`)
                    .then(r => r.json())
                    .then(resp => {
                        $res.empty();
                        if (!resp.success || !resp.data || !resp.data.length) {
                            $res.append('<div class="text-white">Nenhum aluno encontrado.</div>');
                            return;
                        }
                        resp.data.forEach(a => {
                            const turma = a.Nome_Turma ? `${a.Nome_Turma}${a.Etapa ? ' ('+a.Etapa+')' : ''}` : '—';
                            const card = `
                                <div class="student-card"
                                    data-id="${a.ID_Aluno}"
                                    data-nome="${a.Nome_Completo || ''}"
                                    data-matricula="${a.Matricula || ''}"
                                    data-turma="${turma}"
                                    data-turno="${a.Turno || ''}"
                                    data-telefone="${a.Telefone || ''}"
                                    data-endereco="${a.Endereco || ''}"
                                    data-email="${a.Email || ''}">
                                    <div class="student-info">${a.Nome_Completo || 'Aluno'}</div>
                                    <div class="student-details">Matrícula: ${a.Matricula || '—'} | Turma: ${turma} | Turno: ${a.Turno || '—'}</div>
                                </div>`;
                            $res.append(card);
                        });
                    })
                    .catch(() => { $res.empty().append('<div class="text-danger">Erro ao pesquisar.</div>'); });
            });

            // Selecionar aluno
            $(document).on('click', '.student-card', function () {
                alunoSelecionado = {
                    id: $(this).data('id'),
                    nome: $(this).data('nome'),
                    matricula: $(this).data('matricula'),
                    turma: $(this).data('turma'),
                    turno: $(this).data('turno'),
                    telefone: $(this).data('telefone'),
                    endereco: $(this).data('endereco'),
                    email: $(this).data('email')
                };
                $('#selected-student-name').text(alunoSelecionado.nome || 'Aluno');
                $('#selected-student-matricula').text(alunoSelecionado.matricula || '—');
                $('#selected-student-turma').text(alunoSelecionado.turma || '—');
                $('#selected-student-turno').text(alunoSelecionado.turno || '—');
                $('#selected-student').show();
                $('#nome-aluno').val(alunoSelecionado.nome || '');
                $('#matricula-aluno').val(alunoSelecionado.matricula || '');
                $('#telefone').val(alunoSelecionado.telefone || '');
                $('#endereco').val(alunoSelecionado.endereco || '');
                $('#email').val(alunoSelecionado.email || '');
                $('#search-results').hide();
            });

            // Confirmar rematrícula
            $('#btn-confirmar').click(function () {
                if (!alunoSelecionado) { alert('Selecione um aluno.'); return; }
                if (!validarFormulario()) return;
                const body = new URLSearchParams();
                body.append('aluno_id', String(alunoSelecionado.id));
                body.append('ano_letivo', $('#ano-letivo').val());
                body.append('nova_turma_id', $('#nova-turma').val());
                body.append('telefone', $('#telefone').val());
                body.append('endereco', $('#endereco').val());
                body.append('email', $('#email').val());
                fetch('../includes/ajax/rematricular_aluno.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: body.toString()
                })
                    .then(r => r.json())
                    .then(resp => {
                        if (!resp.success) { alert('Erro: ' + (resp.message || 'falha na rematrícula')); return; }
                        alert('Rematrícula registrada com sucesso.');
                        limparFormulario();
                    })
                    .catch(() => alert('Erro ao rematricular.'));
            });

            // Cancelar
            $('#btn-cancelar').click(function () {
                if (confirm('Deseja realmente cancelar a operação? Todos os dados não salvos serão perdidos.')) {
                    limparFormulario();
                }
            });

            function validarFormulario() {
                if (!$('#ano-letivo').val()) { alert('Selecione o ano letivo'); return false; }
                if (!$('#nova-turma').val()) { alert('Selecione a nova turma'); return false; }
                if (!$('#telefone').val()) { alert('Informe o telefone'); return false; }
                if (!$('#endereco').val()) { alert('Informe o endereço'); return false; }
                if (!$('#email').val()) { alert('Informe o e-mail'); return false; }
                return true;
            }

            function limparFormulario() {
                $('#search-aluno').val('');
                $('#search-results').empty().hide();
                $('#selected-student').hide();
                alunoSelecionado = null;
                $('#nome-aluno').val('');
                $('#matricula-aluno').val('');
                $('#telefone').val('');
                $('#endereco').val('');
                $('#email').val('');
                carregarAnos();
                $('#nova-turma').val('');
                $('#novo-turno').val('');
            }
        });
    </script>

</body>

</html>