<?php
// Incluir archivo de conexión
require_once '../../config/database.php';

// Crear conexión
$database = new Database();
$db = $database->getConnection();

// Consulta para obtener todos los eventos
$query = "SELECT e.id_evento, e.titulo, e.descripcion, e.ubicacion, e.fecha_evento, e.hora_inicio, e.hora_fin, 
                 e.max_participantes, e.tarifa_inscripcion, e.imagen, c.nombre as categoria_nombre 
          FROM eventos e 
          LEFT JOIN categorias_eventos c ON e.id_categoria = c.id_categoria 
          ORDER BY e.fecha_evento DESC, e.hora_inicio ASC";
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
                    <a href="calendario.php" class="btn btn-light d-flex align-items-center gap-2">
                        <i class="bi bi-calendar3"></i>
                        <span>Ver Calendario</span>
                    </a>
                    <a href="create.php" class="btn btn-primary d-flex align-items-center gap-2">
                        <i class="bi bi-plus-circle"></i>
                        <span>Nuevo Evento</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de eventos -->
    <div class="card shadow-sm">
        <!-- Filtros de búsqueda -->
        <div class="card-header bg-white border-bottom">
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
                        $queryCategorias = "SELECT DISTINCT nombre FROM categorias_eventos ORDER BY nombre";
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
                        <option value="activo">Activos</option>
                        <option value="finalizado">Finalizados</option>
                        <option value="cancelado">Cancelados</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select bg-light border-0" id="sortBy">
                        <option value="fecha">Ordenar por fecha</option>
                        <option value="titulo">Ordenar por título</option>
                        <option value="participantes">Ordenar por participantes</option>
                    </select>
                </div>
                <div class="col-md-3 text-end">
                    <button class="btn btn-light" id="resetFilters">
                        <i class="bi bi-x-circle me-1"></i> Limpiar filtros
                    </button>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0" id="eventosTable">
                    <thead class="table-light">
                        <tr>
                            <th class="sortable" data-sort="titulo">Título <i class="bi bi-arrow-down-up"></i></th>
                            <th class="sortable" data-sort="categoria">Categoría <i class="bi bi-arrow-down-up"></i></th>
                            <th class="sortable" data-sort="fecha">Fecha <i class="bi bi-arrow-down-up"></i></th>
                            <th>Horario</th>
                            <th>Ubicación</th>
                            <th class="sortable" data-sort="participantes">Participantes <i class="bi bi-arrow-down-up"></i></th>
                            <th>Tarifa</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td class="align-middle">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-2 bg-primary-subtle text-primary">
                                        <i class="bi bi-calendar-event"></i>
                                    </div>
                                    <div>
                                        <div class="fw-medium"><?php echo htmlspecialchars($row['titulo']); ?></div>
                                        <small class="text-muted"><?php echo htmlspecialchars($row['descripcion']); ?></small>
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle">
                                <span class="badge bg-info-subtle text-info">
                                    <i class="bi bi-tag me-1"></i>
                                    <?php echo htmlspecialchars($row['categoria_nombre'] ?? 'Sin categoría'); ?>
                                </span>
                            </td>
                            <td class="align-middle">
                                <div class="badge bg-light text-dark">
                                    <?php echo date('d/m/Y', strtotime($row['fecha_evento'])); ?>
                                </div>
                            </td>
                            <td class="align-middle">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-clock text-primary"></i>
                                    <span><?php echo date('H:i', strtotime($row['hora_inicio'])) . ' - ' . date('H:i', strtotime($row['hora_fin'])); ?></span>
                                </div>
                            </td>
                            <td class="align-middle">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-geo-alt text-success"></i>
                                    <span><?php echo htmlspecialchars($row['ubicacion']); ?></span>
                                </div>
                            </td>
                            <td class="align-middle">
                                <span class="badge bg-primary-subtle text-primary">
                                    <i class="bi bi-people me-1"></i>
                                    <?php echo $row['max_participantes'] > 0 ? $row['max_participantes'] . ' participantes' : 'Sin límite'; ?>
                                </span>
                            </td>
                            <td class="align-middle">
                                <span class="badge bg-success-subtle text-success">
                                    <i class="bi bi-currency-dollar me-1"></i>
                                    <?php echo $row['tarifa_inscripcion'] > 0 ? '$' . number_format($row['tarifa_inscripcion'], 2) : 'Gratis'; ?>
                                </span>
                            </td>
                            <td class="text-end align-middle">
                                <div class="btn-group" role="group">
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
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
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

.card-header {
    padding: 1rem;
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

.sortable {
    cursor: pointer;
    user-select: none;
}

.sortable:hover {
    background-color: rgba(0, 0, 0, 0.02);
}

.sortable.active {
    background-color: rgba(0, 0, 0, 0.05);
}

.sortable.active i {
    color: #0d6efd;
}

.table > :not(caption) > * > * {
    padding: 1rem;
}

.table tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.02);
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
    const table = document.getElementById('eventosTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    const sortableHeaders = document.querySelectorAll('.sortable');

    // Función para filtrar la tabla
    function filterTable() {
        const searchText = searchInput.value.toLowerCase();
        const categoriaFilter = filterCategoria.value;
        const estadoFilter = filterEstado.value;

        Array.from(rows).forEach(row => {
            const titulo = row.cells[0].textContent.toLowerCase();
            const categoria = row.cells[1].textContent;
            const fecha = new Date(row.cells[2].textContent.split('/').reverse().join('-'));
            const hoy = new Date();

            const matchesSearch = titulo.includes(searchText);
            const matchesCategoria = !categoriaFilter || categoria.includes(categoriaFilter);
            const matchesEstado = !estadoFilter || 
                (estadoFilter === 'activo' && fecha >= hoy) ||
                (estadoFilter === 'finalizado' && fecha < hoy) ||
                (estadoFilter === 'cancelado' && row.cells[0].textContent.includes('(Cancelado)'));

            row.style.display = matchesSearch && matchesCategoria && matchesEstado ? '' : 'none';
        });
    }

    // Función para ordenar la tabla
    function sortTable(columnIndex, direction) {
        const tbody = table.getElementsByTagName('tbody')[0];
        const rows = Array.from(tbody.getElementsByTagName('tr'));

        rows.sort((a, b) => {
            let aValue = a.cells[columnIndex].textContent;
            let bValue = b.cells[columnIndex].textContent;

            if (columnIndex === 2) { // Columna de fecha
                aValue = new Date(aValue.split('/').reverse().join('-'));
                bValue = new Date(bValue.split('/').reverse().join('-'));
            } else if (columnIndex === 5) { // Columna de participantes
                aValue = parseInt(aValue);
                bValue = parseInt(bValue);
            }

            return direction === 'asc' ? 
                aValue > bValue ? 1 : -1 : 
                aValue < bValue ? 1 : -1;
        });

        rows.forEach(row => tbody.appendChild(row));
    }

    // Event listeners
    searchInput.addEventListener('input', filterTable);
    filterCategoria.addEventListener('change', filterTable);
    filterEstado.addEventListener('change', filterTable);
    resetFilters.addEventListener('click', () => {
        searchInput.value = '';
        filterCategoria.value = '';
        filterEstado.value = '';
        filterTable();
    });

    // Ordenamiento
    sortableHeaders.forEach(header => {
        header.addEventListener('click', () => {
            const columnIndex = Array.from(header.parentNode.children).indexOf(header);
            const isAsc = header.classList.contains('asc');
            
            sortableHeaders.forEach(h => {
                h.classList.remove('active', 'asc', 'desc');
            });
            
            header.classList.add('active', isAsc ? 'desc' : 'asc');
            sortTable(columnIndex, isAsc ? 'desc' : 'asc');
        });
    });
});
</script>

<?php
// Obtener el contenido del buffer y limpiarlo
$content = ob_get_clean();

// Incluir el header (que ahora incluirá el contenido)
include '../layouts/header.php';
?>
