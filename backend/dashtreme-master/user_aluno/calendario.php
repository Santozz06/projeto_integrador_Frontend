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
    <link rel="stylesheet" href="../css/style.css?v=<?php echo time(); ?>">

    <!-- FullCalendar CSS -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />

<body class="bg-theme bg-theme1 user_aluno_calendario">
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
                                <label class="d-block small mb-1">ANO LETIVO:</label>
                                <select id="ano-select" class="form-control form-control-sm">
                                    <option value="">Carregando...</option>
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
                            <!-- Aluno não cria eventos -->
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
            editable: false,
            selectable: false,
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
                    end: fetchInfo.endStr,
                    ano: $('#ano-select').val() || ''
                    // sem 'publico' para incluir 'todos' + 'alunos' via sessão
                };
                $.getJSON('../includes/ajax/calendario/listar_eventos.php', params)
                    .done(function(res){
                        if (res.success) successCallback(res.data || []);
                        else failureCallback(res.message || 'Falha ao carregar eventos');
                    })
                    .fail(function(xhr){ failureCallback(xhr.statusText || 'Erro ao carregar eventos'); });
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

        // Filtros (Ano e Tipo)
        $('#ano-select, #tipo-evento').change(function () {
            var ano = $('#ano-select').val();
            var tipo = $('#tipo-evento').val();

            calendar.refetchEvents();

            calendar.getEvents().forEach(function (event) {
                var showEvent = true;
                if (tipo !== 'all' && (event.extendedProps.tipo || '') !== tipo) {
                    showEvent = false;
                }
                if (ano && (event.extendedProps.ano_letivo && String(event.extendedProps.ano_letivo) !== String(ano))) {
                    // Mantém eventos globais (ano_letivo null)
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

        // Carregar anos do aluno
        (async function carregarAnos(){
            try {
                const res = await $.getJSON('../includes/ajax/aluno/anos_matriculas.php');
                const $sel = $('#ano-select');
                $sel.empty();
                if (res.success && Array.isArray(res.anos) && res.anos.length) {
                    $sel.append('<option value="">Todos</option>');
                    res.anos.forEach(a => $sel.append(`<option value="${a.ano}">${a.ano}${a.serie ? ' - ' + a.serie : ''}</option>`));
                    const anoAtual = new Date().getFullYear();
                    if ($sel.find(`option[value="${anoAtual}"]`).length) $sel.val(anoAtual);
                } else {
                    $sel.append('<option value="">Todos</option>');
                }
            } catch (e) {
                $('#ano-select').empty().append('<option value="">Todos</option>');
            }
        })();
    });
</script>
</body>

</html>