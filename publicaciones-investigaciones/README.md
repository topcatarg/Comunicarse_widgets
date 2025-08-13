# Plugin Publicaciones Investigaciones

Plugin de WordPress para gestionar publicaciones de tipo "Investigaciones" con archivos adjuntos múltiples, creado por Gonzalo Bianchi.

## Características

- **Custom Post Type**: Tipo de publicación personalizado "Investigaciones"
- **Taxonomías personalizadas**: Categorías y Tags específicos para investigaciones
- **Archivos adjuntos múltiples**: Sistema completo de gestión de archivos
- **Interfaz administrativa intuitiva**: Metabox con drag & drop para archivos
- **Funciones helper**: Conjunto completo de funciones para usar en templates
- **Compatibilidad con temas**: Templates personalizables
- **Responsive**: Interfaz administrativa adaptable a dispositivos móviles

## Estructura de archivos

```
publicaciones-investigaciones/
├── publicaciones-investigaciones.php    # Archivo principal del plugin
├── js/
│   └── admin.js                        # JavaScript del administrador
├── css/
│   └── admin.css                       # Estilos del administrador
└── README.md                           # Este archivo
```

## Instalación

1. **Copiar archivos**: Copia la carpeta completa del plugin a `/wp-content/plugins/`

2. **Crear estructura de directorios**:
```bash
wp-content/plugins/publicaciones-investigaciones/
wp-content/plugins/publicaciones-investigaciones/js/
wp-content/plugins/publicaciones-investigaciones/css/
```

3. **Colocar archivos**:
   - `publicaciones-investigaciones.php` en la raíz del plugin
   - `admin.js` en la carpeta `js/`
   - `admin.css` en la carpeta `css/`

4. **Activar el plugin** desde el panel de administración de WordPress

5. **Verificar instalación**: Ve a "Investigaciones" en el menú del admin

## Uso del Plugin

### En el Administrador

1. **Crear nueva investigación**:
   - Ve a "Investigaciones" > "Agregar Nueva"
   - Completa título, contenido, excerpt
   - Asigna categorías y tags
   - Usa el metabox "Archivos Adjuntos" para subir archivos

2. **Gestión de archivos adjuntos**:
   - Haz clic en "Agregar Archivo" para subir archivos
   - Completa título y descripción para cada archivo
   - Los archivos se pueden reordenar arrastrando
   - Elimina archivos con el botón de papelera

3. **Categorías y Tags**:
   - Gestiona desde "Investigaciones" > "Categorías Investigación"
   - Gestiona desde "Investigaciones" > "Tags Investigación"

### En Templates (Frontend)

#### Funciones disponibles:

**Obtener investigaciones:**
```php
// Obtener todas las investigaciones
$investigaciones = get_investigaciones();

// Con parámetros personalizados
$investigaciones = get_investigaciones(array(
    'posts_per_page' => 5,
    'meta_key' => '_featured',
    'meta_value' => 'yes'
));

// Por categoría
$investigaciones = get_investigaciones_by_category('mi-categoria');

// Por tag
$investigaciones = get_investigaciones_by_tag('mi-tag');
```

**Mostrar archivos adjuntos:**
```php
// En single-investigaciones.php o en el loop
the_investigacion_archivos_adjuntos();

// Con clase personalizada
the_investigacion_archivos_adjuntos(get_the_ID(), 'mis-archivos-custom');

// Solo obtener los archivos (array)
$archivos = get_investigacion_archivos_adjuntos();
```

**Taxonomías:**
```php
// Mostrar categorías de la investigación actual
the_investigacion_categories();

// Mostrar tags
the_investigacion_tags(' | '); // Con separador personalizado

// Obtener categorías como objetos
$categorias = get_investigacion_post_categories();
```

**URLs y navegación:**
```php
// URL del archivo de investigaciones
echo get_investigaciones_archive_url();

// URL de categoría específica
echo get_investigacion_category_archive_url('mi-categoria');

// Breadcrumb
the_investigacion_breadcrumb();
```

**Investigaciones relacionadas:**
```php
$relacionadas = get_related_investigaciones(get_the_ID(), 3);
foreach($relacionadas as $post) {
    setup_postdata($post);
    // mostrar investigación relacionada
}
wp_reset_postdata();
```

## Templates Personalizados

El plugin busca templates en este orden:

### Para investigaciones individuales:
1. `single-investigaciones.php`
2. `single.php`
3. `index.php`

### Para archivo de investigaciones:
1. `archive-investigaciones.php`
2. `archive.php`
3. `index.php`

### Para categorías:
1. `archive-investigaciones-category-{slug}.php`
2. `archive-investigaciones-category.php`
3. `taxonomy-category_investigacion-{slug}.php`
4. `taxonomy-category_investigacion.php`
5. `archive-investigaciones.php`
6. `taxonomy.php`
7. `archive.php`
8. `index.php`

### Para tags:
1. `archive-investigaciones-tag-{slug}.php`
2. `archive-investigaciones-tag.php`
3. `taxonomy-tag_investigacion-{slug}.php`
4. `taxonomy-tag_investigacion.php`
5. `archive-investigaciones.php`
6. `taxonomy.php`
7. `archive.php`
8. `index.php`

## Ejemplo de Template

### single-investigaciones.php
```php
<?php get_header(); ?>

<div class="container">
    <?php while (have_posts()) : the_post(); ?>
        <article class="investigacion-single">
            <!-- Breadcrumb -->
            <?php the_investigacion_breadcrumb(); ?>
            
            <!-- Título -->
            <h1><?php the_title(); ?></h1>
            
            <!-- Metadatos -->
            <div class="investigacion-meta">
                <span>Publicado: <?php echo get_the_date(); ?></span>
                <span>Autor: <?php the_author(); ?></span>
            </div>
            
            <!-- Categorías y Tags -->
            <div class="investigacion-taxonomies">
                <?php if (get_investigacion_post_categories()) : ?>
                    <div class="categorias">
                        <strong>Categorías:</strong> <?php the_investigacion_categories(); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (get_investigacion_post_tags()) : ?>
                    <div class="tags">
                        <strong>Tags:</strong> <?php the_investigacion_tags(); ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Imagen destacada -->
            <?php if (has_post_thumbnail()) : ?>
                <div class="investigacion-thumbnail">
                    <?php the_post_thumbnail('large'); ?>
                </div>
            <?php endif; ?>
            
            <!-- Contenido -->
            <div class="investigacion-content">
                <?php the_content(); ?>
            </div>
            
            <!-- Archivos adjuntos -->
            <?php the_investigacion_archivos_adjuntos(); ?>
            
            <!-- Investigaciones relacionadas -->
            <?php
            $relacionadas = get_related_investigaciones(get_the_ID(), 3);
            if (!empty($relacionadas)) :
            ?>
                <div class="investigaciones-relacionadas">
                    <h3>Investigaciones Relacionadas</h3>
                    <div class="relacionadas-grid">
                        <?php foreach($relacionadas as $post) : setup_postdata($post); ?>
                            <div class="relacionada-item">
                                <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                                <?php if (has_excerpt()) : ?>
                                    <p><?php the_excerpt(); ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; wp_reset_postdata(); ?>
                    </div>
                </div>
            <?php endif; ?>
        </article>
    <?php endwhile; ?>
</div>

<?php get_footer(); ?>
```

## Personalización de Estilos

### CSS básico para archivos adjuntos:
```css
.investigacion-archivos {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin: 20px 0;
}

.investigacion-archivos h4 {
    margin-bottom: 15px;
    color: #333;
}

.archivos-list {
    list-style: none;
    padding: 0;
}

.archivo-item {
    display: flex;
    align-items: center;
    padding: 10px;
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    margin-bottom: 8px;
}

.archivo-icon {
    margin-right: 12px;
    color: #0073aa;
}

.archivo-info {
    flex: 1;
}

.archivo-titulo {
    font-weight: 600;
    margin-bottom: 4px;
}

.archivo-link {
    text-decoration: none;
    color: #0073aa;
}

.archivo-link:hover {
    text-decoration: underline;
}

.archivo-size {
    color: #666;
    font-size: 0.9em;
}

.archivo-descripcion {
    margin: 8px 0 0 0;
    color: #555;
    font-size: 0.95em;
}
```

## API y Hooks

### Hooks disponibles:
- `investigaciones_before_save_archivos` - Antes de guardar archivos
- `investigaciones_after_save_archivos` - Después de guardar archivos
- `investigaciones_file_uploaded` - Cuando se sube un archivo
- `investigaciones_file_deleted` - Cuando se elimina un archivo

### Filtros disponibles:
- `investigaciones_allowed_file_types` - Tipos de archivo permitidos
- `investigaciones_max_files` - Número máximo de archivos
- `investigaciones_max_file_size` - Tamaño máximo por archivo

## Migración desde Drupal

Para migrar contenido desde Drupal (comunicarseweb.com):

1. **Mapeo de campos**:
   - Título Drupal → `post_title`
   - Contenido Drupal → `post_content`
   - Archivos adjuntos → `_investigacion_archivos_adjuntos`

2. **Mapeo de taxonomías**:
   - Categorías Drupal → `category_investigacion`
   - Tags Drupal → `tag_investigacion`

3. **Script de migración** (ejemplo en C#):
```csharp
// Usar Dapper para consultas
// Los modelos van en carpeta separada
// Acceso directo a ambas bases de datos

// Modelo para Investigacion
public class Investigacion
{
    public int Id { get; set; }
    public string Titulo { get; set; }
    public string Contenido { get; set; }
    public List<ArchivoAdjunto> Archivos { get; set; }
    public List<Categoria> Categorias { get; set; }
    public List<Tag> Tags { get; set; }
}

public class ArchivoAdjunto
{
    public string Titulo { get; set; }
    public string Url { get; set; }
    public string Nombre { get; set; }
    public long Size { get; set; }
    public string Descripcion { get; set; }
}
```

## Soporte

Para soporte y consultas, contactar a Gonzalo Bianchi.

## Changelog

### v1.0
- Lanzamiento inicial
- Custom post type para investigaciones
- Taxonomías personalizadas
- Sistema de archivos adjuntos múltiples
- Interfaz administrativa completa
- Funciones helper para templates