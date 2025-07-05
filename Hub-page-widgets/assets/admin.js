/**
 * JavaScript para admin del Widget Hub Page
 * Archivo: admin.js
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        initAdminFunctionality();
    });

    /**
     * Inicializar funcionalidades del admin
     */
    function initAdminFunctionality() {
        initImageSelectors();
        initPreview();
        initValidation();
    }

    /**
     * Selector de imágenes de WordPress
     */
    function initImageSelectors() {
        $(document).on('click', '.hub-select-image', function(e) {
            e.preventDefault();
            
            const button = $(this);
            const targetInput = $('#' + button.data('target'));
            
            // Crear el media uploader
            const mediaUploader = wp.media({
                title: 'Seleccionar imagen para el hub',
                button: {
                    text: 'Usar esta imagen'
                },
                multiple: false,
                library: {
                    type: 'image'
                }
            });

            // Cuando se selecciona una imagen
            mediaUploader.on('select', function() {
                const attachment = mediaUploader.state().get('selection').first().toJSON();
                
                // Insertar URL en el campo
                targetInput.val(attachment.url);
                
                // Mostrar preview si es posible
                showImagePreview(targetInput, attachment.url);
                
                // Marcar como modificado
                targetInput.trigger('change');
            });

            // Abrir el uploader
            mediaUploader.open();
        });
    }

    /**
     * Mostrar preview de imagen
     */
    function showImagePreview(input, imageUrl) {
        const inputContainer = input.parent();
        let preview = inputContainer.find('.image-preview');
        
        if (preview.length === 0) {
            preview = $('<div class="image-preview" style="margin-top: 10px;"></div>');
            inputContainer.append(preview);
        }
        
        preview.html(`
            <img src="${imageUrl}" style="max-width: 150px; max-height: 100px; border-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <br>
            <button type="button" class="button-link remove-image" style="color: #dc3232; margin-top: 5px;">Quitar imagen</button>
        `);
        
        // Manejar quitar imagen
        preview.find('.remove-image').on('click', function() {
            input.val('');
            preview.remove();
            input.trigger('change');
        });
    }

    /**
     * Preview en tiempo real del widget
     */
    function initPreview() {
        // Actualizar preview cuando cambien los campos
        $(document).on('change keyup', '.hub-admin-form input, .hub-admin-form select, .hub-admin-form textarea', function() {
            debounce(updatePreview, 500)();
        });
    }

    /**
     * Actualizar preview del widget
     */
    function updatePreview() {
        const widgetForm = $('.hub-admin-form').closest('form');
        if (widgetForm.length === 0) return;
        
    /**
     * Actualizar preview del widget
     */
    function updatePreview() {
        const widgetForm = $('.hub-admin-form').closest('form');
        if (widgetForm.length === 0) return;
        
        // Recopilar datos del formulario
        const formData = {
            titulo_hub: widgetForm.find('input[name*="titulo_hub"]').val() || 'Hub de Contenido',
            categoria: widgetForm.find('select[name*="categoria"]').val() || 'all',
            imagen_izquierda: widgetForm.find('input[name*="imagen_izquierda"]').val() || '',
            popup_titulo: widgetForm.find('input[name*="popup_titulo"]').val() || '',
            popup_contenido: widgetForm.find('textarea[name*="popup_contenido"]').val() || '',
            publicacion_destacada: widgetForm.find('select[name*="publicacion_destacada"]').val() || '',
            publicacion_sticky: widgetForm.find('select[name*="publicacion_sticky"]').val() || '',
            limite_publicaciones: widgetForm.find('input[name*="limite_publicaciones"]').val() || 8,
            imagen_boton: widgetForm.find('input[name*="imagen_boton"]').val() || '',
            url_boton: widgetForm.find('input[name*="url_boton"]').val() || ''
        };
        
        // Crear o actualizar preview
        let previewContainer = widgetForm.find('.widget-preview');
        if (previewContainer.length === 0) {
            previewContainer = $('<div class="widget-preview" style="margin-top: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 4px; background: #f9f9f9;"></div>');
            widgetForm.append(previewContainer);
        }
        
        // Generar HTML del preview
        const previewHtml = generatePreviewHtml(formData);
        previewContainer.html(`
            <h4 style="margin: 0 0 15px 0; color: #2c5aa0;">Vista previa del widget:</h4>
            ${previewHtml}
        `);
    }

    /**
     * Generar lista de artículos para preview
     */
    function generateArticlesList(count) {
        let html = '';
        const maxCount = Math.min(count, 5);
        
        for (let i = 0; i < maxCount; i++) {
            html += `
                <div style="display: flex; align-items: center; gap: 8px; padding: 5px 0; border-bottom: 1px solid #f0f0f0;">
                    <div style="width: 40px; height: 40px; background: #ddd; border-radius: 2px; flex-shrink: 0;"></div>
                    <div style="flex: 1;">
                        <div style="font-size: 12px; color: #333; font-weight: 500;">Artículo ${i + 1}</div>
                        <div style="font-size: 10px; color: #666;">Extracto del artículo...</div>
                    </div>
                </div>
            `;
        }
        
        return html;
    }

    /**
     * Generar HTML del preview
     */
    function generatePreviewHtml(data) {
        return `
            <div style="border: 1px solid #e0e0e0; border-radius: 4px; overflow: hidden; font-family: Arial, sans-serif; max-width: 600px;">
                <div style="background: #2c5aa0; color: white; padding: 15px;">
                    <h3 style="margin: 0; font-size: 18px;">${data.titulo_hub}</h3>
                </div>
                <div style="padding: 15px; display: grid; grid-template-columns: 1fr 2fr 1fr; gap: 15px; min-height: 200px;">
                    <div style="background: #f5f5f5; border-radius: 4px; height: 120px; position: relative; display: flex; align-items: center; justify-content: center;">
                        ${data.imagen_izquierda ? 
                            `<img src="${data.imagen_izquierda}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 4px;">` :
                            '<span style="color: #999; font-size: 12px;">Imagen izquierda</span>'
                        }
                        ${data.popup_titulo ? '<div style="position: absolute; bottom: 5px; left: 5px; background: rgba(0,0,0,0.7); color: white; padding: 3px 6px; font-size: 10px; border-radius: 2px;">ℹ️ Sobre este contenido</div>' : ''}
                    </div>
                    <div>
                        ${data.publicacion_destacada ? 
                            '<div style="background: #f8f9fa; border-radius: 4px; padding: 10px; margin-bottom: 10px;"><div style="height: 60px; background: #ddd; border-radius: 2px; margin-bottom: 8px;"></div><h4 style="margin: 0; font-size: 14px; color: #2c5aa0;">Publicación destacada</h4></div>' : 
                            ''
                        }
                        <div style="border-top: ${data.publicacion_destacada ? '1px solid #eee; padding-top: 10px;' : ''}">
                            ${generateArticlesList(parseInt(data.limite_publicaciones) || 3)}
                        </div>
                    </div>
                    <div>
                        ${data.publicacion_sticky ? 
                            '<div style="background: linear-gradient(135deg, #2c5aa0 0%, #1e3a5f 100%); border-radius: 4px; padding: 10px; color: white; margin-bottom: 10px;"><div style="height: 50px; background: rgba(255,255,255,0.2); border-radius: 2px; margin-bottom: 8px;"></div><h4 style="margin: 0; font-size: 12px;">Publicación sticky</h4></div>' : 
                            ''
                        }
                        ${data.imagen_boton ? 
                            '<div style="margin-top: auto;"><div style="width: 100%; height: 60px; background: #ddd; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #666;">Botón de redirección</div></div>' :
                            ''
                        }
                    </div>
                </div>
            </div>
        `;
    }
    }

    /**
     * Validación de formulario
     */
    function initValidation() {
        $(document).on('blur', '.hub-admin-form input[type="url"]', function() {
            const input = $(this);
            const url = input.val().trim();
            
            if (url && !isValidUrl(url)) {
                showValidationError(input, 'Por favor, ingresa una URL válida');
            } else {
                clearValidationError(input);
            }
        });

        $(document).on('input', '.hub-admin-form input[type="number"]', function() {
            const input = $(this);
            const value = parseInt(input.val());
            const min = parseInt(input.attr('min'));
            const max = parseInt(input.attr('max'));
            
            if (value < min || value > max) {
                showValidationError(input, `El valor debe estar entre ${min} y ${max}`);
            } else {
                clearValidationError(input);
            }
        });
    }

    /**
     * Mostrar error de validación
     */
    function showValidationError(input, message) {
        clearValidationError(input);
        
        const errorDiv = $(`<div class="validation-error" style="color: #dc3232; font-size: 12px; margin-top: 5px;">${message}</div>`);
        input.after(errorDiv);
        input.css('border-color', '#dc3232');
    }

    /**
     * Limpiar error de validación
     */
    function clearValidationError(input) {
        input.next('.validation-error').remove();
        input.css('border-color', '');
    }

    /**
     * Validar URL
     */
    function isValidUrl(string) {
        try {
            new URL(string);
            return true;
        } catch (_) {
            return false;
        }
    }

    /**
     * Función debounce para optimizar performance
     */
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    /**
     * Funcionalidades adicionales del admin
     */
    function initAdditionalFeatures() {
        // Colapsar/expandir secciones
        $(document).on('click', '.hub-admin-form fieldset legend', function() {
            const fieldset = $(this).parent();
            const content = fieldset.find('> *:not(legend)');
            
            if (content.is(':visible')) {
                content.slideUp(200);
                $(this).append(' <span style="float: right;">▲</span>');
            } else {
                content.slideDown(200);
                $(this).find('span').remove();
            }
        });

        // Tooltips informativos
        $('<style>.hub-tooltip { position: relative; } .hub-tooltip:hover::after { content: attr(data-tooltip); position: absolute; bottom: 100%; left: 50%; transform: translateX(-50%); background: #333; color: white; padding: 5px 8px; border-radius: 4px; font-size: 12px; white-space: nowrap; z-index: 1000; }</style>').appendTo('head');
        
        // Agregar tooltips
        $('.hub-admin-form input[name*="limite_publicaciones"]').addClass('hub-tooltip').attr('data-tooltip', 'Número de artículos que aparecerán en la lista central');
        $('.hub-admin-form select[name*="categoria"]').addClass('hub-tooltip').attr('data-tooltip', 'Categoría de la cual mostrar las publicaciones');
    }

    /**
     * Auto-guardar configuración (opcional)
     */
    function initAutoSave() {
        let autoSaveTimeout;
        
        $(document).on('change', '.hub-admin-form input, .hub-admin-form select, .hub-admin-form textarea', function() {
            clearTimeout(autoSaveTimeout);
            
            autoSaveTimeout = setTimeout(function() {
                // Solo si el widget ya está guardado
                const widgetId = $('.hub-admin-form').closest('.widget').find('.widget-id').val();
                if (widgetId) {
                    saveWidgetConfig();
                }
            }, 2000);
        });
    }

    /**
     * Guardar configuración del widget
     */
    function saveWidgetConfig() {
        const form = $('.hub-admin-form').closest('form');
        const formData = form.serialize();
        
        $.ajax({
            url: window.ajaxurl || '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: formData + '&action=save_widget',
            success: function(response) {
                showNotification('Configuración guardada automáticamente', 'success');
            },
            error: function() {
                showNotification('Error al guardar la configuración', 'error');
            }
        });
    }

    /**
     * Mostrar notificación
     */
    function showNotification(message, type = 'info') {
        const notification = $(`
            <div class="hub-notification" style="
                position: fixed;
                top: 32px;
                right: 20px;
                background: ${type === 'success' ? '#4CAF50' : type === 'error' ? '#f44336' : '#2196F3'};
                color: white;
                padding: 12px 20px;
                border-radius: 4px;
                z-index: 10000;
                box-shadow: 0 4px 12px rgba(0,0,0,0.3);
                animation: slideInRight 0.3s ease;
            ">
                ${message}
            </div>
        `);
        
        $('body').append(notification);
        
        setTimeout(function() {
            notification.fadeOut(300, function() {
                $(this).remove();
            });
        }, 3000);
    }

    // Inicializar funcionalidades adicionales
    initAdditionalFeatures();
    initAutoSave();

    // CSS para animaciones
    $('<style>@keyframes slideInRight { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }</style>').appendTo('head');

})(jQuery);