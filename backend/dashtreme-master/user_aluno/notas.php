<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <title>Minhas Notas - SAS</title>
  <link href="../assets/css/pace.min.css" rel="stylesheet" />
  <script src="../assets/js/pace.min.js"></script>
  <link rel="icon" href="../assets/images/favicon.ico" type="image/x-icon">
  <link href="../assets/plugins/simplebar/css/simplebar.css" rel="stylesheet" />
  <link href="../assets/css/bootstrap.min.css" rel="stylesheet" />
  <link href="../assets/css/animate.css" rel="stylesheet" />
  <link href="../assets/css/icons.css" rel="stylesheet" />
  <link href="../assets/css/sidebar-menu.css" rel="stylesheet" />
  <link href="../assets/css/app-style.css" rel="stylesheet" />
  <link href="../css/style.css" rel="stylesheet" />
</head>

<body class="bg-theme bg-theme1 user_aluno_notas">
    <?php
    require("menu_padrao.php");
    ?>
    <div class="clearfix"></div>

    <!-- Conteúdo -->
    <div class="content-wrapper">
      <div class="container-fluid">
        <div class="row justify-content-center mt-4">
          <div class="col-lg-10">
            <div class="card card-ensino">
              <div class="card-header card-header-ensino">
                <h4 class="mb-0">Minhas Notas</h4>
              </div>
              <div class="card-body">
                <!-- Filtros -->
                <div class="row mb-4">
                  <div class="col-md-4">
                    <label class="text-light mb-2"><strong>Ano Letivo</strong></label>
                    <select id="ano-letivo" class="form-control">
                      <option value="">Carregando...</option>
                    </select>
                  </div>
                  <div class="col-md-4">
                    <label class="text-light mb-2"><strong>Componente Curricular</strong></label>
                    <select id="disciplina" class="form-control">
                      <option value="">Selecione um ano</option>
                    </select>
                  </div>
                  <div class="col-md-4">
                    <label class="text-light mb-2"><strong>Trimestre</strong></label>
                    <select id="trimestre" class="form-control">
                      <option value="1">1º Trimestre</option>
                      <option value="2">2º Trimestre</option>
                      <option value="3">3º Trimestre</option>
                    </select>
                  </div>
                </div>

                <!-- Resultado -->
                <div id="header-disciplina" class="mb-3 d-none">
                  <h5 class="mb-0 text-center"></h5>
                </div>

                <div class="d-none" id="box-notas">
                  <div class="row" id="notas-container"></div>
                </div>

                <div id="no-data" class="text-center text-light py-4 d-none">
                  Selecione o ano e a disciplina para consultar suas notas.
                </div>

                <div class="text-center">
                  <a href="index.php" class="btn btn-voltar mt-3">
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
    $(function() {
      const $ano = $('#ano-letivo');
      const $disc = $('#disciplina');
      const $tri = $('#trimestre');
      const $container = $('#notas-container');
      const $box = $('#box-notas');
      const $no = $('#no-data');
      const $hdr = $('#header-disciplina h5');

      async function carregarAnos() {
        try {
          const res = await $.getJSON('../includes/ajax/aluno/anos_matriculas.php');
          $ano.empty();
          if (res.success && Array.isArray(res.anos) && res.anos.length) {
            res.anos.forEach(a => $ano.append(`<option value="${a.ano}">${a.ano} - ${a.serie || ''}</option>`));
            const anoAtual = new Date().getFullYear();
            if ($ano.find(`option[value="${anoAtual}"]`).length) $ano.val(anoAtual);
          } else {
            $ano.append('<option value="">Sem anos</option>');
          }
        } catch (e) {
          $ano.empty().append('<option value="">Erro ao carregar</option>');
        }
      }

      async function carregarDisciplinas() {
        $disc.empty().append('<option value="">Carregando...</option>');
        const anoSel = $ano.val();
        if (!anoSel) { $disc.empty().append('<option value="">Selecione um ano</option>'); return; }
        try {
          const res = await $.getJSON('../includes/ajax/aluno/componentes_curriculares.php', { ano: anoSel });
          $disc.empty();
          const comps = (res && (res.componentes || res.data)) || [];
          if (res.success && Array.isArray(comps) && comps.length) {
            $disc.append('<option value="">Selecione...</option>');
            comps.forEach(d => {
              const id = d.id ?? d.ID_Disciplina;
              const nome = d.nome ?? d.Nome_Disciplina ?? d.nome_disciplina ?? d.Nome;
              if (id && nome) {
                $disc.append(`<option value="${id}">${nome}</option>`);
              }
            });
          } else {
            $disc.append('<option value="">Sem disciplinas</option>');
          }
        } catch (e) {
          $disc.empty().append('<option value="">Erro ao carregar</option>');
        }
        renderVazio();
      }

      function renderVazio(msg) {
        $box.addClass('d-none');
        $no.removeClass('d-none').text(msg || 'Selecione o ano e a disciplina para consultar suas notas.');
        $('#header-disciplina').addClass('d-none');
        $hdr.text('');
      }

      async function carregarNotas() {
        const anoSel = $ano.val();
        const discSel = $disc.val();
        if (!anoSel || !discSel) { renderVazio(); return; }
        try {
          const res = await $.getJSON('../includes/ajax/aluno/notas_disciplina.php', { ano: anoSel, disciplina_id: discSel, trimestre: $tri.val() });
          if (!res.success || !res.data) { 
            renderVazio('Nenhum dado encontrado.'); 
            return; 
          }
          const et = res.data.etapas || {};
          const nome = res.data.nome_disciplina || 'Componente';
          $hdr.text(`${nome} - ${anoSel} (Trimestre ${$tri.val()})`);
          $('#header-disciplina').removeClass('d-none');
          $container.empty();
          ['1','2','3','4'].forEach(e => {
            const v = et[e];
            const notaDisplay = v !== null && v !== undefined ? parseFloat(v).toFixed(2).replace('.', ',') : '-';
            const card = `
              <div class="col-md-6 col-lg-3">
                <div class="nota-card">
                  <div class="nota-etapa">Nota ${e}</div>
                  <div class="nota-valor">${notaDisplay}</div>
                </div>
              </div>
            `;
            $container.append(card);
          });
          $no.addClass('d-none');
          $box.removeClass('d-none');
        } catch (e) {
          renderVazio('Erro ao carregar notas.');
        }
      }

      // Eventos
      $ano.on('change', carregarDisciplinas);
      $disc.on('change', carregarNotas);
      $tri.on('change', carregarNotas);

      // Init
      carregarAnos().then(carregarDisciplinas);
    });
  </script>
</body>

</html>