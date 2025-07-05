/**
 * JavaScript para el Widget Listado de Noticias
 * Funcionalidades adicionales como lazy loading, "ver más", etc.
 */

(function($) {
    'use strict';

    /**
     * Clase principal del widget
     */
    class ListadoNoticiasWidget {
        constructor() {
            this.widgets = [];
            this.init();
        }

        /**
         * Inicializar todos los widgets en la página
         */
        init() {
            const self = this;
            
            // Esperar a que el DOM esté listo
            $(document).ready(function() {
                self.findWidgets();
                self.setupEventHandlers();
                self.initLazyLoading();
                self.initAnimations();
            });
        }

        /**
         * Encontrar todos los widgets en la página
         */
        findWidgets() {
            const self = this;
            
            $('.listado-noticias-container').each(function() {
                const $widget = $(this);
                const widgetData = {
                    element: $widget,
                    id: $widget.attr('id'),
                    categoria: $widget.data('categoria'),
                    posts: parseInt($widget.data('posts')) || 6,
                    loaded: $widget.find('.noticia-item').length,
                    loading: false
                };
                
                self.widgets.push(widgetData);
                self.initWidget(widgetData);
            });
        }

        /**
         * Inicializar widget individual
         */
        initWidget(widget) {
            // Agregar clases CSS para identificación
            widget.element.addClass('widget-initialized');
            
            // Numerar items para animaciones secuenciales
            widget.element.find('.noticia-item').each(function(index) {
                $(this).attr('data-index', index + 1);
            });

            // Configurar lazy loading para imágenes
            this.setupImageLazyLoading(widget);
            
            // Configurar hover effects mejorados
            this.setupHoverEffects(widget);
        }

        /**
         * Configurar event handlers
         */
        setupEventHandlers() {
            const self = this;

            // Botón "Ver más"
            $(document).on('click', '.btn-ver-mas', function(e) {
                e.preventDefault();
                const widgetId = $(this).data('widget-id');
                const widget = self.findWidgetById(widgetId);
                
                if (widget) {
                    self.loadMorePosts(widget);
                }
            });

            // Teclado para accesibilidad
            $(document).on('keydown', '.noticia-link', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    window.location.href = $(this).attr('href');
                }
            });

            // Responsive handlers
            $(window).on('resize', function() {
                self.handleResize();
            });

            // Scroll para animaciones
            $(window).on('scroll', function() {
                self.handleScroll();
            });
        }

        /**
         * Encontrar widget por ID
         */
        findWidgetById(id) {
            return this.widgets.find(widget => widget.id === id);
        }

        /**
         * Cargar más posts via AJAX
         */
        loadMorePosts(widget) {
            if (widget.loading) return;

            const $button = widget.element.find('.btn-ver-mas');
            widget.loading = true;
            
            // UI feedback
            $button.addClass('loading').text('Cargando...');

            // AJAX request
            $.ajax({
                url: listado_noticias_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'listado_noticias_load_more',
                    categoria: widget.categoria,
                    posts_per_page: widget.posts,
                    offset: widget.loaded,
                    nonce: listado_noticias_ajax.nonce
                },
                success: function(response) {
                    if (response.trim()) {
                        // Crear elementos temporales
                        const $newItems = $(response).hide();
                        
                        // Agregar al grid
                        widget.element.find('.listado-noticias-grid').append($newItems);
                        
                        // Animar entrada
                        $newItems.each(function(index) {
                            const $item = $(this);
                            setTimeout(function() {
                                $item.fadeIn(300).addClass('noticia-item-ajax');
                            }, index * 100);
                        });
                        
                        // Actualizar contador
                        widget.loaded += $newItems.length;
                        
                        // Configurar nuevas imágenes
                        this.setupImageLazyLoading(widget);
                        
                    } else {
                        // No hay más posts
                        $button.text('No hay más noticias').prop('disabled', true);
                    }
                },
                error: function() {
                    console.error('Error cargando más posts');
                    $button.text('Error al cargar').addClass('error');
                },
                complete: function() {
                    widget.loading = false;
                    setTimeout(function() {
                        if (!$button.prop('disabled')) {
                            $button.removeClass('loading').text('Ver más noticias');
                        }
                    }, 500);
                }
            });
        }

        /**
         * Configurar lazy loading de imágenes
         */
        setupImageLazyLoading(widget) {
            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const $img = $(entry.target);
                            const $container = $img.closest('.noticia-item');
                            
                            $img.on('load', function() {
                                $container.removeClass('loading');
                                $(this).addClass('loaded');
                            });
                            
                            observer.unobserve(entry.target);
                        }
                    });
                });

                widget.element.find('.noticia-thumbnail').each(function() {
                    imageObserver.observe(this);
                });
            }
        }

        /**
         * Inicializar lazy loading general
         */
        initLazyLoading() {
            // Lazy loading para widgets que no están en viewport
            if ('IntersectionObserver' in window) {
                const widgetObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const $widget = $(entry.target);
                            $widget.addClass('in-viewport');
                            this.animateWidgetEntry($widget);
                        }
                    });
                }, {
                    threshold: 0.1,
                    rootMargin: '50px'
                });

                this.widgets.forEach(widget => {
                    widgetObserver.observe(widget.element[0]);
                });
            }
        }

        /**
         * Configurar efectos hover mejorados
         */
        setupHoverEffects(widget) {
            widget.element.find('.noticia-item').each(function() {
                const $item = $(this);
                const $img = $item.find('.noticia-thumbnail');
                
                $item.on('mouseenter', function() {
                    // Efecto parallax sutil en la imagen
                    $img.css('transform', 'scale(1.05) translateZ(0)');
                    
                    // Mostrar overlay de categoría con delay
                    setTimeout(function() {
                        $item.find('.noticia-categoria-overlay').addClass('visible');
                    }, 100);
                });
                
                $item.on('mouseleave', function() {
                    $img.css('transform', 'scale(1) translateZ(0)');
                    $item.find('.noticia-categoria-overlay').removeClass('visible');
                });
            });
        }

        /**
         * Inicializar animaciones
         */
        initAnimations() {
            // Animación de entrada staggered para items
            this.widgets.forEach(widget => {
                widget.element.find('.noticia-item').each(function(index) {
                    $(this).css({
                        'animation-delay': (index * 0.1) + 's',
                        'animation-fill-mode': 'both'
                    }).addClass('animate-fade-in-up');
                });
            });
        }

        /**
         * Animar entrada del widget
         */
        animateWidgetEntry($widget) {
            $widget.find('.noticia-item').each(function(index) {
                const $item = $(this);
                setTimeout(function() {
                    $item.addClass('animate-slide-up');
                }, index * 100);
            });
        }

        /**
         * Manejar cambios de tamaño de ventana
         */
        handleResize() {
            // Recalcular layouts si es necesario
            clearTimeout(this.resizeTimeout);
            this.resizeTimeout = setTimeout(() => {
                this.widgets.forEach(widget => {
                    this.updateWidgetLayout(widget);
                });
            }, 250);
        }

        /**
         * Actualizar layout del widget
         */
        updateWidgetLayout(widget) {
            const $grid = widget.element.find('.listado-noticias-grid');
            const containerWidth = widget.element.width();
            
            // Ajustar número de columnas basado en el ancho
            if (containerWidth < 600) {
                $grid.css('grid-template-columns', '1fr');
            } else if (containerWidth < 900) {
                $grid.css('grid-template-columns', 'repeat(2, 1fr)');
            } else {
                $grid.css('grid-template-columns', 'repeat(auto-fit, minmax(300px, 1fr))');
            }
        }

        /**
         * Manejar scroll para animaciones
         */
        handleScroll() {
            // Throttle scroll events
            if (!this.scrollTimeout) {
                this.scrollTimeout = setTimeout(() => {
                    this.checkScrollAnimations();
                    this.scrollTimeout = null;
                }, 16); // ~60fps
            }
        }

        /**
         * Verificar animaciones en scroll
         */
        checkScrollAnimations() {
            const scrollTop = $(window).scrollTop();
            const windowHeight = $(window).height();
            
            this.widgets.forEach(widget => {
                const elementTop = widget.element.offset().top;
                const elementHeight = widget.element.height();
                
                // Verificar si el widget está en viewport
                if (scrollTop + windowHeight > elementTop && 
                    scrollTop < elementTop + elementHeight) {
                    
                    if (!widget.element.hasClass('scroll-animated')) {
                        widget.element.addClass('scroll-animated');
                        this.triggerScrollAnimation(widget);
                    }
                }
            });
        }

        /**
         * Activar animación en scroll
         */
        triggerScrollAnimation(widget) {
            widget.element.find('.noticia-item').each(function(index) {
                const $item = $(this);
                setTimeout(function() {
                    $item.addClass('scroll-visible');
                }, index * 50);
            });
        }

        /**
         * Utilidades públicas
         */
        
        /**
         * Refrescar widget específico
         */
        refreshWidget(widgetId) {
            const widget = this.findWidgetById(widgetId);
            if (widget) {
                this.initWidget(widget);
            }
        }

        /**
         * Obtener estadísticas del widget
         */
        getWidgetStats(widgetId) {
            const widget = this.findWidgetById(widgetId);
            if (widget) {
                return {
                    id: widget.id,
                    categoria: widget.categoria,
                    totalPosts: widget.posts,
                    loadedPosts: widget.loaded,
                    isLoading: widget.loading
                };
            }
            return null;
        }

        /**
         * Destruir widget (cleanup)
         */
        destroyWidget(widgetId) {
            const index = this.widgets.findIndex(widget => widget.id === widgetId);
            if (index !== -1) {
                const widget = this.widgets[index];
                
                // Limpiar event listeners
                widget.element.off();
                widget.element.find('*').off();
                
                // Remover de array
                this.widgets.splice(index, 1);
            }
        }
    }

    /**
     * Funciones helper globales
     */
    window.ListadoNoticiasUtils = {
        
        /**
         * Truncar texto
         */
        truncateText: function(text, maxLength) {
            if (text.length <= maxLength) return text;
            return text.substr(0, maxLength) + '...';
        },

        /**
         * Formatear fecha
         */
        formatDate: function(date, format = 'DD/MM/YYYY') {
            const d = new Date(date);
            const day = String(d.getDate()).padStart(2, '0');
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const year = d.getFullYear();
            
            return format
                .replace('DD', day)
                .replace('MM', month)
                .replace('YYYY', year);
        },

        /**
         * Debounce function
         */
        debounce: function(func, wait, immediate) {
            let timeout;
            return function executedFunction() {
                const context = this;
                const args = arguments;
                
                const later = function() {
                    timeout = null;
                    if (!immediate) func.apply(context, args);
                };
                
                const callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
                
                if (callNow) func.apply(context, args);
            };
        }
    };

    /**
     * Inicializar cuando el documento esté listo
     */
    const widgetManager = new ListadoNoticiasWidget();
    
    // Hacer disponible globalmente para debugging
    window.ListadoNoticiasWidget = widgetManager;

    /**
     * API pública para interactuar con los widgets
     */
    window.ListadoNoticiasAPI = {
        refresh: (id) => widgetManager.refreshWidget(id),
        stats: (id) => widgetManager.getWidgetStats(id),
        destroy: (id) => widgetManager.destroyWidget(id),
        widgets: () => widgetManager.widgets
    };

})(jQuery);