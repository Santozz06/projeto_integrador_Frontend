<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="Dashboard Acadêmico" />
  <meta name="author" content="" />
  <title>Dashboard Acadêmico</title>
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

  <style>
    .navbar {
      background-color: rgba(0, 0, 0, 0.2) !important;
      backdrop-filter: blur(10px);
    }

    /* Bloco de frequência estilizado */
    /* Barra de progresso padrão do template */
    .progress {
      height: 1.2rem;
      background-color: rgba(255,255,255,.1);
      border-radius: .25rem;
      box-shadow: inset 0 1px 2px rgba(0, 0, 0, .1);
      margin-bottom: 8px;
    }
    .progress-bar {
      background-color: #14b6ff;
      font-weight: 600;
      font-size: 1em;
      color: #fff;
      text-align: right;
      padding-right: 10px;
      transition: width .6s ease;
    }

    /* Efeito para o botão Sair */
    #logout-btn {
      transition: all 0.3s ease;
      border-radius: 4px;
      padding: 8px 12px;
    }

    #logout-btn:hover {
      background-color: #ff4444 !important;
      /* Vermelho suave */
      color: white !important;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(255, 68, 68, 0.2);
    }

    #logout-btn i {
      transition: all 0.3s ease;
    }

    #logout-btn:hover i {
      transform: rotate(15deg);
    }
  </style>
</head>

<body class="bg-theme bg-theme1">

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
            <div class="card">
              <div class="card-header">
                <h5>Frequência</h5>
              </div>
              <div class="card-body" id="freq-card">
                <div class="alert alert-light" id="freq-empty">Carregando frequência...</div>
                <div class="row mt-4" id="freq-infos" style="display:none;">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Ano letivo</label>
                      <input type="text" id="freq-ano" class="form-control" readonly>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Turma</label>
                      <input type="text" id="freq-turma" class="form-control" readonly>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Matrícula</label>
                      <input type="text" id="freq-mat" class="form-control" readonly>
                    </div>
                  </div>
                </div>
                <div class="row" id="freq-resumo" style="display:none;">
                  <div class="col-md-12">
                    <div class="alert alert-info mb-2" id="freq-perc" style="background:rgba(20,182,255,0.12);color:#14b6ff;font-weight:600;"></div>
                    <div class="progress" id="freq-progress" style="display:none;">
                      <div class="progress-bar" id="freq-bar-inner" role="progressbar" style="width:0%">0%</div>
                    </div>
                  </div>
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
    (function(){
      // Eventos dos últimos 30 dias do ano atual
      function carregarEventos(){
        var hoje = new Date();
        var start = new Date(); start.setDate(hoje.getDate() - 30);
        var end = hoje;
        var anoAtual = hoje.getFullYear();
        function toISO(d){ return d.toISOString().slice(0,10); }
        $.getJSON('../includes/ajax/calendario/listar_eventos.php', { start: toISO(start), end: toISO(end), ano: anoAtual })
          .done(function(res){
            var data = (res && res.success && Array.isArray(res.data)) ? res.data : [];
            if (!data.length){
              $('#eventos-empty').text('Nenhum evento nos últimos 30 dias').show();
              $('#eventos-list').empty();
              return;
            }
            $('#eventos-empty').hide();
            var html = '';
            for (var i=0;i<data.length;i++){
              var ev = data[i];
              var dt = (ev.start || ev.Data_Inicio || '').split('T')[0] || ev.start;
              var p = (dt||'').split('-');
              var dataBR = (p.length===3) ? (p[2]+'/'+p[1]+'/'+p[0]) : dt;
              var titulo = ev.title || ev.Nome_Evento || '';
              var tipo = (ev.extendedProps && ev.extendedProps.tipo) || ev.Tipo_Evento || '';
              html += '<div class="mb-2"><strong>'+ dataBR +'</strong> - '+ titulo + (tipo ? ' <span class="badge badge-info">'+tipo+'</span>' : '') + '</div>';
            }
            $('#eventos-list').html(html);
          })
          .fail(function(){
            $('#eventos-empty').text('Não foi possível carregar os eventos').show();
            $('#eventos-list').empty();
          });
      }

      // Frequência (resumo)
      function carregarFrequencia(){
        var anoAtual = 2025; // manter alinhado ao restante do sistema
        $.getJSON('../includes/ajax/aluno/frequencia_resumo.php', { ano: anoAtual })
          .done(function(res){
            if (!(res && res.success && res.data)){
              $('#freq-empty').text('Não há frequências registradas').show();
              $('#freq-infos, #freq-resumo').hide();
              return;
            }
            var d = res.data;
            if (!d.matricula){
              $('#freq-empty').text('Matrícula não encontrada para o ano atual').show();
              $('#freq-infos, #freq-resumo').hide();
              return;
            }
            $('#freq-empty').hide();
            $('#freq-ano').val(d.ano || '');
            $('#freq-turma').val(d.turma || '');
            $('#freq-mat').val(d.matricula || '');
            var perc = d.percentual !== null ? parseFloat(d.percentual) : null;
            var percLabel = perc !== null ? perc.toFixed(1).replace('.',',') + '%' : '--';
            $('#freq-perc').text('Presenças: ' + (d.presencas||0) + ' de ' + (d.total||0) + (perc !== null ? ' ('+ percLabel +')' : ''));
            if (perc !== null && !isNaN(perc)) {
              $("#freq-progress").show();
              $("#freq-bar-inner").css("width", Math.max(0, Math.min(100, perc)) + "%").text(percLabel);
            } else {
              $("#freq-progress").hide();
            }
            $('#freq-infos, #freq-resumo').show();
          })
          .fail(function(){
            $('#freq-empty').text('Não foi possível carregar a frequência').show();
            $('#freq-infos, #freq-resumo').hide();
          });
      }

      // Aulas (horários)
      function carregarAulas(){
        // Não filtra por ano aqui para garantir que horários da turma atual apareçam
        $.getJSON('../includes/ajax/aluno/horarios.php')
          .done(function(res){
            var dados = (res && res.success && Array.isArray(res.data)) ? res.data : [];
            if (!dados.length){
              $('#aulas-container').html('<div class="alert alert-light">Nenhuma aula cadastrada</div>');
              return;
            }
            // Monta tabela por dia da semana
            var dias = {1:'Segunda',2:'Terça',3:'Quarta',4:'Quinta',5:'Sexta',6:'Sábado'};
            var map = {};
            for (var i=1;i<=6;i++){ map[i]=[]; }
            for (var j=0;j<dados.length;j++){
              var a = dados[j];
              var hi = (a.Hora_Inicio||'').substring(0,5);
              var hf = (a.Hora_Fim||'').substring(0,5);
              map[a.Dia_Semana] = map[a.Dia_Semana] || [];
              var turmaLabel = a.Nome_Turma ? (' (' + a.Nome_Turma + ')') : '';
              map[a.Dia_Semana].push((a.Nome_Disciplina||'') + ' — ' + hi + ' - ' + hf + turmaLabel);
            }
            var thead = '<thead><tr>';
            var tbody = '<tbody><tr>';
            for (var d=1; d<=5; d++){
              thead += '<th>'+ dias[d] +'</th>';
              var itens = map[d]||[];
              tbody += '<td>'+ (itens.length? itens.join('<br>') : '') +'</td>';
            }
            thead += '</tr></thead>';
            tbody += '</tr></tbody>';
            var table = '<table class="table">'+ thead + tbody +'</table>';
            $('#aulas-container').html(table);
          })
          .fail(function(){
            $('#aulas-container').html('<div class="alert alert-light">Não foi possível carregar as aulas</div>');
          });
      }

      $(function(){
        carregarFrequencia();
        carregarEventos();
        carregarAulas();
      });
    })();
  </script>

</body>

</html>