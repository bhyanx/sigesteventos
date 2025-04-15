<?php
// Incluir archivo de conexión
require_once '../../config/database.php';

// Crear conexión
$database = new Database();
$db = $database->getConnection();

// Obtener ID del ponente
$id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: ID no encontrado.');

// Consulta para obtener los datos del ponente
$query = "SELECT p.* FROM ponentes p WHERE p.id_ponente = ?";
$stmt = $db->prepare($query);
$stmt->bindParam(1, $id);
$stmt->execute();

// Obtener los datos del ponente
$ponente = $stmt->fetch(PDO::FETCH_ASSOC);

// Verificar si se encontró el ponente
if(!$ponente) {
    die('Ponente no encontrado.');
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Expositor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>Detalles del Expositor</h4>
                        <div>
                            <a href="edit.php?id=<?php echo $ponente['id_ponente']; ?>" class="btn btn-warning me-2">
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
                                        <td><?php echo htmlspecialchars($ponente['nombre']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td><?php echo htmlspecialchars($ponente['email']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Teléfono</th>
                                        <td><?php echo htmlspecialchars($ponente['telefono']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Especialidad</th>
                                        <td><?php echo htmlspecialchars($ponente['especialidad']); ?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5 class="border-bottom pb-2">Estadísticas</h5>
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Fecha de Registro</th>
                                        <td><?php echo date('d/m/Y H:i', strtotime($ponente['created_at'])); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Última Actualización</th>
                                        <td><?php echo date('d/m/Y H:i', strtotime($ponente['updated_at'])); ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <h5 class="border-bottom pb-2">Biografía</h5>
                        <div class="card mb-4">
                            <div class="card-body">
                                <?php echo nl2br(htmlspecialchars($ponente['biografia'])); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
