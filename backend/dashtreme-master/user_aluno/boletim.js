document.addEventListener('DOMContentLoaded', function () {
  const urlParams = new URLSearchParams(window.location.search);
  let ano = urlParams.get('ano');
  const tabelaNotas = document.getElementById('tabela-notas');

  function fmt(v){ return (v==null || v==='') ? '-' : String(v).replace('.', ','); }

  function preencher(resp){
    if(!resp || !resp.success){
      console.warn('Falha ao carregar boletim:', resp && resp.message);
      return;
    }
    const cab = resp.cabecalho || {};
    document.getElementById('titulo-boletim').textContent = `Boletim Escolar – ${cab.ano || ano || ''}`;
    document.getElementById('ano-serie').textContent = `${cab.ano || ano || ''}${cab.serie ? ' - '+cab.serie : ''}`;
    if (document.getElementById('turma')) document.getElementById('turma').textContent = cab.turma || '-';
    if (document.getElementById('nome-aluno')) document.getElementById('nome-aluno').textContent = cab.nome || '-';
    if (document.getElementById('matricula')) document.getElementById('matricula').textContent = cab.matricula || '-';
    if (document.getElementById('situacao-aluno')) document.getElementById('situacao-aluno').textContent = cab.status || '-';

    tabelaNotas.innerHTML = '';
    (resp.disciplinas||[]).forEach(function(d){
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${d.nome || '-'}</td>
        <td>${fmt(d.t1)}</td>
        <td>-</td>
        <td>${fmt(d.t2)}</td>
        <td>-</td>
        <td>${fmt(d.t3)}</td>
        <td>-</td>
        <td>-</td>
        <td>-</td>
        <td>${fmt(d.final)}</td>
        <td>-</td>
      `;
      tabelaNotas.appendChild(tr);
    });

    const now = new Date();
    const dataEl = document.getElementById('data-emissao');
    if (dataEl) dataEl.textContent = `Emitido em ${now.toLocaleDateString('pt-BR')}, ${now.toLocaleTimeString('pt-BR').slice(0, 5)}`;
  }

  function carregarPorAno(targetAno){
    const cacheBust = Date.now();
    fetch(`../includes/ajax/aluno/boletim_detalhes.php?ano=${encodeURIComponent(targetAno)}&_=${cacheBust}`)
      .then(function(r){
        if(!r.ok){
          console.error('Falha na requisição do boletim:', r.status, r.statusText);
        }
        return r.json();
      })
      .then(function(resp){
        if(!resp || resp.success!==true){
          alert('Não foi possível carregar o boletim do ano selecionado. Verifique se está logado como aluno e se há matrícula para este ano.');
        }
        preencher(resp);
      })
      .catch(function(err){ console.error(err); alert('Erro ao carregar dados do boletim.'); });
  }

  function inicializar(){
    if (ano) { carregarPorAno(ano); return; }
    const cacheBust = Date.now();
    fetch(`../includes/ajax/aluno/anos_matriculas.php?_=${cacheBust}`)
      .then(r=>r.json())
      .then(function(resp){
        if (resp && resp.success && resp.anos && resp.anos.length){
          ano = resp.anos[0].ano; 
          carregarPorAno(ano);
        }
        else {
          alert('Nenhuma matrícula encontrada para o aluno.');
        }
      }).catch(function(err){ console.error(err); });
  }

  document.getElementById('btn-voltar') && document.getElementById('btn-voltar').addEventListener('click', () => window.history.back());
  document.getElementById('btn-imprimir') && document.getElementById('btn-imprimir').addEventListener('click', () => window.print());

  inicializar();
});