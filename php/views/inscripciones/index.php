<?php
// Incluir archivo de conexión
require_once '../../config/database.php';

// Crear conexión
$database = new Database();
$db = $database->getConnection();

// Consulta para obtener todas las inscripciones
$query = "SELECT i.*,
          p.nombre as participante_nombre,
          p.apellido as participante_apellido,
          p.email as participante_email,
          e.titulo as evento_titulo,
          e.fecha_evento,
          e.hora_inicio,
          e.hora_fin
          FROM inscripciones i
          JOIN participantes p ON i.id_participante = p.id_participante
          LEFT JOIN eventos e ON i.id_evento = e.id_evento
          ORDER BY i.fecha_inscripcion DESC";
$stmt = $db->prepare($query);
$stmt->execute();

// Definir el título de la página
$pageTitle = "Inscripciones";

// Iniciar el buffer de salida
ob_start();
?>

<!-- Contenido de Inscripciones -->
<div class="container-fluid p-0">
    <!-- Encabezado y botones de acción -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <h4 class="mb-0">Lista de Inscripciones</h4>
                </div>
                <div class="d-flex gap-2">
                    <a href="create.php" class="btn btn-primary d-flex align-items-center gap-2">
                        <i class="bi bi-plus-circle"></i>
                        <span>Nueva Inscripción</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de inscripciones -->
    <div class="card shadow-sm">
        <!-- Filtros de búsqueda -->
        <div class="card-header bg-white border-bottom">
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-0 bg-light" id="searchInput" placeholder="Buscar inscripción...">
                    </div>
                </div>
                <div class="col-md-2">
                    <select class="form-select bg-light border-0" id="filterEvento">
                        <option value="">Todos los eventos</option>
                        <?php
                        $queryEventos = "SELECT DISTINCT titulo FROM eventos ORDER BY titulo";
                        $stmtEventos = $db->prepare($queryEventos);
                        $stmtEventos->execute();
                        while ($evento = $stmtEventos->fetch(PDO::FETCH_ASSOC)) {
                            echo '<option value="' . htmlspecialchars($evento['titulo']) . '">' . 
                                 htmlspecialchars($evento['titulo']) . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select bg-light border-0" id="filterEstado">
                        <option value="">Todos los estados</option>
                        <option value="confirmado">Confirmados</option>
                        <option value="pendiente">Pendientes</option>
                        <option value="cancelado">Cancelados</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select bg-light border-0" id="sortBy">
                        <option value="fecha">Ordenar por fecha</option>
                        <option value="participante">Ordenar por participante</option>
                        <option value="evento">Ordenar por evento</option>
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
                <table class="table mb-0" id="inscripcionesTable">
                    <thead class="table-light">
                        <tr>
                            <th class="sortable" data-sort="participante">Participante <i class="bi bi-arrow-down-up"></i></th>
                            <th class="sortable" data-sort="evento">Evento <i class="bi bi-arrow-down-up"></i></th>
                            <th class="sortable" data-sort="fecha">Fecha Inscripción <i class="bi bi-arrow-down-up"></i></th>
                            <th class="sortable" data-sort="estado">Estado <i class="bi bi-arrow-down-up"></i></th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td class="align-middle">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-2 bg-primary-subtle text-primary">
                                        <?php 
                                        $initials = strtoupper(substr($row['participante_nombre'], 0, 1) . substr($row['participante_apellido'], 0, 1));
                                        echo $initials;
                                        ?>
                                    </div>
                                    <div>
                                        <div class="fw-medium"><?php echo htmlspecialchars($row['participante_nombre'] . ' ' . $row['participante_apellido']); ?></div>
                                        <small class="text-muted"><?php echo htmlspecialchars($row['participante_email']); ?></small>
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-2 bg-info-subtle text-info">
                                        <i class="bi bi-calendar-event"></i>
                                    </div>
                                    <div>
                                        <div class="fw-medium"><?php echo htmlspecialchars($row['evento_titulo']); ?></div>
                                        <small class="text-muted">
                                            <?php echo date('d/m/Y', strtotime($row['fecha_evento'])); ?> 
                                            <?php echo date('H:i', strtotime($row['hora_inicio'])); ?> - 
                                            <?php echo date('H:i', strtotime($row['hora_fin'])); ?>
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle">
                                <div class="badge bg-light text-dark">
                                    <?php echo date('d/m/Y H:i', strtotime($row['fecha_inscripcion'])); ?>
                                </div>
                            </td>
                            <td class="align-middle">
                                <?php
                                $estadoClass = '';
                                switch($row['estado_pago']) {
                                    case 'confirmado':
                                        $estadoClass = 'success';
                                        break;
                                    case 'pendiente':
                                        $estadoClass = 'warning';
                                        break;
                                    case 'cancelado':
                                        $estadoClass = 'danger';
                                        break;
                                    default:
                                        $estadoClass = 'secondary';
                                }
                                ?>
                                <span class="badge bg-<?php echo $estadoClass; ?>-subtle text-<?php echo $estadoClass; ?>">
                                    <i class="bi bi-<?php echo $estadoClass === 'confirmado' ? 'check-circle' : 
                                                    ($estadoClass === 'pendiente' ? 'clock' : 'x-circle'); ?> me-1"></i>
                                    <?php echo ucfirst(htmlspecialchars($row['estado_pago'])); ?>
                                </span>
                            </td>
                            <td class="text-end align-middle">
                                <div class="btn-group" role="group">
                                    <a href="view.php?id=<?php echo $row['id_inscripcion']; ?>" 
                                       class="btn btn-primary-subtle btn-sm" 
                                       title="Ver detalles">
                                        <i class="bi bi-eye text-primary"></i>
                                    </a>
                                    <a href="edit.php?id=<?php echo $row['id_inscripcion']; ?>" 
                                       class="btn btn-warning-subtle btn-sm" 
                                       title="Editar">
                                        <i class="bi bi-pencil text-warning"></i>
                                    </a>
                                    <a href="delete.php?id=<?php echo $row['id_inscripcion']; ?>" 
                                       class="btn btn-danger-subtle btn-sm" 
                                       onclick="return confirm('¿Estás seguro de eliminar esta inscripción?')" 
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
    const filterEvento = document.getElementById('filterEvento');
    const filterEstado = document.getElementById('filterEstado');
    const sortBy = document.getElementById('sortBy');
    const resetFilters = document.getElementById('resetFilters');
    const table = document.getElementById('inscripcionesTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    const sortableHeaders = document.querySelectorAll('.sortable');

    // Función para filtrar la tabla
    function filterTable() {
        const searchText = searchInput.value.toLowerCase();
        const eventoFilter = filterEvento.value;
        const estadoFilter = filterEstado.value;

        Array.from(rows).forEach(row => {
            const participante = row.cells[0].textContent.toLowerCase();
            const evento = row.cells[1].textContent;
            const estado = row.cells[3].textContent.toLowerCase();

            const matchesSearch = participante.includes(searchText);
            const matchesEvento = !eventoFilter || evento.includes(eventoFilter);
            const matchesEstado = !estadoFilter || estado.includes(estadoFilter.toLowerCase());

            row.style.display = matchesSearch && matchesEvento && matchesEstado ? '' : 'none';
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
            }

            return direction === 'asc' ? 
                aValue > bValue ? 1 : -1 : 
                aValue < bValue ? 1 : -1;
        });

        rows.forEach(row => tbody.appendChild(row));
    }

    // Event listeners
    searchInput.addEventListener('input', filterTable);
    filterEvento.addEventListener('change', filterTable);
    filterEstado.addEventListener('change', filterTable);
    resetFilters.addEventListener('click', () => {
        searchInput.value = '';
        filterEvento.value = '';
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
