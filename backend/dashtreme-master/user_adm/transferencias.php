<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <link rel="icon" href="../assets/images/favicon.ico" type="image/x-icon">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transferências - SAS</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/app-style.css">
    <link rel="stylesheet" href="../assets/css/icons.css">
    <link rel="stylesheet" href="../assets/css/sidebar-menu.css">
    <link rel="stylesheet" href="../css/style.css">


</head>

<body class="bg-theme bg-theme1 user_adm_transferencias">
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
                                <h4 class="page-title"><i class="zmdi zmdi-account-add mr-2"></i> Transferências
                                </h4>
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
                                    Turma: <span id="selected-student-turma"></span> |
                                    Turno: <span id="selected-student-turno"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Formulário de transferência -->
                        <div class="form-container">
                            <div class="form-section">
                                <div class="bold-title">Turma atual</div>
                                <input type="text" id="turma-aluno" class="form-control" readonly>
                            </div>

                            <div class="form-section">
                                <div class="bold-title">Turno atual</div>
                                <input type="text" id="turno-aluno" class="form-control" readonly>
                            </div>

                            <div class="form-section data-field">
                                <div class="bold-title">DATA DA TRANSFERÊNCIA</div>
                                <input type="date" id="data-transferencia" class="form-control" value="2025-07-14">
                            </div>

                            <div class="form-section">
                                <div class="bold-title">NOME DA ESCOLA DE DESTINO</div>
                                <div class="input-icon">
                                    <input type="text" id="escola-destino" class="form-control"
                                        placeholder="Clique para selecionar" readonly>
                                    <button type="button" class="icon-button" id="btn-open-local"
                                        title="Selecionar escola e localidade"
                                        aria-label="Selecionar escola e localidade">
                                        <i class="zmdi zmdi-city"></i>
                                    </button>
                                </div>
                                <small class="helper-text">Clique no campo ou no ícone para selecionar</small>
                            </div>

                            <div class="form-section" id="container-mun" style="display:none;">
                                <div class="bold-title">MUNICÍPIO/UF</div>
                                <input type="text" id="municipio-uf" class="form-control" placeholder="" readonly>
                                <input type="hidden" id="pais-id">
                                <input type="hidden" id="estado-id">
                                <input type="hidden" id="municipio-id">
                            </div>

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
    <div class="overlay toggle-menu"></div>
    </div>

    <!-- Modal Seletor de Localidade -->
    <div class="modal fade" id="localModal" tabindex="-1" role="dialog" aria-labelledby="localModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="localModalLabel">Selecionar Escola e Localidade</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="sel-escola">Escola de Destino</label>
                        <input type="text" id="sel-escola" class="form-control"
                            placeholder="Ex.: E.M.E.F. José de Anchieta" />
                    </div>
                    <div class="form-group">
                        <label for="sel-pais">País</label>
                        <select id="sel-pais" class="form-control"></select>
                    </div>
                    <div class="form-group">
                        <label for="sel-estado">Estado</label>
                        <select id="sel-estado" class="form-control"></select>
                    </div>
                    <div class="form-group">
                        <label for="sel-municipio">Município</label>
                        <select id="sel-municipio" class="form-control"></select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btn-usar-local">Usar seleção</button>
                </div>
            </div>
        </div>
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
            let cacheEstados = null;
            let cachePaises = null;

            // Data padrão = hoje
            const hoje = new Date();
            const dataFormatada = hoje.toISOString().slice(0, 10);
            $('#data-transferencia').val(dataFormatada);

            // Buscar alunos no servidor
            $('#btn-pesquisar').on('click', function () {
                const termo = $('#search-aluno').val().trim();
                if (termo.length < 2) {
                    alert('Digite pelo menos 2 caracteres para pesquisar');
                    return;
                }
                const $results = $('#search-results');
                $results.empty().append('<div class="text-white">Pesquisando...</div>').show();
                fetch(`../includes/ajax/admin/usuarios/buscar_alunos.php?q=${encodeURIComponent(termo)}`)
                    .then(r => r.json())
                    .then(resp => {
                        $results.empty();
                        if (!resp.success) {
                            $results.append(`<div class="text-danger">Erro: ${resp.message || 'falha na pesquisa'}</div>`);
                            return;
                        }
                        if (!resp.data || resp.data.length === 0) {
                            $results.append('<div class="text-white">Nenhum aluno encontrado.</div>');
                            return;
                        }
                        resp.data.forEach(a => {
                            const turma = a.Nome_Turma ? `${a.Nome_Turma}${a.Etapa ? ' (' + a.Etapa + ')' : ''}` : '—';
                            const turno = a.Turno || '—';
                            const card = `
                                <div class="student-card" 
                                    data-id="${a.ID_Aluno}" 
                                    data-nome="${a.Nome_Completo || ''}" 
                                    data-matricula="${a.Matricula || ''}" 
                                    data-turma="${turma}" 
                                    data-turno="${turno}">
                                    <div class="student-info">${a.Nome_Completo || 'Aluno'}</div>
                                    <div class="student-details">Matrícula: ${a.Matricula || '—'} | Turma: ${turma} | Turno: ${turno}</div>
                                </div>`;
                            $results.append(card);
                        });
                    })
                    .catch(err => {
                        $results.empty().append('<div class="text-danger">Erro ao pesquisar.</div>');
                        console.error(err);
                    });
            });

            // Selecionar aluno
            $(document).on('click', '.student-card', function () {
                alunoSelecionado = {
                    id: $(this).data('id'),
                    nome: $(this).data('nome'),
                    matricula: $(this).data('matricula'),
                    turma: $(this).data('turma'),
                    turno: $(this).data('turno')
                };
                $('#selected-student-name').text(alunoSelecionado.nome || 'Aluno');
                $('#selected-student-matricula').text(alunoSelecionado.matricula || '—');
                $('#selected-student-turma').text(alunoSelecionado.turma || '—');
                $('#selected-student-turno').text(alunoSelecionado.turno || '—');

                $('#turma-aluno').val(alunoSelecionado.turma || '');
                $('#turno-aluno').val(alunoSelecionado.turno || '');
                $('#selected-student').show();
                $('#search-results').hide();
            });

            // Cancelar
            $('#btn-cancelar').on('click', function () {
                if (confirm('Deseja realmente cancelar a operação?')) {
                    limpar();
                }
            });

            // Abrir modal de escola/localidade (ícone ou campo)
            function abrirModalLocal() {
                $('#localModal').modal('show');
                carregarPaisesEstados();
            }
            $('#btn-open-local').on('click', abrirModalLocal);
            $('#escola-destino').on('click', abrirModalLocal);

            $('#sel-estado').on('change', function () {
                const estadoId = $(this).val();
                carregarMunicipios(estadoId);
            });

            $('#btn-usar-local').on('click', function () {
                const estadoId = $('#sel-estado').val();
                const municipioId = $('#sel-municipio').val();
                const paisId = $('#sel-pais').val();
                const escolaNome = $('#sel-escola').val().trim();
                const estado = cacheEstados && cacheEstados.find(e => String(e.id) === String(estadoId));
                const municipioSel = $('#sel-municipio option:selected').text();
                const uf = estado ? (estado.uf || '') : '';
                if (!escolaNome) {
                    alert('Informe o nome da escola de destino.');
                    return;
                }
                if (!municipioId || !estadoId) {
                    alert('Selecione um estado e um município.');
                    return;
                }
                $('#escola-destino').val(escolaNome);
                $('#municipio-uf').val(`${municipioSel}${uf ? '/' + uf : ''}`);
                $('#pais-id').val(paisId || '');
                $('#estado-id').val(estadoId || '');
                $('#municipio-id').val(municipioId || '');
                $('#container-mun').show();
                $('#localModal').modal('hide');
            });

            function carregarPaisesEstados() {
                // Países
                if (cachePaises) { preencherSelect('#sel-pais', cachePaises, 'Selecione o país'); }
                else {
                    fetch('../includes/ajax/shared/localidades/listar_paises.php')
                        .then(r => r.json()).then(resp => {
                            const data = resp && resp.success ? (resp.data || []) : [];
                            cachePaises = data;
                            preencherSelect('#sel-pais', data, 'Selecione o país', 'id', 'nome');
                            const br = data.find(p => (p.nome || '').toLowerCase() === 'brasil');
                            if (br) { $('#sel-pais').val(String(br.id)); }
                        }).catch(() => {
                            preencherSelect('#sel-pais', [], '—');
                        });
                }
                // Estados
                if (cacheEstados) { preencherSelect('#sel-estado', cacheEstados, 'Selecione o estado', 'id', 'nome'); }
                else {
                    fetch('../includes/ajax/shared/localidades/listar_estados.php')
                        .then(r => r.json()).then(resp => {
                            const data = resp && resp.success ? (resp.data || []) : [];
                            cacheEstados = data;
                            preencherSelect('#sel-estado', data, 'Selecione o estado', 'id', 'nome');
                        }).catch(() => {
                            preencherSelect('#sel-estado', [], '—');
                        });
                }
                // Se já houver estado selecionado, recarrega municípios
                const curEstado = $('#estado-id').val();
                if (curEstado) {
                    $('#sel-estado').val(curEstado);
                    carregarMunicipios(curEstado, $('#municipio-id').val());
                } else {
                    preencherSelect('#sel-municipio', [], 'Selecione o município');
                }
                // Pré-preenche escola se já tiver um valor
                const curEscola = $('#escola-destino').val();
                $('#sel-escola').val(curEscola);
            }

            function carregarMunicipios(estadoId, preselectId) {
                if (!estadoId) { preencherSelect('#sel-municipio', [], 'Selecione o município'); return; }
                fetch(`../includes/ajax/shared/localidades/carregar_municipios.php?estado_id=${encodeURIComponent(estadoId)}`)
                    .then(r => r.json()).then(lista => {
                        // lista é um array simples
                        const arr = Array.isArray(lista) ? lista : [];
                        preencherSelect('#sel-municipio', arr, 'Selecione o município', 'id', 'nome');
                        if (preselectId) { $('#sel-municipio').val(String(preselectId)); }
                    }).catch(() => {
                        preencherSelect('#sel-municipio', [], '—');
                    });
            }

            function preencherSelect(sel, data, placeholder, valueKey = 'id', labelKey = 'nome') {
                const $s = $(sel);
                $s.empty();
                if (placeholder) { $s.append(`<option value="">${placeholder}</option>`); }
                (data || []).forEach(it => {
                    const v = it[valueKey] != null ? it[valueKey] : it.id;
                    const l = it[labelKey] != null ? it[labelKey] : it.nome;
                    $s.append(`<option value="${v}">${l}</option>`);
                });
            }

            // Confirmar - registra transferência
            $('#btn-confirmar').on('click', function () {
                if (!alunoSelecionado) {
                    alert('Selecione um aluno primeiro.');
                    return;
                }
                if (!validar()) return;

                const formData = new URLSearchParams();
                formData.append('aluno_id', String(alunoSelecionado.id));
                formData.append('data_transferencia', $('#data-transferencia').val());
                formData.append('escola_destino', $('#escola-destino').val());
                formData.append('municipio_uf', $('#municipio-uf').val());

                fetch('../includes/ajax/admin/matriculas/registrar_transferencia.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: formData.toString()
                })
                    .then(r => r.json())
                    .then(resp => {
                        if (!resp.success) {
                            alert('Erro ao registrar transferência: ' + (resp.message || 'falha'));
                            return;
                        }
                        alert('Transferência registrada com sucesso.');
                        // Oferece gerar PDF de transferência + histórico
                        const params = new URLSearchParams();
                        params.set('aluno_id', String(alunoSelecionado.id));
                        params.set('escola', $('#escola-destino').val());
                        params.set('mun', $('#municipio-uf').val());
                        params.set('data', $('#data-transferencia').val());
                        params.set('auto', '1');
                        const urlDoc = `documento_transferencia.php?${params.toString()}`;
                        if (confirm('Deseja gerar o PDF de Transferência (+ Histórico)?')) {
                            window.open(urlDoc, '_blank');
                        }
                        limpar();
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Erro inesperado ao registrar transferência.');
                    });
            });

            function validar() {
                if (!$('#escola-destino').val()) {
                    alert('Informe o nome da escola de destino.');
                    return false;
                }
                if (!$('#municipio-uf').val()) {
                    alert('Informe o município/UF da escola de destino.');
                    return false;
                }
                return true;
            }

            function limpar() {
                alunoSelecionado = null;
                $('#search-aluno').val('');
                $('#search-results').empty().hide();
                $('#selected-student').hide();
                $('#turma-aluno').val('');
                $('#turno-aluno').val('');
                $('#escola-destino').val('');
                $('#municipio-uf').val('');
                $('#data-transferencia').val(dataFormatada);
            }
        });
    </script>

</body>

</html>