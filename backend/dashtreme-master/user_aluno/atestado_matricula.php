<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <link rel="icon" href="../assets/images/favicon.ico" type="image/x-icon">
  <meta charset="UTF-8">
  <title>Atestado de Matrícula</title>
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
    // Remove tema após carregar html2pdf
    document.body.style.cssText = 'background: #ffffff !important; background-color: #ffffff !important; background-image: none !important;';
    if(document.body.classList) {
      document.body.classList.remove('bg-theme', 'bg-theme1', 'bg-theme2', 'bg-theme3', 'bg-theme4', 'bg-theme5');
    }
  </script>
</head>

<body class="user_aluno_atestado_matricula">

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
      regular de matrícula nesta Instituição de Ensino no curso de <strong><span id="curso">—</span></strong>, de nível <strong><span id="nivel">—</span></strong>,
      modalidade <strong><span id="modalidade">—</span></strong>, no turno <strong><span id="turno">—</span></strong>, conforme registro acadêmico atualizado.
    </div>

    <div class="dados">
      Matrícula nº: <strong><span id="matricula">—</span></strong><br>
      Período Letivo: <strong><span id="periodo">—</span></strong><br>
      Turma/Série: <strong><span id="turma">—</span></strong>
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

  <script>
    (function(){
      document.body.style.opacity = '0';
      
      function popularEDepoisGerar(){
        fetch('../includes/ajax/aluno/atestado_matricula.php')
          .then(r=>r.json())
          .then(resp=>{
            if(!resp.success){ 
              alert('Erro ao carregar dados: ' + (resp.message || 'Desconhecido'));
              throw new Error('Falha ao carregar dados'); 
            }
            var d = resp.data || {};
            document.getElementById('curso').textContent = d.curso || '—';
            document.getElementById('nivel').textContent = d.nivel || '—';
            document.getElementById('modalidade').textContent = d.modalidade || '—';
            document.getElementById('turno').textContent = d.turno || '—';
            document.getElementById('matricula').textContent = d.matricula || '—';
            document.getElementById('periodo').textContent = d.ano || '—';
            document.getElementById('turma').textContent = d.serie || d.turma || '—';

            if(typeof html2pdf === 'undefined') {
              alert('Erro: biblioteca html2pdf não está disponível');
              document.body.style.opacity = '1';
              return;
            }

            const element = document.getElementById('doc');
            html2pdf().set({
              margin: 10,
              filename: 'atestado_matricula.pdf',
              image: { type: 'jpeg', quality: 0.98 },
              html2canvas: { scale: 2 },
              jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            }).from(element).save().then(function(){
              setTimeout(function(){ window.close(); }, 800);
            }).catch(function(err){
              alert('Erro ao gerar PDF. Verifique o console.');
              document.body.style.opacity = '1';
            });
          })
          .catch(function(err){ 
            alert('Erro ao buscar dados. Verifique o console.');
            document.body.style.opacity = '1'; 
          });
      }
      
      window.addEventListener('load', popularEDepoisGerar);
    })();
  </script></body>

</html>