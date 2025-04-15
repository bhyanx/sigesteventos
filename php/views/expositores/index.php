<?php
// Incluir archivo de conexión
require_once '../../config/database.php';

// Crear conexión
$database = new Database();
$db = $database->getConnection();

// Consulta para obtener todos los ponentes
$query = "SELECT p.* FROM ponentes p ORDER BY p.nombre ASC";
$stmt = $db->prepare($query);
$stmt->execute();

// Definir el título de la página
$pageTitle = "Expositores";

// Iniciar el buffer de salida
ob_start();
?>

<!-- Contenido de Expositores -->
<div class="container-fluid p-0">
    <!-- Encabezado y botones de acción -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <div class="icon-circle bg-primary-subtle">
                        <i class="bi bi-person-badge text-primary"></i>
                    </div>
                    <h4 class="mb-0">Lista de Expositores</h4>
                </div>
                <div class="d-flex gap-2">
                    <a href="create.php" class="btn btn-primary d-flex align-items-center gap-2">
                        <i class="bi bi-person-plus"></i>
                        <span>Nuevo Expositor</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de expositores -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Expositor</th>
                            <th>Contacto</th>
                            <th>Especialidad</th>
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
                                        $initials = strtoupper(substr($row['nombre'], 0, 1));
                                        echo $initials;
                                        ?>
                                    </div>
                                    <div>
                                        <div class="fw-medium"><?php echo htmlspecialchars($row['nombre']); ?></div>
                                        <small class="text-muted"><?php echo htmlspecialchars($row['empresa'] ?? ''); ?></small>
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-envelope text-primary"></i>
                                    <span><?php echo htmlspecialchars($row['email']); ?></span>
                                </div>
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    <i class="bi bi-telephone text-success"></i>
                                    <small class="text-muted"><?php echo htmlspecialchars($row['telefono']); ?></small>
                                </div>
                            </td>
                            <td class="align-middle">
                                <span class="badge bg-info-subtle text-info">
                                    <i class="bi bi-award me-1"></i>
                                    <?php echo htmlspecialchars($row['especializacion']); ?>
                                </span>
                            </td>
                            <td class="text-end align-middle">
                                <div class="btn-group" role="group">
                                    <a href="view.php?id=<?php echo $row['id_ponente']; ?>" 
                                       class="btn btn-primary-subtle btn-sm" 
                                       title="Ver detalles">
                                        <i class="bi bi-eye text-primary"></i>
                                    </a>
                                    <a href="edit.php?id=<?php echo $row['id_ponente']; ?>" 
                                       class="btn btn-warning-subtle btn-sm" 
                                       title="Editar">
                                        <i class="bi bi-pencil text-warning"></i>
                                    </a>
                                    <a href="delete.php?id=<?php echo $row['id_ponente']; ?>" 
                                       class="btn btn-danger-subtle btn-sm" 
                                       onclick="return confirm('¿Estás seguro de eliminar este expositor?')" 
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
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: 600;
}

.icon-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
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
</style>

<?php
// Obtener el contenido del buffer y limpiarlo
$content = ob_get_clean();

// Incluir el header (que ahora incluirá el contenido)
include '../layouts/header.php';
?>
