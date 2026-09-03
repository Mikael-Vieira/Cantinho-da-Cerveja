document.addEventListener('DOMContentLoaded', function () {
    var overlay = document.getElementById('checkout-modal-overlay');
    var btnOpen = document.getElementById('btn-open-checkout');
    var btnClose = document.getElementById('btn-close-checkout');
    var btnCancel = document.getElementById('btn-cancel-checkout');

    var radioLocal = document.getElementById('tipo_local');
    var radioEntrega = document.getElementById('tipo_entrega');
    var camposEntrega = document.getElementById('campos-entrega');
    var camposLocal = document.getElementById('campos-local');
    var addressInput = document.getElementById('address');

    if (!overlay) return;

    // Se o modal já abriu sozinho (ex: reabertura após erro de validação),
    // trava o scroll da página igual quando é aberto manualmente.
    if (overlay.classList.contains('is-open')) {
        document.body.style.overflow = 'hidden';
    }

    function openModal() {
        overlay.classList.add('is-open');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        overlay.classList.remove('is-open');
        document.body.style.overflow = '';
    }

    function atualizarCamposPorTipo() {
        var isEntrega = radioEntrega.checked;

        camposEntrega.hidden = !isEntrega;
        camposLocal.hidden = isEntrega;

        // Mantém o "required" alinhado com a validação do backend
        if (isEntrega) {
            addressInput.setAttribute('required', 'required');
        } else {
            addressInput.removeAttribute('required');
        }
    }

    if (btnOpen) {
        btnOpen.addEventListener('click', openModal);
    }

    if (btnClose) {
        btnClose.addEventListener('click', closeModal);
    }

    if (btnCancel) {
        btnCancel.addEventListener('click', closeModal);
    }

    // Fecha ao clicar fora da caixa do modal
    overlay.addEventListener('click', function (event) {
        if (event.target === overlay) {
            closeModal();
        }
    });

    // Fecha com a tecla ESC
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && overlay.classList.contains('is-open')) {
            closeModal();
        }
    });

    // Alterna os campos ao trocar o tipo de pedido
    if (radioLocal && radioEntrega) {
        radioLocal.addEventListener('change', atualizarCamposPorTipo);
        radioEntrega.addEventListener('change', atualizarCamposPorTipo);
        atualizarCamposPorTipo(); // estado inicial
    }
});
