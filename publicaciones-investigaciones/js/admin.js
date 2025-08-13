/**
 * JavaScript para el admin de Investigaciones
 * Maneja la subida y gestión de archivos adjuntos
 */

jQuery(document).ready(function($) {
    var fileFrame;
    var archivoIndex = $('#archivos_list .archivo-item').length;
    
    // Evento para agregar archivos
    $('#add_archivo_btn').on('click', function(e) {
        e.preventDefault();
        
        // Si el frame de medios ya existe, ábrelo
        if (fileFrame) {
            fileFrame.open();
            return;
        }
        
        // Crear el frame de medios
        fileFrame = wp.media({
            title: investigacionesAjax.strings.select_files,
            button: {
                text: investigacionesAjax.strings.use_files
            },
            multiple: true, // Permitir múltiples archivos
            library: {
                type: ['application', 'text'] // Tipos de archivo permitidos
            }
        });
        
        // Cuando se seleccionan archivos
        fileFrame.on('select', function() {
            var selection = fileFrame.state().get('selection');
            
            selection.map(function(attachment) {
                attachment = attachment.toJSON();
                addArchivoToList({
                    url: attachment.url,
                    nombre: attachment.filename,
                    titulo: attachment.title || attachment.filename.replace(/\.[^/.]+$/, ""),
                    size: attachment.filesizeInBytes || 0,
                    descripcion: attachment.caption || ''
                });
            });
        });
        
        fileFrame.open();
    });
    
    // Función para agregar archivo a la lista
    function addArchivoToList(archivo) {
        var extension = archivo.nombre.split('.').pop().toLowerCase();
        var iconClass = getFileIconClass(extension);
        var formattedSize = formatFileSize(archivo.size);
        
        var archivoHtml = `
            <div class="archivo-item" data-index="${archivoIndex}">
                <div class="archivo-info">
                    <div class="archivo-thumbnail">
                        <span class="dashicons ${iconClass}"></span>
                    </div>
                    <div class="archivo-details">
                        <input type="text" name="investigacion_archivos[${archivoIndex}][titulo]" 
                               placeholder="Título del archivo" 
                               value="${escapeHtml(archivo.titulo)}" 
                               class="widefat archivo-titulo" />
                        <input type="hidden" name="investigacion_archivos[${archivoIndex}][url]" 
                               value="${escapeHtml(archivo.url)}" />
                        <input type="hidden" name="investigacion_archivos[${archivoIndex}][nombre]" 
                               value="${escapeHtml(archivo.nombre)}" />
                        <input type="hidden" name="investigacion_archivos[${archivoIndex}][size]" 
                               value="${archivo.size}" />
                        <div class="archivo-meta">
                            <span class="archivo-name">${escapeHtml(archivo.nombre)}</span>
                            <span class="archivo-size">(${formattedSize})</span>
                        </div>
                        <div class="archivo-description">
                            <textarea name="investigacion_archivos[${archivoIndex}][descripcion]" 
                                      placeholder="Descripción del archivo (opcional)"
                                      rows="2" class="widefat">${escapeHtml(archivo.descripcion)}</textarea>
                        </div>
                    </div>
                    <div class="archivo-actions">
                        <a href="${archivo.url}" target="_blank" 
                           class="button button-small" title="Ver archivo">
                            <span class="dashicons dashicons-visibility"></span>
                        </a>
                        <button type="button" class="button button-small remove-archivo" 
                                title="Eliminar archivo">
                            <span class="dashicons dashicons-trash"></span>
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        $('#archivos_list').append(archivoHtml);
        archivoIndex++;
        
        // Mostrar animación de entrada
        $('#archivos_list .archivo-item:last').hide().fadeIn(400);
    }
    
    // Evento para eliminar archivos
    $(document).on('click', '.remove-archivo', function(e) {
        e.preventDefault();
        
        if (confirm(investigacionesAjax.strings.confirm_remove)) {
            var $archivoItem = $(this).closest('.archivo-item');
            
            // Animación de salida
            $archivoItem.fadeOut(400, function() {
                $(this).remove();
                updateArchivoIndexes();
            });
        }
    });
    
    // Función para actualizar los índices de los archivos después de eliminar uno
    function updateArchivoIndexes() {
        $('#archivos_list .archivo-item').each(function(index) {
            $(this).attr('data-index', index);
            
            // Actualizar nombres de los inputs
            $(this).find('input[name*="[titulo]"]').attr('name', `investigacion_archivos[${index}][titulo]`);
            $(this).find('input[name*="[url]"]').attr('name', `investigacion_archivos[${index}][url]`);
            $(this).find('input[name*="[nombre]"]').attr('name', `investigacion_archivos[${index}][nombre]`);
            $(this).find('input[name*="[size]"]').attr('name', `investigacion_archivos[${index}][size]`);
            $(this).find('textarea[name*="[descripcion]"]').attr('name', `investigacion_archivos[${index}][descripcion]`);
        });
        
        archivoIndex = $('#archivos_list .archivo-item').length;
    }
    
    // Función para obtener clase de icono según extensión
    function getFileIconClass(extension) {
        var iconMap = {
            'pdf': 'dashicons-media-document',
            'doc': 'dashicons-media-text',
            'docx': 'dashicons-media-text',
            'xls': 'dashicons-media-spreadsheet',
            'xlsx': 'dashicons-media-spreadsheet',
            'ppt': 'dashicons-media-interactive',
            'pptx': 'dashicons-media-interactive',
            'zip': 'dashicons-media-archive',
            'rar': 'dashicons-media-archive',
            'txt': 'dashicons-media-text',
            'csv': 'dashicons-media-spreadsheet'
        };
        
        return iconMap[extension] || 'dashicons-media-default';
    }
    
    // Función para formatear el tamaño del archivo
    function formatFileSize(bytes) {
        if (bytes >= 1073741824) {
            return (bytes / 1073741824).toFixed(2) + ' GB';
        } else if (bytes >= 1048576) {
            return (bytes / 1048576).toFixed(2) + ' MB';
        } else if (bytes >= 1024) {
            return (bytes / 1024).toFixed(2) + ' KB';
        } else {
            return bytes + ' bytes';
        }
    }
    
    // Función para escapar HTML
    function escapeHtml(text) {
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Hacer la lista de archivos sorteable (opcional)
    if ($.fn.sortable) {
        $('#archivos_list').sortable({
            items: '.archivo-item',
            placeholder: 'archivo-item-placeholder',
            handle: '.archivo-thumbnail',
            opacity: 0.8,
            cursor: 'move',
            update: function(event, ui) {
                updateArchivoIndexes();
            }
        });
    }
    
    // Validación del formulario
    $('#post').on('submit', function() {
        var hasEmptyTitles = false;
        
        $('.archivo-titulo').each(function() {
            if ($(this).val().trim() === '') {
                hasEmptyTitles = true;
                $(this).addClass('error');
            } else {
                $(this).removeClass('error');
            }
        });
        
        if (hasEmptyTitles) {
            alert('Por favor, completa los títulos de todos los archivos adjuntos.');
            return false;
        }
        
        return true;
    });
    
    // Quitar clase de error al escribir en el campo
    $(document).on('input', '.archivo-titulo', function() {
        $(this).removeClass('error');
    });
    
    // Auto-guardar (draft) cuando se agregan o modifican archivos
    var autoSaveTimeout;
    $(document).on('input', '.archivo-titulo, .archivo-details textarea', function() {
        clearTimeout(autoSaveTimeout);
        autoSaveTimeout = setTimeout(function() {
            if (typeof wp !== 'undefined' && wp.autosave && wp.autosave.server) {
                wp.autosave.server.triggerSave();
            }
        }, 3000);
    });
});