<?php
// Incluir archivo de conexión
require_once '../../config/database.php';

// Crear conexión
$database = new Database();
$db = $database->getConnection();

// Obtener ID del ponente
$id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: ID no encontrado.');

// Consulta para obtener los datos del ponente
$query = "SELECT * FROM ponentes WHERE id_ponente = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $id);
$stmt->execute();

// Obtener los datos del ponente
$ponente = $stmt->fetch(PDO::FETCH_ASSOC);

// Verificar si se encontró el ponente
if(!$ponente) {
    header('Location: index.php');
    exit;
}

// Consulta para obtener los eventos del ponente
$queryEventos = "SELECT e.*, c.nombre as categoria_nombre, ep.titulo_presentacion, ep.hora_presentacion
                 FROM eventos e 
                 INNER JOIN eventos_ponentes ep ON e.id_evento = ep.id_evento
                 LEFT JOIN categorias_eventos c ON e.id_categoria = c.id_categoria 
                 WHERE ep.id_ponente = :id 
                 ORDER BY e.fecha_evento DESC";
$stmtEventos = $db->prepare($queryEventos);
$stmtEventos->bindParam(':id', $id);
$stmtEventos->execute();
$eventos = $stmtEventos->fetchAll(PDO::FETCH_ASSOC);

// Definir el título de la página
$pageTitle = "Detalles del Expositor";

// Iniciar el buffer de salida
ob_start();
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Detalles del Expositor</h4>
                    <div>
                        <a href="edit.php?id=<?php echo $id; ?>" class="btn btn-primary me-2">
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
                            <h5 class="card-title"><?php echo htmlspecialchars($ponente['nombre'] . ' ' . $ponente['apellido']); ?></h5>
                            <p class="text-muted"><?php echo htmlspecialchars($ponente['email']); ?></p>
                        </div>
                        <div class="col-md-6 text-end">
                            <span class="badge bg-info">
                                <?php echo count($eventos); ?> eventos asignados
                            </span>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Información Personal</h6>
                            <ul class="list-unstyled">
                                <li><strong>Teléfono:</strong> <?php echo htmlspecialchars($ponente['telefono']); ?></li>
                                <li><strong>Institución:</strong> <?php echo htmlspecialchars($ponente['institucion']); ?></li>
                                <li><strong>Especialidad:</strong> <?php echo htmlspecialchars($ponente['especialidad']); ?></li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Biografía</h6>
                            <p><?php echo nl2br(htmlspecialchars($ponente['biografia'])); ?></p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <h6>Eventos Asignados</h6>
                            <?php if (count($eventos) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Título</th>
                                                <th>Categoría</th>
                                                <th>Fecha</th>
                                                <th>Hora</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($eventos as $evento): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($evento['titulo']); ?></td>
                                                    <td><?php echo htmlspecialchars($evento['categoria_nombre']); ?></td>
                                                    <td><?php echo date('d/m/Y', strtotime($evento['fecha_evento'])); ?></td>
                                                    <td><?php echo date('H:i', strtotime($evento['hora_inicio'])) . ' - ' . date('H:i', strtotime($evento['hora_fin'])); ?></td>
                                                    <td>
                                                        <?php
                                                        $estadoClass = '';
                                                        switch($evento['estado']) {
                                                            case 'activo':
                                                                $estadoClass = 'success';
                                                                break;
                                                            case 'cancelado':
                                                                $estadoClass = 'danger';
                                                                break;
                                                            case 'completado':
                                                                $estadoClass = 'info';
                                                                break;
                                                            default:
                                                                $estadoClass = 'secondary';
                                                        }
                                                        ?>
                                                        <span class="badge bg-<?php echo $estadoClass; ?>">
                                                            <?php echo ucfirst(htmlspecialchars($evento['estado'])); ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info">
                                    El expositor no tiene eventos asignados.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
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
