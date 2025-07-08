// Banco de dados completo das matérias do Ensino Fundamental
const materias = {
    portugues: {
      nome: "LÍNGUA PORTUGUESA",
      conteudo: "Gramática, Leitura e Interpretação de Textos, Produção Textual, Literatura Infantil",
      frequencia: "Frequência: 92%",
      horarios: [
        ["Sala 01", "Segunda 08:00-10:00"],
        ["Sala 01", "Quarta 10:00-12:00"]
      ],
      trimestres: [
        {
          nome: "1° Trimestre",
          conteudo: "Ortografia e produção de pequenos textos",
          data: "18/02/2025"
        },
        {
          nome: "2° Trimestre",
          conteudo: "Leitura e interpretação de contos",
          data: "25/06/2025"
        },
        {
          nome: "3° Trimestre",
          conteudo: "Gramática aplicada e redação",
          data: "25/10/2025"
        }
      ]
    },
    matematica: {
      nome: "MATEMÁTICA",
      conteudo: "Operações Básicas, Frações, Geometria, Resolução de Problemas Matemáticos",
      frequencia: "Frequência: 95%",
      horarios: [
        ["Sala 02", "Terça 08:00-10:00"],
        ["Sala 02", "Quinta 10:00-12:00"]
      ],
      trimestres: [
        {
          nome: "1° Trimestre",
          conteudo: "Operações fundamentais e sistema decimal",
          data: "20/02/2025"
        },
        {
          nome: "2° Trimestre",
          conteudo: "Frações e geometria básica",
          data: "28/06/2025"
        },
        {
          nome: "3° Trimestre",
          conteudo: "Resolução de problemas e medidas",
          data: "28/10/2025"
        }
      ]
    },
    historia: {
      nome: "HISTÓRIA",
      conteudo: "História do Brasil, Civilizações Antigas, Cultura Indígena",
      frequencia: "Frequência: 90%",
      horarios: [
        ["Sala 03", "Segunda 10:00-12:00"],
        ["Sala 03", "Sexta 08:00-10:00"]
      ],
      trimestres: [
        {
          nome: "1° Trimestre",
          conteudo: "Descobrimento do Brasil e colonização",
          data: "19/02/2025"
        },
        {
          nome: "2° Trimestre",
          conteudo: "Período imperial e escravidão",
          data: "26/06/2025"
        },
        {
          nome: "3° Trimestre",
          conteudo: "República e história contemporânea",
          data: "26/10/2025"
        }
      ]
    },
    geografia: {
      nome: "GEOGRAFIA",
      conteudo: "Geografia do Brasil, Estados e Capitais, Relevo e Clima",
      frequencia: "Frequência: 88%",
      horarios: [
        ["Sala 04", "Terça 10:00-12:00"],
        ["Sala 04", "Quinta 08:00-10:00"]
      ],
      trimestres: [
        {
          nome: "1° Trimestre",
          conteudo: "Orientação espacial e mapas",
          data: "21/02/2025"
        },
        {
          nome: "2° Trimestre",
          conteudo: "Relevo e hidrografia brasileira",
          data: "29/06/2025"
        },
        {
          nome: "3° Trimestre",
          conteudo: "Clima e vegetação do Brasil",
          data: "29/10/2025"
        }
      ]
    },
    ciencias: {
      nome: "CIÊNCIAS",
      conteudo: "Corpo Humano, Ecologia, Experimentos Científicos, Sistema Solar",
      frequencia: "Frequência: 93%",
      horarios: [
        ["Laboratório de Ciências", "Quarta 08:00-10:00"],
        ["Laboratório de Ciências", "Sexta 10:00-12:00"]
      ],
      trimestres: [
        {
          nome: "1° Trimestre",
          conteudo: "Sistema solar e planetas",
          data: "22/02/2025"
        },
        {
          nome: "2° Trimestre",
          conteudo: "Corpo humano e saúde",
          data: "30/06/2025"
        },
        {
          nome: "3° Trimestre",
          conteudo: "Ecologia e preservação ambiental",
          data: "30/10/2025"
        }
      ]
    },
    artes: {
      nome: "ARTES",
      conteudo: "História da Arte, Técnicas de Pintura, Teatro, Música",
      frequencia: "Frequência: 97%",
      horarios: [
        ["Sala de Artes", "Segunda 14:00-16:00"]
      ],
      trimestres: [
        {
          nome: "1° Trimestre",
          conteudo: "Arte rupestre e primitiva",
          data: "23/02/2025"
        },
        {
          nome: "2° Trimestre",
          conteudo: "Arte brasileira e folclore",
          data: "01/07/2025"
        },
        {
          nome: "3° Trimestre",
          conteudo: "Arte contemporânea e expressão",
          data: "01/11/2025"
        }
      ]
    },
    educacao_fisica: {
      nome: "EDUCAÇÃO FÍSICA",
      conteudo: "Esportes Coletivos, Atividades Rítmicas, Jogos Tradicionais",
      frequencia: "Frequência: 98%",
      horarios: [
        ["Quadra Esportiva", "Terça 14:00-16:00"],
        ["Quadra Esportiva", "Quinta 14:00-16:00"]
      ],
      trimestres: [
        {
          nome: "1° Trimestre",
          conteudo: "Esportes coletivos: futsal e vôlei",
          data: "24/02/2025"
        },
        {
          nome: "2° Trimestre",
          conteudo: "Atividades rítmicas e dança",
          data: "02/07/2025"
        },
        {
          nome: "3° Trimestre",
          conteudo: "Jogos tradicionais e cooperativos",
          data: "02/11/2025"
        }
      ]
    },
    ingles: {
      nome: "INGLÊS",
      conteudo: "Vocabulário Básico, Conversação, Gramática Simplificada",
      frequencia: "Frequência: 91%",
      horarios: [
        ["Sala 05", "Quarta 14:00-16:00"]
      ],
      trimestres: [
        {
          nome: "1° Trimestre",
          conteudo: "Saudações e vocabulário básico",
          data: "25/02/2025"
        },
        {
          nome: "2° Trimestre",
          conteudo: "Verbos e frases simples",
          data: "03/07/2025"
        },
        {
          nome: "3° Trimestre",
          conteudo: "Conversação e compreensão",
          data: "03/11/2025"
        }
      ]
    },
    ensino_religioso: {
      nome: "ENSINO RELIGIOSO",
      conteudo: "Valores Humanos, Respeito às Diferenças, Tradições Culturais",
      frequencia: "Frequência: 96%",
      horarios: [
        ["Sala 06", "Sexta 14:00-16:00"]
      ],
      trimestres: [
        {
          nome: "1° Trimestre",
          conteudo: "Valores humanos e ética",
          data: "26/02/2025"
        },
        {
          nome: "2° Trimestre",
          conteudo: "Diversidade cultural e religiosa",
          data: "04/07/2025"
        },
        {
          nome: "3° Trimestre",
          conteudo: "Tradições e festividades",
          data: "04/11/2025"
        }
      ]
    }
  };
  
  // Função para carregar os dados da matéria
  function carregarMateria() {
    const urlParams = new URLSearchParams(window.location.search);
    const materiaId = urlParams.get('materia') || 'portugues';
    const ano = urlParams.get('ano') || '2025';
    const serie = urlParams.get('serie') || '5º Ano';
  
    const materia = materias[materiaId] || materias.portugues;
  
    // Atualiza os dados básicos
    document.getElementById('materia-nome').textContent = materia.nome;
    document.getElementById('frequencia-bar').style.width = materia.frequencia.split('%')[0] + '%';
    document.getElementById('frequencia-texto').textContent = materia.frequencia;
  
    // Preenche os trimestres
    const trimestresContainer = document.getElementById('trimestres-container');
    trimestresContainer.innerHTML = materia.trimestres.map(trimestre => `
      <div class="trimestre-card p-3">
        <h6 class="font-weight-bold">${trimestre.nome}</h6>
        <p class="mb-1">${trimestre.conteudo}</p>
        <span class="badge-data">${trimestre.data}</span>
      </div>
    `).join('');
  
    // Atualiza o título da página
    document.title = `${materia.nome} - Dashboard Acadêmico`;
  }
  
  // Carrega os dados quando a página terminar de carregar
  document.addEventListener('DOMContentLoaded', carregarMateria);