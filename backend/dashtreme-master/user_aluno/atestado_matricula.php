<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <link rel="icon" href="../assets/images/favicon.ico" type="image/x-icon">
  <meta charset="UTF-8">
  (function(){
  function limparTemaParaPDF(){
  if(document.getElementById('force-clean-style')) return;
  const style = document.createElement('style');
  style.id = 'force-clean-style';
  style.appendChild(document.createTextNode('\n'
  + 'html, body { background: #ffffff !important; background-image: none !important; }\n'
  + '* { backdrop-filter: none !important; -webkit-backdrop-filter: none !important; box-shadow: none !important;
  text-shadow: none !important; filter: none !important; }\n'
  + '#pageloader-overlay, .overlay, .overlay.toggle-menu, .modal-backdrop, .modal-backdrop.show, .menu-overlay,
  .sidebar-wrapper, .sidebar-menu { display: none !important; }\n'
  + 'body::before, body::after, html::before, html::after { display: none !important; content: none !important; }\n'
  ));
  document.head.appendChild(style);
  }
  function restaurarTema(){ const s=document.getElementById('force-clean-style'); if(s) s.remove(); }

  function _createCleanWrapper(sourceEl){
  try{
  var clone = sourceEl.cloneNode(true);
  var nodes = clone.querySelectorAll('*');
  Array.prototype.forEach.call(nodes, function(n){ n.className=''; n.removeAttribute('style'); });
  clone.className='';
  var wrapper = document.createElement('div');
  wrapper.id = 'pdf-clean-wrapper';
  wrapper.style.position = 'fixed';
  wrapper.style.top = '0';
  wrapper.style.left = '0';
  wrapper.style.zIndex = '2147483647';
  wrapper.style.background = '#ffffff';
  wrapper.style.color = '#000000';
  wrapper.style.padding = '10mm';
  wrapper.style.width = '210mm';
  wrapper.style.minHeight = '297mm';
  wrapper.style.boxSizing = 'border-box';
  wrapper.style.fontFamily = '"Times New Roman", serif';
  wrapper.style.fontSize = '16px';
  wrapper.appendChild(clone);
  document.body.appendChild(wrapper);
  return wrapper;
  }catch(e){ console.error('Erro criar wrapper:', e); return sourceEl; }
  }

  function gerarAtestado(){
  var source = document.getElementById('doc');
  if(!source) { alert('Elemento do documento não encontrado'); return; }
  var cleanEl = _createCleanWrapper(source);
  limparTemaParaPDF();
  html2pdf().set({
  margin: 10,
  filename: 'atestado_matricula.pdf',
  image: { type: 'jpeg', quality: 0.98 },
  html2canvas: { scale: 2, useCORS: true, logging: false },
  jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
  }).from(cleanEl).save().then(function(){
  try{ if(cleanEl && cleanEl.parentNode) cleanEl.parentNode.removeChild(cleanEl); }catch(e){}
  restaurarTema();
  }).catch(function(err){
  console.error(err);
  try{ if(cleanEl && cleanEl.parentNode) cleanEl.parentNode.removeChild(cleanEl); }catch(e){}
  restaurarTema();
  });
  }

  // Se existir botão com id gerar-atestado, ligar o evento. Caso contrário, gerar automaticamente após carregar.
  document.addEventListener('DOMContentLoaded', function(){
  var btn = document.getElementById('gerar-atestado');
  if(btn) btn.addEventListener('click', gerarAtestado);
  else setTimeout(gerarAtestado, 500);
  });
  })();
  Turma/Série: <strong><span id="turma">—</span></strong>
  </div>

  <div class="rodape">
    <strong>Parobé - RS, 08 de julho de 2025</strong>
  </div>

  <div class="autenticidade">
    Para verificar a autenticidade deste documento, acesse:<br>
    <a href="http://meusite.com/autenticacao" target="_blank">http://meusite.com/autenticacao</a>
  </div>

  <div class="codigo">
    Código de verificação: <span>[XXXX-YYYY-ZZZZ]</span>
  </div>

  </div>

  <script>
    function limparTemaParaPDF() {
      const style = document.createElement("style");
      style.id = "force-clean-style";
      style.innerHTML = `
        * {
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
            box-shadow: none !important;
            text-shadow: none !important;
            filter: none !important;
        }
        body, html {
            background: #ffffff !important;
            background-image: none !important;
        }
        .navbar,
        .content-wrapper,
        .overlay,
        .toggle-menu,
        #pageloader-overlay,
        .modal-backdrop,
        .sidebar-wrapper,
        .sidebar-menu {
            display: none !important;
        }
    `;
      document.head.appendChild(style);
    }
    function restaurarTema() {
      const style = document.getElementById("force-clean-style");
      if (style) style.remove();
    }
    $('#gerar-atestado').click(function () {

      limparTemaParaPDF();
      html2pdf().set({
        margin: 10,
        filename: 'atestado_matricula.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: {
          scale: 2,
          useCORS: true,
          logging: false
        },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
      }).from(cleanEl).save().then(() => {
        restaurarTema();
      });
    });


  </script>
  </body>

</html>