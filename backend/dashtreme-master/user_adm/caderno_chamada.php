<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Caderno de Chamada - Admin</title>
  <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/css/app-style.css">
  <link rel="stylesheet" href="../assets/css/icons.css">
  <link rel="stylesheet" href="../assets/css/sidebar-menu.css">
  <link rel="stylesheet" href="../css/style.css">
</head>
<body class="bg-theme bg-theme1 user_adm_cadernoChamada">
  <?php require('menu_padrão.php'); ?>

  <div class="content-wrapper">
    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-12">
          <div class="card" style="background: transparent; border: none; box-shadow: none;">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="page-title"><i class="zmdi zmdi-accounts-list mr-2"></i> Caderno de Chamada</h4>
              </div>

              <div class="form-container">
                <div class="filtros-container">
                  <div class="filtro-item">
                    <div class="bold-title">Ano Letivo</div>
                    <select id="ano-letivo" class="form-control"></select>
                  </div>
                  <div class="filtro-item">
                    <div class="bold-title">Professor (opcional)</div>
                    <select id="professor" class="form-control">
                      <option value="">Todos</option>
                    </select>
                  </div>
                  <div class="filtro-item">
                    <div class="bold-title">Turma</div>
                    <select id="turma" class="form-control">
                      <option value="">Selecione ano/professor</option>
                    </select>
                  </div>
                  <div class="filtro-item">
                    <div class="bold-title">Data Início</div>
                    <input id="data-ini" type="date" class="form-control" />
                  </div>
                  <div class="filtro-item">
                    <div class="bold-title">Data Fim</div>
                    <input id="data-fim" type="date" class="form-control" />
                  </div>
                </div>

                <div class="kpi">
                  <div class="card"><div>Total lançamentos: <strong id="kpi-total">-</strong></div></div>
                  <div class="card"><div>Presenças: <strong id="kpi-pres">-</strong></div></div>
                  <div class="card"><div>Faltas: <strong id="kpi-falt">-</strong></div></div>
                  <div class="card"><div>Percentual médio: <strong id="kpi-perc">-</strong></div></div>
                </div>

                <div class="mb-3">
                  <button id="btn-exportar" class="btn btn-success btn-sm"><i class="zmdi zmdi-download"></i> Exportar CSV</button>
                  <button id="btn-exportar-pdf" class="btn btn-primary btn-sm ml-2"><i class="zmdi zmdi-file-text"></i> Exportar PDF</button>
                </div>

                <div class="kpi" id="resumo-trimestre" style="display:none;">
                  <div class="card"><div>Trimestre 1 - % Presença: <strong id="tri1-perc">-</strong> | Tot: <span id="tri1-tot">-</span></div></div>
                  <div class="card"><div>Trimestre 2 - % Presença: <strong id="tri2-perc">-</strong> | Tot: <span id="tri2-tot">-</span></div></div>
                  <div class="card"><div>Trimestre 3 - % Presença: <strong id="tri3-perc">-</strong> | Tot: <span id="tri3-tot">-</span></div></div>
                </div>

                <div class="table-responsive">
                  <table id="tabela-freq" class="table">
                    <thead>
                      <tr>
                        <th>Aluno</th>
                        <th>Matrícula</th>
                        <th>Turma</th>
                        <th>Presenças</th>
                        <th>Faltas</th>
                        <th>Total</th>
                        <th>% Presença</th>
                        <th>Ações</th>
                      </tr>
                    </thead>
                    <tbody></tbody>
                  </table>
                  <div id="no-results" class="no-results">Nenhum registro encontrado para o período.</div>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="overlay toggle-menu"></div>

  <script src="../assets/js/jquery.min.js"></script>
  <script src="../assets/js/bootstrap.min.js"></script>
  <script src="../assets/plugins/simplebar/js/simplebar.js"></script>
  <script src="../assets/js/sidebar-menu.js"></script>
  <script src="../assets/js/app-script.js"></script>
  <script>
    $(function(){
      let turmas = [];
      function carregarAnos(){
        return $.getJSON('../includes/ajax/listar_anos_letivos.php').then(res => {
          const $sel = $('#ano-letivo');
          $sel.empty();
          if (res.success && Array.isArray(res.data)) {
            res.data.forEach(ano => $sel.append(`<option value="${ano}">${ano}</option>`));
          }
          const hoje = new Date();
          const anoAtual = hoje.getFullYear();
          if ($(`#ano-letivo option[value='${anoAtual}']`).length) $sel.val(anoAtual);
        });
      }
      function carregarProfessores(){
        return $.getJSON('../includes/ajax/listar_professores.php').then(res => {
          const $sel = $('#professor');
          $sel.find('option:not([value=""])').remove();
          if (res.success && Array.isArray(res.data)) {
            res.data.forEach(p => $sel.append(`<option value="${p.ID_Professor}">${p.Nome_Completo}</option>`));
          }
        });
      }
      function atualizarTurmas(){
        const ano = $('#ano-letivo').val();
        const professor = $('#professor').val();
        return $.getJSON('../includes/ajax/listar_turmas.php', { ano, professor_id: professor }).then(res => {
          turmas = (res.success && Array.isArray(res.data)) ? res.data : [];
          const $sel = $('#turma');
          $sel.empty();
          if (turmas.length === 0) {
            $sel.append('<option value="" disabled>(Sem turmas)</option>');
          } else {
            $sel.append('<option value="">Selecione</option>');
            turmas.forEach(t => $sel.append(`<option value="${t.ID_Turma}">${t.Nome_Turma}</option>`));
          }
        });
      }
      function atualizarPeriodoPadrao(){
        const hoje = new Date();
        const first = new Date(hoje.getFullYear(), hoje.getMonth(), 1).toISOString().slice(0,10);
        const last = new Date(hoje.getFullYear(), hoje.getMonth()+1, 0).toISOString().slice(0,10);
        if (!$('#data-ini').val()) $('#data-ini').val(first);
        if (!$('#data-fim').val()) $('#data-fim').val(last);
      }
      function carregarFrequencias(){
        const turma = $('#turma').val();
        if (!turma){ renderTabela([]); return; }
        const data_ini = $('#data-ini').val();
        const data_fim = $('#data-fim').val();
        $.ajax({
          url: '../includes/ajax/frequencias/listar_frequencias.php',
          method: 'GET', dataType: 'json',
          data: { turma_id: turma, data_ini, data_fim }
        }).done(function(res){
          const rows = (res.success && Array.isArray(res.data)) ? res.data : [];
          renderTabela(rows);
          carregarResumoTrimestre();
        }).fail(function(xhr){
          let msg = 'Erro ao listar frequências';
          try { const r = JSON.parse(xhr.responseText); if (r.message) msg = r.message; } catch{}
          alert(msg);
          renderTabela([]);
        });
      }
      function renderTabela(rows){
        const $tbody = $('#tabela-freq tbody');
        $tbody.empty();
        if (!rows || rows.length === 0){ $('#no-results').show(); atualizaKpis([]); return; }
        $('#no-results').hide();
        rows.forEach(r => {
          const tr = $(`
            <tr data-id-matricula="${r.ID_Matricula}">
              <td>${r.Nome}</td>
              <td>${r.Matricula || ''}</td>
              <td><span class='badge-turma'>${r.Turma || ''}</span></td>
              <td>${r.Presentes}</td>
              <td>${r.Faltas}</td>
              <td>${r.Total}</td>
              <td>${r.Percentual !== null ? r.Percentual + '%' : '-'}</td>
              <td><button class="btn btn-info btn-sm btn-detalhes"><i class="zmdi zmdi-eye"></i> Detalhes</button></td>
            </tr>`);
          $tbody.append(tr);
        });
        atualizaKpis(rows);
      }
      function atualizaKpis(rows){
        if (!rows || rows.length === 0){
          $('#kpi-total').text('-'); $('#kpi-pres').text('-'); $('#kpi-falt').text('-'); $('#kpi-perc').text('-');
          return;
        }
        let total=0, pres=0, falt=0; let somaPerc=0, percCount=0;
        rows.forEach(r => { total += r.Total; pres += r.Presentes; falt += r.Faltas; if (r.Percentual !== null){ somaPerc += r.Percentual; percCount++; } });
        const percMedio = percCount>0 ? (somaPerc/percCount).toFixed(1) + '%' : '-';
        $('#kpi-total').text(total); $('#kpi-pres').text(pres); $('#kpi-falt').text(falt); $('#kpi-perc').text(percMedio);
      }

      // init
      Promise.all([carregarAnos(), carregarProfessores()])
        .then(() => { atualizarPeriodoPadrao(); return atualizarTurmas(); })
        .then(() => carregarFrequencias());

      // eventos
      $('#ano-letivo').on('change', function(){ atualizarTurmas().then(carregarFrequencias); });
      $('#professor').on('change', function(){ atualizarTurmas().then(carregarFrequencias); });
      $('#turma').on('change', carregarFrequencias);
      $('#data-ini, #data-fim').on('change', carregarFrequencias);

      // Exportar CSV
      $('#btn-exportar').on('click', function(){
        const turma = $('#turma').val();
        if (!turma){ alert('Selecione uma turma para exportar.'); return; }
        const data_ini = $('#data-ini').val();
        const data_fim = $('#data-fim').val();
        const url = `../includes/ajax/frequencias/exportar_frequencias_csv.php?turma_id=${encodeURIComponent(turma)}&data_ini=${encodeURIComponent(data_ini)}&data_fim=${encodeURIComponent(data_fim)}`;
        window.location.href = url;
      });

      // Exportar PDF (print-friendly)
      $('#btn-exportar-pdf').on('click', function(){
        const turma = $('#turma').val();
        if (!turma){ alert('Selecione uma turma para exportar.'); return; }
        const data_ini = $('#data-ini').val();
        const data_fim = $('#data-fim').val();
        const titulo = 'Caderno de Chamada - Relatório';
        // Coleta dados atuais da tabela
        let linhas = '';
        $('#tabela-freq tbody tr').each(function(){
          const tds = $(this).children('td');
          if (tds.length >= 7){
            const cols = [];
            for (let i=0;i<7;i++){ cols.push(`<td>${$(tds[i]).text()}</td>`); }
            linhas += `<tr>${cols.join('')}</tr>`;
          }
        });
        const kpiTotal = $('#kpi-total').text();
        const kpiPres = $('#kpi-pres').text();
        const kpiFalt = $('#kpi-falt').text();
        const kpiPerc = $('#kpi-perc').text();
        const tri1p = $('#tri1-perc').text(); const tri1t = $('#tri1-tot').text();
        const tri2p = $('#tri2-perc').text(); const tri2t = $('#tri2-tot').text();
        const tri3p = $('#tri3-perc').text(); const tri3t = $('#tri3-tot').text();
        const w = window.open('', '_blank');
        w.document.write(`<!DOCTYPE html><html><head><meta charset=\"utf-8\"><title>${titulo}</title>
          <style>
            body{ font-family: Arial, Helvetica, sans-serif; }
            h2{ margin: 0 0 8px; }
            .muted{ color: #555; font-size: 12px; }
            table{ width: 100%; border-collapse: collapse; margin-top: 12px; }
            th, td{ border: 1px solid #999; padding: 6px 8px; font-size: 12px; text-align: left; }
            th{ background: #eee; }
            .kpi{ display:flex; gap:16px; margin: 8px 0; }
            .kpi div{ background:#f5f5f5; padding:8px 10px; border:1px solid #ddd; border-radius:4px; }
          </style>
        </head><body>
          <h2>${titulo}</h2>
          <div class="muted">Período: ${data_ini} a ${data_fim}</div>
          <div class="kpi">
            <div>Total lançamentos: <strong>${kpiTotal}</strong></div>
            <div>Presenças: <strong>${kpiPres}</strong></div>
            <div>Faltas: <strong>${kpiFalt}</strong></div>
            <div>Percentual médio: <strong>${kpiPerc}</strong></div>
          </div>
          <div class="kpi">
            <div>T1 %: <strong>${tri1p}</strong> | Tot: ${tri1t}</div>
            <div>T2 %: <strong>${tri2p}</strong> | Tot: ${tri2t}</div>
            <div>T3 %: <strong>${tri3p}</strong> | Tot: ${tri3t}</div>
          </div>
          <table>
            <thead>
              <tr>
                <th>Aluno</th><th>Matrícula</th><th>Turma</th><th>Presenças</th><th>Faltas</th><th>Total</th><th>% Presença</th>
              </tr>
            </thead>
            <tbody>${linhas}</tbody>
          </table>
          <script>window.onload=function(){ window.print(); }<\/script>
        </body></html>`);
        w.document.close();
      });

      // Resumo por Trimestre
      function carregarResumoTrimestre(){
        const turma = $('#turma').val();
        if (!turma){ $('#resumo-trimestre').hide(); return; }
        const data_ini = $('#data-ini').val();
        const data_fim = $('#data-fim').val();
        $.ajax({
          url: '../includes/ajax/frequencias/resumo_turma.php',
          method: 'GET', dataType: 'json',
          data: { turma_id: turma, data_ini, data_fim }
        }).done(function(res){
          if (res.success && res.data){
            const t1 = res.data['1'] || { total:0, presentes:0 };
            const t2 = res.data['2'] || { total:0, presentes:0 };
            const t3 = res.data['3'] || { total:0, presentes:0 };
            const p1 = t1.total>0 ? ((t1.presentes/t1.total)*100).toFixed(1)+'%' : '-';
            const p2 = t2.total>0 ? ((t2.presentes/t2.total)*100).toFixed(1)+'%' : '-';
            const p3 = t3.total>0 ? ((t3.presentes/t3.total)*100).toFixed(1)+'%' : '-';
            $('#tri1-perc').text(p1); $('#tri1-tot').text(t1.total);
            $('#tri2-perc').text(p2); $('#tri2-tot').text(t2.total);
            $('#tri3-perc').text(p3); $('#tri3-tot').text(t3.total);
            $('#resumo-trimestre').show();
          } else {
            $('#resumo-trimestre').hide();
          }
        }).fail(function(){ $('#resumo-trimestre').hide(); });
      }

      // Detalhes por aluno (expansível)
      $(document).on('click', '.btn-detalhes', function(){
        const $tr = $(this).closest('tr');
        const mid = $tr.data('id-matricula');
        // Toggle se já existe a linha de detalhes
        if ($tr.next().hasClass('detalhe-row')){ $tr.next().toggle(); return; }
        const data_ini = $('#data-ini').val();
        const data_fim = $('#data-fim').val();
        $.ajax({
          url: '../includes/ajax/frequencias/listar_frequencias_detalhe.php',
          method: 'GET', dataType: 'json',
          data: { matricula_id: mid, data_ini, data_fim }
        }).done(function(res){
          const dados = (res.success && Array.isArray(res.data)) ? res.data : [];
          let html = `<div style="padding:8px 12px;">
            <strong>Detalhes por dia:</strong>
            <div class="table-responsive"><table class="table" style="margin:8px 0;">
              <thead><tr><th>Data</th><th>Presença</th></tr></thead>
              <tbody>`;
          if (dados.length === 0){
            html += `<tr><td colspan="2">Sem registros no período.</td></tr>`;
          } else {
            dados.forEach(d => {
              html += `<tr><td>${d.Data}</td><td>${d.Presenca ? 'Presente' : 'Falta'}</td></tr>`;
            });
          }
          html += `</tbody></table></div></div>`;
          const $detail = $(`<tr class="detalhe-row"><td colspan="8">${html}</td></tr>`);
          $tr.after($detail);
        }).fail(function(xhr){
          let msg = 'Erro ao carregar detalhes';
          try { const r = JSON.parse(xhr.responseText); if (r.message) msg = r.message; } catch{}
          alert(msg);
        });
      });
    });
  </script>
</body>
</html>