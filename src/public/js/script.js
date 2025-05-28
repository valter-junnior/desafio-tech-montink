// app/public/js/script.js

document.addEventListener('DOMContentLoaded', function() {
    // Lógica para adicionar/remover variações no formulário de produto
    const addVariationBtn = document.getElementById('add-variation-btn');
    const variationsContainer = document.getElementById('variations-container');
    const variationTemplate = document.getElementById('variation-template');

    if (addVariationBtn && variationsContainer && variationTemplate) {
        addVariationBtn.addEventListener('click', function() {
            const newVariation = variationTemplate.content.cloneNode(true);
            variationsContainer.appendChild(newVariation);
        });

        variationsContainer.addEventListener('click', function(event) {
            if (event.target.classList.contains('remove-variation-btn')) {
                // Previne remover a última variação
                if (variationsContainer.querySelectorAll('.variation-item').length > 1) {
                    event.target.closest('.variation-item').remove();
                } else {
                    alert('É necessário ter ao menos uma variação.');
                }
            }
        });
    }

    // Lógica para consulta de CEP com ViaCEP
    const lookupCepBtn = document.getElementById('lookup_cep_btn');
    const customerZipcodeInput = document.getElementById('customer_zipcode');
    const customerAddressInput = document.getElementById('customer_address');
    const customerCityInput = document.getElementById('customer_city');
    const customerStateInput = document.getElementById('customer_state');

    if (lookupCepBtn && customerZipcodeInput) {
        lookupCepBtn.addEventListener('click', function() {
            const cep = customerZipcodeInput.value.replace(/\D/g, ''); // Remove não dígitos

            if (cep.length !== 8) {
                alert('CEP inválido. Digite 8 dígitos.');
                return;
            }

            $.ajax({
                url: '/api/cep?cep=' + cep,
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.error) {
                        alert(data.error);
                        customerAddressInput.value = '';
                        customerCityInput.value = '';
                        customerStateInput.value = '';
                    } else if (!data.erro) { // ViaCEP retorna 'erro: true' se não encontrar
                        customerAddressInput.value = data.logradouro + (data.complemento ? ' - ' + data.complemento : '');
                        customerCityInput.value = data.localidade;
                        customerStateInput.value = data.uf;
                    } else {
                        alert('CEP não encontrado.');
                        customerAddressInput.value = '';
                        customerCityInput.value = '';
                        customerStateInput.value = '';
                    }
                },
                error: function() {
                    alert('Erro ao consultar CEP. Tente novamente mais tarde.');
                    customerAddressInput.value = '';
                    customerCityInput.value = '';
                    customerStateInput.value = '';
                }
            });
        });

        // Opcional: Auto-preencher CEP ao digitar e sair do campo
        customerZipcodeInput.addEventListener('blur', function() {
            if (this.value.replace(/\D/g, '').length === 8) {
                lookupCepBtn.click(); // Simula o clique no botão
            }
        });
    }
});