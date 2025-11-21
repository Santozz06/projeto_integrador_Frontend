<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Normas - SAS</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/app-style.css">
    <link rel="stylesheet" href="../assets/css/icons.css">
    <link rel="stylesheet" href="../assets/css/sidebar-menu.css">
    <link rel="stylesheet" href="../css/style.css?v=<?php echo time(); ?>">
</head>

<body class="bg-theme bg-theme1 user_professor_normas">
    <?php
    require("menu_padrao.php");
    ?>

        <div class="main-content">
            <div class="container-normas">
                <h2>Normas da Instituição</h2>
                <h4>Consulte aqui as diretrizes acadêmicas e administrativas.</h4>

                <div class="row">
                    <div class="col-md-3 norma-item">
                        <i class="zmdi zmdi-collection-folder-image"></i>
                        <p>Normas Acadêmicas</p>
                        <a href="#" class="download-btn" data-key="normas" download>
                            <i class="zmdi zmdi-download"></i>
                        </a>
                    </div>
                    <div class="col-md-3 norma-item">
                        <i class="zmdi zmdi-file-text"></i>
                        <p>Avaliações e Recuperações</p>
                        <a href="#" class="download-btn" data-key="avaliacoes" download>
                            <i class="zmdi zmdi-download"></i>
                        </a>
                    </div>
                    <div class="col-md-3 norma-item">
                        <i class="zmdi zmdi-time"></i>
                        <p>Frequência e Pontualidade</p>
                        <a href="#" class="download-btn" data-key="frequencia" download>
                            <i class="zmdi zmdi-download"></i>
                        </a>
                    </div>
                </div>
                

                <div class="actions">
                    <button class="btn-normas btn-baixar-todas">Baixar todas as normas em PDF</button>
                    <button class="btn-normas" id="btnMostrarUploader">Atualizar Normas</button>
                </div>

                <div class="alert-success" id="alertaSucesso">Normas atualizadas com sucesso!</div>

                <div id="uploader" style="display:none; margin-top:20px; text-align:left;">
                    <p style="margin-bottom:10px; color:#fff;">Envie novos PDFs para substituir os padrões ou adicionar outros arquivos.</p>
                    <div class="uploader-grid">
                        <div class="uploader-item">
                            <label>Normas Acadêmicas (PDF)</label>
                            <input type="file" id="fileNormas" accept="application/pdf">
                            <button class="btn-normas" style="margin-top:8px;" onclick="enviarArquivo('normas', 'fileNormas')">Enviar</button>
                        </div>
                        <div class="uploader-item">
                            <label>Avaliações e Recuperações (PDF)</label>
                            <input type="file" id="fileAvaliacoes" accept="application/pdf">
                            <button class="btn-normas" style="margin-top:8px;" onclick="enviarArquivo('avaliacoes', 'fileAvaliacoes')">Enviar</button>
                        </div>
                        <div class="uploader-item">
                            <label>Frequência e Pontualidade (PDF)</label>
                            <input type="file" id="fileFrequencia" accept="application/pdf">
                            <button class="btn-normas" style="margin-top:8px;" onclick="enviarArquivo('frequencia', 'fileFrequencia')">Enviar</button>
                        </div>
                    </div>
                    <div style="margin-top:20px; color:#fff;">
                        <strong>Outros enviados:</strong>
                        <ul id="listaExtras" style="margin-top:8px;"></ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="overlay toggle-menu"></div>
    </div>

    <script src="../assets/js/jquery.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/plugins/simplebar/js/simplebar.js"></script>
    <script src="../assets/js/sidebar-menu.js"></script>
    <script src="../assets/js/app-script.js"></script>
    <script>
        // Mapa de PDFs (preenchido pelo backend)
        let pdfs = { normas: '#', avaliacoes: '#', frequencia: '#' };

        // Simula o download de um arquivo
        function baixarPDF(caminho, nomeArquivo) {
            const link = document.createElement('a');
            link.href = caminho;
            link.download = nomeArquivo;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // Clique em cada botão individual
        document.querySelectorAll('.download-btn').forEach(function(btn){
            btn.addEventListener('click', function(){
                const key = this.getAttribute('data-key');
                const url = pdfs[key] || '#';
                baixarPDF(url, key + '.pdf');
            });
        });

        // Botão "Baixar todas as normas"
        function baixarTodasNormas() {
            for (const key in pdfs) {
                baixarPDF(pdfs[key], `${key}.pdf`);
            }
        }
        // Atribui função ao botão
        document.querySelector('.btn-baixar-todas').addEventListener('click', baixarTodasNormas);

        // Backend: listar e atualizar
        function carregarMap(){
            return $.getJSON('../includes/ajax/professor/normas/listar.php')
                .done(function(resp){
                    if (!resp || !resp.success) throw new Error('Falha ao listar');
                    pdfs = resp.data.map || pdfs;
                    // Atualiza hrefs (por segurança)
                    document.querySelectorAll('.download-btn').forEach(function(btn){
                        const key = btn.getAttribute('data-key');
                        if (key && pdfs[key]) btn.setAttribute('href', pdfs[key]);
                    });
                    // Lista extras
                    const extras = resp.data.extras || [];
                    const ul = document.getElementById('listaExtras');
                    if (ul){
                        ul.innerHTML = '';
                        extras.forEach(function(e){
                            const li = document.createElement('li');
                            const a = document.createElement('a'); a.href = e.url; a.textContent = e.name; a.download = e.name;
                            li.appendChild(a);
                            // Botão Excluir ao lado de cada item extra
                            const btn = document.createElement('button');
                            btn.className = 'btn-normas btn-excluir-extra';
                            btn.setAttribute('type', 'button');
                            btn.setAttribute('data-name', e.name);
                            btn.textContent = 'Excluir';
                            li.appendChild(btn);
                            ul.appendChild(li);
                        });
                    }
                });
        }

        function enviarArquivo(categoria, inputId){
            const input = document.getElementById(inputId);
            if (!input || !input.files || input.files.length === 0){ alert('Selecione um PDF'); return; }
            const fd = new FormData();
            fd.append('categoria', categoria);
            fd.append('arquivo', input.files[0]);
            $.ajax({
                url: '../includes/ajax/professor/normas/upload.php',
                method: 'POST',
                processData: false,
                contentType: false,
                data: fd,
                dataType: 'json'
            }).done(function(resp){
                if (resp && resp.success){
                    const alerta = document.getElementById('alertaSucesso');
                    alerta.style.display = 'block';
                    setTimeout(function(){ alerta.style.display = 'none'; }, 3000);
                    carregarMap();
                } else {
                    alert(resp && resp.message ? resp.message : 'Falha ao enviar');
                }
            }).fail(function(xhr){
                let msg = 'Erro ao enviar arquivo';
                try { const r = JSON.parse(xhr.responseText); if (r.message) msg = r.message; } catch{}
                alert(msg);
            });
        }

        // Excluir arquivo extra
        function excluirArquivo(nome){
            if (!nome) return;
            if (!confirm('Tem certeza que deseja excluir este arquivo?')) return;
            $.ajax({
                url: '../includes/ajax/professor/normas/remover.php',
                method: 'POST',
                data: { name: nome },
                dataType: 'json'
            }).done(function(resp){
                if (resp && resp.success){
                    carregarMap();
                } else {
                    alert(resp && resp.message ? resp.message : 'Falha ao excluir');
                }
            }).fail(function(xhr){
                let msg = 'Erro ao excluir arquivo';
                try { const r = JSON.parse(xhr.responseText); if (r.message) msg = r.message; } catch{}
                alert(msg);
            });
        }

        // Mostrar/Ocultar uploader
        document.getElementById('btnMostrarUploader').addEventListener('click', function(){
            const up = document.getElementById('uploader');
            up.style.display = (up.style.display === 'none' || up.style.display === '') ? 'block' : 'none';
            if (up.style.display === 'block'){ carregarMap(); }
        });

        // Carrega mapeamento ao iniciar
        $(function(){ carregarMap(); });

        // Delegação de evento para excluir
        $('#listaExtras').on('click', '.btn-excluir-extra', function(){
            var nome = this.getAttribute('data-name');
            excluirArquivo(nome);
        });
    </script>

</body>

</html>