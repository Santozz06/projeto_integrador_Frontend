<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico Escolar</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/app-style.css">
    <link rel="stylesheet" href="../assets/css/icons.css">
    <link rel="stylesheet" href="../assets/css/sidebar-menu.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="user_adm_visualizarHistorico">
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
            <thead id="thead-disciplinas">
                <!-- Cabeçalho dinâmico gerado via JS -->
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
                // 1 (Disciplina) + 2 colunas por ano
                td.colSpan = 1 + (anos && anos.length ? anos.length*2 : 0);
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
                (anos || []).forEach((ano)=>{
                    const info = d.porAno[String(ano)] || null;
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

        function montarCabecalhoAnos(anos, seriesPorAno){
            const thead = document.getElementById('thead-disciplinas');
            const trTop = document.createElement('tr');
            const thDisc = document.createElement('th');
            thDisc.rowSpan = 2;
            thDisc.textContent = 'Disciplinas';
            trTop.appendChild(thDisc);
            (anos || []).forEach((ano)=>{
                const th = document.createElement('th');
                th.colSpan = 2;
                const serie = seriesPorAno && seriesPorAno[String(ano)] ? ` (${seriesPorAno[String(ano)]})` : '';
                th.textContent = String(ano) + serie;
                trTop.appendChild(th);
            });
            const trSub = document.createElement('tr');
            (anos || []).forEach(()=>{
                const thNota = document.createElement('th'); thNota.textContent = 'Nota'; trSub.appendChild(thNota);
                const thCH = document.createElement('th'); thCH.textContent = 'CH'; trSub.appendChild(thCH);
            });
            thead.innerHTML = '';
            thead.appendChild(trTop);
            thead.appendChild(trSub);
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
                    // Mostrar todos os anos cursados (ordenados)
                    const anos = (resp.anos || []).slice().sort((a,b)=>a-b);
                    montarCabecalhoAnos(anos, resp.series_por_ano || {});
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