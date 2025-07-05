/**
 * JavaScript para Widget Hub Page
 * Archivo: script.js
 */

(function($) {
    'use strict';

    // Inicialización
    $(document).ready(function() {
        initHubWidget();
    });

    /**
     * Inicializar todas las funcionalidades del widget
     */
    function initHubWidget() {
        initPopups();
        initLazyLoading();
        initAnalytics();
        initAccessibility();
        initResponsive();
    }

    /**
     * Manejar popups "Sobre este contenido"
     */
    function initPopups() {
        // Abrir popup
        $(document).on('click', '.hub-sobre-contenido', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const $popup = $(this).find('.popup-overlay');
            if ($popup.length) {
                $popup.show();
                $('body').addClass('popup-open').css('overflow', 'hidden');
                
                // Focus en el popup para accesibilidad
                $popup.find('.popup-contenido').focus();
            }
        });

        // Cerrar popup
        $(document).on('click', '.popup-cerrar, .popup-overlay', function(e) {
            if (e.target === this) {
                closePopup();
            }
        });

        // Cerrar con Escape
        $(document).on('keydown', function(e) {
            if (e.keyCode === 27) { // ESC
                closePopup();
            }
        });

        // Prevenir que el clic en el contenido del popup lo cierre
        $(document).on('click', '.popup-contenido', function(e) {
            e.stopPropagation();
        });
    }

    /**
     * Cerrar popup
     */
    function closePopup() {
        $('.popup-overlay').hide();
        $('body').removeClass('popup-open').css('overflow', '');
        
        // Devolver focus al trigger
        $('.hub-sobre-contenido').focus();
    }

    /**
     * Lazy loading para imágenes
     */
    function initLazyLoading() {
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                            img.removeAttribute('data-src');
                            img.classList.remove('lazy');
                            observer.unobserve(img);
                        }
                    }
                });
            });

            // Observar imágenes lazy
            $('.hub-page-container img[data-src]').each(function() {
                imageObserver.observe(this);
            });
        }
    }

    /**
     * Analytics tracking
     */
    function initAnalytics() {
        // Track clics en publicaciones
        $('.post-destacado-link, .articulo-link, .post-sticky-link').on('click', function() {
            const title = $(this).find('h3, h4').first().text().trim();
            const type = $(this).closest('.hub-post-destacado').length ? 'destacado' : 
                        $(this).closest('.hub-post-sticky').length ? 'sticky' : 'lista';
            
            // Google Analytics
            if (typeof gtag !== 'undefined') {
                gtag('event', 'hub_article_click', {
                    'article_title': title,
                    'article_type': type,
                    'custom_map': {'metric1': 1}
                });
            }
            
            // Google Tag Manager
            if (typeof dataLayer !== 'undefined') {
                dataLayer.push({
                    'event': 'hub_article_click',
                    'article_title': title,
                    'article_type': type
                });
            }
        });

        // Track apertura de popup
        $('.hub-sobre-contenido').on('click', function() {
            if (typeof gtag !== 'undefined') {
                gtag('event', 'hub_popup_open', {
                    'event_category': 'engagement',
                    'event_label': 'sobre_contenido'
                });
            }
        });

        // Track clic en botón de redirección
        $('.hub-boton-redireccion').on('click', function() {
            const url = $(this).attr('href');
            if (typeof gtag !== 'undefined') {
                gtag('event', 'hub_redirect_click', {
                    'event_category': 'outbound',
                    'event_label': url
                });
            }
        });
    }

    /**
     * Mejoras de accesibilidad
     */
    function initAccessibility() {
        // Navegación por teclado en el popup
        $(document).on('keydown', '.popup-overlay', function(e) {
            if (e.keyCode === 9) { // TAB
                const $popup = $(this).find('.popup-contenido');
                const $focusableElements = $popup.find('a, button, [tabindex]:not([tabindex="-1"])');
                const $firstElement = $focusableElements.first();
                const $lastElement = $focusableElements.last();

                if (e.shiftKey) {
                    if ($(document.activeElement).is($firstElement)) {
                        e.preventDefault();
                        $lastElement.focus();
                    }
                } else {
                    if ($(document.activeElement).is($lastElement)) {
                        e.preventDefault();
                        $firstElement.focus();
                    }
                }
            }
        });

        // ARIA labels dinámicos
        $('.articulo-link').each(function() {
            const title = $(this).find('.articulo-titulo').text().trim();
            $(this).attr('aria-label', `Leer artículo: ${title}`);
        });

        $('.post-destacado-link').each(function() {
            const title = $(this).find('.post-destacado-titulo').text().trim();
            $(this).attr('aria-label', `Leer artículo destacado: ${title}`);
        });

        $('.post-sticky-link').each(function() {
            const title = $(this).find('.post-sticky-titulo').text().trim();
            $(this).attr('aria-label', `Leer artículo destacado: ${title}`);
        });

        // Indicador de carga accesible
        $('.hub-page-container.loading').attr('aria-busy', 'true');
    }

    /**
     * Funcionalidades responsive
     */
    function initResponsive() {
        let resizeTimeout;

        $(window).on('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(function() {
                adjustResponsiveElements();
            }, 250);
        });

        // Ajuste inicial
        adjustResponsiveElements();
    }

    /**
     * Ajustar elementos responsivos
     */
    function adjustResponsiveElements() {
        const windowWidth = $(window).width();

        // Ajustar títulos en móvil
        if (windowWidth <= 480) {
            $('.articulo-titulo, .post-sticky-titulo').each(function() {
                const text = $(this).text();
                if (text.length > 60) {
                    $(this).attr('title', text);
                }
            });
        }

        // Ajustar altura de imágenes según viewport
        if (windowWidth <= 768) {
            $('.hub-imagen-container').css('height', Math.min(250, windowWidth * 0.6));
        }
    }

    /**
     * Cargar más publicaciones (funcionalidad opcional)
     */
    function initLoadMore() {
        $(document).on('click', '.hub-load-more', function(e) {
            e.preventDefault();
            
            const $button = $(this);
            const $container = $button.closest('.hub-page-container');
            const page = parseInt($button.data('page')) || 1;
            const categoria = $button.data('categoria') || '';
            
            $button.text('Cargando...').prop('disabled', true);
            
            $.ajax({
                url: (window.hubWidget && hubWidget.ajaxUrl) || '/wp-admin/admin-ajax.php',
                type: 'POST',
                data: {
                    action: 'hub_load_more_posts',
                    page: page + 1,
                    categoria: categoria,
                    nonce: (window.hubWidget && hubWidget.nonce) || ''
                },
                success: function(response) {
                    if (response.success && response.data.html) {
                        $('.lista-articulos').append(response.data.html);
                        
                        if (response.data.has_more) {
                            $button.data('page', page + 1).text('Cargar más').prop('disabled', false);
                        } else {
                            $button.remove();
                        }
                    } else {
                        $button.text('Error al cargar').prop('disabled', true);
                    }
                },
                error: function() {
                    $button.text('Error al cargar').prop('disabled', true);
                }
            });
        });
    }

    /**
     * Funciones de utilidad
     */
    const utils = {
        /**
         * Truncar texto manteniendo palabras completas
         */
        truncateText: function(text, maxLength) {
            if (text.length <= maxLength) return text;
            
            const truncated = text.substr(0, maxLength);
            const lastSpace = truncated.lastIndexOf(' ');
            
            return lastSpace > 0 ? truncated.substr(0, lastSpace) + '...' : truncated + '...';
        },

        /**
         * Formatear fecha
         */
        formatDate: function(dateString) {
            const date = new Date(dateString);
            const options = { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric',
                locale: 'es-ES'
            };
            return date.toLocaleDateString('es-ES', options);
        },

        /**
         * Verificar si un elemento está en viewport
         */
        isInViewport: function(element) {
            const rect = element.getBoundingClientRect();
            return (
                rect.top >= 0 &&
                rect.left >= 0 &&
                rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
                rect.right <= (window.innerWidth || document.documentElement.clientWidth)
            );
        }
    };

    // Exponer utilidades globalmente si es necesario
    window.hubWidgetUtils = utils;

    /**
     * Manejo de errores de imágenes
     */
    $(document).on('error', '.hub-page-container img', function() {
        const $img = $(this);
        if (!$img.hasClass('error-handled')) {
            $img.addClass('error-handled');
            
            // Imagen placeholder o fallback
            const placeholder = (window.hubWidget && hubWidget.placeholderImage) || 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjQwIiBoZWlnaHQ9IjQwIiBmaWxsPSIjRjVGNUY1Ii8+CjxwYXRoIGQ9Ik0yMCAyN0MyMy4zMTM3IDI3IDI2IDI0LjMxMzcgMjYgMjFDMjYgMTcuNjg2MyAyMy4zMTM3IDE1IDIwIDE1QzE2LjY4NjMgMTUgMTQgMTcuNjg2MyAxNCAyMUMxNCAyNC4zMTM3IDE2LjY4NjMgMjcgMjAgMjdaIiBmaWxsPSIjQ0NDQ0NDIi8+Cjwvc3ZnPgo=';
            
            $img.attr('src', placeholder);
            $img.closest('.hub-imagen-container, .articulo-imagen, .post-destacado-imagen, .post-sticky-imagen')
                .addClass('image-error');
        }
    });

})(jQuery);