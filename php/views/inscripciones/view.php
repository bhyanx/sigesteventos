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

try {
    // Obtener los detalles de la inscripción
    $query = "SELECT i.*, p.nombre as participante_nombre, p.email as participante_email,
              e.titulo as evento_titulo, e.fecha_inicio, e.fecha_fin
              FROM inscripciones i
              JOIN participantes p ON i.id_participante = p.id_participante
              JOIN eventos e ON i.evento_id = e.id
              WHERE i.id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    
    if ($stmt->rowCount() === 0) {
        $_SESSION['error'] = "La inscripción no existe.";
        header('Location: index.php');
        exit;
    }
    
    $inscripcion = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['error'] = "Error al obtener los detalles de la inscripción: " . $e->getMessage();
    header('Location: index.php');
    exit;
}

// Definir el título de la página
$pageTitle = "Detalles de Inscripción";

// Incluir el header
include '../layouts/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Detalles de Inscripción</h4>
                    <div>
                        <a href="edit.php?id=<?php echo $inscripcion['id']; ?>" class="btn btn-warning me-2">
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
                            <h5 class="border-bottom pb-2">Información del Participante</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Nombre</th>
                                    <td><?php echo htmlspecialchars($inscripcion['participante_nombre']); ?></td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td><?php echo htmlspecialchars($inscripcion['participante_email']); ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2">Información del Evento</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Evento</th>
                                    <td><?php echo htmlspecialchars($inscripcion['evento_titulo']); ?></td>
                                </tr>
                                <tr>
                                    <th>Fecha Inicio</th>
                                    <td><?php echo date('d/m/Y H:i', strtotime($inscripcion['fecha_inicio'])); ?></td>
                                </tr>
                                <tr>
                                    <th>Fecha Fin</th>
                                    <td><?php echo date('d/m/Y H:i', strtotime($inscripcion['fecha_fin'])); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <h5 class="border-bottom pb-2">Detalles de la Inscripción</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Estado</th>
                                    <td>
                                        <?php
                                        $estadoClass = '';
                                        switch($inscripcion['estado']) {
                                            case 'confirmado':
                                                $estadoClass = 'success';
                                                break;
                                            case 'pendiente':
                                                $estadoClass = 'warning';
                                                break;
                                            case 'cancelado':
                                                $estadoClass = 'danger';
                                                break;
                                            default:
                                                $estadoClass = 'secondary';
                                        }
                                        ?>
                                        <span class="badge bg-<?php echo $estadoClass; ?>">
                                            <?php echo ucfirst(htmlspecialchars($inscripcion['estado'])); ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Fecha de Inscripción</th>
                                    <td><?php echo date('d/m/Y H:i', strtotime($inscripcion['fecha_inscripcion'])); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Incluir el footer
include '../layouts/footer.php';
?>
