jQuery(document).ready(function($) {
    // Cargar configuración inicial
    loadHubsConfiguration();
    
    // Manejar submit del formulario
    $('#hubs-config-form').on('submit', function(e) {
        e.preventDefault();
        saveHubsConfiguration();
    });

    // Manejar reset
    $('#reset-hubs').on('click', function() {
        if (confirm('¿Restaurar configuración predeterminada? Se perderán todos los cambios.')) {
            resetHubsConfiguration();
        }
    });

    function loadHubsConfiguration() {
        $.post(hubsAjax.ajax_url, {
            action: 'get_hubs_config',
            nonce: hubsAjax.nonce
        }, function(response) {
            if (response.success) {
                renderConfigFields(response.data);
            } else {
                alert('Error al cargar la configuración');
            }
        });
    }

    function saveHubsConfiguration() {
        const hubs = [];
        $('.hub-config-item').each(function() {
            const hubId = $(this).data('hub-id');
            hubs.push({
                id: hubId,
                title: $(this).find('.hub-title').val(),
                description: $(this).find('.hub-description').val(),
                url: $(this).find('.hub-url').val(),
                icon: $(this).find('.hub-icon').val(),
                image: $(this).find('.hub-image').val()
            });
        });

        $.post(hubsAjax.ajax_url, {
            action: 'save_hubs_config',
            nonce: hubsAjax.nonce,
            hubs: JSON.stringify(hubs)
        }, function(response) {
            if (response.success) {
                alert('✅ Configuración guardada exitosamente');
                // Actualizar vista previa
                updatePreview();
            } else {
                alert('❌ Error al guardar la configuración');
            }
        });
    }

    function resetHubsConfiguration() {
        $.post(hubsAjax.ajax_url, {
            action: 'save_hubs_config',
            nonce: hubsAjax.nonce,
            reset: true
        }, function(response) {
            if (response.success) {
                alert('✅ Configuración restaurada');
                location.reload();
            } else {
                alert('❌ Error al restaurar la configuración');
            }
        });
    }

    function renderConfigFields(hubs) {
        const container = $('#hubs-config-fields');
        container.empty();

        hubs.forEach(function(hub) {
            const configItem = $(`
                <div class="hub-config-item" data-hub-id="${hub.id}">
                    <h4>Hub ${hub.id}: ${hub.title}</h4>
                    
                    <label>Título:</label>
                    <input type="text" class="hub-title" value="${hub.title}" placeholder="Nombre del hub">
                    
                    <label>Descripción:</label>
                    <textarea class="hub-description" placeholder="Descripción del hub">${hub.description}</textarea>
                    
                    <label>URL (página de destino):</label>
                    <input type="text" class="hub-url" value="${hub.url}" placeholder="/categoria/nombre-categoria">
                    
                    <label>Icono (emoji):</label>
                    <input type="text" class="hub-icon" value="${hub.icon}" placeholder="🏛️" maxlength="10">
                    
                    <label>Imagen URL (opcional):</label>
                    <input type="text" class="hub-image" value="${hub.image || ''}" placeholder="https://ejemplo.com/imagen.jpg">
                    
                    <button type="button" class="button" onclick="selectImage(${hub.id})">📁 Seleccionar Imagen</button>
                </div>
            `);
            container.append(configItem);
        });
    }

    function updatePreview() {
        // Simular actualización de vista previa
        $('#hubs-preview-container').html('<p>Actualizando vista previa...</p>');
        setTimeout(function() {
            location.reload(); // Recargar para mostrar cambios
        }, 1000);
    }

    // Función global para seleccionar imagen
    window.selectImage = function(hubId) {
        // Si tienes WordPress media uploader disponible
        if (typeof wp !== 'undefined' && wp.media) {
            const mediaUploader = wp.media({
                title: 'Seleccionar Imagen para Hub',
                button: {
                    text: 'Usar esta imagen'
                },
                multiple: false
            });

            mediaUploader.on('select', function() {
                const attachment = mediaUploader.state().get('selection').first().toJSON();
                $(`.hub-config-item[data-hub-id="${hubId}"] .hub-image`).val(attachment.url);
            });

            mediaUploader.open();
        } else {
            // Fallback: pedir URL manualmente
            const imageUrl = prompt('Ingresa la URL de la imagen:');
            if (imageUrl) {
                $(`.hub-config-item[data-hub-id="${hubId}"] .hub-image`).val(imageUrl);
            }
        }
    };

    // Validación en tiempo real
    $(document).on('blur', '.hub-title, .hub-url', function() {
        const $field = $(this);
        const value = $field.val().trim();
        
        if (value === '') {
            $field.css('border-color', '#dc3545');
        } else {
            $field.css('border-color', '#28a745');
        }
    });

    // Preview de emojis
    $(document).on('input', '.hub-icon', function() {
        const $field = $(this);
        const value = $field.val();
        
        // Verificar si es un emoji válido
        if (value.length > 0) {
            $field.css('font-size', '20px');
        }
    });
});