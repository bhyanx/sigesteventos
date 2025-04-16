<?php
// Incluir archivo de conexión
require_once '../../config/database.php';

// Crear conexión
$database = new Database();
$db = $database->getConnection();

// Obtener ID de la inscripción
$id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: ID no encontrado.');

// Consulta para obtener los datos de la inscripción
$query = "SELECT i.*, e.titulo as evento_titulo, p.nombre as participante_nombre, p.apellido as participante_apellido
          FROM inscripciones i
          INNER JOIN eventos e ON i.id_evento = e.id_evento
          INNER JOIN participantes p ON i.id_participante = p.id_participante
          WHERE i.id_inscripcion = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $id);
$stmt->execute();

// Obtener los datos de la inscripción
$inscripcion = $stmt->fetch(PDO::FETCH_ASSOC);

// Verificar si se encontró la inscripción
if(!$inscripcion) {
    header('Location: index.php');
    exit;
}

// Procesar el formulario cuando se envía
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener datos del formulario
    $estado_pago = $_POST['estado_pago'];
    $asistencia = isset($_POST['asistencia']) ? 1 : 0;
    $retroalimentacion = $_POST['retroalimentacion'];

    // Actualizar la inscripción
    $query = "UPDATE inscripciones 
              SET estado_pago = :estado_pago,
                  asistencia = :asistencia,
                  retroalimentacion = :retroalimentacion
              WHERE id_inscripcion = :id";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':estado_pago', $estado_pago);
    $stmt->bindParam(':asistencia', $asistencia);
    $stmt->bindParam(':retroalimentacion', $retroalimentacion);
    $stmt->bindParam(':id', $id);

    if($stmt->execute()) {
        header('Location: view.php?id=' . $id);
        exit;
    } else {
        $error = "Error al actualizar la inscripción.";
    }
}

// Definir el título de la página
$pageTitle = "Editar Inscripción";

// Iniciar el buffer de salida
ob_start();
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Editar Inscripción</h4>
                    <div>
                        <a href="view.php?id=<?php echo $id; ?>" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6>Información del Evento</h6>
                                <ul class="list-unstyled">
                                    <li><strong>Evento:</strong> <?php echo htmlspecialchars($inscripcion['evento_titulo']); ?></li>
                                    <li><strong>Participante:</strong> <?php echo htmlspecialchars($inscripcion['participante_nombre'] . ' ' . $inscripcion['participante_apellido']); ?></li>
                                    <li><strong>Fecha de Inscripción:</strong> <?php echo date('d/m/Y H:i', strtotime($inscripcion['fecha_inscripcion'])); ?></li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="estado_pago" class="form-label">Estado del Pago</label>
                                    <select class="form-select" id="estado_pago" name="estado_pago" required>
                                        <option value="pendiente" <?php echo $inscripcion['estado_pago'] == 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                                        <option value="pagado" <?php echo $inscripcion['estado_pago'] == 'pagado' ? 'selected' : ''; ?>>Pagado</option>
                                        <option value="cancelado" <?php echo $inscripcion['estado_pago'] == 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="asistencia" name="asistencia" <?php echo $inscripcion['asistencia'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="asistencia">
                                            Asistencia Confirmada
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="retroalimentacion" class="form-label">Retroalimentación</label>
                            <textarea class="form-control" id="retroalimentacion" name="retroalimentacion" rows="3"><?php echo htmlspecialchars($inscripcion['retroalimentacion']); ?></textarea>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>
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
