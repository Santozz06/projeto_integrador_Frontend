<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <title>Atestado de Frequência</title>
  <link rel="stylesheet" href="../css/style.css" />
</head>

<body class="user_aluno_frequencia_detalhes" onload="gerarPDF()">

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
      <link rel="stylesheet" href="../css/style.css?v=<?php echo time(); ?>">
        document.body.style.opacity = '0';
    <body class="user_aluno_frequencia_detalhes">
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