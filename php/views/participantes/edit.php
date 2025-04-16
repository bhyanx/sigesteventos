<?php
// Incluir archivo de conexión
require_once '../../config/database.php';

// Crear conexión
$database = new Database();
$db = $database->getConnection();

// Obtener ID del participante
$id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: ID no encontrado.');

// Consulta para obtener los datos del participante
$query = "SELECT id_participante, nombre, apellido, email, telefono, institucion 
          FROM participantes 
          WHERE id_participante = :id";
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

// Procesar el formulario cuando se envía
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener datos del formulario
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $institucion = $_POST['institucion'];

    // Actualizar el participante
    $query = "UPDATE participantes 
              SET nombre = :nombre,
                  apellido = :apellido,
                  email = :email,
                  telefono = :telefono,
                  institucion = :institucion
              WHERE id_participante = :id";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':apellido', $apellido);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':telefono', $telefono);
    $stmt->bindParam(':institucion', $institucion);
    $stmt->bindParam(':id', $id);

    if($stmt->execute()) {
        header('Location: view.php?id=' . $id);
        exit;
    } else {
        $error = "Error al actualizar el participante.";
    }
}

// Definir el título de la página
$pageTitle = "Editar Participante";

// Iniciar el buffer de salida
ob_start();
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Editar Participante</h4>
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
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" 
                                           value="<?php echo htmlspecialchars($participante['nombre']); ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="apellido" class="form-label">Apellido</label>
                                    <input type="text" class="form-control" id="apellido" name="apellido" 
                                           value="<?php echo htmlspecialchars($participante['apellido']); ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($participante['email']); ?>" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="telefono" class="form-label">Teléfono</label>
                                    <input type="text" class="form-control" id="telefono" name="telefono" 
                                           value="<?php echo htmlspecialchars($participante['telefono']); ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="institucion" class="form-label">Institución</label>
                                    <input type="text" class="form-control" id="institucion" name="institucion" 
                                           value="<?php echo htmlspecialchars($participante['institucion']); ?>">
                                </div>
                            </div>
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
