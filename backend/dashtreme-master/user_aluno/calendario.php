<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Calendário Acadêmico</title>
    <link href="../assets/css/pace.min.css" rel="stylesheet" />
    <script src="../assets/js/pace.min.js"></script>
    <link rel="icon" href="../assets/images/favicon.ico" type="image/x-icon">
    <link href="../assets/plugins/simplebar/css/simplebar.css" rel="stylesheet" />
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet" />
    <link href="../assets/css/animate.css" rel="stylesheet" />
    <link href="../assets/css/icons.css" rel="stylesheet" />
    <link href="../assets/css/sidebar-menu.css" rel="stylesheet" />
    <link href="../assets/css/app-style.css" rel="stylesheet" />
    <link href="style.css" rel="stylesheet" />

    <!-- FullCalendar CSS -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />

    <!-- CSS Customizado -->
    <style>
        .fc .fc-daygrid-day-frame {
            min-height: 0 !important;
            padding: 2px !important;
        }

        .fc .fc-daygrid-day-top {
            flex-direction: row !important;
            padding: 2px !important;
        }

        .fc .fc-daygrid-day-number {
            font-size: 0.8em;
            padding: 2px !important;
        }

        .fc .fc-daygrid-event {
            margin: 1px 0 !important;
            font-size: 0.75em !important;
            padding: 0 3px !important;
            border-radius: 2px !important;
        }

        .fc .fc-col-header-cell {
            padding: 4px 0 !important;
        }

        .fc .fc-col-header-cell-cushion {
            font-size: 0.8em;
            font-weight: normal;
            padding: 2px !important;
        }

        .fc .fc-toolbar-title {
            font-size: 1.2em !important;
        }

        /* Estilo para eventos allday */
        .fc .fc-daygrid-event-harness {
            margin-top: 1px !important;
        }

        /* Espaçamento reduzido */
        .fc .fc-view-harness {
            min-height: 400px !important;
        }

        /* Estilos dos eventos por tipo */
        .fc-event.aula {
            background-color: #007bff;
            border-color: #006fe6;
        }

        .fc-event.prova {
            background-color: #dc3545;
            border-color: #d32536;
        }

        .fc-event.feriado {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #212529;
        }

        .fc-event.reuniao {
            background-color: #28a745;
            border-color: #23923d;
        }

        /* Painel de filtros */
        .card-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: #2c5f9e;
            text-transform: uppercase;
        }

        .form-control-sm {
            height: calc(1.5em + 0.5rem + 2px);
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }

        /* Container principal do calendário */
        #calendar-container {
            transition: all 0.3s;
        }

        /* Responsividade */
        @media (max-width: 992px) {
            .fc-toolbar {
                flex-direction: column !important;
                align-items: flex-start;
                gap: 8px;
            }

            .fc-toolbar-chunk {
                margin-bottom: 5px;
            }

            .fc .fc-toolbar-title {
                font-size: 1.1rem !important;
                white-space: normal !important;
            }

            .fc-event {
                font-size: 0.7rem !important;
                padding: 1px 2px !important;
            }

            .fc .fc-daygrid-day-number {
                font-size: 0.7em;
            }

            .fc .fc-col-header-cell-cushion {
                font-size: 0.7em;
            }

            .col-lg-3 {
                margin-bottom: 20px;
            }

            #calendar {
                width: 100% !important;
            }
        }

        #eventoModal .modal-content {
            background-color: #fff;
            color: #333;
        }

        #eventoModal .modal-header {
            border-bottom: 1px solid #e0e0e0;
        }

        #eventoModal .modal-body {
            padding: 20px;
        }

        #eventoModal .form-control {
            background-color: #fff;
            color: #333;
            border: 1px solid #ced4da;
        }

        #eventoModal label {
            color: #495057;
            font-weight: 500;
        }

        #eventoModal .modal-footer {
            border-top: 1px solid #e0e0e0;
        }

        .modal-body * {
            color: #333 !important;
        }

        .navbar {
            background-color: rgba(0, 0, 0, 0.2) !important;
            backdrop-filter: blur(10px);
        }

        .footer {
            position: static !important;
            margin-top: auto;
        }
    </style>
</head>

<body class="bg-theme bg-theme1">
    <?php
    require("menu_padrao.php");
    ?>
    <div class="clearfix"></div>

    <!-- Conteúdo da Página -->
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row mt-3">
                <!-- Painel de Controle -->
                <div class="col-lg-3 col-md-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title mb-3">FILTROS</h6>
                            <div class="mb-3">
                                <label class="d-block small mb-1">TURMA:</label>
                                <select id="turma-select" class="form-control form-control-sm">
                                    <option value="all">Todas as Turmas</option>
                                    <option value="A">Turma A</option>
                                    <option value="B">Turma B</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="d-block small mb-1">TIPO DE EVENTO:</label>
                                <select id="tipo-evento" class="form-control form-control-sm">
                                    <option value="all">Todos</option>
                                    <option value="aula">Aulas</option>
                                    <option value="prova">Provas</option>
                                    <option value="feriado">Feriados</option>
                                    <option value="reuniao">Reuniões</option>
                                </select>
                            </div>
                            <button id="btn-adicionar" class="btn btn-sm btn-primary btn-block">ADICIONAR
                                EVENTO</button>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-body">
                            <h6 class="card-title mb-3">LEGENDA</h6>
                            <div class="form-check small mb-2">
                                <input class="form-check-input" type="checkbox" checked id="legenda-aulas">
                                <label class="form-check-label" for="legenda-aulas">Aulas Normais</label>
                            </div>
                            <div class="form-check small mb-2">
                                <input class="form-check-input" type="checkbox" checked id="legenda-provas">
                                <label class="form-check-label" for="legenda-provas">Provas</label>
                            </div>
                            <div class="form-check small mb-2">
                                <input class="form-check-input" type="checkbox" checked id="legenda-feriados">
                                <label class="form-check-label" for="legenda-feriados">Feriados</label>
                            </div>
                            <div class="form-check small">
                                <input class="form-check-input" type="checkbox" checked id="legenda-reunioes">
                                <label class="form-check-label" for="legenda-reunioes">Reuniões</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Calendário -->
                <div class="col-lg-9 col-md-12" id="calendar-container">
                    <div class="card">
                        <div class="card-body p-2">
                            <div class="table-responsive">
                                <div id='calendar'></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para Adicionar Evento -->
        <div class="modal fade" id="eventoModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Adicionar Novo Evento</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="form-evento">
                            <div class="form-group">
                                <label>Título do Evento</label>
                                <input type="text" class="form-control" id="evento-titulo" required>
                            </div>
                            <div class="form-group">
                                <label>Tipo de Evento</label>
                                <select class="form-control" id="evento-tipo" required>
                                    <option value="aula">Aula</option>
                                    <option value="prova">Prova</option>
                                    <option value="feriado">Feriado</option>
                                    <option value="reuniao">Reunião</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Turma</label>
                                <select class="form-control" id="evento-turma">
                                    <option value="all">Todas as Turmas</option>
                                    <option value="A">Turma A</option>
                                    <option value="B">Turma B</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Data de Início</label>
                                <input type="datetime-local" class="form-control" id="evento-inicio" required>
                            </div>
                            <div class="form-group">
                                <label>Data de Término</label>
                                <input type="datetime-local" class="form-control" id="evento-fim">
                            </div>
                            <div class="form-group">
                                <label>Descrição</label>
                                <textarea class="form-control" id="evento-descricao" rows="3"></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" id="btn-salvar-evento">Salvar Evento</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</body>

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
    $(document).ready(function () {
        // Inicializa o calendário
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'pt-br',
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'title',
                center: '',
                right: 'prev,next today'
            },
            dayMaxEvents: 3, // Limita a quantidade de eventos mostrados por dia
            dayPopoverFormat: { month: 'short', day: 'numeric' },
            eventDisplay: 'list-item', // Exibe eventos como lista
            views: {
                dayGridMonth: {
                    dayHeaderFormat: { weekday: 'short' }, // Só mostra a abreviação do dia
                    dayMaxEventRows: 4 // Quantidade máxima de linhas de eventos
                }
            },
            navLinks: true,
            editable: true,
            selectable: true,
            businessHours: {
                daysOfWeek: [1, 2, 3, 4, 5], // Segunda a sexta
                startTime: '07:00',
                endTime: '18:00'
            },
            eventDidMount: function (info) {
                // Cores baseadas no tipo vindo do backend
                const tipo = (info.event.extendedProps.tipo || '').toLowerCase();
                const cores = {
                    'feriado': '#ffc107',
                    'reuniao': '#28a745',
                    'evento': '#6f42c1',
                    'conselho': '#17a2b8',
                    'formacao': '#6610f2'
                };
                const cor = cores[tipo] || '#6c757d';
                info.el.style.backgroundColor = cor;
                info.el.style.borderColor = cor;

                // Tooltip para descrição
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
                const params = {
                    start: fetchInfo.startStr,
                    end: fetchInfo.endStr
                    // sem 'publico' para incluir 'todos' + 'alunos' via sessão
                };
                $.getJSON('../includes/ajax/calendario/listar_eventos.php', params)
                    .done(function(res){
                        if (res.success) successCallback(res.data || []);
                        else failureCallback(res.message || 'Falha ao carregar eventos');
                    })
                    .fail(function(xhr){ failureCallback(xhr.statusText || 'Erro ao carregar eventos'); });
            },
            dateClick: function (info) {
                // Preenche a data inicial quando clica em um dia
                $('#evento-inicio').val(info.dateStr + 'T08:00');
                $('#eventoModal').modal('show');
            },
            eventClick: function (info) {
                // Preenche o modal para edição quando clica em um evento
                $('#evento-titulo').val(info.event.title);
                $('#evento-tipo').val(info.event.extendedProps.tipo || 'aula');
                $('#evento-turma').val(info.event.extendedProps.turma || 'all');
                $('#evento-descricao').val(info.event.extendedProps.description || '');

                // Formata as datas para o input datetime-local
                const start = new Date(info.event.start);
                const startStr = start.toISOString().slice(0, 16);
                $('#evento-inicio').val(startStr);

                if (info.event.end) {
                    const end = new Date(info.event.end);
                    const endStr = end.toISOString().slice(0, 16);
                    $('#evento-fim').val(endStr);
                } else {
                    $('#evento-fim').val('');
                }

                $('#eventoModal').modal('show');

                info.jsEvent.preventDefault();
            }
        });

        calendar.render();

        // Adiciona listener para redimensionar o calendário quando o menu é aberto/fechado
        $('.toggle-menu').click(function () {
            setTimeout(function () {
                calendar.updateSize();
            }, 300);
        });

        // Adiciona listener para redimensionamento da janela
        $(window).resize(function () {
            calendar.updateSize();
        });

        // Filtros
        $('#turma-select, #tipo-evento').change(function () {
            var turma = $('#turma-select').val();
            var tipo = $('#tipo-evento').val();

            calendar.getEvents().forEach(function (event) {
                var showEvent = true;

                if (turma !== 'all' && event.extendedProps.turma !== turma && event.extendedProps.turma !== 'all') {
                    showEvent = false;
                }

                if (tipo !== 'all' && event.extendedProps.tipo !== tipo) {
                    showEvent = false;
                }

                event.setProp('display', showEvent ? 'auto' : 'none');
            });
        });

        // Filtros por legenda
        $('.form-check-input').change(function () {
            var tipo = $(this).attr('id').replace('legenda-', '');
            var isChecked = $(this).is(':checked');

            // Mapeia os IDs dos checkboxes para os tipos reais usados nos eventos
            var tipoMap = {
                'aulas': 'aula',
                'provas': 'prova',
                'feriados': 'feriado',
                'reunioes': 'reuniao'
            };

            var tipoEvento = tipoMap[tipo] || tipo;

            calendar.getEvents().forEach(function (event) {
                if (event.extendedProps.tipo === tipoEvento) {
                    event.setProp('display', isChecked ? 'auto' : 'none');
                }
            });
        });

        // Botão para adicionar evento
        $('#btn-adicionar').click(function () {
            $('#form-evento')[0].reset();
            $('#evento-inicio').val(new Date().toISOString().slice(0, 16));
            $('#eventoModal').modal('show');
        });

        // Salvar evento
        $('#btn-salvar-evento').click(function () {
            const title = $('#evento-titulo').val();
            const tipo = $('#evento-tipo').val();
            const turma = $('#evento-turma').val();
            const start = $('#evento-inicio').val();
            const end = $('#evento-fim').val();
            const description = $('#evento-descricao').val();

            if (!title) {
                alert('Por favor, insira um título para o evento');
                return;
            }

            // Cores baseadas no tipo de evento
            let color;
            switch (tipo) {
                case 'aula': color = '#007bff'; break;
                case 'prova': color = '#dc3545'; break;
                case 'feriado': color = '#ffc107'; break;
                case 'reuniao': color = '#28a745'; break;
                default: color = '#6c757d';
            }

            calendar.addEvent({
                title: title,
                start: start,
                end: end || null,
                color: color,
                extendedProps: {
                    tipo: tipo,
                    turma: turma,
                    description: description
                }
            });

            $('#eventoModal').modal('hide');
        });
    });
</script>
</body>

</html>