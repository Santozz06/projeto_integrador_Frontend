<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <title>Componente Curricular - SAS (Sistema Academico Santos)</title>
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
  <style>
    :root {
      --azul-principal: #2c5f9e;
      --texto-escuro: #333333;
      --cinza-texto: #666666;
      --borda: #e0e0e0;
    }

    .card-componente {
      border-radius: 10px;
      border: 1px solid var(--borda);
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .card-header-componente {
      background-color: var(--azul-principal);
      color: white;
      border-radius: 10px 10px 0 0 !important;
      padding: 15px 20px;
    }

    .trimestre-card {
      border-left: 4px solid var(--azul-principal);
      margin-bottom: 15px;
      transition: all 0.3s;
    }

    .trimestre-card:hover {
      transform: translateX(5px);
    }

    .btn-voltar {
      background-color: var(--azul-principal);
      color: white;
      border: none;
      padding: 10px 25px;
      border-radius: 5px;
      margin-top: 20px;
    }

    .btn-voltar:hover {
      background-color: #1e4a7e;
      color: white;
    }

    .badge-data {
      background-color: #e9f0f8;
      color: var(--azul-principal);
      font-weight: 500;
      padding: 5px 10px;
      border-radius: 20px;
    }

    .texto-frequencia {
      color: white;
      background-color: var(--azul-principal);
      display: inline-block;
      padding: 3px 10px;
      border-radius: 15px;
      font-size: 0.85rem;
      margin-top: 5px;
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

    <!-- Conteúdo da Página -->
    <div class="content-wrapper">
      <div class="container-fluid">
        <div class="row justify-content-center mt-4">
          <div class="col-lg-8">
            <div class="card card-componente">
              <div class="card-header card-header-componente">
                <h4 class="mb-0">Componente curricular</h4>
                <h5 id="disciplina-nome" class="mb-0 mt-2">–</h5>
                <div id="header-sub" class="mt-1 small"></div>
              </div>
              <div class="card-body">
                <!-- Seção Aluno -->
                <div class="mb-4" id="sec-frequencia">
                  <h5 class="text-dark mb-3">Aluno</h5>
                  <p class="mb-0">Frequência na matéria</p>
                  <div class="progress mt-2" style="height: 10px;">
                    <div id="barra-freq" class="progress-bar bg-success" style="width: 0%"></div>
                  </div>
                  <span id="texto-freq" class="texto-frequencia" style="display:none"></span>
                  <div id="freq-vazia" class="text-muted" style="display:none">Sem registros de frequência para este ano.</div>
                </div>

                <hr>

                <!-- Plano de Ensino -->
                <div class="mb-4" id="sec-plano">
                  <h5 class="text-dark mb-3">Plano de ensino</h5>
                  <div id="plano-conteudo">
                    <div class="text-muted">Carregando plano de ensino…</div>
                  </div>
                </div>

                <!-- Botão Voltar -->
                <div class="text-center">
                  <a href="componente_curricular.php" class="btn btn-voltar">
                    <i class="zmdi zmdi-arrow-left mr-2"></i> Voltar
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
         <div class="overlay toggle-menu"></div>
    </div>

  </div>

  <!-- Scripts -->
  <script src="../assets/js/jquery.min.js"></script>
  <script src="../assets/js/popper.min.js"></script>
  <script src="../assets/js/bootstrap.min.js"></script>
  <script src="../assets/plugins/simplebar/js/simplebar.js"></script>
  <script src="../assets/js/sidebar-menu.js"></script>
  <script src="../assets/js/app-script.js"></script>
  <script>
    (function(){
      function getParam(n){
        var m = new RegExp('[?&]'+n+'=([^&#]*)').exec(window.location.search);
        return m ? decodeURIComponent(m[1].replace(/\+/g,' ')) : null;
      }
      function nl2br(str){
        return (str||'').replace(/\n/g,'<br>');
      }
      function renderHeader(h){
        if(!h) return;
        $('#disciplina-nome').text(h.disciplina || 'Disciplina');
        var parts = [];
        if (h.professor) parts.push('Professor: ' + h.professor);
        if (h.turma) parts.push('Turma: ' + h.turma);
        if (h.etapa) parts.push('Etapa: ' + h.etapa);
        if (h.ano) parts.push('Ano: ' + h.ano);
        $('#header-sub').text(parts.join(' • '));
        var f = h.frequencia || {};
        if (f.percentual == null){
          $('#freq-vazia').show();
          $('#texto-freq').hide();
          $('#barra-freq').css('width','0%');
        } else {
          var p = Math.max(0, Math.min(100, parseInt(f.percentual,10) || 0));
          $('#barra-freq').css('width', p+'%');
          $('#texto-freq').text(p + '% de presenças' + (f.total? (' (' + (f.presentes||0) + '/' + (f.total||0) + ')') : '')).show();
          $('#freq-vazia').hide();
        }
      }
      function renderPlano(plano){
        var $wrap = $('#plano-conteudo');
        $wrap.empty();
        if (!plano){
          $wrap.html('<div class="text-muted">Não há plano de ensino registrado para esta disciplina.</div>');
          return;
        }
        var campos = [
          {k:'Conteudo', t:'Conteúdo'},
          {k:'Objetivos', t:'Objetivos'},
          {k:'Metodologia', t:'Metodologia'},
          {k:'Avaliacao', t:'Avaliação'}
        ];
        var rendered = 0;
        campos.forEach(function(c){
          var val = plano[c.k] || plano[c.k.toLowerCase()];
          if (val && String(val).trim().length){
            var card = [
              '<div class="trimestre-card p-3">',
              '  <h6 class="font-weight-bold">' + c.t + '</h6>',
              '  <p class="mb-1">' + nl2br(String(val)) + '</p>',
              '</div>'
            ].join('');
            $wrap.append(card);
            rendered++;
          }
        });
        if (!rendered){
          $wrap.html('<div class="text-muted">Plano de ensino sem conteúdo disponível.</div>');
        }
      }
      function carregar(){
        var ano = getParam('ano');
        var disc = getParam('disciplina');
        if (!ano || !disc){
          $('#plano-conteudo').html('<div class="text-danger">Parâmetros ausentes (ano/disciplinar).</div>');
          return;
        }
        var url = '../includes/ajax/aluno/componentes_detalhes.php?ano=' + encodeURIComponent(ano) + '&disciplina=' + encodeURIComponent(disc);
        $.getJSON(url).done(function(resp){
          if (resp && resp.success){
            renderHeader(resp.header);
            renderPlano(resp.plano);
          } else {
            $('#plano-conteudo').html('<div class="text-danger">' + (resp && resp.message ? resp.message : 'Falha ao carregar dados') + '</div>');
          }
        }).fail(function(){
          $('#plano-conteudo').html('<div class="text-danger">Erro ao consultar detalhes do componente.</div>');
        });
      }
      $(function(){ carregar(); });
    })();
  </script>
  
</body>

</html>