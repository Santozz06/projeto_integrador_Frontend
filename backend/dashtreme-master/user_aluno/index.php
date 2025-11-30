<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="SAS" />
  <meta name="author" content="" />
  <title>SAS</title>
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
</head>

<body class="bg-theme bg-theme1 user_aluno_index">

  <!-- Start wrapper-->
  <div id="wrapper">
    <?php
    require("menu_padrao.php");
    ?>

    <div class="clearfix"></div>

    <div class="content-wrapper">
      <div class="container-fluid">

        <!-- Bloco de Frequência/Informações Acadêmicas -->
        <div class="row">
          <div class="col-12">
            <div class="welcome-message">
              <h4 class="welcome-title">Bem-vindo, Aluno!</h4>
              <p class="welcome-text">Aqui você pode acompanhar sua frequência, notas, eventos e muito mais.</p>

              <div class="quick-stats">
                <div class="stat-item">
                  <div class="stat-label">Ano Letivo</div>
                  <div class="stat-value" id="freq-ano">-</div>
                </div>
                <div class="stat-item">
                  <div class="stat-label">Turma</div>
                  <div class="stat-value" id="freq-turma">-</div>
                </div>
                <div class="stat-item">
                  <div class="stat-label">Matrícula</div>
                  <div class="stat-value" id="freq-mat">-</div>
                </div>
                <div class="stat-item">
                  <div class="stat-label">Frequência</div>
                  <div class="stat-value" id="freq-perc">-</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Bloco de Eventos Próximos -->
        <div class="row mt-4">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h5>Eventos próximos</h5>
              </div>
              <div class="card-body" id="eventos-card">
                <div class="alert alert-light" id="eventos-empty">Carregando eventos...</div>
                <div id="eventos-list"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Bloco de Aulas -->
        <div class="row mt-4">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h5>Aulas</h5>
              </div>
              <div class="card-body" id="aulas-card">
                <div class="table-responsive" id="aulas-container">
                  <div class="alert alert-light" id="aulas-empty">Carregando aulas...</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="overlay toggle-menu"></div>

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
  <!-- loader scripts -->
  <!-- Custom scripts -->
  <script src="../assets/js/app-script.js"></script>

  <script>
    (function () {
      // Eventos próximos (hoje + 60 dias)
      function carregarEventos() {
        var hoje = new Date();
        var start = hoje;
        var end = new Date(); end.setDate(hoje.getDate() + 60);
        function toISO(d) { return d.toISOString().slice(0, 10); }
        var url = '../includes/ajax/calendario/listar_eventos.php?start=' + toISO(start) + '&end=' + toISO(end);
        $.getJSON(url)
          .done(function (res) {
            var data = (res && res.success && Array.isArray(res.data)) ? res.data : [];
            if (!data.length) {
              $('#eventos-empty').text('Nenhum evento próximo').show();
              $('#eventos-list').empty();
              return;
            }
            $('#eventos-empty').hide();
            var html = '';
            for (var i = 0; i < data.length; i++) {
              var ev = data[i];
              var dt = (ev.start || ev.Data_Inicio || '').split('T')[0] || ev.start;
              var p = (dt || '').split('-');
              var dataBR = (p.length === 3) ? (p[2] + '/' + p[1] + '/' + p[0]) : dt;
              var titulo = ev.title || ev.Nome_Evento || '';
              var tipo = (ev.extendedProps && ev.extendedProps.tipo) || ev.Tipo_Evento || '';
              html += '<div class="mb-2"><strong>' + dataBR + '</strong> - ' + titulo + (tipo ? ' <span class="badge badge-info">' + tipo + '</span>' : '') + '</div>';
            }
            $('#eventos-list').html(html);
          })
          .fail(function (xhr, status, error) {
            $('#eventos-empty').text('Não foi possível carregar os eventos').show();
            $('#eventos-list').empty();
          });
      }

      // Frequência (resumo)
      function carregarFrequencia() {
        var anoAtual = new Date().getFullYear();
        var url = '../includes/ajax/aluno/frequencia_resumo.php?ano=' + anoAtual;
        $.getJSON(url)
          .done(function (res) {
            if (!(res && res.success && res.data)) {
              $('#freq-ano').text('-');
              $('#freq-turma').text('-');
              $('#freq-mat').text('-');
              $('#freq-perc').text('-');
              return;
            }
            var d = res.data;
            if (!d.matricula && !d.turma) {
              $('#freq-ano').text('-');
              $('#freq-turma').text('-');
              $('#freq-mat').text('-');
              $('#freq-perc').text('-');
              return;
            }
            $('#freq-ano').text(d.ano || '-');
            $('#freq-turma').text(d.turma || '-');
            $('#freq-mat').text(d.matricula || '-');
            var perc = d.percentual !== null ? parseFloat(d.percentual) : null;
            var percLabel = perc !== null ? perc.toFixed(1).replace('.', ',') + '%' : '--';
            $('#freq-perc').text(percLabel);
          })
          .fail(function (xhr, status, error) {
            $('#freq-ano').text('-');
            $('#freq-turma').text('-');
            $('#freq-mat').text('-');
            $('#freq-perc').text('-');
          });
      }

      // Aulas (horários)
      function carregarAulas() {
        $.getJSON('../includes/ajax/aluno/horarios.php')
          .done(function (res) {
            var dados = (res && res.success && Array.isArray(res.data)) ? res.data : [];
            if (!dados.length) {
              $('#aulas-container').html('<div class="alert alert-light">Nenhuma aula cadastrada</div>');
              return;
            }
            // Monta tabela por dia da semana
            var dias = { 1: 'Segunda', 2: 'Terça', 3: 'Quarta', 4: 'Quinta', 5: 'Sexta', 6: 'Sábado' };
            var map = {};
            for (var i = 1; i <= 6; i++) { map[i] = []; }
            for (var j = 0; j < dados.length; j++) {
              var a = dados[j];
              var hi = (a.Hora_Inicio || '').substring(0, 5);
              var hf = (a.Hora_Fim || '').substring(0, 5);
              map[a.Dia_Semana] = map[a.Dia_Semana] || [];
              var turmaLabel = a.Nome_Turma ? (' (' + a.Nome_Turma + ')') : '';
              map[a.Dia_Semana].push((a.Nome_Disciplina || '') + ' — ' + hi + ' - ' + hf + turmaLabel);
            }
            var thead = '<thead><tr>';
            var tbody = '<tbody><tr>';
            for (var d = 1; d <= 5; d++) {
              thead += '<th>' + dias[d] + '</th>';
              var itens = map[d] || [];
              tbody += '<td>' + (itens.length ? itens.join('<br>') : '') + '</td>';
            }
            thead += '</tr></thead>';
            tbody += '</tr></tbody>';
            var table = '<table class="table">' + thead + tbody + '</table>';
            $('#aulas-container').html(table);
          })
          .fail(function () {
            $('#aulas-container').html('<div class="alert alert-light">Não foi possível carregar as aulas</div>');
          });
      }

      $(function () {
        // Atualiza nome do aluno no welcome
        const nome = $('.user-title').text() || 'Aluno';
        $('.welcome-title').text('Bem-vindo, ' + nome.split(' ')[0] + '!');

        carregarFrequencia();
        carregarEventos();
        carregarAulas();
      });
    })();
  </script>

</body>

</html>