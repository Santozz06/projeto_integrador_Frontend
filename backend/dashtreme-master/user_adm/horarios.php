<?php
require_once '../includes/bootstrap.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../acesso_negado.php');
    exit;
}

require_once '../includes/config/conexao.php';

// Carregar listas para selects
$turmas = $pdo->query("SELECT ID_Turma, Nome_Turma, Ano_Letivo FROM Turmas ORDER BY Ano_Letivo DESC, Nome_Turma ASC")->fetchAll(PDO::FETCH_ASSOC);
$disciplinas = $pdo->query("SELECT ID_Disciplina, Nome_Disciplina, ID_Professor FROM Disciplinas ORDER BY Nome_Disciplina ASC")->fetchAll(PDO::FETCH_ASSOC);
$professores = $pdo->query("SELECT p.ID_Professor, u.Nome_Completo FROM Professores p INNER JOIN Usuarios u ON u.ID_Usuario = p.ID_Professor ORDER BY u.Nome_Completo ASC")->fetchAll(PDO::FETCH_ASSOC);

$semana = [1=>'Segunda-feira',2=>'Terça-feira',3=>'Quarta-feira',4=>'Quinta-feira',5=>'Sexta-feira',6=>'Sábado',7=>'Domingo'];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <link rel="icon" href="../assets/images/favicon.ico" type="image/x-icon">
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <title>Horários de Aulas - Admin</title>
  <link href="../assets/css/bootstrap.min.css" rel="stylesheet" />
  <link href="../assets/css/icons.css" rel="stylesheet" />
  <link href="../assets/css/sidebar-menu.css" rel="stylesheet" />
  <link href="../assets/css/app-style.css" rel="stylesheet" />
  <link rel="stylesheet" href="../css/style.css">
</head>
<body class="bg-theme bg-theme1 user_adm_horarios">

<?php require('menu_padrão.php'); ?>

<div class="content-wrapper">
  <div class="container-fluid">
    <div class="row pt-2 pb-2">
      <div class="col-sm-9"><h4 class="page-title">Horários de Aulas</h4></div>
    </div>

    <div class="card">
      <div class="card-body">
        <div class="form-container mb-3">
          <form id="formHorario">
          <input type="hidden" id="id" name="id" />
          <div class="form-section">
            <div class="form-row">
              <div class="form-group col-md-4">
                <label>Turma</label>
                <select class="form-control" id="turma_id" name="turma_id" required>
                  <option value="">Selecione...</option>
                  <?php foreach($turmas as $t): ?>
                    <option value="<?= $t['ID_Turma'] ?>"><?= htmlspecialchars($t['Nome_Turma']) ?> (<?= htmlspecialchars($t['Ano_Letivo']) ?>)</option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group col-md-4">
                <label>Disciplina</label>
                <select class="form-control" id="disciplina_id" name="disciplina_id" required>
                  <option value="">Selecione...</option>
                  <?php foreach($disciplinas as $d): ?>
                    <option value="<?= $d['ID_Disciplina'] ?>" data-prof="<?= $d['ID_Professor'] ?>"><?= htmlspecialchars($d['Nome_Disciplina']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group col-md-4">
                <label>Professor</label>
                <select class="form-control" id="professor_id" name="professor_id" required>
                  <option value="">Selecione...</option>
                  <?php foreach($professores as $p): ?>
                    <option value="<?= $p['ID_Professor'] ?>"><?= htmlspecialchars($p['Nome_Completo']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-3">
                <label>Dia da Semana</label>
                <select class="form-control" id="dia_semana" name="dia_semana" required>
                  <option value="">Selecione...</option>
                  <?php foreach($semana as $i=>$n): ?>
                    <option value="<?= $i ?>"><?= $n ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group col-md-2">
                <label>Início</label>
                <input type="time" class="form-control" id="hora_inicio" name="hora_inicio" required />
              </div>
              <div class="form-group col-md-2">
                <label>Fim</label>
                <input type="time" class="form-control" id="hora_fim" name="hora_fim" required />
              </div>
              <div class="form-group col-md-2">
                <label>Sala</label>
                <input type="text" class="form-control" id="sala" name="sala" />
              </div>
              <div class="form-group col-md-3">
                <label>Ano Letivo</label>
                <input type="number" class="form-control" id="ano" name="ano" placeholder="Opcional" />
              </div>
            </div>
            <div class="form-group">
              <label>Observação</label>
              <input type="text" class="form-control" id="observacao" name="observacao" />
            </div>
            <div class="text-right">
              <button type="submit" class="btn btn-salvar">Salvar</button>
              <button type="button" id="btnLimpar" class="btn btn-cancelar">Limpar</button>
            </div>
          </div>
          </form>
        </div>

        <div class="card mt-4 horarios-list-card">
          <div class="card-body">
            <h5 class="section-title mb-3">HORÁRIOS CADASTRADOS</h5>
            <div class="table-responsive tabela-horarios-wrapper">
              <table class="table table-bordered table-striped tabela-horarios" id="tabelaHorarios">
            <thead>
              <tr>
                <th>Turma</th>
                <th>Disciplina</th>
                <th>Professor</th>
                <th>Dia</th>
                <th>Início</th>
                <th>Fim</th>
                <th>Sala</th>
                <th>Ano</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="../assets/js/jquery.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
<script src="../assets/plugins/simplebar/js/simplebar.js"></script>
<script src="../assets/js/sidebar-menu.js"></script>
<script src="../assets/js/app-script.js"></script>
<script>
const SEMANA = {1:'Segunda',2:'Terça',3:'Quarta',4:'Quinta',5:'Sexta',6:'Sábado',7:'Domingo'};

function carregarLista(){
  $.getJSON('../includes/ajax/horarios/listar.php')
    .done(function(r){
      const $tb = $('#tabelaHorarios tbody');
      $tb.empty();
      if (!r.success || !Array.isArray(r.data) || r.data.length===0){
        $tb.append('<tr><td colspan="9" class="text-center text-muted">Nenhum horário cadastrado</td></tr>');
        return;
      }
      r.data.forEach(h=>{
        const tr = `<tr>
          <td>${h.Nome_Turma}</td>
          <td>${h.Nome_Disciplina}</td>
          <td>${h.Professor_Nome}</td>
          <td>${SEMANA[h.Dia_Semana]||h.Dia_Semana}</td>
          <td>${h.Hora_Inicio?.substring(0,5)||''}</td>
          <td>${h.Hora_Fim?.substring(0,5)||''}</td>
          <td>${h.Sala||''}</td>
          <td>${h.Ano_Letivo||''}</td>
          <td>
            <button class="btn btn-sm btn-editar" onclick='editar(${JSON.stringify(h)})'>Editar</button>
            <button class="btn btn-sm btn-excluir" onclick='remover(${h.ID_Horario})'>Excluir</button>
          </td>
        </tr>`;
        $tb.append(tr);
      });
    })
    .fail(()=>{
      alert('Não foi possível carregar a lista.');
    });
}

function editar(h){
  $('#id').val(h.ID_Horario);
  $('#turma_id').val(h.ID_Turma);
  $('#disciplina_id').val(h.ID_Disciplina);
  $('#professor_id').val(h.ID_Professor);
  $('#dia_semana').val(h.Dia_Semana);
  $('#hora_inicio').val((h.Hora_Inicio||'').substring(0,5));
  $('#hora_fim').val((h.Hora_Fim||'').substring(0,5));
  $('#sala').val(h.Sala||'');
  $('#ano').val(h.Ano_Letivo||'');
  $('#observacao').val(h.Observacao||'');
  window.scrollTo({top:0, behavior:'smooth'});
}

function remover(id){
  if (!confirm('Deseja excluir este horário?')) return;
  $.post('../includes/ajax/horarios/deletar.php', {id})
    .done(function(r){
      try { r = JSON.parse(r); } catch {}
      if (r && r.success){ carregarLista(); }
      else { alert(r.message||'Erro ao excluir'); }
    })
    .fail(()=> alert('Erro ao excluir'));
}

$('#disciplina_id').on('change', function(){
  const prof = $(this).find(':selected').data('prof');
  if (prof) $('#professor_id').val(prof);
});

$('#btnLimpar').on('click', function(){
  $('#formHorario')[0].reset();
  $('#id').val('');
});

$('#formHorario').on('submit', function(e){
  e.preventDefault();

  // Validação simples: hora fim deve ser maior que início
  const ini = $('#hora_inicio').val();
  const fim = $('#hora_fim').val();
  if (ini && fim) {
    const [ih, im] = ini.split(':').map(Number);
    const [fh, fm] = fim.split(':').map(Number);
    const startMin = ih*60 + im;
    const endMin = fh*60 + fm;
    if (endMin <= startMin) {
      alert('Hora de término deve ser maior que a hora de início.');
      return;
    }
  }

  const data = $(this).serialize();
  $.post('../includes/ajax/horarios/salvar.php', data)
    .done(function(r){
      try { r = JSON.parse(r); } catch {}
      if (r && r.success){
        $('#formHorario')[0].reset();
        $('#id').val('');
        carregarLista();
      } else {
        alert((r && r.message) ? r.message : 'Erro ao salvar');
      }
    })
    .fail(function(xhr){
      let msg = 'Erro ao salvar';
      if (xhr && xhr.responseText) {
        try {
          const j = JSON.parse(xhr.responseText);
          if (j && j.message) msg = j.message;
        } catch {
          msg = xhr.status + ' ' + (xhr.statusText||'') + '\n' + xhr.responseText;
        }
      }
      alert(msg);
    });
});

$(function(){ carregarLista(); });
</script>
</body>
</html>