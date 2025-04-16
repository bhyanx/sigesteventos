<?php
// Incluir archivo de conexión
require_once '../../config/database.php';

// Crear conexión
$database = new Database();
$db = $database->getConnection();

// Obtener eventos para el select
$queryEventos = "SELECT id_evento, titulo FROM eventos ORDER BY titulo";
$stmtEventos = $db->prepare($queryEventos);
$stmtEventos->execute();
$eventos = $stmtEventos->fetchAll(PDO::FETCH_ASSOC);

// Obtener participantes para el select
$queryParticipantes = "SELECT id_participante, CONCAT(nombre, ' ', apellido) as nombre_completo FROM participantes ORDER BY nombre";
$stmtParticipantes = $db->prepare($queryParticipantes);
$stmtParticipantes->execute();
$participantes = $stmtParticipantes->fetchAll(PDO::FETCH_ASSOC);

// Procesar el formulario cuando se envía
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener datos del formulario
    $id_evento = $_POST['id_evento'];
    $id_participante = $_POST['id_participante'];
    $estado_pago = $_POST['estado_pago'];

    // Insertar la inscripción
    $query = "INSERT INTO inscripciones (id_evento, id_participante, estado_pago) 
              VALUES (:id_evento, :id_participante, :estado_pago)";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id_evento', $id_evento);
    $stmt->bindParam(':id_participante', $id_participante);
    $stmt->bindParam(':estado_pago', $estado_pago);

    if($stmt->execute()) {
        $id = $db->lastInsertId();
        header('Location: view.php?id=' . $id);
        exit;
    } else {
        $error = "Error al crear la inscripción.";
    }
}

// Definir el título de la página
$pageTitle = "Crear Inscripción";

// Iniciar el buffer de salida
ob_start();
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Crear Inscripción</h4>
                    <div>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="id_evento" class="form-label">Evento</label>
                                    <select class="form-select select2" id="id_evento" name="id_evento" required>
                                        <option value="">Seleccione un evento</option>
                                        <?php foreach ($eventos as $evento): ?>
                                            <option value="<?php echo $evento['id_evento']; ?>">
                                                <?php echo htmlspecialchars($evento['titulo']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="id_participante" class="form-label">Participante</label>
                                    <select class="form-select select2" id="id_participante" name="id_participante" required>
                                        <option value="">Seleccione un participante</option>
                                        <?php foreach ($participantes as $participante): ?>
                                            <option value="<?php echo $participante['id_participante']; ?>">
                                                <?php echo htmlspecialchars($participante['nombre_completo']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="estado_pago" class="form-label">Estado del Pago</label>
                                    <select class="form-select" id="estado_pago" name="estado_pago">
                                        <option value="pendiente">Pendiente</option>
                                        <option value="completado">Completado</option>
                                        <option value="reembolsado">Reembolsado</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Crear Inscripción
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

<!-- Agregar Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

<!-- Agregar Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Inicializar Select2 para los selects
    $('.select2').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'Seleccione una opción',
        allowClear: true
    });
});
</script>
