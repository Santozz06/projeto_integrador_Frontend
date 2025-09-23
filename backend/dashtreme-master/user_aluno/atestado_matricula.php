<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <title>Atestado de Matrícula</title>
  <style>
    body {
      font-family: "Times New Roman", serif;
      margin: 40px 60px;
      font-size: 16px;
      color: #000;
      line-height: 1.5;
      opacity: 0;
      /* Inicia invisível */
      transition: opacity 0.1s;
    }

    .cabecalho {
      text-align: center;
      font-size: 14px;
      margin-bottom: 30px;
      line-height: 1.4;
    }

    .titulo {
      text-align: center;
      font-weight: bold;
      font-size: 20px;
      margin: 30px 0;
      text-transform: uppercase;
    }

    .texto {
      text-align: justify;
      line-height: 1.8;
      margin-bottom: 20px;
    }

    .dados {
      margin: 20px 0;
      line-height: 1.8;
    }

    .rodape {
      margin-top: 40px;
      text-align: right;
    }

    .autenticidade {
      margin-top: 30px;
      font-size: 14px;
      text-align: center;
    }

    .codigo {
      font-weight: bold;
      margin-top: 10px;
      text-align: center;
    }

    strong {
      font-weight: bold;
    }

    @media print {
      body {
        margin: 0;
        opacity: 1;
        /* Garante visibilidade na impressão */
      }
    }

    .navbar {
      background-color: rgba(0, 0, 0, 0.2) !important;
      backdrop-filter: blur(10px);
    }
  </style>
</head>

<body onload="gerarPDF()">

  <div id="doc">

    <div class="cabecalho">
      República Federativa do Brasil<br>
      Ministério da Educação<br>
      Secretaria de Educação Profissional e Tecnológica<br>
      [Nome da Instituição de Ensino]<br>
      [Nome do Campus ou Unidade]
    </div>

    <div class="titulo">ATESTADO DE MATRÍCULA</div>

    <div class="texto">
      Atestamos, para os fins que se fizerem necessários, que o(a) estudante abaixo identificado(a) possui vínculo
      regular de matrícula nesta Instituição de Ensino no curso de <strong>[CURSO]</strong>, de nível <strong>[TÉCNICO /
        SUPERIOR]</strong>, modalidade <strong>[PRESENCIAL / EAD]</strong>, no turno <strong>[MATUTINO / VESPERTINO /
        NOTURNO / INTEGRAL]</strong>, conforme registro acadêmico atualizado.
    </div>

    <div class="dados">
      Matrícula nº: <strong>[000000000]</strong><br>
      Período Letivo: <strong>[2025]</strong><br>
      Duração: <strong>[13/02/2025] a [12/12/2025]</strong>
    </div>

    <div class="rodape">
      <strong>Parobé - RS, 08 de julho de 2025</strong>
    </div>

    <div class="autenticidade">
      Para verificar a autenticidade deste documento, acesse:<br>
      <a href="http://meusite.com/autenticacao" target="_blank">http://meusite.com/autenticacao</a>
    </div>

    <div class="codigo">
      Código de verificação: <span>[XXXX-YYYY-ZZZZ]</span>
    </div>

  </div>

  <!-- Script PDF automático -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
  <script>
    function gerarPDF() {
      // deixa invisível só no carregamento
      document.body.style.opacity = '0';

      const element = document.getElementById('doc');
      html2pdf().set({
        margin: 10,
        filename: 'atestado_matricula.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
      }).from(element).save().then(() => {
        // volta para página anterior após 1 segundo
        setTimeout(() => {
          window.location.href = "ensino.php";
        }, 1000);
      }).catch(err => {
        // se der erro, mostra o conteúdo para debug
        document.body.style.opacity = '1';
        console.error('Erro ao gerar PDF:', err);
      });
    }
  </script>

</body>

</html>