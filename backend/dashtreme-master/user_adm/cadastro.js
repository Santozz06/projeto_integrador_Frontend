 $(document).ready(function() {
            // Limpa mensagens após 5 segundos
            setTimeout(function() {
                $('.alert').alert('close');
            }, 5000);

            // Validação em tempo real
            $('input, select').on('blur', function() {
                if (this.checkValidity()) {
                    $(this).removeClass('is-invalid');
                } else {
                    $(this).addClass('is-invalid');
                }
            });
        });