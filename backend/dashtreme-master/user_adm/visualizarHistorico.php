<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico Escolar</title>
    <style>
        body {
            font-family: "Times New Roman", serif;
            margin: 40px 60px;
            font-size: 14px;
            color: #000;
            line-height: 1.4;
        }

        .no-print {
            display: block;
            margin-bottom: 20px;
        }

        .btn-print {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .btn-print:hover {
            background-color: #2980b9;
        }

        .cabecalho {
            text-align: center;
            margin-bottom: 30px;
        }

        .titulo {
            text-align: center;
            font-weight: bold;
            font-size: 18px;
            margin: 20px 0;
            text-decoration: underline;
        }

        .dados-aluno {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .dados-aluno td {
            padding: 5px;
            border: 1px solid #ddd;
        }

        .tabela-disciplinas {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .tabela-disciplinas th,
        .tabela-disciplinas td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }

        .tabela-disciplinas th {
            background-color: #f2f2f2;
        }

        .rodape {
            margin-top: 40px;
        }

        .assinaturas {
            width: 100%;
            margin-top: 50px;
        }

        .assinaturas td {
            padding-top: 50px;
            text-align: center;
            width: 50%;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                margin: 0;
            }
        }
        .navbar {
            background-color: rgba(0, 0, 0, 0.2) !important;
            backdrop-filter: blur(10px);
        }
    </style>
</head>

<body>
    <!-- Botão de impressão  -->
    <div class="no-print">
        <button class="btn-print" onclick="window.print()">
            <i class="zmdi zmdi-print"></i> Imprimir Histórico
        </button>
        <button class="btn-print" onclick="gerarPDF()" style="margin-left: 10px;">
            <i class="zmdi zmdi-download"></i> Baixar PDF
        </button>
    </div>

    <!-- Conteúdo do Histórico -->
    <div id="conteudo-historico">
        <div class="cabecalho">
            <h2>ESCOLA MUNICIPAL DE ENSINO FUNDAMENTAL</h2>
            <p>Rua Y, 123 - Centro - Parobé/RS - CEP: 95630-000</p>
            <p>Telefone: (51) 1234-5678 - Email: contato@escola.com.br</p>
        </div>

        <div class="titulo">HISTÓRICO ESCOLAR</div>

        <table class="dados-aluno">
            <tr>
                <td width="25%"><strong>Nome:</strong> <span id="nome-aluno">Aluno</span></td>
                <td width="25%"><strong>INEP:</strong> <span id="inep-aluno">12345678</span></td>
                <td width="25%"><strong>Matrícula:</strong> <span id="matricula-aluno">20230001</span></td>
            </tr>
            <tr>
                <td><strong>Nascimento:</strong> <span id="nascimento-aluno">10/05/2010</span></td>
                <td><strong>Nacionalidade:</strong> <span id="nacionalidade-aluno">Brasileira</span></td>
                <td><strong>Naturalidade:</strong> <span id="naturalidade-aluno">Parobé/RS</span></td>
            </tr>
            <tr>
                <td colspan="2"><strong>Filiação:</strong> <span id="filiacao-aluno">Pai e Mãe</span></td>
                <td><strong>NIS:</strong> <span id="nis-aluno">123.45678.90-1</span></td>
            </tr>
        </table>

        <table class="tabela-disciplinas">
            <thead>
                <tr>
                    <th rowspan="2">Disciplinas</th>
                    <th colspan="2">1º Ano</th>
                    <th colspan="2">2º Ano</th>
                    <th colspan="2">3º Ano</th>
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
            <tbody id="dados-disciplinas">
            </tbody>
        </table>

        <div class="observacoes">
            <p><strong>Observações:</strong> <span id="observacoes-aluno">Aluno apresentou ótimo desempenho durante todo o período letivo.</span></p>
        </div>

        <table class="assinaturas">
            <tr>
                <td>
                    ___________________________<br>
                    <strong>Data:</strong> <span id="data-emissao">08/07/2025</span>
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
                <a href="http://meusite.com.br/validar" target="_blank">http://meusite.com.br/validar</a>
            </p>
            <p>Código de verificação: <strong id="codigo-verificacao">HIST-20230001-7A9B2C</strong></p>
        </div>
    </div>

    <!-- Script para PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        function getAlunoIdParam(){
            const params = new URLSearchParams(window.location.search);
            return params.get('aluno_id');
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
            document.getElementById('observacoes-aluno').textContent = ' ';

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
                td.colSpan = 7;
                td.textContent = 'Sem registros de notas.';
                tr.appendChild(td);
                tbody.appendChild(tr);
                return;
            }
            // Cabeçalho já está fixo como 1º, 2º, 3º ano — se desejar dinamizar, precisaremos gerar o thead.
            disciplinas.forEach(d => {
                const tr = document.createElement('tr');
                const tdNome = document.createElement('td');
                tdNome.textContent = d.nome;
                tr.appendChild(tdNome);
                const colAnos = [0,1,2];
                colAnos.forEach((i)=>{
                    const ano = anos[i];
                    const info = ano ? d.porAno[String(ano)] : null;
                    const tdNota = document.createElement('td');
                    tdNota.textContent = info && info.nota != null ? String(info.nota).replace('.', ',') : '—';
                    tr.appendChild(tdNota);
                    const tdCH = document.createElement('td');
                    tdCH.textContent = info && info.ch != null ? info.ch : '—';
                    tr.appendChild(tdCH);
                });
                tbody.appendChild(tr);
            });
        }

        function atualizarCabecalhoAnos(anos){
            const ths = document.querySelectorAll('.tabela-disciplinas thead tr:first-child th');
            // ths[0] é "Disciplinas"; depois 3 colunas (colspan=2) para os anos
            for (let i = 0; i < 3; i++){
                const idx = i + 1;
                if (ths[idx]){
                    const label = anos[i] ? String(anos[i]) : `${i+1}º Ano`;
                    ths[idx].textContent = label;
                }
            }
        }

        window.onload = function(){
            const alunoId = getAlunoIdParam();
            if (!alunoId){
                alert('Parâmetro aluno_id ausente.');
                return;
            }
            fetch(`../includes/ajax/obter_historico_aluno.php?aluno_id=${encodeURIComponent(alunoId)}`)
                .then(r => r.json())
                .then(resp => {
                    if (!resp.success){
                        alert('Erro ao carregar histórico: ' + (resp.message || 'desconhecido'));
                        return;
                    }
                    preencherCabecalho(resp.aluno || {});
                    if (resp.observacoes) {
                        document.getElementById('observacoes-aluno').textContent = resp.observacoes;
                    }
                    // Se houver mais de 3 anos, usamos os primeiros 3 em ordem asc
                    const anos = (resp.anos || []).slice(0,3);
                    atualizarCabecalhoAnos(anos);
                    preencherDisciplinas(anos, resp.disciplinas || []);
                })
                .catch(err => alert('Erro: ' + err));
        };

        // Função para gerar PDF
        function gerarPDF() {
            const element = document.getElementById('conteudo-historico');
            const matricula = document.getElementById('matricula-aluno').textContent || 'aluno';
            const filename = `historico_${matricula}.pdf`;
            
            html2pdf().set({
                margin: 10,
                filename: filename,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            }).from(element).save();
        }

        // Função para gerar código aleatório
        function gerarCodigoAleatorio() {
            const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ0123456789';
            let result = '';
            for (let i = 0; i < 6; i++) {
                result += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            return result;
        }
    </script>
</body>
</html>