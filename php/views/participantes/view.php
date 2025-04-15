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
          WHERE p.id_participante = ?
          GROUP BY p.id_participante";
$stmt = $db->prepare($query);
$stmt->bindParam(1, $id);
$stmt->execute();

// Obtener los datos del participante
$participante = $stmt->fetch(PDO::FETCH_ASSOC);

// Verificar si se encontró el participante
if(!$participante) {
    die('Participante no encontrado.');
}

// Consulta para obtener el historial de inscripciones
$query = "SELECT i.*, e.titulo as evento_titulo, e.fecha_evento, e.hora_inicio, e.hora_fin
          FROM inscripciones i
          JOIN eventos e ON i.id_evento = e.id_evento
          WHERE i.id_participante = ?
          ORDER BY i.fecha_inscripcion DESC";
$stmt = $db->prepare($query);
$stmt->bindParam(1, $id);
$stmt->execute();
$inscripciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Participante</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>Detalles del Participante</h4>
                        <div>
                            <a href="edit.php?id=<?php echo $participante['id_participante']; ?>" class="btn btn-warning me-2">
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
                                <h5 class="border-bottom pb-2">Información Personal</h5>
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Nombre</th>
                                        <td><?php echo htmlspecialchars($participante['nombre'] . ' ' . $participante['apellido']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td><?php echo htmlspecialchars($participante['email']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Teléfono</th>
                                        <td><?php echo htmlspecialchars($participante['telefono']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Institución</th>
                                        <td><?php echo htmlspecialchars($participante['institucion']); ?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5 class="border-bottom pb-2">Estadísticas</h5>
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Total Inscripciones</th>
                                        <td>
                                            <span class="badge bg-info">
                                                <?php echo $participante['total_inscripciones']; ?> eventos
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Fecha de Registro</th>
                                        <td><?php echo date('d/m/Y H:i', strtotime($participante['created_at'])); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Última Actualización</th>
                                        <td><?php echo date('d/m/Y H:i', strtotime($participante['updated_at'])); ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <h5 class="border-bottom pb-2">Historial de Inscripciones</h5>
                        <?php if (count($inscripciones) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Evento</th>
                                            <th>Fecha Inicio</th>
                                            <th>Fecha Fin</th>
                                            <th>Estado</th>
                                            <th>Fecha Inscripción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($inscripciones as $inscripcion): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($inscripcion['evento_titulo']); ?></td>
                                                <td><?php echo date('d/m/Y H:i', strtotime($inscripcion['fecha_evento'])); ?></td>
                                                <td><?php echo date('d/m/Y H:i', strtotime($inscripcion['hora_fin'])); ?></td>
                                                <td>
                                                    <?php
                                                    $estadoClass = '';
                                                    switch($inscripcion['estado']) {
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
                                                        <?php echo ucfirst(htmlspecialchars($inscripcion['estado'])); ?>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
