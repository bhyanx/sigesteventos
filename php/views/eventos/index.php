<?php
// Incluir archivo de conexión
require_once '../../config/database.php';

// Crear conexión
$database = new Database();
$db = $database->getConnection();

// Consulta para obtener todos los eventos
$query = "SELECT e.*, c.nombre as categoria_nombre, 
          COUNT(i.id_inscripcion) as total_inscripciones 
          FROM eventos e 
          LEFT JOIN categorias_eventos c ON e.id_categoria = c.id_categoria
          LEFT JOIN inscripciones i ON e.id_evento = i.id_evento 
          GROUP BY e.id_evento 
          ORDER BY e.fecha_evento DESC";
$stmt = $db->prepare($query);
$stmt->execute();

// Definir el título de la página
$pageTitle = "Eventos";

// Iniciar el buffer de salida
ob_start();
?>

<!-- Contenido de Eventos -->
<div class="container-fluid p-0">
    <!-- Encabezado y botones de acción -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <h4 class="mb-0">Lista de Eventos</h4>
                </div>
                <div class="d-flex gap-2">
                    <a href="create.php" class="btn btn-primary d-flex align-items-center gap-2">
                        <i class="bi bi-plus-circle"></i>
                        <span>Nuevo Evento</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros de búsqueda -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-0 bg-light" id="searchInput" placeholder="Buscar evento...">
                    </div>
                </div>
                <div class="col-md-2">
                    <select class="form-select bg-light border-0" id="filterCategoria">
                        <option value="">Todas las categorías</option>
                        <?php
                        $queryCategorias = "SELECT * FROM categorias_eventos ORDER BY nombre";
                        $stmtCategorias = $db->prepare($queryCategorias);
                        $stmtCategorias->execute();
                        while ($categoria = $stmtCategorias->fetch(PDO::FETCH_ASSOC)) {
                            echo '<option value="' . htmlspecialchars($categoria['nombre']) . '">' . 
                                 htmlspecialchars($categoria['nombre']) . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select bg-light border-0" id="filterEstado">
                        <option value="">Todos los estados</option>
                        <option value="activo">Activo</option>
                        <option value="cancelado">Cancelado</option>
                        <option value="completado">Completado</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select bg-light border-0" id="sortBy">
                        <option value="fecha">Ordenar por fecha</option>
                        <option value="inscripciones">Ordenar por inscripciones</option>
                        <option value="categoria">Ordenar por categoría</option>
                    </select>
                </div>
                <div class="col-md-3 text-end">
                    <button class="btn btn-light" id="resetFilters">
                        <i class="bi bi-x-circle me-1"></i> Limpiar filtros
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Grid de eventos -->
    <div class="row g-4" id="eventosGrid">
        <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
        <div class="col-md-6 col-lg-4 evento-card">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar-circle me-2 bg-primary-subtle text-primary">
                            <i class="bi bi-calendar-event"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0"><?php echo htmlspecialchars($row['titulo']); ?></h5>
                            <small class="text-muted"><?php echo htmlspecialchars($row['categoria_nombre']); ?></small>
                        </div>
                    </div>
                    
                    <p class="card-text text-muted small mb-3">
                        <?php echo htmlspecialchars($row['descripcion']); ?>
                    </p>

                    <div class="d-flex flex-column gap-2 mb-3">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-calendar text-primary me-2"></i>
                            <span><?php echo date('d/m/Y', strtotime($row['fecha_evento'])); ?></span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-clock text-primary me-2"></i>
                            <span><?php echo date('H:i', strtotime($row['hora_inicio'])); ?> - <?php echo date('H:i', strtotime($row['hora_fin'])); ?></span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-geo-alt text-success me-2"></i>
                            <span><?php echo htmlspecialchars($row['ubicacion']); ?></span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-info-subtle text-info">
                            <i class="bi bi-people me-1"></i>
                            <?php echo $row['total_inscripciones']; ?> inscritos
                        </span>
                        <div class="btn-group">
                            <a href="view.php?id=<?php echo $row['id_evento']; ?>" 
                               class="btn btn-primary-subtle btn-sm" 
                               title="Ver detalles">
                                <i class="bi bi-eye text-primary"></i>
                            </a>
                            <a href="edit.php?id=<?php echo $row['id_evento']; ?>" 
                               class="btn btn-warning-subtle btn-sm" 
                               title="Editar">
                                <i class="bi bi-pencil text-warning"></i>
                            </a>
                            <a href="delete.php?id=<?php echo $row['id_evento']; ?>" 
                               class="btn btn-danger-subtle btn-sm" 
                               onclick="return confirm('¿Estás seguro de eliminar este evento?')" 
                               title="Eliminar">
                                <i class="bi bi-trash text-danger"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<style>
.avatar-circle {
    width: 32px;
    height: 32px;
    background-color: #e2e8f0;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: 600;
    color: #64748b;
}

.btn-primary-subtle {
    background-color: rgba(13, 110, 253, 0.1);
    border: none;
    color: #0d6efd;
}

.btn-primary-subtle:hover {
    background-color: rgba(13, 110, 253, 0.2);
    color: #0d6efd;
}

.btn-warning-subtle {
    background-color: rgba(255, 193, 7, 0.1);
    border: none;
    color: #ffc107;
}

.btn-warning-subtle:hover {
    background-color: rgba(255, 193, 7, 0.2);
    color: #ffc107;
}

.btn-danger-subtle {
    background-color: rgba(220, 53, 69, 0.1);
    border: none;
    color: #dc3545;
}

.btn-danger-subtle:hover {
    background-color: rgba(220, 53, 69, 0.2);
    color: #dc3545;
}

.btn-group .btn {
    margin: 0 2px;
    padding: 0.5rem 0.75rem;
    border-radius: 0.375rem;
}

.btn-group .btn:first-child {
    margin-left: 0;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

.card {
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-5px);
}

.input-group-text, .form-control, .form-select {
    border-radius: 0.5rem;
}

.input-group-text {
    padding-left: 1rem;
    padding-right: 1rem;
}

.form-control:focus, .form-select:focus {
    box-shadow: none;
    background-color: #f8f9fa;
}

.badge {
    padding: 0.5em 0.75em;
    font-weight: 500;
}

.btn-light {
    border: 1px solid rgba(0, 0, 0, 0.1);
}

.btn-light:hover {
    background-color: rgba(0, 0, 0, 0.05);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const filterCategoria = document.getElementById('filterCategoria');
    const filterEstado = document.getElementById('filterEstado');
    const sortBy = document.getElementById('sortBy');
    const resetFilters = document.getElementById('resetFilters');
    const eventosGrid = document.getElementById('eventosGrid');
    const cards = document.querySelectorAll('.evento-card');

    // Función para filtrar las cards
    function filterCards() {
        const searchText = searchInput.value.toLowerCase();
        const categoriaFilter = filterCategoria.value;
        const estadoFilter = filterEstado.value;

        cards.forEach(card => {
            const titulo = card.querySelector('.card-title').textContent.toLowerCase();
            const categoria = card.querySelector('.text-muted').textContent;
            const estado = card.querySelector('.badge').textContent.trim();

            const matchesSearch = titulo.includes(searchText);
            const matchesCategoria = !categoriaFilter || categoria.includes(categoriaFilter);
            const matchesEstado = !estadoFilter || estado.includes(estadoFilter);

            card.style.display = matchesSearch && matchesCategoria && matchesEstado ? '' : 'none';
        });
    }

    // Función para ordenar las cards
    function sortCards() {
        const sortValue = sortBy.value;
        const cardsArray = Array.from(cards);
        
        cardsArray.sort((a, b) => {
            let aValue, bValue;
            
            switch(sortValue) {
                case 'fecha':
                    aValue = new Date(a.querySelector('.bi-calendar').nextSibling.textContent.trim().split('/').reverse().join('-'));
                    bValue = new Date(b.querySelector('.bi-calendar').nextSibling.textContent.trim().split('/').reverse().join('-'));
                    break;
                case 'inscripciones':
                    aValue = parseInt(a.querySelector('.badge').textContent.match(/\d+/)[0]);
                    bValue = parseInt(b.querySelector('.badge').textContent.match(/\d+/)[0]);
                    break;
                case 'categoria':
                    aValue = a.querySelector('.text-muted').textContent;
                    bValue = b.querySelector('.text-muted').textContent;
                    break;
            }
            
            return aValue > bValue ? 1 : -1;
        });
        
        cardsArray.forEach(card => eventosGrid.appendChild(card));
    }

    // Event listeners
    searchInput.addEventListener('input', filterCards);
    filterCategoria.addEventListener('change', filterCards);
    filterEstado.addEventListener('change', filterCards);
    sortBy.addEventListener('change', sortCards);
    
    resetFilters.addEventListener('click', () => {
        searchInput.value = '';
        filterCategoria.value = '';
        filterEstado.value = '';
        sortBy.value = 'fecha';
        filterCards();
        sortCards();
    });
});
</script>

<?php
// Obtener el contenido del buffer y limpiarlo
$content = ob_get_clean();

// Incluir el header (que ahora incluirá el contenido)
include '../layouts/header.php';
?>
