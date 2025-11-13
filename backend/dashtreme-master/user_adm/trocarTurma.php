<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Trocar de Turma - SAS (Sistema Academico Santos)</title>
  <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/css/app-style.css">
  <link rel="stylesheet" href="../assets/css/icons.css">
  <link rel="stylesheet" href="../assets/plugins/simplebar/css/simplebar.css">
  <link rel="stylesheet" href="../assets/css/sidebar-menu.css">
  <link rel="stylesheet" href="../css/style.css">
</head>

<body class="bg-theme bg-theme1 user_adm_trocarTurma">
  <?php
  require("menu_padrão.php");
  ?>

  <div class="content-wrapper">
    <div class="container-fluid">
      <div class="row pt-2 pb-2">
        <div class="col-sm-12">
          <h4 class="page-title">Trocar de Turma</h4>
        </div>
      </div>

      <form id="formBuscaAluno" class="search-box">
        <div class="form-group">
          <label for="buscaAluno">Pesquisar por nome ou matrícula:</label>
          <input type="text" class="form-control" id="buscaAluno" placeholder="Digite o nome ou matrícula...">
        </div>
        <div class="form-group text-right">
          <button type="submit" class="btn btn-custom-secondary">Buscar</button>
        </div>
      </form>

      <div id="resultadoBusca" class="d-none">
        <div class="card bg-transparent border-0">
          <div class="card-body">
            <h5 class="card-title text-white">Dados do aluno</h5>
            <p class="text-white"><strong>Nome:</strong> <span id="resultadoNome"></span></p>
            <p class="text-white"><strong>Matrícula:</strong> <span id="resultadoMatricula">20251001</span></p>
            <p class="text-white"><strong>Turma atual:</strong> <span id="resultadoTurma">—</span></p>

            <div class="form-group mt-3">
              <label for="novaTurma">Nova turma:</label>
              <select id="novaTurma" class="form-control">
                <option value="">Selecione a nova turma...</option>
              </select>
            </div>

            <div class="form-group text-right">
              <button class="btn btn-custom-primary" id="btnConfirmarTroca">
                <i class="zmdi zmdi-refresh mr-1"></i> Confirmar troca
              </button>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
  <!--Overlay-->
  <div class="overlay toggle-menu"></div>


  </div>

  <script src="../assets/js/jquery.min.js"></script>
  <script src="../assets/js/bootstrap.min.js"></script>
  <script src="../assets/plugins/simplebar/js/simplebar.js"></script>
  <script src="../assets/js/sidebar-menu.js"></script>
  <script src="../assets/js/app-script.js"></script>
  <script>
    $(function () {
      let aluno = null;

      function carregarTurmasPorAno(ano) {
        const $sel = $('#novaTurma');
        $sel.empty().append('<option value="">Carregando turmas...</option>');
        fetch(`../includes/ajax/listar_turmas.php?ano=${encodeURIComponent(ano)}`)
          .then(r => r.json())
          .then(resp => {
            $sel.empty().append('<option value="">Selecione a nova turma...</option>');
            if (resp.success && resp.data) {
              resp.data.forEach(t => {
                const label = `${t.Nome_Turma}${t.Etapa ? ' ('+t.Etapa+')' : ''}`;
                $sel.append(`<option value="${t.ID_Turma}">${label}</option>`);
              });
            } else {
              $sel.append('<option value="">Nenhuma turma encontrada</option>');
            }
          })
          .catch(() => {
            $sel.empty().append('<option value="">Erro ao listar turmas</option>');
          });
      }

      $('#formBuscaAluno').on('submit', function (e) {
        e.preventDefault();
        const termo = $('#buscaAluno').val().trim();
        if (termo.length < 2) {
          alert('Digite pelo menos 2 caracteres para pesquisar');
          return;
        }
        fetch(`../includes/ajax/buscar_alunos.php?q=${encodeURIComponent(termo)}`)
          .then(r => r.json())
          .then(resp => {
            if (!resp.success || !resp.data || !resp.data.length) {
              alert('Aluno não encontrado.');
              return;
            }
            // pega o primeiro da lista (pode evoluir para lista selecionável)
            const a = resp.data[0];
            aluno = a;
            $('#resultadoNome').text(a.Nome_Completo || 'Aluno');
            $('#resultadoMatricula').text(a.Matricula || '—');
            const turmaAtual = a.Nome_Turma ? `${a.Nome_Turma}${a.Etapa ? ' ('+a.Etapa+')' : ''}` : '—';
            $('#resultadoTurma').text(turmaAtual);
            $('#resultadoBusca').removeClass('d-none');
            $('html, body').animate({ scrollTop: $('#resultadoBusca').offset().top - 100 }, 300);
            if (a.Ano_Letivo) carregarTurmasPorAno(a.Ano_Letivo);
          })
          .catch(() => alert('Erro ao pesquisar aluno.'));
      });

      $('#btnConfirmarTroca').on('click', function (e) {
        e.preventDefault();
        if (!aluno) {
          alert('Pesquise e selecione um aluno primeiro.');
          return;
        }
        const novaTurmaId = $('#novaTurma').val();
        if (!novaTurmaId) {
          alert('Selecione a nova turma.');
          return;
        }

        const body = new URLSearchParams();
        body.append('aluno_id', String(aluno.ID_Aluno));
        body.append('nova_turma_id', String(novaTurmaId));
        fetch('../includes/ajax/trocar_turma.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: body.toString()
        })
          .then(r => r.json())
          .then(resp => {
            if (!resp.success) {
              alert('Erro: ' + (resp.message || 'falha na troca'));
              return;
            }
            alert('Troca realizada com sucesso.');
            // opcional: atualizar turma atual na UI
            const novaOptText = $('#novaTurma option:selected').text();
            $('#resultadoTurma').text(novaOptText || '—');
          })
          .catch(() => alert('Erro ao confirmar troca.'));
      });
    });
  </script>
</body>

</html>