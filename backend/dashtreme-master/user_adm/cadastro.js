$(document).ready(function () {
  // Limpa mensagens após 5 segundos (fallback para modo não-AJAX)
  setTimeout(function () {
    $(".alert").alert("close");
  }, 5000);

  // Toast container (se não existir)
  if ($("#toast-container").length === 0) {
    $("body").append(
      '<div id="toast-container" style="position:fixed;top:1rem;right:1rem;z-index:1060"></div>'
    );
  }

  function showToast(msg, ok = true) {
    const id = "t" + Date.now();
    const html = `<div id="${id}" class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-delay="4000" style="min-width:240px;">
                <div class="toast-header ${
                  ok ? "bg-success text-white" : "bg-danger text-white"
                }">
                    <strong class="mr-auto">${ok ? "Sucesso" : "Erro"}</strong>
                    <small>agora</small>
                    <button type="button" class="ml-2 mb-1 close text-white" data-dismiss="toast" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="toast-body">${msg}</div>
            </div>`;
    $("#toast-container").append(html);
    $("#" + id).toast("show");
    setTimeout(() => {
      $("#" + id).remove();
    }, 4500);
  }

  // Validação simples em blur
  $("input, select").on("blur", function () {
    if (this.checkValidity()) {
      $(this).removeClass("is-invalid");
    } else {
      $(this).addClass("is-invalid");
    }
  });

  function ajaxSubmit(form, tipo) {
    const $form = $(form);
    const fd = new FormData(form);
    fd.append("ajax", "1");
    // Desabilita botões
    const $btn = $form.find('button[type="submit"]');
    const original = $btn.text();
    $btn.prop("disabled", true).text("Salvando...");

    fetch("cadastro.php", {
      method: "POST",
      body: fd,
    })
      .then((r) => r.json())
      .then((j) => {
        if (!j) throw new Error("Resposta inválida");
        if (!j.success) {
          showToast(j.mensagem || "Erro ao salvar.", false);
          return;
        }
        showToast(j.mensagem || "Salvo com sucesso!", true);
        // Funções para limpeza profunda (remoção total de dados de edição)
        function clearAlunoForm() {
          const $f = $("#formAluno");
          // limpar inputs text/date/email/password/number/hidden (exceto tipo)
          $f.find('input:not([name="tipo"])').each(function () {
            const t = this.type;
            if (
              [
                "text",
                "date",
                "email",
                "password",
                "number",
                "tel",
                "hidden",
              ].includes(t)
            ) {
              if (this.name === "id_aluno") {
                this.value = "";
                return;
              }
              if (t === "hidden") {
                if (this.name !== "id_aluno") return;
              }
              this.value = "";
            }
          });
          // selects
          $f.find("select").each(function () {
            $(this).val("").trigger("change");
          });
          // radios: marcar padrão "nao" para NEE
          $f.find("input[type=radio][name=nee]").prop("checked", false);
          $("#nee-nao").prop("checked", true);
          // checkboxes
          $f.find("input[type=checkbox]").prop("checked", false);
          // textareas
          $f.find("textarea").val("");
          // esconder bloco necessidades
          $f.find(".needs-box").hide();
          // tornar senha obrigatória novamente para novo cadastro
          $f.find(
            'input[name="senha"], input[name="confirmarSenhaAluno"]'
          ).attr("required", true);
        }
        function clearServidorForm() {
          const $f = $("#formServidor");
          $f.find('input:not([name="tipo"])').each(function () {
            const t = this.type;
            if (
              [
                "text",
                "date",
                "email",
                "password",
                "number",
                "tel",
                "hidden",
              ].includes(t)
            ) {
              if (this.name === "id_servidor") {
                this.value = "";
                return;
              }
              if (t === "hidden") {
                if (this.name !== "id_servidor") return;
              }
              this.value = "";
            }
          });
          $f.find("select").each(function () {
            $(this).val("").trigger("change");
          });
          $f.find("textarea").val("");
          $f.find("input[type=checkbox]").prop("checked", false);
          $f.find("input[type=radio]").prop("checked", false);
          // senha obrigatória novamente
          $f.find(
            'input[name="senha"], input[name="confirmarSenhaServidor"]'
          ).attr("required", true);
        }
        if (j.tipo === "aluno" && j.tabela) {
          const $tbody = $('table:contains("Alunos Cadastrados") tbody');
          if ($tbody.length) {
            $tbody.html(j.tabela);
            $tbody.find("tr.table-success").removeClass("table-success");
          }
          $('a[href="#aluno"]').tab("show");
          clearAlunoForm();
        } else if (j.tipo === "servidor" && j.tabela) {
          const $tbody = $('table:contains("Servidores Cadastrados") tbody');
          if ($tbody.length) {
            $tbody.html(j.tabela);
            $tbody.find("tr.table-success").removeClass("table-success");
          }
          $('a[href="#servidor"]').tab("show");
          clearServidorForm();
        }
      })
      .catch((err) => {
        console.error(err);
        showToast("Falha na comunicação com o servidor.", false);
      })
      .finally(() => {
        $btn.prop("disabled", false).text(original);
      });
  }

  // Intercepta submits
  $("#formAluno").on("submit", function (e) {
    // Usa lógica de validação já presente no arquivo principal (não duplicada aqui)
    // Se houver campos inválidos pelo HTML5, deixa o comportamento normal
    if (this.checkValidity() === false) return; // fallback submit normal
    e.preventDefault();
    ajaxSubmit(this, "aluno");
  });

  $("#formServidor").on("submit", function (e) {
    if (this.checkValidity() === false) return;
    e.preventDefault();
    ajaxSubmit(this, "servidor");
  });
  // Lógica de criação de coluna removida conforme solicitação.
});
