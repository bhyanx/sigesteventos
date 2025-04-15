<?php
// php/config/database.php
// Habilitar mostrado de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


class Database {
    private $host = "localhost";
    private $database_name = "sistema_eventos_academicos";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->database_name,
                $this->username,
                $this->password
            );
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
        }
        return $this->conn;
    }
}


// Función para hacer solicitudes a la API
function callAPI($method, $url, $data = false) {
    $curl = curl_init();
   
    // Debug: Mostrar información de la solicitud
    echo "<pre class='alert alert-info'>";
    echo "Debug - Solicitud API:\n";
    echo "URL: " . $url . "\n";
    echo "Método: " . $method . "\n";
    if ($data) {
        echo "Datos: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";
    }
    echo "</pre>";
   
    switch ($method) {
        case "GET":
            if ($data) {
                $url = sprintf("%s?%s", $url, http_build_query($data));
            }
            break;
        case "POST":
            curl_setopt($curl, CURLOPT_POST, true);
            if ($data) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            }
            break;
        case "PUT":
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
            if ($data) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            }
            break;
        case "DELETE":
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
            break;
    }
   
    // URL y opciones
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Accept: application/json'
    ));
   
    // Ejecutar
    $result = curl_exec($curl);
    curl_close($curl);
   
    return json_decode($result, true);
}


// Constante para la URL base de la API
define('API_URL', 'http://localhost:3000/api');
?>
