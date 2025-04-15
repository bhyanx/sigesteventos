<?php
// Incluir archivo de conexión
require_once '../../config/database.php';

// Crear conexión
$database = new Database();
$db = $database->getConnection();

// Obtener lista de participantes
$query = "SELECT id, nombre, email FROM participantes ORDER BY nombre ASC";
$stmt = $db->prepare($query);
$stmt->execute();
$participantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener lista de eventos
$query = "SELECT id, titulo, fecha_inicio, fecha_fin FROM eventos WHERE estado = 'activo' ORDER BY fecha_inicio ASC";
$stmt = $db->prepare($query);
$stmt->execute();
$eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_participante = $_POST['id_participante'] ?? '';
    $evento_id = $_POST['evento_id'] ?? '';
    $fecha_inscripcion = $_POST['fecha_inscripcion'] ?? '';
    $estado = $_POST['estado'] ?? 'pendiente';
    $metodo_pago = $_POST['metodo_pago'] ?? '';
    $comprobante = $_POST['comprobante'] ?? '';

    // Validate required fields
    if (empty($id_participante) || empty($evento_id) || empty($fecha_inscripcion)) {
        $error = "Todos los campos son obligatorios";
    } else {
        try {
            $query = "INSERT INTO inscripciones (id_participante, evento_id, fecha_inscripcion, estado, metodo_pago, comprobante) 
                     VALUES (:id_participante, :evento_id, :fecha_inscripcion, :estado, :metodo_pago, :comprobante)";
            $stmt = $db->prepare($query);
            $stmt->execute([
                'id_participante' => $id_participante,
                'evento_id' => $evento_id,
                'fecha_inscripcion' => $fecha_inscripcion,
                'estado' => $estado,
                'metodo_pago' => $metodo_pago,
                'comprobante' => $comprobante
            ]);

            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $error = "Error al crear la inscripción: " . $e->getMessage();
        }
    }
}

// Definir el título de la página
$pageTitle = "Nueva Inscripción";

// Incluir el header
include '../layouts/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Nueva Inscripción</h4>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver
                    </a>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="id_participante" class="form-label">Participante</label>
                            <select class="form-select" id="id_participante" name="id_participante" required>
                                <option value="">Seleccione un participante...</option>
                                <?php foreach ($participantes as $participante): ?>
                                    <option value="<?php echo $participante['id']; ?>">
                                        <?php echo htmlspecialchars($participante['nombre'] . ' (' . $participante['email'] . ')'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">
                                Por favor seleccione un participante.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="evento_id" class="form-label">Evento</label>
                            <select class="form-select" id="evento_id" name="evento_id" required>
                                <option value="">Seleccione un evento...</option>
                                <?php foreach ($eventos as $evento): ?>
                                    <option value="<?php echo $evento['id']; ?>">
                                        <?php 
                                        echo htmlspecialchars($evento['titulo'] . ' (' . 
                                            date('d/m/Y H:i', strtotime($evento['fecha_inicio'])) . ' - ' . 
                                            date('d/m/Y H:i', strtotime($evento['fecha_fin'])) . ')'); 
                                        ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">
                                Por favor seleccione un evento.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="fecha_inscripcion" class="form-label">Fecha de Inscripción</label>
                            <input type="datetime-local" class="form-control" id="fecha_inscripcion" name="fecha_inscripcion" required>
                            <div class="invalid-feedback">
                                Por favor seleccione una fecha de inscripción.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="estado" class="form-label">Estado</label>
                            <select class="form-select" id="estado" name="estado" required>
                                <option value="">Seleccione un estado...</option>
                                <option value="pendiente">Pendiente</option>
                                <option value="confirmado">Confirmado</option>
                                <option value="cancelado">Cancelado</option>
                            </select>
                            <div class="invalid-feedback">
                                Por favor seleccione un estado.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="metodo_pago" class="form-label">Método de Pago</label>
                            <input type="text" class="form-control" id="metodo_pago" name="metodo_pago">
                        </div>

                        <div class="mb-3">
                            <label for="comprobante" class="form-label">Comprobante</label>
                            <input type="text" class="form-control" id="comprobante" name="comprobante">
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Guardar Inscripción
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Incluir el footer
include '../layouts/footer.php';
?>
