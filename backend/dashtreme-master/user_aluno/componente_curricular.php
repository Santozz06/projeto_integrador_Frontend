<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <title>Ensino Fundamental - Dashboard Acadêmico</title>
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
    .card-ensino {
      border-radius: 0.5rem;
      border: 1px solid rgba(20,182,255,0.13);
      background: rgba(255,255,255,0.04);
      box-shadow: 0 2px 8px rgba(20,182,255,0.07);
      transition: box-shadow 0.3s, transform 0.2s;
    }
    .card-ensino:hover {
      box-shadow: 0 6px 18px rgba(20,182,255,0.13);
      transform: translateY(-2px) scale(1.01);
    }
    .card-header-ensino {
      border-radius: 0.5rem 0.5rem 0 0 !important;
      background: linear-gradient(90deg, #2c5f9e 60%, #14b6ff 100%) !important;
      color: #fff;
      border: none;
      padding: 15px 20px;
    }
    .materia-card {
      border-left: 4px solid #14b6ff;
      margin-bottom: 15px;
      transition: background 0.3s, box-shadow 0.3s, transform 0.2s;
      padding: 15px 18px;
      display: flex;
      align-items: center;
      background: rgba(255,255,255,0.04);
      border-radius: 0.5rem;
      color: #fff;
    }
    .materia-card:hover {
      background: rgba(20,182,255,0.10);
      box-shadow: 0 3px 8px rgba(20,182,255,0.13);
      transform: translateX(3px);
      text-decoration: none;
    }
    .icone-img {
      width: 28px;
      height: 28px;
      object-fit: contain;
      margin-right: 14px;
    }
    .badge-turma {
      background: rgba(57, 180, 236, 0.12);
      color: #ffffffff;
      font-weight: 600;
      padding: 6px 14px;
      border-radius: 20px;
      margin-left: auto;
      font-size: 0.95em;
      letter-spacing: 0.2px;
      box-shadow: none;
    }
    .materia-card h6 {
      margin: 0;
      font-size: 1.08rem;
      line-height: 1.5;
      color: #fff;
      font-weight: 500;
      letter-spacing: 0.2px;
    }
    .btn-voltar {
      background: #2c5f9e;
      color: #fff;
      border: none;
      padding: 10px 28px;
      border-radius: 5px;
      margin-top: 20px;
      font-weight: 500;
      letter-spacing: 0.5px;
      transition: background 0.2s;
    }
    .btn-voltar:hover {
      background: #14b6ff;
      color: #fff;
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
          <div class="col-lg-10">
            <div class="card card-ensino">
              <div class="card-header card-header-ensino">
                <h4 class="mb-0">Ensino Fundamental</h4>
              </div>
              <div class="card-body">
                <div class="row align-items-center mb-3">
                  <div class="col-md-6"><span class="text-light">Selecione o ano letivo:</span></div>
                  <div class="col-md-6 text-end">
                    <select id="select-ano" class="form-select form-select-sm" style="max-width: 200px; display: inline-block;"></select>
                  </div>
                </div>

                <div id="lista-componentes" class="row"></div>

                <!-- Botão Voltar -->
                <div class="text-center">
                  <a href="index.php" class="btn btn-voltar">
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
      function iconFor(nome){
        if(!nome) return '../user_aluno/imagens/icon_ciencias.png';
        var s = (''+nome).toLowerCase();
        if (s.includes('portugues') || s.includes('português')) return '../user_aluno/imagens/icon_pt.png';
        if (s.includes('matem')) return '../user_aluno/imagens/icon_matematica.png';
        if (s.includes('ciên') || s.includes('cien')) return '../user_aluno/imagens/icon_ciencias.png';
        if (s.includes('hist')) return '../user_aluno/imagens/icon_historia.png';
        if (s.includes('geo')) return '../user_aluno/imagens/icon_geografia.png';
        if (s.includes('ingl')) return '../user_aluno/imagens/icon_ingles.png';
        if (s.includes('física') || s.includes('fisica')) return '../user_aluno/imagens/icon_edFisica.png';
        if (s.includes('arte')) return '../user_aluno/imagens/icon_artes.png';
        if (s.includes('relig')) return '../user_aluno/imagens/icon_ensinoReligioso.png';
        return '../user_aluno/imagens/icon_ciencias.png';
      }

      function renderAnos(anos, selecionado){
        var $sel = $('#select-ano');
        $sel.empty();
        if(!Array.isArray(anos) || anos.length===0){
          $sel.append('<option value="">Sem anos</option>');
          return;
        }
        anos.forEach(function(a){
          var opt = $('<option>').val(a).text(a);
          if (String(a)===String(selecionado)) opt.attr('selected','selected');
          $sel.append(opt);
        });
      }

      function renderComponentes(ano, comps){
        var $wrap = $('#lista-componentes');
        $wrap.empty();
        if(!Array.isArray(comps) || comps.length===0){
          $wrap.append('<div class="col-12 text-muted">Nenhum componente encontrado para o ano selecionado.</div>');
          return;
        }
        comps.forEach(function(c){
          var turmaLabel = (c.turmas && c.turmas[0] && c.turmas[0].nome_turma) ? c.turmas[0].nome_turma : '';
          var card = [
            '<div class="col-md-6">',
            '  <a class="text-decoration-none" href="componenteCurricular_detalhes.php?ano=' + encodeURIComponent(ano) + '&disciplina=' + encodeURIComponent(c.id) + '">',
            '    <div class="materia-card">',
            '      <img src="' + iconFor(c.nome) + '" class="icone-img" alt="Icone">',
            '      <h6 class="d-inline-block font-weight-bold">' + (c.nome || '') + '</h6>',
            '      <span class="badge-turma">' + (turmaLabel || '') + '</span>',
            '    </div>',
            '  </a>',
            '</div>'
          ].join('');
          $wrap.append(card);
        });
      }

      function carregar(ano){
        var url = '../includes/ajax/aluno/componentes_curriculares.php';
        if (ano) url += ('?ano=' + encodeURIComponent(ano));
        $.getJSON(url).done(function(resp){
          if(resp && resp.success){
            renderAnos(resp.anos || [], resp.ano_selecionado);
            renderComponentes(resp.ano_selecionado, resp.componentes || []);
          } else {
            $('#lista-componentes').html('<div class="col-12 text-warning">Nao foi possivel carregar os componentes.</div>');
          }
        }).fail(function(){
          $('#lista-componentes').html('<div class="col-12 text-danger">Erro ao consultar componentes.</div>');
        });
      }

      $(function(){
        carregar();
        $(document).on('change', '#select-ano', function(){
          carregar($(this).val());
        });
      });
    })();
  </script>
  
</body>

</html>