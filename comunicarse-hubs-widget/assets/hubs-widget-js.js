/**
 * ComunicarSe Hubs Widget JavaScript
 */
(function($) {
    'use strict';

    // Inicializar cuando el DOM esté listo
    $(document).ready(function() {
        initHubsWidget();
    });

    /**
     * Inicializar el widget de hubs
     */
    function initHubsWidget() {
        // Agregar tracking de clicks
        trackHubClicks();
        
        // Mejorar accesibilidad
        enhanceAccessibility();
        
        // Agregar animaciones suaves
        addSmoothAnimations();
        
        // Lazy load de imágenes si es necesario
        handleImageLoading();
    }

    /**
     * Tracking de clicks en los hubs
     */
    function trackHubClicks() {
        $('.hub-card').on('click', function(e) {
            const hubTitle = $(this).find('.hub-title').text();
            const hubUrl = $(this).attr('href');
            
            // Si tienes Google Analytics
            if (typeof gtag !== 'undefined') {
                gtag('event', 'hub_click', {
                    'hub_name': hubTitle,
                    'hub_url': hubUrl
                });
            }
            
            // Console log para debug
            console.log('Hub clicked:', hubTitle, hubUrl);
        });
    }

    /**
     * Mejorar accesibilidad
     */
    function enhanceAccessibility() {
        $('.hub-card').each(function() {
            const $card = $(this);
            const title = $card.find('.hub-title').text();
            
            // Agregar aria-label descriptivo
            $card.attr('aria-label', `Ir a la sección de ${title}`);
            
            // Manejar navegación por teclado
            $card.on('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    window.location.href = $(this).attr('href');
                }
            });
            
            // Focus visible
            $card.on('focus', function() {
                $(this).addClass('hub-focused');
            }).on('blur', function() {
                $(this).removeClass('hub-focused');
            });
        });
    }

    /**
     * Animaciones suaves
     */
    function addSmoothAnimations() {
        // Intersection Observer para animaciones al hacer scroll
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        $(entry.target).addClass('animate-in');
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '50px'
            });

            $('.hub-card').each(function(index) {
                const $card = $(this);
                
                // Agregar delay escalonado
                $card.css('animation-delay', (index * 100) + 'ms');
                
                observer.observe(this);
            });
        }
    }

    /**
     * Manejo de carga de imágenes
     */
    function handleImageLoading() {
        $('.hub-image img').each(function() {
            const $img = $(this);
            const $container = $img.parent('.hub-image');
            
            // Agregar loading placeholder
            $container.addClass('loading');
            
            $img.on('load', function() {
                $container.removeClass('loading').addClass('loaded');
            }).on('error', function() {
                // Si la imagen falla, mostrar el icono fallback
                const fallbackIcon = $container.data('fallback-icon') || '📄';
                $img.hide();
                $container.removeClass('loading').addClass('error')
                          .append(`<span class="hub-icon">${fallbackIcon}</span>`);
            });
            
            // Si la imagen ya está cargada
            if (this.complete) {
                $img.trigger('load');
            }
        });
    }

    /**
     * Utilidades adicionales para el admin
     */
    window.ComunicarSeHubs = {
        /**
         * Refrescar vista previa del widget
         */
        refreshPreview: function() {
            const $preview = $('#hubs-preview-container');
            if ($preview.length) {
                $preview.html('<div class="loading">Actualizando vista previa...</div>');
                
                $.post(hubsAjax.ajax_url, {
                    action: 'get_hubs_preview',
                    nonce: hubsAjax.nonce
                }, function(response) {
                    if (response.success) {
                        $preview.html(response.data);
                        initHubsWidget(); // Re-inicializar eventos
                    }
                });
            }
        },

        /**
         * Validar configuración de hub
         */
        validateHub: function(hubData) {
            const errors = [];
            
            if (!hubData.title || hubData.title.trim() === '') {
                errors.push('El título es requerido');
            }
            
            if (!hubData.url || hubData.url.trim() === '') {
                errors.push('La URL es requerida');
            }
            
            if (hubData.url && !isValidUrl(hubData.url)) {
                errors.push('La URL no es válida');
            }
            
            return errors;
        },

        /**
         * Exportar configuración actual
         */
        exportConfiguration: function() {
            $.post(hubsAjax.ajax_url, {
                action: 'get_hubs_config',
                nonce: hubsAjax.nonce
            }, function(response) {
                if (response.success) {
                    const config = {
                        hubs: response.data,
                        exportDate: new Date().toISOString(),
                        version: '1.0'
                    };
                    
                    downloadJson(config, 'comunicarse-hubs-config.json');
                }
            });
        },

        /**
         * Importar configuración
         */
        importConfiguration: function() {
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = '.json';
            
            input.onchange = function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        try {
                            const config = JSON.parse(e.target.result);
                            if (config.hubs && Array.isArray(config.hubs)) {
                                $.post(hubsAjax.ajax_url, {
                                    action: 'save_hubs_config',
                                    nonce: hubsAjax.nonce,
                                    hubs: JSON.stringify(config.hubs)
                                }, function(response) {
                                    if (response.success) {
                                        alert('Configuración importada exitosamente');
                                        location.reload();
                                    }
                                });
                            } else {
                                alert('Archivo de configuración inválido');
                            }
                        } catch (error) {
                            alert('Error al leer el archivo de configuración');
                        }
                    };
                    reader.readAsText(file);
                }
            };
            
            input.click();
        }
    };

    /**
     * Funciones auxiliares
     */
    function isValidUrl(string) {
        try {
            new URL(string, window.location.origin);
            return true;
        } catch (_) {
            return false;
        }
    }

    function downloadJson(data, filename) {
        const dataStr = JSON.stringify(data, null, 2);
        const dataBlob = new Blob([dataStr], { type: 'application/json' });
        
        const link = document.createElement('a');
        link.href = URL.createObjectURL(dataBlob);
        link.download = filename;
        link.click();
    }

    /**
     * Manejar responsive behavior
     */
    function handleResponsive() {
        const $widget = $('.comunicarse-hubs-widget');
        const resizeObserver = new ResizeObserver(function(entries) {
            entries.forEach(function(entry) {
                const width = entry.contentRect.width;
                const $target = $(entry.target);
                
                $target.toggleClass('compact', width < 600);
                $target.toggleClass('wide', width > 1000);
            });
        });

        $widget.each(function() {
            resizeObserver.observe(this);
        });
    }

    // Inicializar responsive behavior si ResizeObserver está disponible
    if ('ResizeObserver' in window) {
        handleResponsive();
    }

    /**
     * Manejar modo oscuro si el tema lo soporta
     */
    function handleDarkMode() {
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            $('.comunicarse-hubs-widget').addClass('dark-mode');
        }

        // Escuchar cambios en el modo oscuro
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
            $('.comunicarse-hubs-widget').toggleClass('dark-mode', e.matches);
        });
    }

    // Inicializar modo oscuro
    handleDarkMode();

})(jQuery);