<?php
// Incluir archivo de conexión
require_once '../../config/database.php';

// Crear conexión
$database = new Database();
$db = $database->getConnection();

// Obtener ID del participante
$id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: ID no encontrado.');

// Consulta para obtener los datos del participante
$query = "SELECT p.*, 
          COUNT(i.id_inscripcion) as total_inscripciones,
          GROUP_CONCAT(e.titulo SEPARATOR ', ') as eventos_inscritos
          FROM participantes p 
          LEFT JOIN inscripciones i ON p.id_participante = i.id_participante
          LEFT JOIN eventos e ON i.id_evento = e.id_evento
          WHERE p.id_participante = :id
          GROUP BY p.id_participante";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $id);
$stmt->execute();

// Obtener los datos del participante
$participante = $stmt->fetch(PDO::FETCH_ASSOC);

// Verificar si se encontró el participante
if(!$participante) {
    header('Location: index.php');
    exit;
}

// Consulta para obtener el historial de inscripciones
$queryInscripciones = "SELECT i.*, e.titulo as evento_titulo, e.fecha_evento, e.hora_inicio, e.hora_fin
                      FROM inscripciones i
                      JOIN eventos e ON i.id_evento = e.id_evento
                      WHERE i.id_participante = :id
                      ORDER BY i.fecha_inscripcion DESC";
$stmtInscripciones = $db->prepare($queryInscripciones);
$stmtInscripciones->bindParam(':id', $id);
$stmtInscripciones->execute();
$inscripciones = $stmtInscripciones->fetchAll(PDO::FETCH_ASSOC);

// Definir el título de la página
$pageTitle = "Detalles del Participante";

// Iniciar el buffer de salida
ob_start();
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Detalles del Participante</h4>
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
                            <h5 class="card-title"><?php echo htmlspecialchars($participante['nombre'] . ' ' . $participante['apellido']); ?></h5>
                            <p class="text-muted"><?php echo htmlspecialchars($participante['email']); ?></p>
                        </div>
                        <div class="col-md-6 text-end">
                            <span class="badge bg-info">
                                <?php echo $participante['total_inscripciones']; ?> eventos inscritos
                            </span>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Información Personal</h6>
                            <ul class="list-unstyled">
                                <li><strong>Teléfono:</strong> <?php echo htmlspecialchars($participante['telefono']); ?></li>
                                <li><strong>Institución:</strong> <?php echo htmlspecialchars($participante['institucion']); ?></li>
                                <li><strong>Fecha de Registro:</strong> <?php echo date('d/m/Y H:i', strtotime($participante['fecha_creacion'])); ?></li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Estadísticas</h6>
                            <ul class="list-unstyled">
                                <li><strong>Total de Inscripciones:</strong> <?php echo $participante['total_inscripciones']; ?></li>
                                <li><strong>Eventos Inscritos:</strong> <?php echo htmlspecialchars($participante['eventos_inscritos'] ?? 'Ninguno'); ?></li>
                            </ul>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <h6>Historial de Inscripciones</h6>
                            <?php if (count($inscripciones) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Evento</th>
                                                <th>Fecha</th>
                                                <th>Horario</th>
                                                <th>Estado</th>
                                                <th>Fecha Inscripción</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($inscripciones as $inscripcion): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($inscripcion['evento_titulo']); ?></td>
                                                    <td><?php echo date('d/m/Y', strtotime($inscripcion['fecha_evento'])); ?></td>
                                                    <td><?php echo date('H:i', strtotime($inscripcion['hora_inicio'])) . ' - ' . date('H:i', strtotime($inscripcion['hora_fin'])); ?></td>
                                                    <td>
                                                        <?php
                                                        $estadoClass = '';
                                                        switch($inscripcion['estado_pago']) {
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
                                                        <span class="badge bg-<?php echo $estadoClass; ?>">
                                                            <?php echo ucfirst(htmlspecialchars($inscripcion['estado_pago'])); ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo date('d/m/Y H:i', strtotime($inscripcion['fecha_inscripcion'])); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info">
                                    El participante no tiene inscripciones registradas.
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

