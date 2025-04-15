<?php
// Incluir archivo de conexión
require_once '../../config/database.php';

// Crear conexión
$database = new Database();
$db = $database->getConnection();

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Preparar la consulta
        $query = "INSERT INTO ponentes (nombre, apellido, email, telefono, institucion, especialidad, biografia) 
                 VALUES (:nombre, :apellido, :email, :telefono, :institucion, :especialidad, :biografia)";
        $stmt = $db->prepare($query);

        // Vincular los parámetros
        $stmt->bindParam(':nombre', $_POST['nombre']);
        $stmt->bindParam(':apellido', $_POST['apellido']);
        $stmt->bindParam(':email', $_POST['email']);
        $stmt->bindParam(':telefono', $_POST['telefono']);
        $stmt->bindParam(':institucion', $_POST['institucion']);
        $stmt->bindParam(':especialidad', $_POST['especialidad']);
        $stmt->bindParam(':biografia', $_POST['biografia']);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            header('Location: index.php');
            exit;
        }
    } catch (PDOException $e) {
        $error = "Error al crear el ponente: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Expositor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>Nuevo Ponente</h4>
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
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nombre" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                                    <div class="invalid-feedback">
                                        Por favor ingrese el nombre.
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="apellido" class="form-label">Apellido</label>
                                    <input type="text" class="form-control" id="apellido" name="apellido" required>
                                    <div class="invalid-feedback">
                                        Por favor ingrese el apellido.
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                                <div class="invalid-feedback">
                                    Por favor ingrese un email válido.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="telefono" name="telefono">
                            </div>

                            <div class="mb-3">
                                <label for="institucion" class="form-label">Institución</label>
                                <input type="text" class="form-control" id="institucion" name="institucion">
                            </div>

                            <div class="mb-3">
                                <label for="especialidad" class="form-label">Especialidad</label>
                                <input type="text" class="form-control" id="especialidad" name="especialidad">
                            </div>

                            <div class="mb-3">
                                <label for="biografia" class="form-label">Biografía</label>
                                <textarea class="form-control" id="biografia" name="biografia" rows="3"></textarea>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Guardar Ponente
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script para la validación del formulario
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
        })()
    </script>
</body>
</html>
