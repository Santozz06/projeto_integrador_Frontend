<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Atividades - Dashboard Acadêmico</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/app-style.css">
    <link rel="stylesheet" href="../assets/css/icons.css">
    <link rel="stylesheet" href="../assets/css/sidebar-menu.css">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background: linear-gradient(to right, #2c3e50, #3498db);
            color: #ecf0f1;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container-select {
            max-width: 900px;
            margin: 40px auto;
            background: rgba(255, 255, 255, 0.05);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        .atividade-form select,
        .atividade-form input[type="text"],
        .atividade-form input[type="date"] {
            background-color: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            border: 1px solid #71affe;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 15px;
            width: 100%;
        }

        .btn {
            background-color: #1abc9c;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #16a085;
        }

        .btn-secondary {
            background-color: #7f8c8d;
        }

        .btn-secondary:hover {
            background-color: #616a6b;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 0.85em;
            margin-right: 5px;
        }

        .btn-danger {
            background-color: #e74c3c;
            color: #fff;
        }

        .btn-danger:hover {
            background-color: #c0392b;
        }

        .atividade-item {
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.08);
        }

        .atividade-titulo {
            color: #ffffff;
            font-weight: 600;
        }

        .atividade-info {
            font-size: 0.9em;
            color: #ffffff;
        }

        .navbar {
            background-color: rgba(0, 0, 0, 0.2) !important;
            backdrop-filter: blur(10px);
        }
    </style>
</head>

<body class="bg-theme bg-theme1">
    <?php
    require("menu_padrao.php");
    ?>

        <!-- Conteúdo Principal -->
        <div class="content-wrapper">
            <div class="container-select">
                <h2>Gerenciar Atividades</h2>
                <div class="atividade-form">
                    <select id="filtroDisciplina">
                        <option value="Todas">Todas as Disciplinas</option>
                        <option value="Matemática">Matemática</option>
                        <option value="História">História</option>
                        <option value="Geografia">Geografia</option>
                    </select>

                    <input type="text" id="tituloAtividade" placeholder="Título da atividade">
                    <input type="date" id="dataAtividade">
                    <select id="disciplinaAtividade">
                        <option value="Matemática">Matemática</option>
                        <option value="História">História</option>
                        <option value="Geografia">Geografia</option>
                    </select>
                    <div class="btn-group" style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <button class="btn btn-sm" id="btnSalvarAtividade" onclick="salvarAtividade()">Adicionar
                            Atividade</button>
                        <button class="btn btn-secondary btn-sm" id="btnCancelarEdicao" style="display: none;"
                            onclick="cancelarEdicao()">Cancelar</button>
                    </div>

                </div>

                <div id="listaAtividades"></div>
            </div>
            <div class="overlay toggle-menu"></div>
        </div>

        <script src="../assets/js/jquery.min.js"></script>
        <script src="../assets/js/bootstrap.min.js"></script>
        <script src="../assets/plugins/simplebar/js/simplebar.js"></script>
        <script src="../assets/js/sidebar-menu.js"></script>
        <script src="../assets/js/app-script.js"></script>
        <script src="botaoSair.js"></script>

        <script>
            const atividades = [
                { titulo: "Exercício de Gráficos", data: "2025-08-20", disciplina: "Matemática" },
                { titulo: "Mapa do Brasil", data: "2025-08-21", disciplina: "Geografia" },
            ];

            let indiceEditando = null;

            function renderizarAtividades() {
                const filtro = document.getElementById("filtroDisciplina").value;
                const lista = document.getElementById("listaAtividades");
                lista.innerHTML = "";

                const filtradas = atividades.filter((a) => filtro === "Todas" || a.disciplina === filtro);

                if (filtradas.length === 0) {
                    lista.innerHTML = '<p style="text-align:center; color:#bdc3c7; font-style:italic;">Nenhuma atividade encontrada</p>';
                    return;
                }

                filtradas.forEach((a, index) => {
                    const div = document.createElement("div");
                    div.className = "atividade-item";
                    div.innerHTML = `
          <div class="atividade-titulo">${a.titulo}</div>
          <div class="atividade-info">${a.data} - ${a.disciplina}</div>
          <div style="margin-top: 10px;">
            <button class="btn btn-sm btn-primary" onclick="editarAtividade(${index})">Editar</button>
            <button class="btn btn-sm btn-danger" onclick="removerAtividade(${index})">Remover</button>
          </div>
        `;
                    lista.appendChild(div);
                });
            }

            function salvarAtividade() {
                const titulo = document.getElementById("tituloAtividade").value.trim();
                const data = document.getElementById("dataAtividade").value;
                const disciplina = document.getElementById("disciplinaAtividade").value;

                if (!titulo || !data) {
                    alert("Preencha todos os campos.");
                    return;
                }

                if (indiceEditando !== null) {
                    atividades[indiceEditando] = { titulo, data, disciplina };
                    indiceEditando = null;
                    document.getElementById("btnSalvarAtividade").textContent = "Adicionar Atividade";
                    document.getElementById("btnCancelarEdicao").style.display = "none";
                } else {
                    atividades.push({ titulo, data, disciplina });
                }

                limparFormulario();
                renderizarAtividades();
            }

            function editarAtividade(index) {
                const atividade = atividades[index];
                document.getElementById("tituloAtividade").value = atividade.titulo;
                document.getElementById("dataAtividade").value = atividade.data;
                document.getElementById("disciplinaAtividade").value = atividade.disciplina;
                document.getElementById("btnSalvarAtividade").textContent = "Atualizar Atividade";
                document.getElementById("btnCancelarEdicao").style.display = "inline-block";
                indiceEditando = index;
            }

            function removerAtividade(index) {
                if (confirm("Deseja remover esta atividade?")) {
                    atividades.splice(index, 1);
                    renderizarAtividades();
                    cancelarEdicao();
                }
            }

            function cancelarEdicao() {
                limparFormulario();
                indiceEditando = null;
                document.getElementById("btnSalvarAtividade").textContent = "Adicionar Atividade";
                document.getElementById("btnCancelarEdicao").style.display = "none";
            }

            function limparFormulario() {
                document.getElementById("tituloAtividade").value = "";
                document.getElementById("dataAtividade").value = "";
            }

            document.getElementById("filtroDisciplina").addEventListener("change", renderizarAtividades);
            renderizarAtividades();
        </script>
</body>

</html>