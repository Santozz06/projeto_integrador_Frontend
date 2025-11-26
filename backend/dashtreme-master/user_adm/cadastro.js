$(document).ready(function () {
  // Limpa mensagens após 5 segundos
  setTimeout(function () {
    $(".alert").alert("close");
  }, 5000);

  // Toast container
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
                <div class="toast-body" style="background:#fff; color:#000;">${msg}</div>
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
          $btn.prop("disabled", false).text(original);
          return;
        }
        showToast(j.mensagem || "Salvo com sucesso!", true);

        setTimeout(() => {
          window.location.href = "cadastro.php";
        }, 1500);
      })
      .catch((err) => {
        showToast("Falha na comunicação com o servidor.", false);
        $btn.prop("disabled", false).text(original);
      });
  }

  $("#formAluno").on("submit", function (e) {
    e.preventDefault();
    e.stopPropagation();

    if (this.checkValidity() === false) {
      $(this).addClass("was-validated");
      return false;
    }
    ajaxSubmit(this, "aluno");
    return false;
  });

  $("#formServidor").on("submit", function (e) {
    e.preventDefault();
    e.stopPropagation();

    if (this.checkValidity() === false) {
      $(this).addClass("was-validated");
      return false;
    }
    ajaxSubmit(this, "servidor");
    return false;
  });
});
