<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Caderno de Chamada - Dashboard Acadêmico</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/app-style.css">
    <link rel="stylesheet" href="../assets/css/icons.css">
    <link rel="stylesheet" href="../assets/css/sidebar-menu.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            background: linear-gradient(to right, #2c3e50, #3498db);
            color: #ecf0f1;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }

        .topbar-nav {
            height: 60px;
            z-index: 1000;
        }

        .content-wrapper {
            padding: 40px 20px;
            padding-top: 80px;
            min-height: calc(100vh - 60px);
        }

        .container-presenca {
            max-width: 950px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.05);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #ffffff;
        }

        .filtros {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .filtros select,
        .filtros input[type="date"] {
            flex: 1;
            min-width: 200px;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #71affe;
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .info-aula {
            background: rgba(255, 255, 255, 0.08);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
        }

        .info-aula p {
            margin: 5px 0;
        }

        .table-presenca {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .table-presenca th {
            background-color: rgba(113, 175, 254, 0.2);
            color: #ffffff;
            padding: 12px;
            text-align: left;
        }

        .table-presenca td {
            padding: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .radio-group {
            display: flex;
            gap: 10px;
        }

        .radio-option {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .radio-option input {
            accent-color: #1abc9c;
        }

        .btn-group {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 20px;
        }

        .btn {
            padding: 8px 18px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            font-size: 0.9em;
        }

        .btn-cancelar {
            background-color: #e74c3c;
            color: white;
        }

        .btn-cancelar:hover {
            background-color: #e74c3c;
        }

        .btn-salvar {
            background-color: #1abc9c;
            color: white;
        }

        .btn-salvar:hover {
            background-color: #16a085;
        }

        @media (max-width: 768px) {
            .content-wrapper {
                padding: 20px;
                padding-top: 70px;
            }

            .filtros {
                flex-direction: column;
                gap: 10px;
            }

            .btn-group {
                flex-direction: column;
                align-items: stretch;
            }

            .btn {
                width: 100%;
            }

            .table-presenca {
                overflow-x: auto;
                display: block;
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

        <!-- Conteúdo Principal -->
        <div class="content-wrapper">
            <div class="container-presenca">
                <h2>Caderno de Chamada</h2>

                <div class="filtros">
                    <select id="turmaSelect">
                        <option value="" disabled selected>-- Escolha uma turma --</option>
                    </select>
                    <input type="date" id="dataPresenca" />
                </div>

                <div class="filtros" style="margin-top: -5px;">
                    <div style="flex:1; min-width:200px;">
                        <label for="mesRelatorio" style="display:block; font-size:12px; opacity:.9; margin-bottom:4px;">Relatório mensal</label>
                        <input type="month" id="mesRelatorio" style="width:100%; padding:10px; border-radius:8px; border:1px solid #71affe; background:rgba(255,255,255,.1); color:#fff;" />
                    </div>
                    <div style="display:flex; gap:10px; align-items:flex-end;">
                        <button class="btn btn-salvar" id="btnExportCsv" title="Exportar CSV">Exportar CSV</button>
                        <button class="btn btn-salvar" id="btnExportPdf" title="Exportar PDF">Exportar PDF</button>
                    </div>
                </div>

                <div class="info-aula">
                    <p><strong>Data:</strong> <span id="dataInfo"></span></p>
                    <p><strong>Turma:</strong> <span id="turmaInfo"></span></p>
                </div>

                <table class="table-presenca">
                    <thead>
                        <tr>
                            <th>Matrícula</th>
                            <th>Nome</th>
                            <th>Presença</th>
                        </tr>
                    </thead>
                    <tbody id="tabelaAlunos">
                        <!-- JS irá preencher -->
                    </tbody>
                </table>

                <div class="btn-group">
                    <button class="btn btn-cancelar"
                        onclick="window.location.href='caderno_chamada.php'">Cancelar</button>
                    <button class="btn btn-salvar" type="button" onclick="marcarTodos('presente')">Marcar todos presentes</button>
                    <button class="btn btn-salvar" onclick="salvarPresenca()">Salvar</button>
                </div>
            </div>
        </div>
        <div class="overlay toggle-menu"></div>
    </div>

    <!-- Scripts -->
    <script src="../assets/js/jquery.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/plugins/simplebar/js/simplebar.js"></script>
    <script src="../assets/js/sidebar-menu.js"></script>
    <script src="../assets/js/app-script.js"></script>
    <!-- jsPDF (para exportar PDF no cliente) -->
    <script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf-autotable@3.8.1/dist/jspdf.plugin.autotable.min.js"></script>

    <script>
        // Evita reinicializar o sidebar-menu aqui (app-script.js já faz isso globalmente)
        $(function(){ inicializar(); });

        let turmaAtual = null;
        let alunosCache = [];
        let presencasMap = {}; // { ID_Matricula: 'P'|'A'|'J' }

        function inicializar(){
            const hoje = new Date().toISOString().split('T')[0];
            $('#dataPresenca').val(hoje);
            carregarTurmas();
            $('#turmaSelect').on('change', function(){ turmaAtual = this.value; atualizarInfo(); carregarAlunosEPresencas(); });
            $('#dataPresenca').on('change', function(){ atualizarInfo(); carregarPresencas(); });
            $('.btn-salvar').on('click', function(e){ e.preventDefault(); salvarPresenca(); });
            $('#btnExportCsv').on('click', function(e){ e.preventDefault(); exportarMensal('csv'); });
            $('#btnExportPdf').on('click', function(e){ e.preventDefault(); exportarMensal('pdf'); });
            // Seta mês atual
            const now = new Date();
            $('#mesRelatorio').val(`${now.getFullYear()}-${String(now.getMonth()+1).padStart(2,'0')}`);
        }

        function atualizarInfo(){
            const turmaTxt = $('#turmaSelect option:selected').text() || '';
            const data = $('#dataPresenca').val();
            $('#turmaInfo').text(turmaTxt);
            if (data){ const p = data.split('-'); $('#dataInfo').text(`${p[2]}/${p[1]}/${p[0]}`); }
        }

        function carregarTurmas(){
            const ano = 2025;
            const $sel = $('#turmaSelect');
            $sel.prop('disabled', true).empty().append('<option value="" disabled selected>Carregando turmas...</option>');
            $.getJSON('../includes/ajax/listar_turmas.php', { ano })
                .done(function(res){
                    $sel.empty().append('<option value="" disabled selected>-- Escolha uma turma --</option>');
                    if (res && res.success && Array.isArray(res.data) && res.data.length){
                        res.data.forEach(t => $sel.append(`<option value="${t.ID_Turma}">${t.Nome_Turma} (${t.Turno||''})</option>`));
                        $sel.prop('disabled', false);
                    } else {
                        $sel.append('<option value="" disabled>Nenhuma turma encontrada</option>');
                    }
                })
                .fail(function(){ $sel.empty().append('<option value="" disabled>Falha ao carregar turmas</option>'); });
        }

        function carregarAlunosEPresencas(){
            if (!turmaAtual){ $('#tabelaAlunos').html(''); return; }
            // Carrega alunos
            $.getJSON('../includes/ajax/listar_alunos_por_turma.php', { turma_id: turmaAtual })
                .done(function(res){ alunosCache = (res && res.success && Array.isArray(res.data)) ? res.data : []; renderTabela(); carregarPresencas(); })
                .fail(function(){ alunosCache = []; renderTabela(); });
        }

        function carregarPresencas(){
            if (!turmaAtual){ return; }
            const data = $('#dataPresenca').val();
            if (!data){ return; }
            $.getJSON('../includes/ajax/professor/presencas/listar.php', { turma_id: turmaAtual, data })
                .done(function(res){
                    presencasMap = {};
                    if (res && res.success && Array.isArray(res.data)){
                        res.data.forEach(r => { presencasMap[parseInt(r.ID_Matricula,10)] = (r.Status||'P'); });
                    }
                    // aplica seleção nas linhas
                    aplicarPresencasNaTabela();
                })
                .fail(function(){ /* mantém default */ });
        }

        function renderTabela(){
            const $tbody = $('#tabelaAlunos');
            $tbody.empty();
            if (!alunosCache.length){
                $tbody.append('<tr><td colspan="3">Nenhum aluno encontrado para esta turma.</td></tr>');
                return;
            }
            alunosCache.forEach((aluno, idx) => {
                const idMat = aluno.ID_Matricula;
                const linha = `
                    <tr data-idmatricula="${idMat}">
                        <td>${aluno.Matricula || ''}</td>
                        <td>${aluno.Nome_Completo || ''}</td>
                        <td>
                          <div class="radio-group">
                            <label class="radio-option"><input type="radio" name="p_${idMat}" value="presente"> Presente</label>
                            <label class="radio-option"><input type="radio" name="p_${idMat}" value="ausente"> Ausente</label>
                            <label class="radio-option"><input type="radio" name="p_${idMat}" value="justificado"> Justificado</label>
                          </div>
                        </td>
                    </tr>`;
                $tbody.append(linha);
            });
        }

        function aplicarPresencasNaTabela(){
            Object.keys(presencasMap).forEach(idMatStr => {
                const idMat = parseInt(idMatStr, 10);
                const cod = presencasMap[idMat];
                let val = 'presente';
                if (cod === 'A') val = 'ausente';
                else if (cod === 'J') val = 'justificado';
                $(`input[name="p_${idMat}"][value="${val}"]`).prop('checked', true);
            });
            // Default para os demais: presente
            $('#tabelaAlunos tr').each(function(){
                const idMat = parseInt($(this).attr('data-idmatricula'),10);
                if (!presencasMap[idMat]){
                    $(`input[name="p_${idMat}"][value="presente"]`).prop('checked', true);
                }
            });
        }

        function salvarPresenca(){
            if (!turmaAtual){ alert('Selecione uma turma.'); return; }
            const data = $('#dataPresenca').val();
            if (!data){ alert('Selecione a data.'); return; }
            const updates = [];
            $('#tabelaAlunos tr').each(function(){
                const idMat = parseInt($(this).attr('data-idmatricula'),10);
                const val = $(`input[name="p_${idMat}"]:checked`).val();
                updates.push({ id_matricula: idMat, status: val });
            });
            $.ajax({
                url: '../includes/ajax/professor/presencas/salvar.php',
                method: 'POST',
                contentType: 'application/json; charset=utf-8',
                data: JSON.stringify({ turma_id: turmaAtual, data, updates }),
                dataType: 'json'
            }).done(function(res){
                if (res && res.success){ alert('Presenças salvas com sucesso!'); carregarPresencas(); }
                else { alert(res && res.message ? res.message : 'Falha ao salvar'); }
            }).fail(function(xhr){
                let msg = 'Erro ao salvar presenças';
                try { const r = JSON.parse(xhr.responseText); if (r.message) msg = r.message; } catch{}
                alert(msg);
            });
        }

        function marcarTodos(tipo){
            if (tipo !== 'presente' && tipo !== 'ausente' && tipo !== 'justificado') return;
            $('#tabelaAlunos tr').each(function(){
                const idMat = $(this).attr('data-idmatricula');
                $(`input[name="p_${idMat}"][value="${tipo}"]`).prop('checked', true);
            });
        }

        function exportarMensal(formato){
            if (!turmaAtual){ alert('Selecione uma turma.'); return; }
            const mes = $('#mesRelatorio').val();
            if (!mes){ alert('Selecione o mês do relatório.'); return; }
            if (formato === 'csv'){
                const url = `../includes/ajax/professor/presencas/relatorio_mensal.php?turma_id=${encodeURIComponent(turmaAtual)}&mes=${encodeURIComponent(mes)}&formato=csv`;
                window.open(url, '_blank');
                return;
            }
            if (formato === 'pdf'){
                // Busca JSON e gera PDF no cliente
                $.getJSON('../includes/ajax/professor/presencas/relatorio_mensal.php', { turma_id: turmaAtual, mes: mes, formato: 'json' })
                    .done(function(res){
                        if (!res || !res.success){ alert('Falha ao gerar relatório.'); return; }
                        // Garante nome da turma
                        const dados = res.data || {};
                        if (!dados.turma_nome){
                            const nomeSel = $('#turmaSelect option:selected').text();
                            if (nomeSel) dados.turma_nome = nomeSel;
                        }
                        gerarPdfMensal(dados);
                    })
                    .fail(function(){ alert('Erro ao carregar dados do relatório.'); });
            }
        }

        function gerarPdfMensal(data){
            if (!window.jspdf || !window.jspdf.jsPDF){
                alert('Biblioteca de PDF não carregada. Verifique sua conexão com a internet.');
                return;
            }
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF({ orientation: 'landscape', format: 'a4' }); // sempre A4
            if (typeof doc.autoTable !== 'function'){
                alert('Plugin AutoTable não carregado. Aguarde alguns segundos e tente novamente.');
                return;
            }

            const margins = { top: 16, left: 10, right: 10 };
            const pageWidth = doc.internal.pageSize.getWidth();
            const usableWidth = pageWidth - margins.left - margins.right;

            // Larguras (mm)
            const wMat = 22;
            const wNome = 68; // ligeiramente menor para caber mais dias
            const wDia = 7;
            const wTot = 8;
            const wPerc = 14;
            const fixedWidth = wMat + wNome + (3 * wTot) + wPerc; // parte fixa sem dias

            // Quantos dias cabem por tabela em A4
            const dias = Array.isArray(data.dias) ? data.dias : [];
            const numDias = dias.length;
            const maxDiasPorBloco = Math.max(1, Math.floor((usableWidth - fixedWidth) / wDia));

            // Título
            doc.setFontSize(12);
            const turmaTitulo = data.turma_nome ? data.turma_nome : `Turma ${data.turma_id}`;
            const titulo = `Relatório de Chamada - ${turmaTitulo} - ${data.mes}`;
            doc.text(titulo, margins.left, 12);

            // Gera uma ou mais tabelas em blocos de dias
            let bloco = 0;
            for (let start = 0; start < numDias; start += maxDiasPorBloco) {
                const end = Math.min(start + maxDiasPorBloco, numDias);
                const diasBloco = dias.slice(start, end);

                // Cabeçalho do bloco
                const head = [[
                    'Matrícula',
                    'Nome',
                    ...diasBloco.map(d => { const p = d.split('-'); return `${p[2]}/${p[1]}`; }),
                    'P', 'A', 'J', '% Presença'
                ]];

                // Corpo do bloco
                const body = [];
                data.linhas.forEach(l => {
                    const row = [l.Matricula, l.Nome];
                    diasBloco.forEach(d => {
                        const st = l.dias[d] || '';
                        row.push(st);
                    });
                    row.push(l.totais.P);
                    row.push(l.totais.A);
                    row.push(l.totais.J);
                    row.push(l.percentual !== null ? (l.percentual+'%') : '');
                    body.push(row);
                });

                // Estilos de coluna deste bloco
                const firstDayIdx = 2;
                const lastDayIdx = firstDayIdx + diasBloco.length - 1;
                const idxP = lastDayIdx + 1;
                const idxA = lastDayIdx + 2;
                const idxJ = lastDayIdx + 3;
                const idxPerc = lastDayIdx + 4;

                const colStyles = {
                    0: { cellWidth: wMat },
                    1: { cellWidth: wNome, overflow: 'ellipsize' },
                };
                for (let i = firstDayIdx; i <= lastDayIdx; i++) {
                    colStyles[i] = { cellWidth: wDia, halign: 'center' };
                }
                colStyles[idxP] = { cellWidth: wTot, halign: 'center' };
                colStyles[idxA] = { cellWidth: wTot, halign: 'center' };
                colStyles[idxJ] = { cellWidth: wTot, halign: 'center' };
                colStyles[idxPerc] = { cellWidth: wPerc, halign: 'center' };

                // Nova página para blocos além do primeiro
                if (bloco > 0) {
                    doc.addPage('a4', 'landscape');
                    doc.setFontSize(12);
                    doc.text(`${titulo} (Continuação)`, margins.left, 12);
                }

                doc.autoTable({
                    head: head,
                    body: body,
                    startY: 14 + 4,
                    margin: margins,
                    theme: 'grid',
                    styles: { fontSize: 7, cellPadding: 1.2, lineWidth: 0.1, valign: 'middle' },
                    headStyles: { fillColor: [52, 152, 219], halign: 'center', fontStyle: 'bold' },
                    columnStyles: colStyles,
                    tableWidth: 'auto',
                });

                bloco++;
            }

            doc.save(`chamada_${data.turma_id}_${data.mes}.pdf`);
        }
    </script>
</body>

</html>