  // Verifica se o usuário está logado e no lugar certo
    const expectedUserType = window.location.pathname.includes('professor') ? 'professor' :
      window.location.pathname.includes('aluno') ? 'aluno' : 'admin';

    if (localStorage.getItem('isLoggedIn') !== 'true' ||
      localStorage.getItem('userType') !== expectedUserType) {
      localStorage.clear();
      window.location.href = '../login.php';
    }

    function logout() {
      // Remove os dados de autenticação
      localStorage.removeItem('isLoggedIn');
      localStorage.removeItem('userType');
      localStorage.removeItem('username');

      // Adiciona o alerta antes do redirecionamento
      alert('Você saiu do sistema!');
      window.location.href = '../login.php';
    }

    // Vincula ao botão "Sair"
    document.addEventListener('DOMContentLoaded', function () {
      const logoutBtn = document.getElementById('logout-btn');
      if (logoutBtn) {
        logoutBtn.addEventListener('click', function (e) {
          e.preventDefault();
          logout();
        });
      }
    });