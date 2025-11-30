<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <title>Ensino - Emitir Atestado de Matrícula</title>
  <link href="../assets/css/pace.min.css" rel="stylesheet" />
  <script src="../assets/js/pace.min.js"></script>
  <link rel="icon" href="../assets/images/favicon.ico" type="image/x-icon">
  <link href="../assets/plugins/simplebar/css/simplebar.css" rel="stylesheet" />
  <link href="../assets/css/bootstrap.min.css" rel="stylesheet" />
  <link href="../assets/css/animate.css" rel="stylesheet" />
  <link href="../assets/css/icons.css" rel="stylesheet" />
  <link href="../assets/css/sidebar-menu.css" rel="stylesheet" />
  <link href="../assets/css/app-style.css" rel="stylesheet" />
  <link rel="stylesheet" href="../css/style.css">
</head>

<body class="bg-theme bg-theme1 user_aluno_atestado_frequencia">

  <?php require("menu_padrao.php"); ?>

  <div class="clearfix"></div>

  <!-- Conteúdo da Página -->
  <div class="content-wrapper">
    <div class="container-fluid">
      <div class="row justify-content-center mt-4">
        <div class="col-lg-10">
          <div class="card shadow-lg rounded-lg">
            <div class="card-header">
              <h4 class="mb-0"><i class="zmdi zmdi-assignment mr-2"></i> Atestado de matrícula</h4>
            </div>
            <div class="card-body">
              <a href="ensino.php" class="btn btn-primary btn-voltar-custom">
                <i class="zmdi zmdi-arrow-left mr-1"></i> VOLTAR
              </a>
              <button id="gerar-atestado" class="btn btn-success mt-3">Gerar Atestado</button>
              <div id="pdf-container" style="display:none;"></div>
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
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

  <script>
    (function () {
      async function buscarDadosAtestado() {
        const resp = await fetch('../includes/ajax/aluno/matricula_resumo.php');
        if (!resp.ok) return null;
        return await resp.json();
      }

      function limparTemaParaPDF() {
        if (document.getElementById('force-clean-style')) return;
        const style = document.createElement('style');
        style.id = 'force-clean-style';
        style.appendChild(document.createTextNode(`
          html, body { background: #ffffff !important; background-image: none !important; }
          * { backdrop-filter: none !important; -webkit-backdrop-filter: none !important; box-shadow: none !important; text-shadow: none !important; filter: none !important; }
          #pageloader-overlay, .overlay, .overlay.toggle-menu, .modal-backdrop, .modal-backdrop.show, .menu-overlay, .sidebar-wrapper, .sidebar-menu { display: none !important; }
          body::before, body::after, html::before, html::after { display: none !important; content: none !important; }
        `));
        document.head.appendChild(style);
      }

      function restaurarTema() {
        const style = document.getElementById('force-clean-style');
        if (style) style.remove();
      }

      async function gerarAtestado() {
        const dados = await buscarDadosAtestado();
        if (!dados || !dados.success || !dados.data) {
          alert('Não foi possível obter os dados do atestado.');
          return;
        }

        const d = dados.data;
        const dataAtual = new Date();
        const dia = String(dataAtual.getDate()).padStart(2, '0');
        const mes = String(dataAtual.getMonth() + 1).padStart(2, '0');
        const anoCorrente = dataAtual.getFullYear();
        const dataFormatada = `${dia}/${mes}/${anoCorrente}`;

        const pdfContent = `
        <div id="doc" style="width: 210mm; min-height: 297mm; padding: 25mm; font-family: 'Times New Roman', serif; font-size: 16px; color: #000;">
          <div style="text-align: center; margin-bottom: 40px;">
            <div>República Federativa do Brasil</div>
            <div>Ministério da Educação</div>
            <div style="font-weight: bold;">${d.turma || 'Escola'}</div>
          </div>

          <div style="text-align: center; font-weight: bold; font-size: 22px; margin: 60px 0;">ATESTADO DE MATRÍCULA</div>

          <div style="text-align: justify; margin-bottom: 40px;">
            Atestamos, para os fins que se fizerem necessários, que o(a) estudante <strong>${d.nome || ''}</strong>,
            matrícula nº <strong>${d.matricula || ''}</strong>, está regularmente matriculado(a) na turma
            <strong>${d.turma || ''}</strong>, turno <strong>${d.turno || ''}</strong>, no ano letivo de
            <strong>${d.ano || ''}</strong>.
          </div>

          <div style="text-align: right;">Parobé - RS, ${dataFormatada}</div>

          <div class="autenticidade" style="margin-top:40px; font-size:14px;">
            Para verificar a autenticidade deste documento, acesse:<br>
            <a href="http://meusite.com/autenticacao" target="_blank">http://meusite.com/autenticacao</a>
          </div>

          <div class="codigo" style="margin-top:10px; font-size:14px;">
            Código de verificação: <span>[XXXX-YYYY-ZZZZ]</span>
          </div>
        </div>
        `;

        const pdfContainer = document.getElementById('pdf-container');
        pdfContainer.innerHTML = pdfContent;
        pdfContainer.style.display = 'block';

        limparTemaParaPDF();

        html2pdf().set({
          margin: 10,
          filename: 'atestado_matricula.pdf',
          image: { type: 'jpeg', quality: 0.98 },
          html2canvas: { scale: 2, useCORS: true, logging: false },
          jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        })
          .from(pdfContainer)
          .save()
          .then(() => {
            pdfContainer.style.display = 'none';
            restaurarTema();
          })
          .catch(() => {
            pdfContainer.style.display = 'none';
            restaurarTema();
          });
      }

      document.addEventListener('DOMContentLoaded', () => {
        const btn = document.getElementById('gerar-atestado');
        if (btn) btn.addEventListener('click', gerarAtestado);
      });
    })();
  </script>

</body>

</html>