<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <link rel="icon" href="../assets/images/favicon.ico" type="image/x-icon">
    <meta charset="UTF-8">
    <title>Histórico Escolar</title>
    <link href="../assets/css/app-style.css?v=<?php echo time(); ?>" rel="stylesheet" />
    <link rel="stylesheet" href="../css/style.css?v=<?php echo time(); ?>" />
    <style>
        body {
            background: #ffffff !important;
            background-color: #ffffff !important;
            background-image: none !important;
        }

        body::before,
        body::after {
            display: none !important;
        }
    </style>
    <script>
        // Disponibiliza o id do aluno logado via sessão
        const ALUNO_ID = <?php echo isset($_SESSION['usuario_id']) ? (int) $_SESSION['usuario_id'] : 'null'; ?>;
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.body.style.cssText = 'background: #ffffff !important; background-color: #ffffff !important; background-image: none !important;';
            document.body.classList.remove('bg-theme', 'bg-theme1', 'bg-theme2', 'bg-theme3', 'bg-theme4', 'bg-theme5');
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
</head>

<body class="user_aluno_historico">
    <div class="no-print">
        <button class="btn" onclick="window.history.back()">Voltar</button>
        <button class="btn ml-8" onclick="gerarPDF()">Baixar PDF</button>
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
            <thead id="thead-disciplinas"><!-- dinâmico --></thead>
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
            let r = ''; for (let i = 0; i < 6; i++) r += chars.charAt(Math.floor(Math.random() * chars.length));
            return r;
        }
        function montarCabecalhoAnos(anos, seriesPorAno) {
            const thead = document.getElementById('thead-disciplinas');
            const trTop = document.createElement('tr');
            const thDisc = document.createElement('th');
            thDisc.rowSpan = 2; thDisc.textContent = 'Disciplinas';
            trTop.appendChild(thDisc);
            (anos || []).forEach(ano => {
                const th = document.createElement('th'); th.colSpan = 2; const serie = seriesPorAno && seriesPorAno[String(ano)] ? ` (${seriesPorAno[String(ano)]})` : ''; th.textContent = String(ano) + serie; trTop.appendChild(th);
            });
            const trSub = document.createElement('tr');
            (anos || []).forEach(_ => {
                const thN = document.createElement('th'); thN.textContent = 'Nota'; trSub.appendChild(thN);
                const thC = document.createElement('th'); thC.textContent = 'CH'; trSub.appendChild(thC);
            });
            thead.innerHTML = ''; thead.appendChild(trTop); thead.appendChild(trSub);
        }
        function preencherCabecalho(aluno) {
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
        function preencherDisciplinas(anos, disciplinas) {
            const tbody = document.getElementById('dados-disciplinas');
            tbody.innerHTML = '';
            if (!disciplinas || !disciplinas.length) {
                const tr = document.createElement('tr');
                const td = document.createElement('td');
                td.colSpan = 1 + (anos && anos.length ? anos.length * 2 : 0); td.textContent = 'Sem registros de notas.'; tr.appendChild(td); tbody.appendChild(tr); return;
            }
            disciplinas.forEach(d => {
                const tr = document.createElement('tr');
                const tdNome = document.createElement('td'); tdNome.textContent = d.nome; tr.appendChild(tdNome);
                (anos || []).forEach(ano => {
                    const info = d.porAno[String(ano)] || null;
                    const tdNota = document.createElement('td'); tdNota.textContent = info && info.nota != null ? String(info.nota).replace('.', ',') : '—'; tr.appendChild(tdNota);
                    const tdCH = document.createElement('td'); tdCH.textContent = info && info.ch != null ? info.ch : '—'; tr.appendChild(tdCH);
                });
                tbody.appendChild(tr);
            });
        }

        function carregar() {
            if (!ALUNO_ID) { alert('Sessão expirada. Faça login novamente.'); return; }
            fetch(`../includes/ajax/shared/historico/obter_historico_aluno.php?aluno_id=${encodeURIComponent(ALUNO_ID)}`)
                .then(r => r.json())
                .then(resp => {
                    if (!resp.success) { alert('Erro ao carregar histórico.'); return; }
                    preencherCabecalho(resp.aluno || {});
                    const anos = (resp.anos || []).slice().sort((a, b) => a - b); montarCabecalhoAnos(anos, resp.series_por_ano || {});
                    preencherDisciplinas(anos, resp.disciplinas || []);
                    if (resp.observacoes) document.getElementById('observacoes-aluno').textContent = resp.observacoes;
                }).catch(err => alert('Erro: ' + err));
        }
        function gerarPDF() {
            const element = document.getElementById('conteudo-historico');
            const matricula = document.getElementById('matricula-aluno').textContent || 'aluno';
            // Criar wrapper limpo para geração do PDF (evita efeitos do tema)
            function _createCleanWrapper(sourceEl) {
                try {
                    var clone = sourceEl.cloneNode(true);
                    var nodes = clone.querySelectorAll('*');
                    Array.prototype.forEach.call(nodes, function (n) { n.className = ''; n.removeAttribute('style'); });
                    clone.className = '';
                    var wrapper = document.createElement('div');
                    wrapper.id = 'pdf-clean-wrapper';
                    wrapper.style.position = 'fixed';
                    wrapper.style.top = '0';
                    wrapper.style.left = '0';
                    wrapper.style.zIndex = '2147483647';
                    wrapper.style.background = '#ffffff';
                    wrapper.style.color = '#000000';
                    wrapper.style.padding = '10mm';
                    wrapper.style.width = '210mm';
                    wrapper.style.minHeight = '297mm';
                    wrapper.style.boxSizing = 'border-box';
                    wrapper.style.fontFamily = '"Times New Roman", serif';
                    wrapper.style.fontSize = '14px';
                    wrapper.appendChild(clone);
                    document.body.appendChild(wrapper);
                    return wrapper;
                } catch (e) { console.error('Erro ao criar wrapper limpo:', e); return sourceEl; }
            }

            var source = document.getElementById('conteudo-historico');
            var cleanEl = _createCleanWrapper(source);

            function _injectCleanStyles() {
                if (document.getElementById('pdf-clean-style')) return;
                var s = document.createElement('style'); s.id = 'pdf-clean-style';
                s.type = 'text/css';
                s.appendChild(document.createTextNode('\n'
                    + 'html, body { background: #ffffff !important; }\n'
                    + '#pageloader-overlay, .overlay, .overlay.toggle-menu, .modal-backdrop, .modal-backdrop.show, .menu-overlay, #pageloader-overlay { display: none !important; }\n'
                    + 'body::before, body::after, html::before, html::after { display: none !important; content: none !important; background: none !important; }\n'
                    + '#pdf-clean-wrapper, #pdf-clean-wrapper * { background: transparent !important; background-image: none !important; background-color: transparent !important; color: #000 !important; box-shadow: none !important; text-shadow: none !important; filter: none !important; -webkit-filter: none !important; backdrop-filter: none !important; -webkit-backdrop-filter: none !important; }\n'
                ));
                document.head.appendChild(s);
            }
            function _removeCleanStyles() { try { var ss = document.getElementById('pdf-clean-style'); if (ss && ss.parentNode) ss.parentNode.removeChild(ss); } catch (e) { } }

            _injectCleanStyles();
            html2pdf().set({ margin: 10, filename: `historico_${matricula}.pdf`, image: { type: 'jpeg', quality: 0.98 }, html2canvas: { scale: 2 }, jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' } }).from(cleanEl).save()
                .then(function () { try { if (cleanEl && cleanEl.parentNode) cleanEl.parentNode.removeChild(cleanEl); } catch (e) { } _removeCleanStyles(); })
                .catch(function () { try { if (cleanEl && cleanEl.parentNode) cleanEl.parentNode.removeChild(cleanEl); } catch (e) { } _removeCleanStyles(); });
        }
        window.onload = carregar;
    </script>
    <div class="overlay toggle-menu"></div>
</body>

</html>