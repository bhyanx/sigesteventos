<?php
// Incluir archivo de conexión
require_once '../../config/database.php';

// Crear conexión
$database = new Database();
$db = $database->getConnection();

// Obtener ID del ponente
$id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: ID no encontrado.');

try {
    // Eliminar el ponente
    $query = "DELETE FROM ponentes WHERE id_ponente = ?";
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $id);
    $stmt->execute();

    // Redirigir a la lista de ponentes
    header('Location: index.php');
    exit;
} catch (PDOException $e) {
    die('Error al eliminar el ponente: ' . $e->getMessage());
}
?> 
