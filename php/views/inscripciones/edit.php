<?php
// Incluir archivo de conexión
require_once '../../config/database.php';

// Crear conexión
$database = new Database();
$db = $database->getConnection();

// Verificar si se proporcionó un ID
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = $_GET['id'];

// Obtener la inscripción actual
$query = "SELECT * FROM inscripciones WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $id);
$stmt->execute();
$inscripcion = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$inscripcion) {
    header('Location: index.php');
    exit;
}

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
            $query = "UPDATE inscripciones 
                     SET id_participante = :id_participante,
                         evento_id = :evento_id,
                         fecha_inscripcion = :fecha_inscripcion,
                         estado = :estado,
                         metodo_pago = :metodo_pago,
                         comprobante = :comprobante
                     WHERE id_inscripcion = :id_inscripcion";
            $stmt = $db->prepare($query);
            $stmt->execute([
                'id_participante' => $id_participante,
                'evento_id' => $evento_id,
                'fecha_inscripcion' => $fecha_inscripcion,
                'estado' => $estado,
                'metodo_pago' => $metodo_pago,
                'comprobante' => $comprobante,
                'id_inscripcion' => $id
            ]);

            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $error = "Error al actualizar la inscripción: " . $e->getMessage();
        }
    }
}

// Definir el título de la página
$pageTitle = "Editar Inscripción";

// Incluir el header
include '../layouts/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Editar Inscripción</h4>
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
                                <option value="">Seleccione un participante</option>
                                <?php foreach ($participantes as $participante): ?>
                                    <option value="<?= $participante['id'] ?>" <?= $inscripcion['id_participante'] == $participante['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($participante['nombre'] . ' ' . $participante['email']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Por favor seleccione un participante</div>
                        </div>

                        <div class="mb-3">
                            <label for="evento_id" class="form-label">Evento</label>
                            <select class="form-select" id="evento_id" name="evento_id" required>
                                <option value="">Seleccione un evento</option>
                                <?php foreach ($eventos as $evento): ?>
                                    <option value="<?= $evento['id'] ?>" <?= $inscripcion['evento_id'] == $evento['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($evento['titulo']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Por favor seleccione un evento</div>
                        </div>

                        <div class="mb-3">
                            <label for="fecha_inscripcion" class="form-label">Fecha de Inscripción</label>
                            <input type="date" class="form-control" id="fecha_inscripcion" name="fecha_inscripcion" 
                                   value="<?= htmlspecialchars($inscripcion['fecha_inscripcion']) ?>" required>
                            <div class="invalid-feedback">Por favor ingrese la fecha de inscripción</div>
                        </div>

                        <div class="mb-3">
                            <label for="estado" class="form-label">Estado</label>
                            <select class="form-select" id="estado" name="estado" required>
                                <option value="pendiente" <?= $inscripcion['estado'] == 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                                <option value="confirmada" <?= $inscripcion['estado'] == 'confirmada' ? 'selected' : '' ?>>Confirmada</option>
                                <option value="cancelada" <?= $inscripcion['estado'] == 'cancelada' ? 'selected' : '' ?>>Cancelada</option>
                            </select>
                            <div class="invalid-feedback">Por favor seleccione un estado</div>
                        </div>

                        <div class="mb-3">
                            <label for="metodo_pago" class="form-label">Método de Pago</label>
                            <select class="form-select" id="metodo_pago" name="metodo_pago">
                                <option value="">Seleccione un método de pago</option>
                                <option value="efectivo" <?= $inscripcion['metodo_pago'] == 'efectivo' ? 'selected' : '' ?>>Efectivo</option>
                                <option value="tarjeta" <?= $inscripcion['metodo_pago'] == 'tarjeta' ? 'selected' : '' ?>>Tarjeta</option>
                                <option value="transferencia" <?= $inscripcion['metodo_pago'] == 'transferencia' ? 'selected' : '' ?>>Transferencia</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="comprobante" class="form-label">Comprobante de Pago</label>
                            <input type="text" class="form-control" id="comprobante" name="comprobante" 
                                   value="<?= htmlspecialchars($inscripcion['comprobante'] ?? '') ?>">
                        </div>

                        <button type="submit" class="btn btn-primary">Actualizar Inscripción</button>
                        <a href="index.php" class="btn btn-secondary">Cancelar</a>
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
