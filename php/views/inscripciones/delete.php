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
    // Verificar si la inscripción existe
    $query = "SELECT id FROM inscripciones WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        $_SESSION['error'] = "La inscripción no existe.";
        header('Location: index.php');
        exit;
    }

    // Eliminar la inscripción
    $query = "DELETE FROM inscripciones WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Inscripción eliminada correctamente.";
    } else {
        $_SESSION['error'] = "Error al eliminar la inscripción.";
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Error al eliminar la inscripción: " . $e->getMessage();
}

header('Location: index.php');
exit; 