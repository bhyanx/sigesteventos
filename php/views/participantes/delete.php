<?php
// Incluir archivo de conexión
require_once '../../config/database.php';

// Crear conexión
$database = new Database();
$db = $database->getConnection();

// Obtener ID del participante
$id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: ID no encontrado.');

try {
    // Iniciar transacción
    $db->beginTransaction();

    // Primero eliminar las inscripciones relacionadas
    $query = "DELETE FROM inscripciones WHERE participante_id = ?";
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $id);
    $stmt->execute();

    // Luego eliminar el participante
    $query = "DELETE FROM participantes WHERE id_participante = ?";
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $id);
    $stmt->execute();

    // Confirmar transacción
    $db->commit();

    // Redirigir a la lista de participantes
    header('Location: index.php');
    exit;
} catch (PDOException $e) {
    // Revertir transacción en caso de error
    $db->rollBack();
    die('Error al eliminar el participante: ' . $e->getMessage());
}
?> 