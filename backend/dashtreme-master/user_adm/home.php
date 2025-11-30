<?php
require_once '../includes/bootstrap.php';

// Ano letivo atual (maior Ano_Letivo em Turmas ou Matriculas)
$anoLetivoAtual = null;
try {
  $stmt = $pdo->query("SELECT MAX(Ano_Letivo) as ano FROM Turmas");
  $anoLetivoAtual = $stmt->fetchColumn();
  if (!$anoLetivoAtual) {
    $stmt = $pdo->query("SELECT MAX(Ano_Letivo) as ano FROM Matriculas");
    $anoLetivoAtual = $stmt->fetchColumn();
  }
  if (!$anoLetivoAtual) {
    $anoLetivoAtual = date('Y');
  }
} catch (Exception $e) {
  $anoLetivoAtual = date('Y');
}


$anoLetivoAtual = date('Y') === '2025' ? '2025' : $anoLetivoAtual;

// Total de matrículas ativas no ano letivo atual
$totalMatriculas = 0;
try {
  $stmt = $pdo->prepare("SELECT COUNT(*) FROM Matriculas WHERE Status = 'Ativa' AND Ano_Letivo = ?");
  $stmt->execute([$anoLetivoAtual]);
  $totalMatriculas = $stmt->fetchColumn();
} catch (Exception $e) {
}

// Últimas movimentações (matrículas, transferências, rematrículas)
$ultimasMovimentacoes = [];
try {
  $sql = "SELECT m.Data_Matricula as data, u.Nome_Completo as aluno, m.Tipo_Matricula as tipo
      FROM Matriculas m
      INNER JOIN Usuarios u ON m.ID_Aluno = u.ID_Usuario
      WHERE m.Ano_Letivo = ?
      ORDER BY m.Data_Matricula DESC, m.ID_Matricula DESC
      LIMIT 5";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([$anoLetivoAtual]);
  $ultimasMovimentacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
}

// Avisos importantes (eventos futuros do calendário acadêmico)
$avisos = [];
try {
  $hoje = date('Y-m-d');
  $sql = "SELECT Nome_Evento, Descricao, Data_Inicio FROM Calendario_Academico WHERE Data_Inicio >= ? AND (Ano_Letivo = ? OR Ano_Letivo IS NULL) ORDER BY Data_Inicio ASC LIMIT 2";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([$hoje, $anoLetivoAtual]);
  $avisos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="SAS - Administrador" />
  <meta name="author" content="" />
  <title>SAS - Admin</title>
  <!-- loader-->
  <link href="../assets/css/pace.min.css" rel="stylesheet" />
  <script src="../assets/js/pace.min.js"></script>
  <!--favicon-->
  <link rel="icon" href="../assets/images/favicon.ico" type="image/x-icon">
  <!-- simplebar CSS-->
  <link href="../assets/plugins/simplebar/css/simplebar.css" rel="stylesheet" />
  <!-- Bootstrap core CSS-->
  <link href="../assets/css/bootstrap.min.css" rel="stylesheet" />
  <!-- animate CSS-->
  <link href="../assets/css/animate.css" rel="stylesheet" type="text/css" />
  <!-- Icons CSS-->
  <link href="../assets/css/icons.css" rel="stylesheet" type="text/css" />
  <!-- Sidebar CSS-->
  <link href="../assets/css/sidebar-menu.css" rel="stylesheet" />
  <!-- Custom Style-->
  <link href="../assets/css/app-style.css" rel="stylesheet" />
  <link href="../css/style.css" rel="stylesheet" />

</head>

<body class="bg-theme bg-theme1 user_adm_home">

  <?php
  // Unificar o menu com as demais páginas para refletir novas opções imediatamente
  require("menu_padrão.php");
  ?>


  <div class="clearfix"></div>

  <div class="content-wrapper">
    <div class="container-fluid">


      <div class="row">
        <div class="col-12 col-lg-8">
          <div class="card">
            <div class="card-header text-white" style="background-color: var(--azul-cabecalho);">
              <h5 class="text-white">Painel do Administrador</h5>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="card bg-light-primary">
                    <div class="card-body">
                      <h6 class="card-title">Ano Letivo Atual</h6>
                      <h4 class="mb-0"><?php echo htmlspecialchars($anoLetivoAtual); ?></h4>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="card bg-light-success">
                    <div class="card-body">
                      <h6 class="card-title">Total de Matrículas</h6>
                      <h4 class="mb-0"><?php echo htmlspecialchars($totalMatriculas); ?></h4>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-12 col-lg-4">
          <div class="card">
            <div class="card-header">
              <h5>Avisos Importantes</h5>
            </div>
            <div class="card-body">
              <?php if (!empty($avisos)): ?>
                <?php foreach ($avisos as $aviso): ?>
                  <div class="alert alert-warning">
                    <strong><?php echo htmlspecialchars($aviso['Nome_Evento']); ?>:</strong>
                    <?php echo htmlspecialchars($aviso['Descricao']); ?>
                    <?php if (!empty($aviso['Data_Inicio'])): ?>
                      <br><small>Data: <?php echo date('d/m/Y', strtotime($aviso['Data_Inicio'])); ?></small>
                    <?php endif; ?>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <div class="alert alert-warning">Nenhum aviso importante no momento.</div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>

      <div class="row mt-4">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h5>Acesso Rápido</h5>
            </div>
            <div class="card-body">
              <div class="row text-center">
                <div class="col-6 col-md-3">
                  <a href="cadastro.php#aluno" class="btn btn-round btn-alunos">
                    <i class="zmdi zmdi-accounts-alt fa-2x"></i><br> Alunos
                  </a>
                </div>
                <div class="col-6 col-md-3">
                  <a href="cadastroTurmas.php" class="btn btn-round btn-turmas">
                    <i class="zmdi zmdi-group-work fa-2x"></i><br> Turmas
                  </a>
                </div>
                <div class="col-6 col-md-3">
                  <a href="cadastro.php#servidor" class="btn btn-round btn-servidores">
                    <i class="zmdi zmdi-account-box fa-2x"></i><br> Servidores
                  </a>
                </div>
                <div class="col-6 col-md-3">
                  <a href="calendario.php" class="btn btn-round btn-calendario">
                    <i class="zmdi zmdi-calendar fa-2x"></i><br> Calendário
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Bloco de Últimas Movimentações -->
      <div class="row mt-4">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h5>Últimas Movimentações</h5>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                      <th>Data</th>
                      <th>Aluno</th>
                      <th>Tipo</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (!empty($ultimasMovimentacoes)): ?>
                      <?php foreach ($ultimasMovimentacoes as $mov): ?>
                        <tr>
                          <td><?php echo date('d/m/Y', strtotime($mov['data'])); ?></td>
                          <td><?php echo htmlspecialchars($mov['aluno']); ?></td>
                          <td>
                            <?php
                            $tipo = strtolower($mov['tipo']);
                            $badge = 'badge-secondary';
                            if ($tipo === 'Matrícula')
                              $badge = 'badge-success';
                            elseif ($tipo === 'Transferência')
                              $badge = 'badge-info';
                            elseif ($tipo === 'Rematrícula')
                              $badge = 'badge-primary';
                            ?>
                            <span class="badge <?php echo $badge; ?>"><?php echo htmlspecialchars($mov['tipo']); ?></span>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <tr>
                        <td colspan="3">Nenhuma movimentação recente.</td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>


      <!--start overlay-->
      <div class="overlay toggle-menu"></div>
    </div>
  </div>



  <!-- Bootstrap core JavaScript-->
  <script src="../assets/js/jquery.min.js"></script>
  <!-- Bootstrap core JavaScript-->
  <script src="../assets/js/jquery.min.js"></script>
  <script src="../assets/js/popper.min.js"></script>
  <script src="../assets/js/bootstrap.min.js"></script>

  <!-- simplebar js -->
  <script src="../assets/plugins/simplebar/js/simplebar.js"></script>
  <!-- sidebar-menu js -->
  <script src="../assets/js/sidebar-menu.js"></script>
  <!-- Custom scripts -->
  <script src="../assets/js/app-script.js"></script>

  <script>
    $(document).ready(function () {
      // Simular notificações
      const notificacoes = [
        {
          id: 1,
          tipo: 'primary',
          icone: 'zmdi-account-add',
          titulo: 'Novo aluno matriculado',
          tempo: 'Há 5 minutos',
          lida: false
        },
        {
          id: 2,
          tipo: 'success',
          icone: 'zmdi-calendar-check',
          titulo: 'Reunião pedagógica hoje',
          tempo: 'Há 1 hora',
          lida: false
        },
        {
          id: 3,
          tipo: 'warning',
          icone: 'zmdi-alert-circle',
          titulo: 'Documentação pendente',
          tempo: 'Ontem',
          lida: false
        }
      ];

      // Atualizar contador de notificações
      function atualizarContador() {
        const naoLidas = notificacoes.filter(n => !n.lida).length;
        $('.alert-count').text(naoLidas);
        if (naoLidas === 0) {
          $('.alert-count').hide();
        } else {
          $('.alert-count').show();
        }
      }

      // Marcar todas como lidas
      $('#marcarTodasComoLidas').click(function () {
        notificacoes.forEach(n => n.lida = true);
        atualizarContador();
        $(this).closest('.dropdown-menu').find('.dropdown-item').addClass('lida');
        $(this).text('Todas marcadas como lidas');
      });


      $(document).on('click', '.dropdown-item', function () {
        const index = $(this).index() - 1;
        if (index >= 0 && index < notificacoes.length) {
          notificacoes[index].lida = true;
          atualizarContador();
          $(this).addClass('lida');
        }
      });

      // Inicializar
      atualizarContador();
    });
  </script>
</body>

</html>