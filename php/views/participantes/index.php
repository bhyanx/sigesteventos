<?php
// Incluir archivo de conexión
require_once '../../config/database.php';

// Crear conexión
$database = new Database();
$db = $database->getConnection();

// Consulta para obtener todos los participantes
$query = "SELECT p.id_participante, p.nombre, p.apellido, p.email, p.telefono, p.institucion, 
          COUNT(i.id_inscripcion) as total_inscripciones 
          FROM participantes p 
          LEFT JOIN inscripciones i ON p.id_participante = i.id_participante 
          GROUP BY p.id_participante 
          ORDER BY p.nombre ASC";
$stmt = $db->prepare($query);
$stmt->execute();

// Definir el título de la página
$pageTitle = "Participantes";

// Iniciar el buffer de salida
ob_start();
?>

<!-- Contenido de Participantes -->
<div class="container-fluid p-0">
    <!-- Encabezado y botones de acción -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <h4 class="mb-0">Lista de Participantes</h4>
                </div>
                <div class="d-flex gap-2">
                    <a href="create.php" class="btn btn-primary d-flex align-items-center gap-2">
                        <i class="bi bi-person-plus"></i>
                        <span>Nuevo Participante</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de participantes -->
    <div class="card shadow-sm">
        <!-- Filtros de búsqueda -->
        <div class="card-header bg-white border-bottom">
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-0 bg-light" id="searchInput" placeholder="Buscar participante...">
                    </div>
                </div>
                <div class="col-md-2">
                    <select class="form-select bg-light border-0" id="filterInstitucion">
                        <option value="">Todas las instituciones</option>
                        <?php
                        $queryInstituciones = "SELECT DISTINCT institucion FROM participantes WHERE institucion IS NOT NULL ORDER BY institucion";
                        $stmtInstituciones = $db->prepare($queryInstituciones);
                        $stmtInstituciones->execute();
                        while ($institucion = $stmtInstituciones->fetch(PDO::FETCH_ASSOC)) {
                            echo '<option value="' . htmlspecialchars($institucion['institucion']) . '">' . 
                                 htmlspecialchars($institucion['institucion']) . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select bg-light border-0" id="filterInscripciones">
                        <option value="">Todas las inscripciones</option>
                        <option value="0">Sin inscripciones</option>
                        <option value="1">Con inscripciones</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select bg-light border-0" id="sortBy">
                        <option value="nombre">Ordenar por nombre</option>
                        <option value="inscripciones">Ordenar por inscripciones</option>
                        <option value="institucion">Ordenar por institución</option>
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
                <table class="table mb-0" id="participantesTable">
                                <thead class="table-light">
                                    <tr>
                            <th class="sortable" data-sort="nombre">Nombre <i class="bi bi-arrow-down-up"></i></th>
                                        <th>Email</th>
                                        <th>Teléfono</th>
                            <th class="sortable" data-sort="institucion">Institución <i class="bi bi-arrow-down-up"></i></th>
                            <th class="sortable" data-sort="inscripciones">Inscripciones <i class="bi bi-arrow-down-up"></i></th>
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
                                        $initials = strtoupper(substr($row['nombre'], 0, 1) . substr($row['apellido'], 0, 1));
                                        echo $initials;
                                        ?>
                                    </div>
                                    <div>
                                        <div class="fw-medium"><?php echo htmlspecialchars($row['nombre'] . ' ' . $row['apellido']); ?></div>
                                        <small class="text-muted"><?php echo htmlspecialchars($row['email']); ?></small>
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-envelope text-primary"></i>
                                    <span><?php echo htmlspecialchars($row['email']); ?></span>
                                </div>
                            </td>
                            <td class="align-middle">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-telephone text-success"></i>
                                    <span><?php echo htmlspecialchars($row['telefono']); ?></span>
                                </div>
                            </td>
                            <td class="align-middle">
                                <span class="badge bg-light text-dark">
                                    <?php echo htmlspecialchars($row['institucion']); ?>
                                </span>
                            </td>
                            <td class="align-middle">
                                <span class="badge bg-info-subtle text-info">
                                    <i class="bi bi-calendar-check me-1"></i>
                                                <?php echo $row['total_inscripciones']; ?> eventos
                                            </span>
                                        </td>
                            <td class="text-end align-middle">
                                            <div class="btn-group" role="group">
                                    <a href="view.php?id=<?php echo $row['id_participante']; ?>" 
                                       class="btn btn-primary-subtle btn-sm" 
                                       title="Ver detalles">
                                        <i class="bi bi-eye text-primary"></i>
                                    </a>
                                    <a href="edit.php?id=<?php echo $row['id_participante']; ?>" 
                                       class="btn btn-warning-subtle btn-sm" 
                                       title="Editar">
                                        <i class="bi bi-pencil text-warning"></i>
                                    </a>
                                    <a href="delete.php?id=<?php echo $row['id_participante']; ?>" 
                                       class="btn btn-danger-subtle btn-sm" 
                                       onclick="return confirm('¿Estás seguro de eliminar este participante?')" 
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

.badge-info {
    background-color: #dbeafe;
    color: #1e40af;
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
    const filterInstitucion = document.getElementById('filterInstitucion');
    const filterInscripciones = document.getElementById('filterInscripciones');
    const sortBy = document.getElementById('sortBy');
    const resetFilters = document.getElementById('resetFilters');
    const table = document.getElementById('participantesTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    const sortableHeaders = document.querySelectorAll('.sortable');

    // Función para filtrar la tabla
    function filterTable() {
        const searchText = searchInput.value.toLowerCase();
        const institucionFilter = filterInstitucion.value;
        const inscripcionesFilter = filterInscripciones.value;

        Array.from(rows).forEach(row => {
            const nombre = row.cells[0].textContent.toLowerCase();
            const institucion = row.cells[3].textContent;
            const inscripciones = parseInt(row.cells[4].textContent);

            const matchesSearch = nombre.includes(searchText);
            const matchesInstitucion = !institucionFilter || institucion.includes(institucionFilter);
            const matchesInscripciones = !inscripcionesFilter || 
                (inscripcionesFilter === '0' && inscripciones === 0) ||
                (inscripcionesFilter === '1' && inscripciones > 0);

            row.style.display = matchesSearch && matchesInstitucion && matchesInscripciones ? '' : 'none';
        });
    }

    // Función para ordenar la tabla
    function sortTable(columnIndex, direction) {
        const tbody = table.getElementsByTagName('tbody')[0];
        const rows = Array.from(tbody.getElementsByTagName('tr'));

        rows.sort((a, b) => {
            let aValue = a.cells[columnIndex].textContent;
            let bValue = b.cells[columnIndex].textContent;

            if (columnIndex === 4) { // Columna de inscripciones
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
    filterInstitucion.addEventListener('change', filterTable);
    filterInscripciones.addEventListener('change', filterTable);
    resetFilters.addEventListener('click', () => {
        searchInput.value = '';
        filterInstitucion.value = '';
        filterInscripciones.value = '';
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
