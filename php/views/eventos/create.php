<?php
// Incluir archivo de conexión
require_once '../../config/database.php';

// Crear conexión
$database = new Database();
$db = $database->getConnection();

// Obtener categorías para el select
$queryCategorias = "SELECT id_categoria, nombre FROM categorias_eventos ORDER BY nombre";
$stmtCategorias = $db->prepare($queryCategorias);
$stmtCategorias->execute();
$categorias = $stmtCategorias->fetchAll(PDO::FETCH_ASSOC);

// Procesar el formulario cuando se envía
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener datos del formulario
    $titulo = $_POST['titulo'];
    $id_categoria = $_POST['id_categoria'];
    $descripcion = $_POST['descripcion'];
    $ubicacion = $_POST['ubicacion'];
    $fecha_evento = $_POST['fecha_evento'];
    $hora_inicio = $_POST['hora_inicio'];
    $hora_fin = $_POST['hora_fin'];
    $max_participantes = $_POST['max_participantes'];
    $tarifa_inscripcion = $_POST['tarifa_inscripcion'];

    // Insertar el evento
    $query = "INSERT INTO eventos (titulo, id_categoria, descripcion, ubicacion, fecha_evento, hora_inicio, hora_fin, max_participantes, tarifa_inscripcion) 
              VALUES (:titulo, :id_categoria, :descripcion, :ubicacion, :fecha_evento, :hora_inicio, :hora_fin, :max_participantes, :tarifa_inscripcion)";
    
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

    if($stmt->execute()) {
        $id = $db->lastInsertId();
        header('Location: view.php?id=' . $id);
        exit;
    } else {
        $error = "Error al crear el evento.";
    }
}

// Definir el título de la página
$pageTitle = "Crear Evento";

// Iniciar el buffer de salida
ob_start();
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Crear Evento</h4>
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
                                    <label for="titulo" class="form-label">Título</label>
                                    <input type="text" class="form-control" id="titulo" name="titulo" required>
                                </div>

                                <div class="mb-3">
                                    <label for="id_categoria" class="form-label">Categoría</label>
                                    <select class="form-select" id="id_categoria" name="id_categoria" required>
                                        <option value="">Seleccione una categoría</option>
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
                                    <label for="ubicacion" class="form-label">Ubicación</label>
                                    <input type="text" class="form-control" id="ubicacion" name="ubicacion">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="fecha_evento" class="form-label">Fecha del Evento</label>
                                    <input type="date" class="form-control" id="fecha_evento" name="fecha_evento" required>
                                </div>

                                <div class="mb-3">
                                    <label for="hora_inicio" class="form-label">Hora de Inicio</label>
                                    <input type="time" class="form-control" id="hora_inicio" name="hora_inicio" required>
                                </div>

                                <div class="mb-3">
                                    <label for="hora_fin" class="form-label">Hora de Fin</label>
                                    <input type="time" class="form-control" id="hora_fin" name="hora_fin" required>
                                </div>

                                <div class="mb-3">
                                    <label for="max_participantes" class="form-label">Máximo de Participantes</label>
                                    <input type="number" class="form-control" id="max_participantes" name="max_participantes" min="0">
                                </div>

                                <div class="mb-3">
                                    <label for="tarifa_inscripcion" class="form-label">Tarifa de Inscripción</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="tarifa_inscripcion" name="tarifa_inscripcion" step="0.01" min="0">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Crear Evento
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
