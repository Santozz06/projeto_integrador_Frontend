<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <title>Atestado de Frequência</title>
  <style>
    body {
      font-family: "Times New Roman", serif;
      margin: 40px 60px;
      font-size: 16px;
      color: #000;
      line-height: 1.5;
      opacity: 0;
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

    @media print {
      body {
        margin: 0;
        opacity: 1;
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
      [Nome do Campus ou Unidade]<br>
      Rua, Bairro - Parobé - 95630-000
    </div>

    <div class="titulo">ATESTADO DE FREQUÊNCIA</div>

    <div class="texto">
      Atestamos, para os fins a que se fizerem necessários, que <strong><span id="nome-aluno">Aluno(a)</span></strong>,
      CPF nº <strong><span id="cpf-aluno">—</span></strong>, possui vínculo regular de estudante nesta instituição sob o nº de
      matrícula <strong><span id="matricula-aluno">—</span></strong>.
      modalidade <strong><span id="modalidade">Presencial</span></strong>, no turno <strong><span id="turno">—</span></strong>, ofertado pela <strong>NOME DA
        INSTITUIÇÃO</strong>.
    </div>

    <div class="texto">
      Atestamos ainda, que <strong><span id="nome-aluno-2">Aluno(a)</span></strong> apresenta frequência global de <strong><span id="percentual">—</span>%</strong>
      no período letivo <strong id="ano-letivo">2025</strong>.
    </div>

    <div class="rodape">
      <strong>Parobé - RS, <span id="data-emissao">08 de julho de 2025</span></strong>
    </div>

    <div class="autenticidade">
      Para verificar a autenticidade deste documento, acesse:<br>
      <a href="http://meusite.com/autenticacao" target="_blank">http://meusite.com/autenticacao</a>
    </div>

    <div class="codigo">
      Código de verificação: <span id="codigo-verificacao">FREQ-2025-7A9B2C</span>
    </div>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
  <script>
    (function(){
      const urlParams = new URLSearchParams(window.location.search);
      const ano = urlParams.get('ano') || new Date().getFullYear();
      document.getElementById('ano-letivo').textContent = ano;
      document.getElementById('codigo-verificacao').textContent = `FREQ-${ano}-${Math.random().toString(36).substr(2, 6).toUpperCase()}`;

      const hoje = new Date();
      const options = { day: 'numeric', month: 'long', year: 'numeric' };
      document.getElementById('data-emissao').textContent = hoje.toLocaleDateString('pt-BR', options);

      function preencherDadosBasicos(aluno){
        if(!aluno) return;
        document.getElementById('nome-aluno').textContent = aluno.Nome_Completo || '—';
        document.getElementById('nome-aluno-2').textContent = aluno.Nome_Completo || '—';
        document.getElementById('cpf-aluno').textContent = aluno.CPF || '—';
      }

      function preencherFrequencia(data){
        if(!data) return;
        document.getElementById('percentual').textContent = (data.percentual!=null? String(data.percentual) : '—');
        document.getElementById('ano-letivo').textContent = data.ano || ano;
        document.getElementById('matricula-aluno').textContent = data.matricula || '—';
      }

      function gerarPDF(){
        document.body.style.opacity = '0';
        const element = document.getElementById('doc');
        html2pdf().set({
          margin: 10,
          filename: `atestado_frequencia_${ano}.pdf`,
          image: { type: 'jpeg', quality: 0.98 },
          html2canvas: { scale: 2 },
          jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        }).from(element).save().then(function(){
          setTimeout(function(){ window.history.back(); }, 800);
        }).catch(function(err){
          console.error('Erro ao gerar PDF:', err);
          document.body.style.opacity = '1';
        });
      }

      // Carregar dados em paralelo e gerar PDF após ambos
      Promise.all([
        // Historico para obter Nome/CPF usando id em sessão (será resolvido no servidor)
        fetch(`../includes/ajax/obter_historico_aluno.php?aluno_id=${encodeURIComponent(<?php echo isset($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : 0; ?>)}`)
          .then(r=>r.json()).catch(()=>({success:false})),
        fetch(`../includes/ajax/aluno/frequencia_resumo.php?ano=${encodeURIComponent(ano)}`)
          .then(r=>r.json()).catch(()=>({success:false}))
      ]).then(function(resps){
        var hist = resps[0] && resps[0].success ? resps[0] : null;
        var freq = resps[1] && resps[1].success ? resps[1] : null;
        if (hist && hist.aluno) preencherDadosBasicos(hist.aluno);
        if (freq && freq.data) preencherFrequencia(freq.data);
        gerarPDF();
      }).catch(function(){ gerarPDF(); });
    })();
  </script>
</body>

</html>