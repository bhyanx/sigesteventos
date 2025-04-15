<?php
// Incluir archivo de conexión
require_once '../../config/database.php';

// Crear conexión
$database = new Database();
$db = $database->getConnection();

// Obtener estadísticas
try {
    // Total de eventos
    $query = "SELECT COUNT(*) as total FROM eventos";
    $stmt = $db->query($query);
    $totalEventos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Eventos activos (usando fecha_evento para determinar si está activo)
    $query = "SELECT COUNT(*) as total FROM eventos WHERE fecha_evento >= CURDATE()";
    $stmt = $db->query($query);
    $eventosActivos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Total de participantes
    $query = "SELECT COUNT(*) as total FROM participantes";
    $stmt = $db->query($query);
    $totalParticipantes = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Total de inscripciones
    $query = "SELECT COUNT(*) as total FROM inscripciones";
    $stmt = $db->query($query);
    $totalInscripciones = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Próximos eventos
    $query = "SELECT * FROM eventos WHERE fecha_evento >= CURDATE() ORDER BY fecha_evento LIMIT 5";
    $stmt = $db->query($query);
    $proximosEventos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Últimas inscripciones
    $query = "SELECT i.*, p.nombre, p.apellido, e.titulo 
              FROM inscripciones i 
              JOIN participantes p ON i.id_participante = p.id_participante 
              JOIN eventos e ON i.id_evento = e.id_evento 
              ORDER BY i.fecha_inscripcion DESC LIMIT 5";
    $stmt = $db->query($query);
    $ultimasInscripciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Definir el título de la página
$pageTitle = "Dashboard";

// Iniciar el buffer de salida
ob_start();
?>

<!-- Contenido del Dashboard -->
<div class="container-fluid p-0">
    <!-- Fila de tarjetas de estadísticas -->
    <div class="row g-4 mb-4">
        <!-- Total Eventos -->
        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <h6 class="text-muted mb-2">Total Eventos</h6>
                <div class="d-flex align-items-center">
                    <h3 class="mb-0 me-3"><?php echo $totalEventos; ?></h3>
                    <i class="bi bi-calendar-event text-primary fs-4"></i>
                </div>
            </div>
        </div>

        <!-- Eventos Activos -->
        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <h6 class="text-muted mb-2">Eventos Activos</h6>
                <div class="d-flex align-items-center">
                    <h3 class="mb-0 me-3"><?php echo $eventosActivos; ?></h3>
                    <i class="bi bi-check-circle text-success fs-4"></i>
                </div>
            </div>
        </div>

        <!-- Total Participantes -->
        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <h6 class="text-muted mb-2">Total Participantes</h6>
                <div class="d-flex align-items-center">
                    <h3 class="mb-0 me-3"><?php echo $totalParticipantes; ?></h3>
                    <i class="bi bi-people text-info fs-4"></i>
                </div>
            </div>
        </div>

        <!-- Total Inscripciones -->
        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <h6 class="text-muted mb-2">Total Inscripciones</h6>
                <div class="d-flex align-items-center">
                    <h3 class="mb-0 me-3"><?php echo $totalInscripciones; ?></h3>
                    <i class="bi bi-clipboard-check text-warning fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Fila de contenido -->
    <div class="row g-4">
        <!-- Próximos Eventos -->
        <div class="col-xl-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold">Próximos Eventos</h6>
                    <a href="../eventos/index.php" class="btn btn-primary btn-sm">Ver Todos</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Título</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($proximosEventos as $evento): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($evento['titulo']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($evento['fecha_evento'])); ?></td>
                                    <td>
                                        <span class="badge badge-success">
                                            Activo
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Últimas Inscripciones -->
        <div class="col-xl-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold">Últimas Inscripciones</h6>
                    <a href="../inscripciones/index.php" class="btn btn-primary btn-sm">Ver Todas</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Participante</th>
                                    <th>Evento</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ultimasInscripciones as $inscripcion): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($inscripcion['nombre'] . ' ' . $inscripcion['apellido']); ?></td>
                                    <td><?php echo htmlspecialchars($inscripcion['titulo']); ?></td>
                                    <td>
                                        <span class="badge <?php 
                                            echo $inscripcion['estado_pago'] == 'pagado' ? 'badge-success' : 
                                                ($inscripcion['estado_pago'] == 'pendiente' ? 'badge-warning' : 'badge-danger'); 
                                        ?>">
                                            <?php echo ucfirst($inscripcion['estado_pago']); ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Obtener el contenido del buffer y limpiarlo
$content = ob_get_clean();

// Incluir el header (que ahora incluirá el contenido)
include '../layouts/header.php';
?>
