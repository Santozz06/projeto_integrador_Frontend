document.addEventListener('DOMContentLoaded', function () {
  // Obter parâmetro da URL
  const urlParams = new URLSearchParams(window.location.search);
  const ano = urlParams.get('ano') || '2023';

  // Lista completa de matérias para todos os anos
  const materiasBase = [
    'Língua Portuguesa',
    'Matemática',
    'Ciências',
    'História',
    'Geografia',
    'Artes',
    'Educação Física',
    'Língua Inglesa'
  ];

  // Atualizar o título do boletim
  document.getElementById('titulo-boletim').textContent = `Boletim Escolar – ${ano}`;

  // Atualizar ano/série
  const series = {
    '2023': '2023 - 6° Ano',
    '2024': '2024 - 7° Ano',
    '2025': '2025 - 8° Ano'
  };
  document.getElementById('ano-serie').textContent = series[ano] || series['2023'];
  const turmas = {
    '2023': '161',
    '2024': '171',
    '2025': '181'
  };
  document.getElementById('turma').textContent = turmas[ano] || turmas['2023'];
  // Limpar tabela antes de preencher
  const tabelaNotas = document.getElementById('tabela-notas');
  tabelaNotas.innerHTML = '';

  // Preencher tabela com todas matérias (sem notas)
  materiasBase.forEach(materia => {
    const row = document.createElement('tr');
    row.innerHTML = `
      <td>${materia}</td>
      <td>-</td>
      <td>-</td>
      <td>-</td>
      <td>-</td>
      <td>-</td>
      <td>-</td>
      <td>-</td>
      <td>-</td>
      <td>-</td>
      <td>-</td>
    `;
    tabelaNotas.appendChild(row);
  });

  // Atualizar data de emissão
  const now = new Date();
  document.getElementById('data-emissao').textContent =
    `Emitido em ${now.toLocaleDateString('pt-BR')}, ${now.toLocaleTimeString('pt-BR').slice(0, 5)}`;

  // Botões
  document.getElementById('btn-voltar').addEventListener('click', () => window.history.back());
  document.getElementById('btn-imprimir').addEventListener('click', () => window.print());
});