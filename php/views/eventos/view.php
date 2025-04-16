<?php
// Incluir archivo de conexión
require_once '../../config/database.php';

// Crear conexión
$database = new Database();
$db = $database->getConnection();

// Obtener ID del evento
$id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: ID no encontrado.');

// Obtener el evento con información de la categoría
$query = "SELECT e.*, c.nombre as nombre_categoria 
          FROM eventos e 
          LEFT JOIN categorias_eventos c ON e.id_categoria = c.id_categoria 
          WHERE e.id_evento = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $id);
$stmt->execute();
$evento = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$evento) {
    header('Location: index.php');
    exit;
}

// Obtener inscripciones del evento
$queryInscripciones = "SELECT i.*, p.nombre, p.apellido, p.email 
                      FROM inscripciones i 
                      JOIN participantes p ON i.id_participante = p.id_participante 
                      WHERE i.id_evento = :id 
                      ORDER BY i.fecha_inscripcion DESC";
$stmtInscripciones = $db->prepare($queryInscripciones);
$stmtInscripciones->bindParam(':id', $id);
$stmtInscripciones->execute();
$inscripciones = $stmtInscripciones->fetchAll(PDO::FETCH_ASSOC);

// Calcular estadísticas
$totalInscritos = count($inscripciones);
$porcentajeOcupacion = $evento['max_participantes'] > 0 
    ? round(($totalInscritos / $evento['max_participantes']) * 100, 2) 
    : 0;

// Definir el título de la página
$pageTitle = "Detalles del Evento";

// Iniciar el buffer de salida
ob_start();
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Detalles del Evento</h4>
                    <div>
                        <a href="edit.php?id=<?php echo $id; ?>" class="btn btn-primary me-2">
                            <i class="bi bi-pencil"></i> Editar
                        </a>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="card-title"><?php echo htmlspecialchars($evento['titulo']); ?></h5>
                            <p class="text-muted"><?php echo htmlspecialchars($evento['nombre_categoria']); ?></p>
                        </div>
                        
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h6>Descripción</h6>
                            <p><?php echo nl2br(htmlspecialchars($evento['descripcion'])); ?></p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Información del Evento</h6>
                            <ul class="list-unstyled">
                                <li><strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($evento['fecha_evento'])); ?></li>
                                <li><strong>Horario:</strong> <?php echo date('H:i', strtotime($evento['hora_inicio'])); ?> - <?php echo date('H:i', strtotime($evento['hora_fin'])); ?></li>
                                <li><strong>Ubicación:</strong> <?php echo htmlspecialchars($evento['ubicacion']); ?></li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Información de Inscripción</h6>
                            <ul class="list-unstyled">
                                <li><strong>Máximo de Participantes:</strong> <?php echo $evento['max_participantes']; ?></li>
                                <li><strong>Inscritos:</strong> <?php echo $totalInscritos; ?></li>
                                <li><strong>Ocupación:</strong> <?php echo $porcentajeOcupacion; ?>%</li>
                                <li><strong>Tarifa:</strong> $<?php echo number_format($evento['tarifa_inscripcion'], 2); ?></li>
                            </ul>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <h6>Participantes Inscritos</h6>
                            <?php if (count($inscripciones) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Nombre</th>
                                                <th>Email</th>
                                                <th>Fecha de Inscripción</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($inscripciones as $inscripcion): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($inscripcion['nombre'] . ' ' . $inscripcion['apellido']); ?></td>
                                                    <td><?php echo htmlspecialchars($inscripcion['email']); ?></td>
                                                    <td><?php echo date('d/m/Y H:i', strtotime($inscripcion['fecha_inscripcion'])); ?></td>
                                                    
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info">
                                    No hay participantes inscritos en este evento.
                                </div>
                            <?php endif; ?>
                        </div>
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
