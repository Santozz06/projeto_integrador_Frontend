<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <link rel="icon" href="../assets/images/favicon.ico" type="image/x-icon">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rematrículas - SAS</title>
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
                    <div class="card">
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
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <div class="bold-title">CEP</div>
                                                <input type="text" id="cep" class="form-control" placeholder="00000-000"
                                                    maxlength="9">
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <div class="bold-title">Logradouro</div>
                                                <input type="text" id="logradouro" class="form-control"
                                                    placeholder="Rua, Avenida, etc">
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group">
                                                <div class="bold-title">Nº</div>
                                                <input type="text" id="numero" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <div class="bold-title">Complemento</div>
                                                <input type="text" id="complemento" class="form-control"
                                                    placeholder="Apto, Bloco, etc (opcional)">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <div class="bold-title">Bairro</div>
                                                <input type="text" id="bairro" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <div class="bold-title">UF</div>
                                                <select id="ufEndereco" class="form-control">
                                                    <option value="">Selecione...</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <div class="bold-title">Município</div>
                                                <select id="municipio" class="form-control">
                                                    <option value="">Selecione o UF primeiro...</option>
                                                </select>
                                            </div>
                                        </div>
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

            // Carrega estados para o select de UF
            function carregarEstados() {
                fetch('../includes/ajax/shared/localidades/listar_estados.php')
                    .then(r => r.json())
                    .then(resp => {
                        const $uf = $('#ufEndereco');
                        $uf.empty().append('<option value="">Selecione...</option>');
                        if (resp.success && resp.data) {
                            resp.data.forEach(estado => {
                                $uf.append(`<option value="${estado.id}">${estado.nome}</option>`);
                            });
                        }
                    })
                    .catch(() => console.error('Erro ao carregar estados'));
            }

            // Quando mudar o UF, carrega municípios
            $('#ufEndereco').on('change', function () {
                const estadoId = $(this).val();
                const $municipio = $('#municipio');
                if (!estadoId) {
                    $municipio.empty().append('<option value="">Selecione o UF primeiro...</option>');
                    return;
                }
                $municipio.empty().append('<option value="">Carregando...</option>');
                fetch(`../includes/ajax/shared/localidades/carregar_municipios.php?estado_id=${estadoId}`)
                    .then(r => r.json())
                    .then(data => {
                        $municipio.empty().append('<option value="">Selecione...</option>');
                        data.forEach(m => {
                            $municipio.append(`<option value="${m.id}">${m.nome}</option>`);
                        });
                    })
                    .catch(() => {
                        $municipio.empty().append('<option value="">Erro ao carregar</option>');
                    });
            });

            // Busca CEP via ViaCEP
            function aplicarMascaraCEP(input) {
                let cep = input.value.replace(/\D/g, '');
                if (cep.length > 8) cep = cep.substring(0, 8);
                if (cep.length > 5) cep = cep.substring(0, 5) + '-' + cep.substring(5);
                input.value = cep;
            }

            $('#cep').on('input', function () {
                aplicarMascaraCEP(this);
                const cep = this.value.replace(/\D/g, '');
                if (cep.length === 8) {
                    buscarCEP(cep);
                }
            });

            async function buscarCEP(cep) {
                try {
                    const resp = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
                    const data = await resp.json();
                    if (data.erro) {
                        console.warn('CEP não encontrado');
                        return;
                    }
                    // Preenche campos
                    if (data.logradouro) $('#logradouro').val(data.logradouro);
                    if (data.bairro) $('#bairro').val(data.bairro);
                    if (data.complemento) $('#complemento').val(data.complemento);

                    // Busca o código do estado pela sigla
                    const estados = await fetch('../includes/ajax/shared/localidades/listar_estados.php').then(r => r.json());
                    if (estados.success && data.uf) {
                        const estado = estados.data.find(e => e.uf === data.uf);
                        if (estado) {
                            $('#ufEndereco').val(estado.id).trigger('change');
                            // Aguarda carregar municípios e seleciona pelo código IBGE
                            setTimeout(() => {
                                if (data.ibge) {
                                    $('#municipio').val(data.ibge);
                                }
                            }, 500);
                        }
                    }
                } catch (e) {
                    console.error('Erro ao buscar CEP:', e);
                }
            }

            carregarEstados();

            // Carrega anos letivos e define padrão (ano atual e/ou próximo)
            function carregarAnos() {
                const $ano = $('#ano-letivo');
                $ano.empty().append('<option value="">Carregando anos...</option>');
                fetch('../includes/ajax/shared/academico/listar_anos_letivos.php')
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
                fetch(`../includes/ajax/admin/turmas/listar_turmas.php?ano=${encodeURIComponent(ano)}`)
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
                fetch(`../includes/ajax/admin/usuarios/buscar_alunos.php?q=${encodeURIComponent(termo)}`)
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
                    cep: $(this).data('cep') || '',
                    logradouro: $(this).data('logradouro') || '',
                    numero: $(this).data('numero') || '',
                    complemento: $(this).data('complemento') || '',
                    bairro: $(this).data('bairro') || '',
                    uf_endereco: $(this).data('uf_endereco') || '',
                    municipio: $(this).data('municipio') || '',
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
                $('#cep').val(alunoSelecionado.cep || '');
                $('#logradouro').val(alunoSelecionado.logradouro || '');
                $('#numero').val(alunoSelecionado.numero || '');
                $('#complemento').val(alunoSelecionado.complemento || '');
                $('#bairro').val(alunoSelecionado.bairro || '');
                $('#email').val(alunoSelecionado.email || '');
                
                // Carrega UF e município se disponível
                if (alunoSelecionado.uf_endereco) {
                    $('#ufEndereco').val(alunoSelecionado.uf_endereco).trigger('change');
                    if (alunoSelecionado.municipio) {
                        setTimeout(() => {
                            $('#municipio').val(alunoSelecionado.municipio);
                        }, 500);
                    }
                }
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
                body.append('cep', $('#cep').val());
                body.append('logradouro', $('#logradouro').val());
                body.append('numero', $('#numero').val());
                body.append('complemento', $('#complemento').val());
                body.append('bairro', $('#bairro').val());
                body.append('uf_endereco', $('#ufEndereco').val());
                body.append('municipio', $('#municipio').val());
                body.append('email', $('#email').val());
                fetch('../includes/ajax/admin/matriculas/rematricular_aluno.php', {
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
                if (!$('#cep').val()) { alert('Informe o CEP'); return false; }
                if (!$('#logradouro').val()) { alert('Informe o logradouro'); return false; }
                if (!$('#numero').val()) { alert('Informe o número'); return false; }
                if (!$('#bairro').val()) { alert('Informe o bairro'); return false; }
                if (!$('#ufEndereco').val()) { alert('Selecione o UF'); return false; }
                if (!$('#municipio').val()) { alert('Selecione o município'); return false; }
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
                $('#cep').val('');
                $('#logradouro').val('');
                $('#numero').val('');
                $('#complemento').val('');
                $('#bairro').val('');
                $('#ufEndereco').val('');
                $('#municipio').val('');
                $('#email').val('');
                carregarAnos();
                $('#nova-turma').val('');
            }
        });
    </script>

</body>

</html>