<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <link rel="icon" href="../assets/images/favicon.ico" type="image/x-icon">
  <meta charset="UTF-8">
  <title>Atestado de Frequência</title>
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
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
  <script>
    document.body.style.cssText = 'background: #ffffff !important; background-color: #ffffff !important; background-image: none !important;';
    if (document.body.classList) {
      document.body.classList.remove('bg-theme', 'bg-theme1', 'bg-theme2', 'bg-theme3', 'bg-theme4', 'bg-theme5');
    }
  </script>
</head>

<body class="user_aluno_frequencia_detalhes">

  <div id="doc">
    <div class="cabecalho">
      República Federativa do Brasil<br>
      Ministério da Educação<br>
      Secretaria de Educação Profissional e Tecnológica<br>
      [Nome da Instituição de Ensino]<br>
      [Nome do Campus ou Unidade]<br>
      Rua, Bairro - Parobé - 95630-000
    </div>

    <div class="titulo">ATESTADO DE FREQUÊNCIA</div>

    <div class="texto">
      Atestamos, para os fins a que se fizerem necessários, que <strong><span id="nome-aluno">Aluno(a)</span></strong>,
      CPF nº <strong><span id="cpf-aluno">—</span></strong>, possui vínculo regular de estudante nesta instituição sob o
      nº de
      matrícula <strong><span id="matricula-aluno">—</span></strong>,
      modalidade <strong><span id="modalidade">Presencial</span></strong>, no turno <strong><span
          id="turno">—</span></strong>, ofertado pela <strong>NOME DA
        ESCOLA</strong>.
    </div>

    <div class="texto">
      Registra-se o percentual de frequência de <strong><span id="percentual">—</span>%</strong> no ano de <strong><span
          id="ano">—</span></strong>.
    </div>

    <div class="data-local">
      Parobé, <span id="data-emissao">—</span>.
    </div>

    <div class="assinatura">
      _________________________________________________<br>
      Assinatura do(a) Diretor(a) ou Secretário(a) Escolar
    </div>
  </div>

  <script>
    (function () {
      document.body.style.opacity = '0';
      const params = new URLSearchParams(window.location.search);
      const ano = params.get('ano') || '2025';

      function preencherDadosBasicos(aluno) {
        document.getElementById('nome-aluno').textContent = aluno.Nome_Completo || aluno.nome || '—';
        document.getElementById('cpf-aluno').textContent = aluno.CPF || aluno.cpf || '—';
        document.getElementById('matricula-aluno').textContent = aluno.Matricula || aluno.matricula || '—';
      }

      function preencherFrequencia(data) {
        document.getElementById('percentual').textContent = data.percentual !== null ? data.percentual : '—';
        document.getElementById('ano').textContent = data.ano || ano;
        const hoje = new Date();
        const dataStr = hoje.toLocaleDateString('pt-BR', { day: 'numeric', month: 'long', year: 'numeric' });
        document.getElementById('data-emissao').textContent = dataStr;

        if (data.turma) {
          document.getElementById('turno').textContent = data.turno || 'Integral';
          document.getElementById('modalidade').textContent = data.modalidade || 'Presencial';
        }
      }

      function gerarPDF() {
        if (typeof html2pdf === 'undefined') {
          alert('Erro: biblioteca html2pdf não está disponível');
          document.body.style.opacity = '1';
          return;
        }

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

        var source = document.getElementById('doc');
        var cleanEl = _createCleanWrapper(source);

        // Injetar estilos temporários que escondem overlays/pseudo-elements
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

        html2pdf().set({
          margin: 10,
          filename: `atestado_frequencia_${ano}.pdf`,
          image: { type: 'jpeg', quality: 0.98 },
          html2canvas: { scale: 2 },
          jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        }).from(cleanEl).save().then(function () {
          setTimeout(function () {
            try { if (cleanEl && cleanEl.parentNode) cleanEl.parentNode.removeChild(cleanEl); } catch (e) { }
            _removeCleanStyles();
            window.history.back();
          }, 800);
        }).catch(function (err) {
          alert('Erro ao gerar PDF. Verifique o console.');
          try { if (cleanEl && cleanEl.parentNode) cleanEl.parentNode.removeChild(cleanEl); } catch (e) { }
          _removeCleanStyles();
          document.body.style.opacity = '1';
        });
      }

      Promise.all([
        fetch(`../includes/ajax/shared/historico/obter_historico_aluno.php?aluno_id=${encodeURIComponent(<?php echo isset($_SESSION['usuario_id']) ? (int) $_SESSION['usuario_id'] : 0; ?>)}`)
          .then(r => r.json()).catch(() => ({ success: false })),
        fetch(`../includes/ajax/aluno/frequencia_resumo.php?ano=${encodeURIComponent(ano)}`)
          .then(r => r.json()).catch(() => ({ success: false }))
      ]).then(function (resps) {
        var hist = resps[0] && resps[0].success ? resps[0] : null;
        var freq = resps[1] && resps[1].success ? resps[1] : null;
        if (hist && hist.aluno) preencherDadosBasicos(hist.aluno);
        if (freq && freq.data) preencherFrequencia(freq.data);
        gerarPDF();
      }).catch(function () {
        alert('Erro ao carregar dados. Gerando PDF mesmo assim...');
        gerarPDF();
      });
    })();
  </script>
</body>

</html>