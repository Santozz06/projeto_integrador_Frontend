<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Histórico Escolar</title>
    <style>
        body { font-family: "Times New Roman", serif; margin: 40px 60px; font-size: 14px; color: #000; line-height: 1.4; }
        .no-print { margin-bottom: 16px; }
        .btn { background:#3498db; color:#fff; border:none; padding:8px 14px; border-radius:4px; cursor:pointer; }
        .btn:hover { background:#2980b9; }
        .cabecalho { text-align: center; margin-bottom: 30px; }
        .titulo { text-align: center; font-weight: bold; font-size: 18px; margin: 20px 0; text-decoration: underline; }
        .dados-aluno, .tabela-disciplinas { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .dados-aluno td { padding: 5px; border: 1px solid #ddd; }
        .tabela-disciplinas th, .tabela-disciplinas td { border: 1px solid #000; padding: 8px; text-align: center; }
        .tabela-disciplinas th { background-color: #f2f2f2; }
        .assinaturas { width: 100%; margin-top: 50px; }
        .assinaturas td { padding-top: 50px; text-align: center; width: 50%; }
        @media print { .no-print { display:none } body { margin:0 } }
    </style>
    <script>
        // Disponibiliza o id do aluno logado via sessão
        const ALUNO_ID = <?php echo isset($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : 'null'; ?>;
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
</head>
<body>
    <div class="no-print">
        <button class="btn" onclick="window.history.back()">Voltar</button>
        <button class="btn" onclick="gerarPDF()" style="margin-left:8px">Baixar PDF</button>
    </div>

    <div id="conteudo-historico">
        <div class="cabecalho">
            <h2>ESCOLA MUNICIPAL DE ENSINO FUNDAMENTAL</h2>
            <p>Rua Y, 123 - Centro - Parobé/RS - CEP: 95630-000</p>
            <p>Telefone: (51) 1234-5678 - Email: contato@escola.com.br</p>
        </div>

        <div class="titulo">HISTÓRICO ESCOLAR</div>

        <table class="dados-aluno">
            <tr>
                <td width="25%"><strong>Nome:</strong> <span id="nome-aluno">—</span></td>
                <td width="25%"><strong>INEP:</strong> <span id="inep-aluno">—</span></td>
                <td width="25%"><strong>Matrícula:</strong> <span id="matricula-aluno">—</span></td>
            </tr>
            <tr>
                <td><strong>Nascimento:</strong> <span id="nascimento-aluno">—</span></td>
                <td><strong>Nacionalidade:</strong> <span id="nacionalidade-aluno">—</span></td>
                <td><strong>Naturalidade:</strong> <span id="naturalidade-aluno">—</span></td>
            </tr>
            <tr>
                <td colspan="2"><strong>Filiação:</strong> <span id="filiacao-aluno">—</span></td>
                <td><strong>NIS:</strong> <span id="nis-aluno">—</span></td>
            </tr>
        </table>

        <table class="tabela-disciplinas">
            <thead>
                <tr>
                    <th rowspan="2">Disciplinas</th>
                    <th colspan="2" id="col-ano-1">1º Ano</th>
                    <th colspan="2" id="col-ano-2">2º Ano</th>
                    <th colspan="2" id="col-ano-3">3º Ano</th>
                </tr>
                <tr>
                    <th>Nota</th>
                    <th>CH</th>
                    <th>Nota</th>
                    <th>CH</th>
                    <th>Nota</th>
                    <th>CH</th>
                </tr>
            </thead>
            <tbody id="dados-disciplinas"></tbody>
        </table>

        <div class="observacoes">
            <p><strong>Observações:</strong> <span id="observacoes-aluno">—</span></p>
        </div>

        <table class="assinaturas">
            <tr>
                <td>
                    ___________________________<br>
                    <strong>Data:</strong> <span id="data-emissao">—</span>
                </td>
                <td>
                    ___________________________<br>
                    <strong>Diretor(a)</strong>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    ___________________________<br>
                    <strong>Secretário(a) Escolar</strong>
                </td>
            </tr>
        </table>

        <div class="autenticidade">
            <p>Para verificar a autenticidade deste documento, acesse:
                <a href="#" target="_blank">http://meusite.com.br/validar</a>
            </p>
            <p>Código de verificação: <strong id="codigo-verificacao">—</strong></p>
        </div>
    </div>

    <script>
        function gerarCodigoAleatorio() {
            const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ0123456789';
            let r=''; for(let i=0;i<6;i++) r += chars.charAt(Math.floor(Math.random()*chars.length));
            return r;
        }
        function atualizarCabecalhoAnos(anos){
            const ths = document.querySelectorAll('.tabela-disciplinas thead tr:first-child th');
            for (let i=0;i<3;i++){
                const idx = i+1; if (ths[idx]) ths[idx].textContent = anos[i] ? String(anos[i]) : `${i+1}º Ano`;
            }
        }
        function preencherCabecalho(aluno){
            document.getElementById('nome-aluno').textContent = aluno.Nome_Completo || '—';
            document.getElementById('matricula-aluno').textContent = aluno.Matricula || '—';
            document.getElementById('inep-aluno').textContent = aluno.INEP || '—';
            document.getElementById('nascimento-aluno').textContent = aluno.Data_Nascimento ? new Date(aluno.Data_Nascimento).toLocaleDateString('pt-BR') : '—';
            document.getElementById('nacionalidade-aluno').textContent = aluno.Nacionalidade || '—';
            document.getElementById('naturalidade-aluno').textContent = aluno.Naturalidade || '—';
            document.getElementById('filiacao-aluno').textContent = aluno.Filiacao || '—';
            document.getElementById('nis-aluno').textContent = aluno.NIS || '—';
            const hoje = new Date();
            document.getElementById('data-emissao').textContent = hoje.toLocaleDateString('pt-BR');
            document.getElementById('codigo-verificacao').textContent = `HIST-${aluno.Matricula || 'ALUNO'}-${gerarCodigoAleatorio()}`;
        }
        function preencherDisciplinas(anos, disciplinas){
            const tbody = document.getElementById('dados-disciplinas');
            tbody.innerHTML = '';
            if (!disciplinas || !disciplinas.length){
                const tr = document.createElement('tr');
                const td = document.createElement('td');
                td.colSpan = 7; td.textContent = 'Sem registros de notas.'; tr.appendChild(td); tbody.appendChild(tr); return;
            }
            disciplinas.forEach(d=>{
                const tr = document.createElement('tr');
                const tdNome = document.createElement('td'); tdNome.textContent = d.nome; tr.appendChild(tdNome);
                const idxs=[0,1,2];
                idxs.forEach(i=>{
                    const ano = anos[i]; const info = ano ? d.porAno[String(ano)] : null;
                    const tdNota = document.createElement('td'); tdNota.textContent = info && info.nota!=null ? String(info.nota).replace('.', ',') : '—'; tr.appendChild(tdNota);
                    const tdCH = document.createElement('td'); tdCH.textContent = info && info.ch!=null ? info.ch : '—'; tr.appendChild(tdCH);
                });
                tbody.appendChild(tr);
            });
        }

        function carregar(){
            if (!ALUNO_ID){ alert('Sessão expirada. Faça login novamente.'); return; }
            fetch(`../includes/ajax/obter_historico_aluno.php?aluno_id=${encodeURIComponent(ALUNO_ID)}`)
                .then(r=>r.json())
                .then(resp=>{
                    if(!resp.success){ alert('Erro ao carregar histórico.'); return; }
                    preencherCabecalho(resp.aluno||{});
                    const anos = (resp.anos||[]).slice(0,3); atualizarCabecalhoAnos(anos);
                    preencherDisciplinas(anos, resp.disciplinas||[]);
                    if (resp.observacoes) document.getElementById('observacoes-aluno').textContent = resp.observacoes;
                }).catch(err=> alert('Erro: '+err));
        }
        function gerarPDF(){
            const element=document.getElementById('conteudo-historico');
            const matricula = document.getElementById('matricula-aluno').textContent || 'aluno';
            html2pdf().set({ margin:10, filename:`historico_${matricula}.pdf`, image:{type:'jpeg',quality:0.98}, html2canvas:{scale:2}, jsPDF:{unit:'mm',format:'a4',orientation:'portrait'} }).from(element).save();
        }
        window.onload = carregar;
    </script>
</body>
</html>