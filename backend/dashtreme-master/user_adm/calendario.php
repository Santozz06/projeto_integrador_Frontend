<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Calendário Institucional - Admin</title>
    <link href="../assets/css/pace.min.css" rel="stylesheet" />
    <script src="../assets/js/pace.min.js"></script>
    <link rel="icon" href="../assets/images/favicon.ico" type="image/x-icon">
    <link href="../assets/plugins/simplebar/css/simplebar.css" rel="stylesheet" />
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet" />
    <link href="../assets/css/animate.css" rel="stylesheet" />
    <link href="../assets/css/icons.css" rel="stylesheet" />
    <link href="../assets/css/sidebar-menu.css" rel="stylesheet" />
    <link href="../assets/css/app-style.css" rel="stylesheet" />
    <link rel="stylesheet" href="style.css">

    <!-- FullCalendar CSS -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />

    <!-- CSS Customizado -->
    <style>
        html, body {
            height: 100%;
            min-height: 100%;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
        }
        body {
            flex: 1 0 auto;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .content-wrapper {
            flex: 1 0 auto;
        }
        .footer {
            flex-shrink: 0;
            background: transparent;
            color: #fff;
            border: none;
            text-align: center;
            padding: 15px 0 10px 0;
        }
        body {
            background: linear-gradient(to right, #2c3e50, #3498db);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #ecf0f1;
        }

        .content-wrapper {
            padding-top: 80px;
        }

        .card {
            background: rgba(255, 255, 255, 0.05);
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        .card-title {
            color: #71affe;
            font-weight: 600;
            text-transform: uppercase;
        }

        .form-control,
        .form-select {
            background-color: rgba(255, 255, 255, 0.15);
            border: 1px solid #71affe;
            color: #fff;
            border-radius: 6px;
        }

        .form-control:focus,
        .form-select:focus {
            background-color: rgba(255, 255, 255, 0.2);
            color: #fff;
            border-color: #1abc9c;
            box-shadow: 0 0 0 0.2rem rgba(26, 188, 156, 0.25);
        }

        .btn {
            border-radius: 6px;
            font-weight: 500;
        }

        .btn-primary {
            background-color: #1abc9c;
            border: none;
        }

        .btn-primary:hover {
            background-color: #16a085;
        }

        .btn-primary:not(:disabled):not(.disabled):active,
        .btn-primary:not(:disabled):not(.disabled).active,
        .show>.btn-primary.dropdown-toggle {
            background-color: #16a085;
            box-shadow: none;
        }

        .btn-secondary {
            background-color: #7f8c8d;
            border: none;
        }

        .btn-secondary:hover {
            background-color: #616a6b;
        }

        .btn-secondary:not(:disabled):not(.disabled):active,
        .btn-secondary:not(:disabled):not(.disabled).active,
        .show>.btn-secondary.dropdown-toggle {
            background-color: #616a6b;
            box-shadow: none;
        }

        .btn:focus,
        .btn:active {
            outline: none !important;
            box-shadow: none !important;
        }

        .btn-info {
            background-color: #3498db;
            border: none;
        }

        .btn-info:hover {
            background-color: #2980b9;
        }

        .fc .fc-toolbar-title,
        .fc .fc-col-header-cell-cushion,
        .fc .fc-daygrid-day-number {
            color: #ecf0f1;
        }

        .fc-event {
            font-size: 0.75em !important;
        }

        .modal-content {
            background-color: rgba(255, 255, 255, 0.95);
            color: #333;
        }

        label {
            color: #ffffff;
            font-weight: 600;
        }

        option {
            color: #333;
        }

        #calendar {
            background-color: rgba(255, 255, 255, 0.03);
            border-radius: 8px;
            padding: 10px;
        }

        #calendar {
            width: 100%;
            height: 100%;
            min-height: 600px;
        }

        #tiposModal .modal-content {
            background-color: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            color: #ecf0f1;
            border-radius: 12px;
        }

        #tiposModal .modal-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
        }

        #tiposModal .modal-title {
            color: #71affe;
            font-weight: bold;
        }

        #tiposModal table th {
            color: #71affe;
            background-color: transparent;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
        }

        #tiposModal table td {
            color: #ecf0f1;
            background-color: transparent;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        #tiposModal input.form-control,
        #tiposModal input[type="color"] {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid #71affe;
            color: #ecf0f1;
        }

        #tiposModal input::placeholder {
            color: #bdc3c7;
        }

        #tiposModal .btn-outline-primary {
            color: #71affe;
            border-color: #71affe;
        }

        #tiposModal .btn-outline-primary:hover {
            background-color: #71affe;
            color: #fff;
        }

        #tiposModal .btn-outline-danger {
            color: #e74c3c;
            border-color: #e74c3c;
        }

        #tiposModal .btn-outline-danger:hover {
            background-color: #e74c3c;
            color: white;
        }

        #tiposModal .btn-primary {
            background-color: #1abc9c;
            border: none;
        }

        #tiposModal .btn-primary:hover {
            background-color: #16a085;
        }

        #tiposModal .btn-secondary {
            background-color: #7f8c8d;
            border: none;
        }

        #tiposModal .btn-secondary:hover {
            background-color: #616a6b;
        }

        #eventoModal .modal-content {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            color: #ecf0f1;
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        #eventoModal label {
            color: #71affe;
            font-weight: 600;
        }

        #eventoModal .form-control,
        #eventoModal textarea {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid #71affe;
            color: #fff;
        }

        #eventoModal .form-control:focus,
        #eventoModal textarea:focus {
            background-color: rgba(255, 255, 255, 0.2);
            border-color: #1abc9c;
            box-shadow: 0 0 0 0.2rem rgba(26, 188, 156, 0.25);
        }

        /* Estilo da barra de dia da lista */
        .fc-list-day {
            background: rgba(255, 255, 255, 0.05) !important;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #71affe !important;
            font-weight: bold;
            border-radius: 6px;
        }

        /* Texto do dia */
        .fc-list-day-text,
        .fc-list-day-side-text {
            color: #71affe !important;
        }

        /* Estilo das linhas de eventos */
        .fc-list-event td {
            background-color: rgba(255, 255, 255, 0.03) !important;
            color: #ecf0f1 !important;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        /* Título e horário */
        .fc-list-event-title,
        .fc-list-event-time {
            color: #ecf0f1 !important;
            font-weight: 500;
        }

        /* Hover sobre eventos da lista */
        .fc-list-event:hover td {
            background-color: rgba(255, 255, 255, 0.07) !important;
        }

        .fc-view-harness {
            min-height: 600px !important;
        }

        @media (max-width: 991.98px) {
            .content-wrapper {
                padding-top: 70px;
                margin-left: 0 !important;
            }

            #sidebar-wrapper {
                position: fixed;
                left: -250px;
                top: 0;
                width: 250px;
                height: 100%;
                background-color: #000000;
                transition: all 0.3s ease-in-out;
                z-index: 9999;
            }

            #wrapper.menu-toggled #sidebar-wrapper {
                left: 0;
            }

            .overlay.toggle-menu {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100vw;
                height: 100vh;
                background-color: #000000;
                z-index: 9998;
            }

            #wrapper.menu-toggled .overlay.toggle-menu {
                display: block;
            }

            .brand-logo h5,
            .sidebar-menu li span {
                display: inline-block;
            }
        }



        @media (max-width: 767.98px) {
            .row {
                flex-direction: column-reverse;
            }

            .col-lg-3,
            .col-lg-9 {
                width: 100%;
                max-width: 100%;
            }

            #calendar-container {
                overflow-x: auto;
            }

            #calendar {
                min-width: 600px;
            }

            .fc-toolbar {
                flex-direction: column !important;
                align-items: flex-start !important;
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
    require("menu_padrão.php");
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
                            <i class="zmdi zmdi-check-all"></i>
                            <span class="d-none d-sm-inline"> Publicar</span>
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
                        <div class="mb-3">
                            <label>Público-alvo:</label>
                            <select id="publico-alvo" class="form-control form-control-sm">
                                <option value="all">Todos</option>
                                <option value="todos">Todos os usuários</option>
                                <option value="professores">Somente Professores</option>
                                <option value="alunos">Somente Alunos</option>
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
                                        <label>Público-alvo</label>
                                        <select class="form-control" id="evento-publico" required>
                                            <option value="todos">Todos os usuários</option>
                                            <option value="professores">Somente Professores</option>
                                            <option value="alunos">Somente Alunos</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Início</label>
                                        <input type="datetime-local" class="form-control" id="evento-inicio" required>
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
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
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
                                        <input type="color" class="form-control form-control-sm p-1" id="novo-tipo-cor"
                                            value="#6c757d">
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
        let currentEvent = null;
        let editandoTipo = null;

        var tiposEventos = [];

        function fetchTiposEventos(callback){
            $.getJSON('../includes/ajax/calendario/tipos/listar_tipos.php')
                .done(function(res){
                    if (res && res.success && res.data){ tiposEventos = res.data; }
                    atualizarSelectsTipos();
                    if (typeof callback === 'function') callback();
                })
                .fail(function(){
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

        function atualizarSelectsTipos(selecionadoEvento, selecionadoFiltro){
            var $selEvento = $('#evento-tipo');
            var $selFiltro = $('#tipo-evento');

            if ($selEvento.length){
                var valEvento = selecionadoEvento || $selEvento.val();
                $selEvento.empty();
                for (var i = 0; i < tiposEventos.length; i++){
                    var t = tiposEventos[i];
                    $selEvento.append($('<option></option>').val(t.nome).text(t.label));
                }
                if (valEvento){ $selEvento.val(valEvento); }
            }

            if ($selFiltro.length){
                var valFiltro = (typeof selecionadoFiltro !== 'undefined' && selecionadoFiltro !== null) ? selecionadoFiltro : ($selFiltro.val() || 'all');
                $selFiltro.empty();
                $selFiltro.append('<option value="all">Todos</option>');
                for (var j = 0; j < tiposEventos.length; j++){
                    var tt = tiposEventos[j];
                    $selFiltro.append($('<option></option>').val(tt.nome).text(tt.label + 's'));
                }
                $selFiltro.val(valFiltro);
            }
        }

        function carregarTiposEventos() {
            const tbody = $('#tipos-eventos-body');
            tbody.empty();

            for (var i = 0; i < tiposEventos.length; i++){
                var tipo = tiposEventos[i];
                var disabled = tipo.is_default ? 'disabled title="Tipo padrão"' : '';
                var row = '' +
                    '<tr>' +
                    '  <td>' + (tipo.label || tipo.nome) + '</td>' +
                    '  <td><span class="badge" style="background-color: ' + (tipo.cor || '#6c757d') + '">&nbsp;&nbsp;&nbsp;</span></td>' +
                    '  <td>' +
                    '    <button class="btn btn-sm btn-outline-primary editar-tipo" data-nome="' + tipo.nome + '" ' + disabled + '>' +
                    '      <i class="zmdi zmdi-edit"></i>' +
                    '    </button> ' +
                    '    <button class="btn btn-sm btn-outline-danger excluir-tipo" data-nome="' + tipo.nome + '" ' + disabled + '>' +
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
            atualizarSelectsTipos();
        }

        $(document).ready(function () {
            // Carregar tipos antes de inicializar o calendário
            fetchTiposEventos(initCalendar);
            $('.toggle-menu').click(function () {
                $('#wrapper').toggleClass('menu-toggled');
            });

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
                events: function(fetchInfo, successCallback, failureCallback) {
                    const params = {
                        start: fetchInfo.startStr,
                        end: fetchInfo.endStr,
                        tipo: $('#tipo-evento').val(),
                        publico: $('#publico-alvo').val()
                    };
                    $.getJSON('../includes/ajax/calendario/listar_eventos.php', params)
                        .done(function(res){
                            if (res.success) successCallback(res.data || []);
                            else failureCallback(res.message || 'Falha ao carregar eventos');
                        })
                        .fail(function(xhr){
                            failureCallback(xhr.statusText || 'Erro ao carregar eventos');
                        });
                },
                dateClick: function (info) {
                    currentEvent = null;
                    $('#form-evento')[0].reset();
                    $('#evento-inicio').val(info.dateStr + 'T08:00');
                    $('#evento-publico').val('todos');
                    $('#btn-excluir-evento').hide();
                    $('#eventoModal').modal('show');
                },
                eventClick: function (info) {
                    currentEvent = info.event;
                    $('#evento-titulo').val(info.event.title);
                    atualizarSelectsTipos(info.event.extendedProps.tipo || 'evento');
                    $('#evento-descricao').val(info.event.extendedProps.description || '');
                    $('#evento-publico').val(info.event.extendedProps.publico || 'todos');

                    $('#evento-inicio').val(info.event.start.toISOString().slice(0, 16));
                    $('#evento-fim').val(info.event.end ? info.event.end.toISOString().slice(0, 16) : '');
                    $('#btn-excluir-evento').show();
                    $('#eventoModal').modal('show');
                    info.jsEvent.preventDefault();
                },
                eventDrop: function(info){ atualizarDatasEvento(info.event); },
                eventResize: function(info){ atualizarDatasEvento(info.event); }
            });

            calendar.render();
            function ajustarVisualizacaoCalendario() {
                const calendarApi = calendar.getCalendar();
                const width = window.innerWidth;

                if (width < 768) {
                    calendarApi.changeView('listMonth');
                } else {
                    calendarApi.changeView('dayGridMonth');
                }

                calendarApi.updateSize();
            }

            // Chamar na inicialização e no redimensionamento
            $(document).ready(function () {
                ajustarVisualizacaoCalendario();
                $(window).on('resize', ajustarVisualizacaoCalendario);
            });

            // Responsividade e redimensionamento
            $('.toggle-menu').click(function(){ setTimeout(function(){ calendar.updateSize(); }, 300); });
            $(window).resize(function(){ calendar.updateSize(); });

            // Filtros: recarregar do backend
            $('#tipo-evento, #publico-alvo').change(function () {
                calendar.refetchEvents();
            });

            // Legendas
            $('.form-check-input').change(function () {
                const tipo = $(this).attr('id').replace('legenda-', '');
                const ativo = $(this).is(':checked');
                const map = {
                    'feriados': 'feriado',
                    'reunioes': 'reuniao',
                    'eventos': 'evento',
                    'conselhos': 'conselho',
                    'formacoes': 'formacao'
                };
                const tipoFiltrado = map[tipo] || tipo;
                calendar.getEvents().forEach(event => {
                    if (event.extendedProps.tipo === tipoFiltrado) {
                        event.setProp('display', ativo ? 'auto' : 'none');
                    }
                });
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
                const title = $('#evento-titulo').val();
                const tipo = $('#evento-tipo').val();
                const start = $('#evento-inicio').val();
                const end = $('#evento-fim').val();
                const description = $('#evento-descricao').val();
                const publico = $('#evento-publico').val();

                if (!title) return alert('Título é obrigatório.');

                const payload = {
                    id: currentEvent ? currentEvent.id : undefined,
                    title: title,
                    tipo: tipo,
                    descricao: description,
                    inicio: start,
                    fim: end || null,
                    publico: publico
                };

                $.ajax({
                    url: '../includes/ajax/calendario/salvar_evento.php',
                    method: 'POST',
                    contentType: 'application/json; charset=utf-8',
                    data: JSON.stringify(payload),
                    dataType: 'json'
                }).done(function(res){
                    if (res.success){
                        $('#eventoModal').modal('hide');
                        calendar.refetchEvents();
                    } else {
                        alert(res.message || 'Falha ao salvar evento');
                    }
                }).fail(function(xhr){
                    alert('Erro ao salvar: ' + (xhr.responseText || xhr.statusText));
                });
            });

            $('#btn-excluir-evento').click(function () {
                if (currentEvent && confirm('Deseja excluir este evento?')) {
                    $.post('../includes/ajax/calendario/excluir_evento.php', { id: currentEvent.id })
                        .done(function(res){
                            try { res = typeof res === 'string' ? JSON.parse(res) : res; } catch(e) {}
                            if (res && res.success) {
                                $('#eventoModal').modal('hide');
                                calendar.refetchEvents();
                            } else {
                                alert((res && res.message) || 'Falha ao excluir');
                            }
                        }).fail(function(xhr){
                            alert('Erro ao excluir: ' + (xhr.responseText || xhr.statusText));
                        });
                }
            });

            function atualizarDatasEvento(event){
                const payload = {
                    id: event.id,
                    title: event.title,
                    tipo: event.extendedProps.tipo || 'evento',
                    descricao: event.extendedProps.description || '',
                    inicio: event.startStr,
                    fim: event.endStr,
                    publico: event.extendedProps.publico || 'todos'
                };
                $.ajax({ url: '../includes/ajax/calendario/salvar_evento.php', method: 'POST', contentType:'application/json', data: JSON.stringify(payload) })
                    .done(function(res){ if (!(res && res.success)) alert('Falha ao atualizar evento.'); })
                    .fail(function(){ alert('Erro ao atualizar evento.'); });
            }

            // Importar/Exportar removidos a pedido — sem handlers

            $('#btn-publicar-calendario').click(function () {
                const publico = $('#publico-alvo').val();
                const tipo = $('#tipo-evento').val();
                const view = calendar.view;
                const start = view.activeStart.toISOString().slice(0,10);
                const end = view.activeEnd.toISOString().slice(0,10);

                if (!publico || !['todos','professores','alunos'].includes(publico)) {
                    alert('Selecione um público-alvo válido (Todos, Professores ou Alunos).');
                    return;
                }

                if (!confirm(`Publicar eventos de ${start} a ${end} para: ${publico}?`)) return;

                $.post('../includes/ajax/calendario/publicar.php', { publico, start, end, tipo })
                    .done(function(res){
                        try { res = typeof res === 'string' ? JSON.parse(res) : res; } catch(e) {}
                        if (res && res.success) {
                            alert('Calendário publicado com sucesso!');
                            calendar.refetchEvents();
                        } else {
                            alert((res && res.message) || 'Falha ao publicar.');
                        }
                    })
                    .fail(function(xhr){
                        alert('Erro ao publicar: ' + (xhr.responseText || xhr.statusText));
                    });
            });
            }
        });

        // Editar/Excluir tipo
        $(document).on('click', '.editar-tipo', function () {
            var tipo = $(this).data('nome');
            var tipoInfo = null;
            for (var i = 0; i < tiposEventos.length; i++){
                if (tiposEventos[i].nome === tipo){ tipoInfo = tiposEventos[i]; break; }
            }
            if (tipoInfo && !tipoInfo.is_default) {
                $('#novo-tipo-nome').val(tipoInfo.label || tipoInfo.nome);
                $('#novo-tipo-cor').val(tipoInfo.cor || '#6c757d');
                editandoTipo = tipo;
                $('#btn-adicionar-tipo').text('Atualizar');
            }
        });

        $(document).on('click', '.excluir-tipo', function () {
            var nome = $(this).data('nome');
            var tipoInfo = null;
            for (var i = 0; i < tiposEventos.length; i++){
                if (tiposEventos[i].nome === nome){ tipoInfo = tiposEventos[i]; break; }
            }
            if (tipoInfo && tipoInfo.is_default){ alert('Tipos padrão não podem ser removidos.'); return; }
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