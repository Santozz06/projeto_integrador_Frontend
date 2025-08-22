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
                padding-top: 60px;
            }

            #sidebar-wrapper {
                width: 70px;
                overflow: hidden;
            }

            .brand-logo h5,
            .sidebar-menu li span {
                display: none;
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
                            <button class="btn btn-primary" id="btn-importar-calendario">
                                <i class="zmdi zmdi-cloud-upload"></i>
                                <span class="d-none d-sm-inline"> Importar</span>
                            </button>
                            <button class="btn btn-info" id="btn-exportar-calendario">
                                <i class="zmdi zmdi-cloud-download"></i>
                                <span class="d-none d-sm-inline"> Exportar</span>
                            </button>
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
                                    <option value="feriado">Feriados</option>
                                    <option value="reuniao">Reuniões</option>
                                    <option value="evento">Eventos</option>
                                    <option value="conselho">Conselhos</option>
                                    <option value="formacao">Formações</option>
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
                                            <select class="form-control" id="evento-tipo" required>
                                                <option value="feriado">Feriado</option>
                                                <option value="reuniao">Reunião</option>
                                                <option value="evento">Evento Institucional</option>
                                                <option value="conselho">Conselho de Classe</option>
                                                <option value="formacao">Formação Pedagógica</option>
                                            </select>
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
                    <<div class="modal fade" id="tiposModal" tabindex="-1" role="dialog">
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

                <footer class="footer text-center py-3">
                    <div class="container">
                        <span>© 2023 Dashboard Acadêmico</span>
                    </div>
                </footer>
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
        <script src="botaoSair.js"></script>
        <!-- JS Customizado -->
        <script>
            // Escopo global
            let currentEvent = null;
            let editandoTipo = null;

            const tiposEventos = [
                { nome: 'feriado', cor: '#ffc107', label: 'Feriado' },
                { nome: 'reuniao', cor: '#28a745', label: 'Reunião' },
                { nome: 'evento', cor: '#6f42c1', label: 'Evento Institucional' },
                { nome: 'conselho', cor: '#17a2b8', label: 'Conselho de Classe' },
                { nome: 'formacao', cor: '#6610f2', label: 'Formação Pedagógica' }
            ];

            function carregarTiposEventos() {
                const tbody = $('#tipos-eventos-body');
                tbody.empty();

                tiposEventos.forEach(tipo => {
                    const tr = `
                <tr>
                    <td>${tipo.label}</td>
                    <td><span class="badge" style="background-color: ${tipo.cor}">&nbsp;&nbsp;&nbsp;</span></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary editar-tipo" data-nome="${tipo.nome}">
                            <i class="zmdi zmdi-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger excluir-tipo" data-nome="${tipo.nome}">
                            <i class="zmdi zmdi-delete"></i>
                        </button>
                    </td>
                </tr>
            `;
                    tbody.append(tr);
                });

                // Limpa campos após carregar
                $('#novo-tipo-nome').val('');
                $('#novo-tipo-cor').val('#6c757d');
                editandoTipo = null;
                $('#btn-adicionar-tipo').text('Adicionar');
            }

            $(document).ready(function () {
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
                        const tipo = info.event.extendedProps.tipo;
                        const tipoConfig = tiposEventos.find(t => t.nome === tipo);
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
                    }
                    , events: [
                        {
                            title: 'Feriado Municipal',
                            start: new Date(),
                            extendedProps: {
                                tipo: 'feriado',
                                description: 'Aniversário da cidade - Ponto facultativo'
                            }
                        },
                        {
                            title: 'Reunião Pedagógica',
                            start: new Date(new Date().setDate(new Date().getDate() + 7)),
                            end: new Date(new Date().setDate(new Date().getDate() + 7.5)),
                            extendedProps: {
                                tipo: 'reuniao',
                                description: 'Planejamento trimestral - Sala dos professores'
                            }
                        },
                        {
                            title: 'Feira Cultural',
                            start: new Date(new Date().setDate(new Date().getDate() + 14)),
                            extendedProps: {
                                tipo: 'evento',
                                description: 'Evento aberto à comunidade - Quadra poliesportiva'
                            }
                        },
                        {
                            title: 'Conselho de Classe',
                            start: new Date(new Date().setDate(new Date().getDate() + 21)),
                            extendedProps: {
                                tipo: 'conselho',
                                description: 'Avaliação do 1º bimestre - Sala de reuniões'
                            }
                        }
                    ],
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
                        $('#evento-tipo').val(info.event.extendedProps.tipo || 'evento');
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
                $('.toggle-menu').click(() => setTimeout(() => calendar.updateSize(), 300));
                $(window).resize(() => calendar.updateSize());

                // Filtro por tipo
                $('#tipo-evento').change(function () {
                    const tipo = $(this).val();
                    calendar.getEvents().forEach(event => {
                        const show = tipo === 'all' || event.extendedProps.tipo === tipo;
                        event.setProp('display', show ? 'auto' : 'none');
                    });
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
                    $('#evento-inicio').val(new Date().toISOString().slice(0, 16));
                    $('#btn-excluir-evento').hide();
                    $('#eventoModal').modal('show');
                });

                $('#btn-gerenciar-tipos').click(function () {
                    carregarTiposEventos();
                    $('#tiposModal').modal('show');
                });

                $('#btn-adicionar-tipo').click(function () {
                    const nomeDigitado = $('#novo-tipo-nome').val().trim();
                    const cor = $('#novo-tipo-cor').val();

                    if (!nomeDigitado) return alert('Informe o nome do tipo.');

                    const nome = nomeDigitado.toLowerCase().replace(/\s+/g, '-');

                    if (editandoTipo) {
                        // Atualizar tipo existente
                        const index = tiposEventos.findIndex(t => t.nome === editandoTipo);
                        if (index !== -1) {
                            tiposEventos[index] = {
                                nome: nome,
                                cor: cor,
                                label: nomeDigitado
                            };
                            alert('Tipo atualizado com sucesso!');
                        }
                        editandoTipo = null;
                    } else {
                        // Adicionar novo tipo
                        if (tiposEventos.some(t => t.nome === nome)) {
                            alert('Esse tipo já existe.');
                            return;
                        }
                        tiposEventos.push({
                            nome: nome,
                            cor: cor,
                            label: nomeDigitado
                        });
                        alert('Tipo adicionado com sucesso!');
                    }

                    carregarTiposEventos();
                });

                $('#btn-salvar-evento').click(function () {
                    const title = $('#evento-titulo').val();
                    const tipo = $('#evento-tipo').val();
                    const start = $('#evento-inicio').val();
                    const end = $('#evento-fim').val();
                    const description = $('#evento-descricao').val();
                    const notificar = $('#evento-notificar').is(':checked');

                    if (!title) return alert('Título é obrigatório.');

                    const tipoEvento = tiposEventos.find(t => t.nome === tipo);
                    const color = tipoEvento ? tipoEvento.cor : '#6c757d';

                    const eventData = {
                        title,
                        start,
                        end: end || null,
                        color,
                        extendedProps: {
                            tipo,
                            description
                        }
                    };

                    if (currentEvent) {
                        currentEvent.setProp('title', title);
                        currentEvent.setStart(start);
                        currentEvent.setEnd(end || null);
                        currentEvent.setProp('color', color);
                        currentEvent.setExtendedProp('tipo', tipo);
                        currentEvent.setExtendedProp('description', description);
                    } else {
                        calendar.addEvent(eventData);
                    }

                    if (notificar) {
                        alert('Usuários serão notificados!');
                    }

                    $('#eventoModal').modal('hide');
                });

                $('#btn-excluir-evento').click(function () {
                    if (currentEvent && confirm('Deseja excluir este evento?')) {
                        currentEvent.remove();
                        $('#eventoModal').modal('hide');
                    }
                });

                $('#btn-importar-calendario').click(function () {
                    alert('Funcionalidade de importação será implementada aqui');
                });

                $('#btn-exportar-calendario').click(function () {
                    alert('Funcionalidade de exportação será implementada aqui');
                });

                $('#btn-publicar-calendario').click(function () {
                    if (confirm('Deseja publicar as alterações?')) {
                        alert('Calendário publicado com sucesso!');
                    }
                });
            });

            // Editar/Excluir tipo
            $(document).on('click', '.editar-tipo', function () {
                const tipo = $(this).data('nome');
                const tipoInfo = tiposEventos.find(t => t.nome === tipo);
                if (tipoInfo) {
                    $('#novo-tipo-nome').val(tipoInfo.label);
                    $('#novo-tipo-cor').val(tipoInfo.cor);
                    editandoTipo = tipo;
                    $('#btn-adicionar-tipo').text('Atualizar');
                }
            });

            $(document).on('click', '.excluir-tipo', function () {
                const tipo = $(this).data('nome');
                if (confirm('Deseja excluir o tipo "' + tipo + '"?')) {
                    const index = tiposEventos.findIndex(t => t.nome === tipo);
                    if (index !== -1) {
                        tiposEventos.splice(index, 1);
                        carregarTiposEventos();
                    }
                }
            });
        </script>

</body>

</html>