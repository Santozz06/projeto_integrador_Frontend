<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documento de Transferência</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/app-style.css">
    <link rel="stylesheet" href="../assets/css/icons.css">
    <link rel="stylesheet" href="../assets/css/sidebar-menu.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="user_adm_documentoTransferencia">
    <div class="no-print">
        <button id="btn-pdf">Baixar PDF</button>
    </div>

    <div id="doc-transferencia">
        <div class="cabecalho">
            <h2>ESCOLA MUNICIPAL DE ENSINO FUNDAMENTAL</h2>
            <div>Rua Y, 123 - Centro - Parobé/RS - CEP: 95630-000</div>
            <div>Telefone: (51) 1234-5678 - Email: contato@escola.com.br</div>
        </div>

        <div class="titulo">DECLARAÇÃO DE TRANSFERÊNCIA</div>

        <div class="bloco">
            <span class="label">Aluno(a):</span> <span id="aluno-nome">—</span><br>
            <span class="label">Matrícula:</span> <span id="aluno-matricula">—</span><br>
            <span class="label">Turma/Turno atuais:</span> <span id="aluno-turma-turno">—</span>
        </div>

        <div class="bloco">
            <span class="label">Escola de destino:</span> <span id="escola-destino">—</span><br>
            <span class="label">Município/UF:</span> <span id="municipio-uf">—</span><br>
            <span class="label">Data da transferência:</span> <span id="data-transferencia">—</span>
        </div>

        <div class="bloco">
            Declaramos, para os devidos fins, que o(a) aluno(a) acima foi transferido(a) desta instituição na data indicada.
        </div>

        <table class="assinaturas">
            <tr>
                <td>
                    ___________________________<br>
                    Diretor(a)
                </td>
                <td>
                    ___________________________<br>
                    Secretário(a) Escolar
                </td>
            </tr>
        </table>

        <div class="titulo">HISTÓRICO ESCOLAR (Resumo)</div>

        <table class="tabela">
            <tr>
                <td width="35%"><span class="label">Nascimento:</span> <span id="aluno-nascimento">—</span></td>
                <td width="35%"><span class="label">Nacionalidade:</span> <span id="aluno-nacionalidade">—</span></td>
                <td width="30%"><span class="label">Naturalidade:</span> <span id="aluno-naturalidade">—</span></td>
            </tr>
            <tr>
                <td colspan="2"><span class="label">Filiação:</span> <span id="aluno-filiacao">—</span></td>
                <td><span class="label">NIS:</span> <span id="aluno-nis">—</span></td>
            </tr>
        </table>

        <table class="grid">
            <thead>
                <tr>
                    <th rowspan="2">Disciplinas</th>
                    <th colspan="2" id="ano-col-1">Ano 1</th>
                    <th colspan="2" id="ano-col-2">Ano 2</th>
                    <th colspan="2" id="ano-col-3">Ano 3</th>
                </tr>
                <tr>
                    <th>Nota</th><th>CH</th>
                    <th>Nota</th><th>CH</th>
                    <th>Nota</th><th>CH</th>
                </tr>
            </thead>
            <tbody id="tbody-disciplinas"></tbody>
        </table>

        <div><span class="label">Observações:</span> <span id="obs">—</span></div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        function qp(name){ const p=new URLSearchParams(window.location.search); return p.get(name)||''; }

        function preencherCabecalho(aluno){
            document.getElementById('aluno-nome').textContent = aluno.Nome_Completo || '—';
            document.getElementById('aluno-matricula').textContent = aluno.Matricula || '—';
            const nasc = aluno.Data_Nascimento ? new Date(aluno.Data_Nascimento).toLocaleDateString('pt-BR') : '—';
            document.getElementById('aluno-nascimento').textContent = nasc;
            document.getElementById('aluno-nacionalidade').textContent = aluno.Nacionalidade || '—';
            document.getElementById('aluno-naturalidade').textContent = aluno.Naturalidade || '—';
            document.getElementById('aluno-filiacao').textContent = aluno.Filiacao || '—';
            document.getElementById('aluno-nis').textContent = aluno.NIS || '—';
        }

        function preencherAnos(anos){
            const a1 = anos[0] ? String(anos[0]) : 'Ano 1';
            const a2 = anos[1] ? String(anos[1]) : 'Ano 2';
            const a3 = anos[2] ? String(anos[2]) : 'Ano 3';
            document.getElementById('ano-col-1').textContent = a1;
            document.getElementById('ano-col-2').textContent = a2;
            document.getElementById('ano-col-3').textContent = a3;
        }

        function preencherDisciplinas(anos, disciplinas){
            const tb = document.getElementById('tbody-disciplinas');
            tb.innerHTML = '';
            if (!disciplinas || !disciplinas.length){
                const tr = document.createElement('tr');
                const td = document.createElement('td');
                td.colSpan = 7; td.textContent = 'Sem registros.'; tr.appendChild(td); tb.appendChild(tr); return;
            }
            disciplinas.forEach(d=>{
                const tr = document.createElement('tr');
                const tdNome = document.createElement('td');
                tdNome.textContent = d.nome; tr.appendChild(tdNome);
                [0,1,2].forEach(i=>{
                    const ano = anos[i]; const info = ano ? d.porAno[String(ano)] : null;
                    const tdN = document.createElement('td'); tdN.textContent = info && info.nota!=null ? String(info.nota).replace('.',',') : '—'; tr.appendChild(tdN);
                    const tdC = document.createElement('td'); tdC.textContent = info && info.ch!=null ? info.ch : '—'; tr.appendChild(tdC);
                });
                tb.appendChild(tr);
            });
        }

        function gerarPDF(){
            const el = document.getElementById('doc-transferencia');
            const mat = document.getElementById('aluno-matricula').textContent || 'aluno';
            const data = (qp('data')||'').replaceAll('-','');
            const filename = `transferencia_${mat}_${data||'hoje'}.pdf`;
            html2pdf().set({margin:10, filename, image:{type:'jpeg',quality:0.98}, html2canvas:{scale:2}, jsPDF:{unit:'mm', format:'a4', orientation:'portrait'}})
                .from(el).save();
        }

        document.getElementById('btn-pdf').addEventListener('click', gerarPDF);

        // Boot
        (function(){
            const alunoId = qp('aluno_id');
            const escola = qp('escola');
            const munuf = qp('mun');
            const data = qp('data');
            const auto = qp('auto');
            document.getElementById('escola-destino').textContent = escola || '—';
            document.getElementById('municipio-uf').textContent = munuf || '—';
            document.getElementById('data-transferencia').textContent = data ? new Date(data).toLocaleDateString('pt-BR') : new Date().toLocaleDateString('pt-BR');

            // Turma/Turno atuais (opcional): podemos exibir via uma busca simplificada
            if (!alunoId){ return; }
            // Carrega matricula ativa para exibir turma/turno
            fetch(`../includes/ajax/obter_matricula_ativa.php?aluno_id=${encodeURIComponent(alunoId)}`)
                .then(r=>r.json()).then(m=>{
                    if (m && m.success && m.data){
                        const labelTurma = m.data.Nome_Turma ? `${m.data.Nome_Turma}${m.data.Etapa ? ' ('+m.data.Etapa+')' : ''}` : '—';
                        document.getElementById('aluno-turma-turno').textContent = `${labelTurma} / ${m.data.Turno || '—'}`;
                    }
                }).catch(()=>{});

            // Carrega histórico
            fetch(`../includes/ajax/obter_historico_aluno.php?aluno_id=${encodeURIComponent(alunoId)}`)
                .then(r=>r.json())
                .then(resp=>{
                    if (!resp.success) return;
                    preencherCabecalho(resp.aluno||{});
                    document.getElementById('obs').textContent = resp.observacoes || '';
                    const anos = (resp.anos||[]).slice(0,3);
                    preencherAnos(anos);
                    preencherDisciplinas(anos, resp.disciplinas||[]);
                    if (auto === '1') { setTimeout(gerarPDF, 200); }
                });
        })();
    </script>
</body>

</html>
