<?php
// Incluir archivo de conexión
require_once '../../config/database.php';

// Crear conexión
$database = new Database();
$db = $database->getConnection();

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'] ?? '';
    $id_categoria = $_POST['id_categoria'] ?? null;
    $descripcion = $_POST['descripcion'] ?? '';
    $ubicacion = $_POST['ubicacion'] ?? '';
    $fecha_evento = $_POST['fecha_evento'] ?? '';
    $hora_inicio = $_POST['hora_inicio'] ?? '';
    $hora_fin = $_POST['hora_fin'] ?? '';
    $max_participantes = $_POST['max_participantes'] ?? 0;
    $tarifa_inscripcion = $_POST['tarifa_inscripcion'] ?? 0.00;
    $imagen = $_POST['imagen'] ?? null;

    // Validar campos requeridos
    if (empty($titulo) || empty($fecha_evento) || empty($hora_inicio) || empty($hora_fin) || empty($ubicacion)) {
        $error = "Por favor complete todos los campos requeridos.";
    } else {
        // Insertar nuevo evento
        $query = "INSERT INTO eventos (titulo, id_categoria, descripcion, ubicacion, fecha_evento, hora_inicio, hora_fin, max_participantes, tarifa_inscripcion, imagen) 
                 VALUES (:titulo, :id_categoria, :descripcion, :ubicacion, :fecha_evento, :hora_inicio, :hora_fin, :max_participantes, :tarifa_inscripcion, :imagen)";
        
        try {
            $stmt = $db->prepare($query);
            $stmt->bindParam(':titulo', $titulo);
            $stmt->bindParam(':id_categoria', $id_categoria);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':ubicacion', $ubicacion);
            $stmt->bindParam(':fecha_evento', $fecha_evento);
            $stmt->bindParam(':hora_inicio', $hora_inicio);
            $stmt->bindParam(':hora_fin', $hora_fin);
            $stmt->bindParam(':max_participantes', $max_participantes);
            $stmt->bindParam(':tarifa_inscripcion', $tarifa_inscripcion);
            $stmt->bindParam(':imagen', $imagen);

            if ($stmt->execute()) {
                header('Location: index.php');
                exit;
            } else {
                $error = "Error al crear el evento.";
            }
        } catch (PDOException $e) {
            $error = "Error de base de datos: " . $e->getMessage();
        }
    }
}

// Obtener categorías para el select
$query = "SELECT id_categoria, nombre FROM categorias_eventos ORDER BY nombre ASC";
$stmt = $db->prepare($query);
$stmt->execute();
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Definir el título de la página
$pageTitle = "Nuevo Evento";

// Incluir el header
include '../layouts/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Crear Nuevo Evento</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form method="POST" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="titulo" class="form-label">Título *</label>
                            <input type="text" class="form-control" id="titulo" name="titulo" required>
                            <div class="invalid-feedback">Por favor ingrese un título.</div>
                        </div>

                        <div class="mb-3">
                            <label for="id_categoria" class="form-label">Categoría</label>
                            <select class="form-select" id="id_categoria" name="id_categoria">
                                <option value="">Seleccione una categoría...</option>
                                <?php foreach ($categorias as $categoria): ?>
                                    <option value="<?php echo $categoria['id_categoria']; ?>">
                                        <?php echo htmlspecialchars($categoria['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="ubicacion" class="form-label">Ubicación *</label>
                            <input type="text" class="form-control" id="ubicacion" name="ubicacion" required>
                            <div class="invalid-feedback">Por favor ingrese una ubicación.</div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="fecha_evento" class="form-label">Fecha del Evento *</label>
                                <input type="date" class="form-control" id="fecha_evento" name="fecha_evento" required>
                                <div class="invalid-feedback">Por favor seleccione una fecha.</div>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="hora_inicio" class="form-label">Hora de Inicio *</label>
                                <input type="time" class="form-control" id="hora_inicio" name="hora_inicio" required>
                                <div class="invalid-feedback">Por favor seleccione una hora de inicio.</div>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="hora_fin" class="form-label">Hora de Fin *</label>
                                <input type="time" class="form-control" id="hora_fin" name="hora_fin" required>
                                <div class="invalid-feedback">Por favor seleccione una hora de fin.</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="max_participantes" class="form-label">Máximo de Participantes</label>
                                <input type="number" class="form-control" id="max_participantes" name="max_participantes" min="0" value="0">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="tarifa_inscripcion" class="form-label">Tarifa de Inscripción</label>
                                <input type="number" class="form-control" id="tarifa_inscripcion" name="tarifa_inscripcion" min="0" step="0.01" value="0.00">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="imagen" class="form-label">URL de la Imagen</label>
                            <input type="text" class="form-control" id="imagen" name="imagen">
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="index.php" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Crear Evento</button>
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
