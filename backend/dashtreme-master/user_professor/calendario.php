<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Calendário - SAS</title>
    <link href="../assets/css/pace.min.css" rel="stylesheet" />
    <script src="../assets/js/pace.min.js"></script>
    <link rel="icon" href="../assets/images/favicon.ico" type="image/x-icon">
    <link href="../assets/plugins/simplebar/css/simplebar.css" rel="stylesheet" />
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet" />
    <link href="../assets/css/animate.css" rel="stylesheet" />
    <link href="../assets/css/icons.css" rel="stylesheet" />
    <link href="../assets/css/sidebar-menu.css" rel="stylesheet" />
    <link href="../assets/css/app-style.css" rel="stylesheet" />
    <link rel="stylesheet" href="../css/style.css">

    <!-- FullCalendar CSS -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />

    
</head>

<body class="bg-theme bg-theme1 user_professor_calendario">
    <?php
    require("menu_padrao.php");
    ?>

        <div class="clearfix"></div>

        <!-- Conteúdo -->
        <div class="content-wrapper">
            <div class="container-fluid">
                <!-- Botões administrativos -->
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="d-flex flex-wrap gap-2">
                            <button class="btn btn-success" id="btn-publicar-calendario">
                                <i class="zmdi zmdi-save"></i>
                                <span class="d-none d-sm-inline"> Salvar</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Filtros -->
                    <div class="col-lg-3 col-md-12 mb-4">
                        <div class="card p-3">
                            <h6 class="card-title">Filtros</h6>
                            <div class="mb-3">
                                <label>Tipo de Evento:</label>
                                <select id="tipo-evento" class="form-control form-control-sm">
                                    <option value="all">Todos</option>
                                </select>
                            </div>
                            <button id="btn-adicionar" class="btn btn-primary btn-sm btn-block mt-2">Adicionar
                                Evento</button>
                            <button id="btn-gerenciar-tipos" class="btn btn-secondary btn-sm btn-block mt-2">Gerenciar
                                Tipos</button>
                        </div>

                        <div class="card p-3 mt-3">
                            <h6 class="card-title">Legenda</h6>
                            <div class="form-check small"><input class="form-check-input" type="checkbox" checked
                                    id="legenda-feriados"><label class="form-check-label">Feriados</label></div>
                            <div class="form-check small"><input class="form-check-input" type="checkbox" checked
                                    id="legenda-reunioes"><label class="form-check-label">Reuniões</label></div>
                            <div class="form-check small"><input class="form-check-input" type="checkbox" checked
                                    id="legenda-eventos"><label class="form-check-label">Eventos</label></div>
                            <div class="form-check small"><input class="form-check-input" type="checkbox" checked
                                    id="legenda-conselhos"><label class="form-check-label">Conselhos</label></div>
                            <div class="form-check small"><input class="form-check-input" type="checkbox" checked
                                    id="legenda-formacoes"><label class="form-check-label">Formações</label></div>
                        </div>
                    </div>

                    <!-- Calendário -->
                    <div class="col-lg-9 col-md-12">
                        <div class="card p-3">
                            <div id="calendar-container">
                                <div id="calendar"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Evento -->
                    <div class="modal fade" id="eventoModal" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content p-3">
                                <div class="modal-header">
                                    <h5 class="modal-title">Adicionar Evento</h5>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <form id="form-evento">
                                        <div class="form-group">
                                            <label>Título</label>
                                            <input type="text" class="form-control" id="evento-titulo" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Tipo</label>
                                            <select class="form-control" id="evento-tipo" required></select>
                                        </div>
                                        <div class="form-group">
                                            <label>Início</label>
                                            <input type="datetime-local" class="form-control" id="evento-inicio"
                                                required>
                                        </div>
                                        <div class="form-group">
                                            <label>Término</label>
                                            <input type="datetime-local" class="form-control" id="evento-fim">
                                        </div>
                                        <div class="form-group">
                                            <label>Descrição</label>
                                            <textarea class="form-control" id="evento-descricao" rows="3"></textarea>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-danger mr-auto" id="btn-excluir-evento"
                                        style="display: none;">Excluir</button>
                                    <button type="button" class="btn btn-secondary"
                                        data-dismiss="modal">Cancelar</button>
                                    <button type="button" class="btn btn-primary" id="btn-salvar-evento">Salvar</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Gerenciar Tipos -->
                    <div class="modal fade" id="tiposModal" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Tipos de Evento</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover">
                                            <thead>
                                                <tr>
                                                    <th class="w-50">Tipo</th>
                                                    <th class="w-25">Cor</th>
                                                    <th class="w-25">Ações</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tipos-eventos-body"></tbody>
                                        </table>
                                    </div>

                                    <hr>

                                    <h6 class="mt-3">Adicionar/Editar Tipo</h6>
                                    <div class="form-row align-items-end">
                                        <div class="col-12 col-md-5 mb-2 mb-md-0">
                                            <label class="small">Nome do Tipo</label>
                                            <input type="text" class="form-control form-control-sm" id="novo-tipo-nome"
                                                placeholder="Ex: Workshop">
                                        </div>
                                        <div class="col-8 col-md-4 mb-2 mb-md-0">
                                            <label class="small">Cor</label>
                                            <input type="color" class="form-control form-control-sm p-1"
                                                id="novo-tipo-cor" value="#6c757d">
                                        </div>
                                        <div class="col-4 col-md-3">
                                            <button class="btn btn-primary btn-sm btn-block" id="btn-adicionar-tipo">
                                                <i class="zmdi zmdi-plus"></i> <span
                                                    class="d-none d-md-inline">Adicionar</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer py-2">
                                    <button type="button" class="btn btn-sm btn-secondary"
                                        data-dismiss="modal">Fechar</button>
                                </div>
                            </div>
                        </div>
                </div>

                
            </div>
            <div class="overlay toggle-menu"></div>
        </div>

        <!-- Scripts -->
        <script src="../assets/js/jquery.min.js"></script>
        <script src="../assets/js/popper.min.js"></script>
        <script src="../assets/js/bootstrap.min.js"></script>
        <script src="../assets/plugins/simplebar/js/simplebar.js"></script>
        <script src="../assets/js/sidebar-menu.js"></script>
        <script src="../assets/js/app-script.js"></script>
        <script src='https://cdn.jsdelivr.net/npm/moment@2.29.1/min/moment.min.js'></script>
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/pt-br.js'></script>
       
        <!-- JS Customizado -->
    <script>
            // Escopo global
            var currentEvent = null;
            var editandoTipo = null;

            var tiposEventos = [];

            function fetchTiposEventos(callback){
                $.getJSON('../includes/ajax/calendario/tipos/listar_tipos.php')
                    .done(function(res){
                        if (res && res.success && res.data){
                            tiposEventos = res.data;
                        }
                        atualizarSelectsTipos();
                        if (typeof callback === 'function') callback();
                    })
                    .fail(function(){
                        // fallback: defaults locais caso backend indisponível
                        tiposEventos = [
                            { nome: 'feriado', cor: '#ffc107', label: 'Feriado', is_default: true },
                            { nome: 'reuniao', cor: '#28a745', label: 'Reunião', is_default: true },
                            { nome: 'evento', cor: '#6f42c1', label: 'Evento Institucional', is_default: true },
                            { nome: 'conselho', cor: '#17a2b8', label: 'Conselho de Classe', is_default: true },
                            { nome: 'formacao', cor: '#6610f2', label: 'Formação Pedagógica', is_default: true }
                        ];
                        atualizarSelectsTipos();
                        if (typeof callback === 'function') callback();
                    });
            }

            // Atualiza os selects de tipos com base no array tiposEventos
            function atualizarSelectsTipos(selecionadoEvento, selecionadoFiltro){
                var $selEvento = $('#evento-tipo');
                var $selFiltro = $('#tipo-evento');

                // Modal: apenas tipos existentes
                if ($selEvento.length){
                    var valEvento = selecionadoEvento || $selEvento.val();
                    $selEvento.empty();
                    for (var i = 0; i < tiposEventos.length; i++){
                        var t = tiposEventos[i];
                        var $opt = $('<option></option>').attr('value', t.nome).text(t.label);
                        $selEvento.append($opt);
                    }
                    if (valEvento){ $selEvento.val(valEvento); }
                }

                // Filtro: mantém opção "Todos"
                if ($selFiltro.length){
                    var valFiltro = (typeof selecionadoFiltro !== 'undefined' && selecionadoFiltro !== null)
                        ? selecionadoFiltro : ($selFiltro.val() || 'all');
                    $selFiltro.empty();
                    $selFiltro.append('<option value="all">Todos</option>');
                    for (var j = 0; j < tiposEventos.length; j++){
                        var tt = tiposEventos[j];
                        var $opt2 = $('<option></option>').attr('value', tt.nome).text(tt.label + 's');
                        $selFiltro.append($opt2);
                    }
                    $selFiltro.val(valFiltro);
                }
            }

            function carregarTiposEventos() {
                var tbody = $('#tipos-eventos-body');
                tbody.empty();

                for (var i = 0; i < tiposEventos.length; i++){
                    var tipo = tiposEventos[i];
                    var disabledDelete = '';
                    var editTitle = '';
                    var row = '' +
                        '<tr>' +
                        '  <td>' + (tipo.label || tipo.nome) + '</td>' +
                        '  <td><span class="badge" style="background-color: ' + (tipo.cor || '#6c757d') + '">&nbsp;&nbsp;&nbsp;</span></td>' +
                        '  <td>' +
                        '    <button class="btn btn-sm btn-outline-primary editar-tipo" data-nome="' + tipo.nome + '" ' + editTitle + '>' +
                        '      <i class="zmdi zmdi-edit"></i>' +
                        '    </button> ' +
                        '    <button class="btn btn-sm btn-outline-danger excluir-tipo" data-nome="' + tipo.nome + '" ' + disabledDelete + '>' +
                        '      <i class="zmdi zmdi-delete"></i>' +
                        '    </button>' +
                        '  </td>' +
                        '</tr>';
                    tbody.append(row);
                }

                // Limpa campos após carregar
                $('#novo-tipo-nome').val('');
                $('#novo-tipo-cor').val('#6c757d');
                editandoTipo = null;
                $('#btn-adicionar-tipo').text('Adicionar');
                // Após recarregar a lista, atualiza selects
                atualizarSelectsTipos();
            }

            $(document).ready(function () {
                // Primeiro: buscar tipos do backend e então inicializar calendário
                fetchTiposEventos(initCalendar);

                function initCalendar(){
                // Inicializa calendário
                var calendarEl = document.getElementById('calendar');
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    locale: 'pt-br',
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
                    },
                    dayMaxEvents: 3,
                    eventDisplay: 'list-item',
                    navLinks: true,
                    editable: true,
                    selectable: true,
                    businessHours: {
                        daysOfWeek: [1, 2, 3, 4, 5],
                        startTime: '07:00',
                        endTime: '18:00'
                    },
                    eventDidMount: function (info) {
                        var tipo = info.event.extendedProps.tipo;
                        var tipoConfig = null;
                        for (var k = 0; k < tiposEventos.length; k++){
                            if (tiposEventos[k].nome === tipo){ tipoConfig = tiposEventos[k]; break; }
                        }
                        if (tipoConfig) {
                            info.el.style.backgroundColor = tipoConfig.cor;
                            info.el.style.borderColor = tipoConfig.cor;
                        }
                        if (info.event.extendedProps.description) {
                            $(info.el).tooltip({
                                title: info.event.extendedProps.description,
                                placement: 'top',
                                trigger: 'hover',
                                container: 'body'
                            });
                        }
                    },
                    events: function(fetchInfo, successCallback, failureCallback){
                        var params = {
                            start: fetchInfo.startStr,
                            end: fetchInfo.endStr
                            // sem 'publico' para incluir 'todos' + 'professores' via sessão
                        };
                        $.getJSON('../includes/ajax/calendario/listar_eventos.php', params)
                            .done(function(res){
                                if (res.success) successCallback(res.data || []);
                                else failureCallback(res.message || 'Falha ao carregar eventos');
                            })
                            .fail(function(xhr){ failureCallback(xhr.statusText || 'Erro ao carregar eventos'); });
                    },
                    dateClick: function (info) {
                        currentEvent = null;
                        $('#form-evento')[0].reset();
                        $('#evento-inicio').val(info.dateStr + 'T08:00');
                        $('#btn-excluir-evento').hide();
                        $('#eventoModal').modal('show');
                    },
                    eventClick: function (info) {
                        currentEvent = info.event;
                        $('#evento-titulo').val(info.event.title);
                        atualizarSelectsTipos(info.event.extendedProps.tipo || 'evento');
                        $('#evento-descricao').val(info.event.extendedProps.description || '');
                        $('#evento-notificar').prop('checked', false);

                        $('#evento-inicio').val(info.event.start.toISOString().slice(0, 16));
                        $('#evento-fim').val(info.event.end ? info.event.end.toISOString().slice(0, 16) : '');
                        $('#btn-excluir-evento').show();
                        $('#eventoModal').modal('show');
                        info.jsEvent.preventDefault();
                    }
                });

                calendar.render();
                function ajustarVisualizacaoCalendario() {
                    var width = window.innerWidth;
                    if (width < 768) {
                        calendar.changeView('listMonth');
                    } else {
                        calendar.changeView('dayGridMonth');
                    }
                    calendar.updateSize();
                }

                // Chamar na inicialização e no redimensionamento
                ajustarVisualizacaoCalendario();
                $(window).on('resize', ajustarVisualizacaoCalendario);

                // Responsividade e redimensionamento
                $('.toggle-menu').click(function(){ setTimeout(function(){ calendar.updateSize(); }, 300); });
                $(window).resize(function(){ calendar.updateSize(); });

                // Filtro por tipo
                $('#tipo-evento').change(function () {
                    var tipo = $(this).val();
                    var events = calendar.getEvents();
                    for (var i = 0; i < events.length; i++){
                        var ev = events[i];
                        var show = (tipo === 'all' || (ev.extendedProps && ev.extendedProps.tipo === tipo));
                        ev.setProp('display', show ? 'auto' : 'none');
                    }
                });

                // Legendas
                $('.form-check-input').change(function () {
                    var tipo = $(this).attr('id').replace('legenda-', '');
                    var ativo = $(this).is(':checked');
                    var map = {
                        'feriados': 'feriado',
                        'reunioes': 'reuniao',
                        'eventos': 'evento',
                        'conselhos': 'conselho',
                        'formacoes': 'formacao'
                    };
                    var tipoFiltrado = map[tipo] || tipo;
                    var events = calendar.getEvents();
                    for (var i = 0; i < events.length; i++){
                        var ev = events[i];
                        if (ev.extendedProps && ev.extendedProps.tipo === tipoFiltrado) {
                            ev.setProp('display', ativo ? 'auto' : 'none');
                        }
                    }
                });

                $('#btn-adicionar').click(function () {
                    currentEvent = null;
                    $('#form-evento')[0].reset();
                    atualizarSelectsTipos();
                    $('#evento-inicio').val(new Date().toISOString().slice(0, 16));
                    $('#btn-excluir-evento').hide();
                    $('#eventoModal').modal('show');
                });

                $('#btn-gerenciar-tipos').click(function () {
                    fetchTiposEventos(function(){
                        carregarTiposEventos();
                        $('#tiposModal').modal('show');
                    });
                });

                $('#btn-adicionar-tipo').click(function () {
                    var nomeDigitado = $('#novo-tipo-nome').val().trim();
                    var cor = $('#novo-tipo-cor').val();

                    if (!nomeDigitado) { alert('Informe o nome do tipo.'); return; }

                    var nome = nomeDigitado.toLowerCase().replace(/\s+/g, '-');
                    var payload = { label: nomeDigitado, cor: cor, nome: nome };
                    if (editandoTipo) { payload.old = editandoTipo; }

                    $.ajax({
                        url: '../includes/ajax/calendario/tipos/salvar_tipo.php',
                        method: 'POST',
                        contentType: 'application/json; charset=utf-8',
                        data: JSON.stringify(payload),
                        dataType: 'json'
                    }).done(function(res){
                        if (res && res.success){
                            var selEvento = $('#evento-tipo').val();
                            var selFiltro = $('#tipo-evento').val();
                            editandoTipo = null;
                            $('#btn-adicionar-tipo').text('Adicionar');
                            fetchTiposEventos(function(){
                                carregarTiposEventos();
                                atualizarSelectsTipos(selEvento, selFiltro);
                                alert('Tipo salvo com sucesso!');
                            });
                        } else {
                            alert((res && res.message) || 'Falha ao salvar tipo');
                        }
                    }).fail(function(xhr){
                        alert('Erro ao salvar tipo: ' + (xhr.responseText || xhr.statusText));
                    });
                });

                $('#btn-salvar-evento').click(function () {
                    var title = $('#evento-titulo').val();
                    var tipo = $('#evento-tipo').val();
                    var start = $('#evento-inicio').val();
                    var end = $('#evento-fim').val();
                    var description = $('#evento-descricao').val();

                    if (!title) { alert('Título é obrigatório.'); return; }

                    var payload = {
                        id: currentEvent ? (parseInt(currentEvent.id, 10) || 0) : 0,
                        title: title,
                        tipo: tipo,
                        inicio: start,
                        fim: end || '',
                        descricao: description
                    };

                    $.ajax({
                        url: '../includes/ajax/calendario/salvar_evento.php',
                        method: 'POST',
                        contentType: 'application/json; charset=utf-8',
                        data: JSON.stringify(payload),
                        dataType: 'json'
                    }).done(function(res){
                        if (res && res.success) {
                            $('#eventoModal').modal('hide');
                            calendar.refetchEvents();
                        } else {
                            alert((res && res.message) || 'Falha ao salvar evento');
                        }
                    }).fail(function(xhr){
                        alert('Erro ao salvar evento: ' + (xhr.responseText || xhr.statusText));
                    });
                });

                $('#btn-excluir-evento').click(function () {
                    if (!currentEvent) return;
                    if (!confirm('Deseja excluir este evento?')) return;
                    var id = parseInt(currentEvent.id, 10) || 0;
                    if (id <= 0) { alert('Evento sem ID salvo.'); return; }
                    $.ajax({
                        url: '../includes/ajax/calendario/excluir_evento.php',
                        method: 'POST',
                        data: { id: id },
                        dataType: 'json'
                    }).done(function(res){
                        if (res && res.success) {
                            $('#eventoModal').modal('hide');
                            calendar.refetchEvents();
                        } else {
                            alert((res && res.message) || 'Falha ao excluir evento');
                        }
                    }).fail(function(xhr){
                        alert('Erro ao excluir evento: ' + (xhr.responseText || xhr.statusText));
                    });
                });

                // Importar/Exportar removidos a pedido — sem handlers

                $('#btn-publicar-calendario').click(function () {
                    // Neste contexto, o botão "Salvar" apenas força um recarregamento dos eventos
                    calendar.refetchEvents();
                    alert('Eventos sincronizados.');
                });
                } // fecha function initCalendar
            });

            // Editar/Excluir tipo
            $(document).on('click', '.editar-tipo', function () {
                var nomeTipo = $(this).data('nome');
                var tipoInfo = null;
                for (var i = 0; i < tiposEventos.length; i++){
                    if (tiposEventos[i].nome === nomeTipo){ tipoInfo = tiposEventos[i]; break; }
                }
                if (tipoInfo) {
                    $('#novo-tipo-nome').val(tipoInfo.label || tipoInfo.nome);
                    $('#novo-tipo-cor').val(tipoInfo.cor || '#6c757d');
                    editandoTipo = nomeTipo;
                    $('#btn-adicionar-tipo').text('Atualizar');
                }
            });

            $(document).on('click', '.excluir-tipo', function () {
                var nome = $(this).data('nome');
                var tipoInfo = null;
                for (var i = 0; i < tiposEventos.length; i++){
                    if (tiposEventos[i].nome === nome){ tipoInfo = tiposEventos[i]; break; }
                }
                // Permitir excluir inclusive tipos padrão
                if (confirm('Deseja excluir o tipo "' + nome + '"?')) {
                    $.ajax({
                        url: '../includes/ajax/calendario/tipos/remover_tipo.php',
                        method: 'POST',
                        contentType: 'application/json; charset=utf-8',
                        data: JSON.stringify({ nome: nome }),
                        dataType: 'json'
                    }).done(function(res){
                        if (res && res.success){
                            fetchTiposEventos(function(){
                                carregarTiposEventos();
                                atualizarSelectsTipos();
                            });
                        } else {
                            alert((res && res.message) || 'Falha ao remover tipo');
                        }
                    }).fail(function(xhr){
                        alert('Erro ao remover tipo: ' + (xhr.responseText || xhr.statusText));
                    });
                }
            });
        </script>

</body>

</html>