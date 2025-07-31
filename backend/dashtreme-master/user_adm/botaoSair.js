// botaoSair.js

// Lógica de verificação de sessão
(function () {
  const isLoggedIn = localStorage.getItem('isLoggedIn') === 'true';
  const userType = localStorage.getItem('userType');

  if (!isLoggedIn || !userType) {
    window.location.href = '../login.html';
    return;
  }

  // Verifica se a pasta atual é compatível com o tipo de usuário
  const path = window.location.pathname.toLowerCase();

  if (
    (userType === 'admin' && !path.includes('/user_adm/')) ||
    (userType === 'professor' && !path.includes('/user_professor/')) ||
    (userType === 'aluno' && !path.includes('/user_aluno/'))
  ) {
    window.location.href = '../login.html';
  }
})();

// Lógica do botão de sair
document.getElementById('logout-btn')?.addEventListener('click', function () {
  localStorage.clear();
  window.location.href = '../login.html';
});
