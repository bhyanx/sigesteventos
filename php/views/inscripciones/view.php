<?php
// Incluir archivo de conexión
require_once '../../config/database.php';

// Crear conexión
$database = new Database();
$db = $database->getConnection();

// Obtener el ID de la inscripción
$id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$id) {
    header('Location: index.php');
    exit;
}

// Consulta para obtener los detalles de la inscripción
$query = "SELECT i.*, e.titulo as evento_titulo, e.fecha_evento, e.hora_inicio, e.hora_fin,
          p.nombre as participante_nombre, p.apellido as participante_apellido, p.email as participante_email
          FROM inscripciones i
          JOIN eventos e ON i.id_evento = e.id_evento
          JOIN participantes p ON i.id_participante = p.id_participante
          WHERE i.id_inscripcion = :id";

$stmt = $db->prepare($query);
$stmt->bindParam(':id', $id);
$stmt->execute();

$inscripcion = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$inscripcion) {
    header('Location: index.php');
    exit;
}

// Definir el título de la página
$pageTitle = "Detalles de Inscripción";

// Iniciar el buffer de salida
ob_start();
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Detalles de Inscripción</h4>
                    <div>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-3">Información del Evento</h5>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <strong>Evento:</strong>
                                    <span class="text-muted"><?php echo htmlspecialchars($inscripcion['evento_titulo']); ?></span>
                                </li>
                                <li class="mb-2">
                                    <strong>Fecha:</strong>
                                    <span class="text-muted"><?php echo date('d/m/Y', strtotime($inscripcion['fecha_evento'])); ?></span>
                                </li>
                                <li class="mb-2">
                                    <strong>Horario:</strong>
                                    <span class="text-muted"><?php echo date('H:i', strtotime($inscripcion['hora_inicio'])); ?> - <?php echo date('H:i', strtotime($inscripcion['hora_fin'])); ?></span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">Información del Participante</h5>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <strong>Nombre:</strong>
                                    <span class="text-muted"><?php echo htmlspecialchars($inscripcion['participante_nombre'] . ' ' . $inscripcion['participante_apellido']); ?></span>
                                </li>
                                <li class="mb-2">
                                    <strong>Email:</strong>
                                    <span class="text-muted"><?php echo htmlspecialchars($inscripcion['participante_email']); ?></span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h5 class="mb-3">Estado de la Inscripción</h5>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <strong>Estado del Pago:</strong>
                                    <span class="badge bg-<?php 
                                        switch($inscripcion['estado_pago']) {
                                            case 'completado':
                                                echo 'success';
                                                break;
                                            case 'pendiente':
                                                echo 'warning';
                                                break;
                                            case 'reembolsado':
                                                echo 'danger';
                                                break;
                                            default:
                                                echo 'secondary';
                                        }
                                    ?>">
                                        <?php echo htmlspecialchars($inscripcion['estado_pago']); ?>
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="edit.php?id=<?php echo $id; ?>" class="btn btn-primary">
                            <i class="bi bi-pencil"></i> Editar Inscripción
                        </a>
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
