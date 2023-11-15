<?php
// Configuración de cabeceras para permitir solicitudes desde cualquier origen y métodos específicos
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

// Incluye el archivo de conexión a la base de datos
include('db_connection.php');

// Obtiene el método de la solicitud (GET, POST, PUT, DELETE)
$method = $_SERVER['REQUEST_METHOD'];

// Manejar las solicitudes según el método
switch ($method) {
    case 'GET':
        handleGetRequest();
        break;
    case 'POST':
        handlePostRequest();
        break;
    case 'PUT':
        handlePutRequest();
        break;
    case 'DELETE':
        handleDeleteRequest();
        break;
    default:
        echo json_encode(['error' => 'Método no permitido']);
}

// Función para manejar solicitudes GET
function handleGetRequest() {
    global $conn;

    // Obtiene el nombre de la tabla desde los parámetros de la URL
    $table = $_GET['table'] ?? null;

    // Verifica si se proporcionó el parámetro "table"
    if ($table === null) {
        echo json_encode(['error' => 'Falta el parámetro "table"']);
        return;
    }

    // Maneja la solicitud según la tabla especificada
    switch ($table) {
        case 'metas_incentivos':
            getRecords($conn, 'metas_incentivos');
            break;
        default:
            echo json_encode(['error' => 'Tabla no válida']);
    }
}

// Función para manejar solicitudes POST
function handlePostRequest() {
    global $conn;

    // Obtiene el nombre de la tabla y los datos desde la entrada del flujo (stream) PHP
    $table = $_POST['table'] ?? null;
    $data = json_decode(file_get_contents('php://input'), true);

    // Verifica si se proporcionaron tanto el nombre de la tabla como los datos
    if ($table === null || $data === null) {
        echo json_encode(['error' => 'Falta el parámetro "table" o los datos']);
        return;
    }

    // Maneja la solicitud según la tabla especificada
    switch ($table) {
        case 'metas_incentivos':
            insertRecord($conn, 'metas_incentivos', $data);
            break;
        default:
            echo json_encode(['error' => 'Tabla no válida']);
    }
}

// Función para manejar solicitudes PUT
function handlePutRequest() {
    global $conn;

    // Obtiene el nombre de la tabla y los datos desde el método PUT
    $table = $_PUT['table'] ?? null;
    $data = json_decode(file_get_contents('php://input'), true);

    // Verifica si se proporcionaron tanto el nombre de la tabla como los datos
    if ($table === null || $data === null) {
        echo json_encode(['error' => 'Falta el parámetro "table" o los datos']);
        return;
    }

    // Maneja la solicitud según la tabla especificada
    switch ($table) {
        case 'metas_incentivos':
            updateRecord($conn, 'metas_incentivos', $data);
            break;
        default:
            echo json_encode(['error' => 'Tabla no válida']);
    }
}

// Función para manejar solicitudes DELETE
function handleDeleteRequest() {
    global $conn;

    // Obtiene el nombre de la tabla y el ID desde el método DELETE
    $table = $_DELETE['table'] ?? null;
    $id = $_DELETE['id'] ?? null;

    // Verifica si se proporcionaron tanto el nombre de la tabla como el ID
    if ($table === null || $id === null) {
        echo json_encode(['error' => 'Falta el parámetro "table" o "id"']);
        return;
    }

    // Maneja la solicitud según la tabla especificada
    switch ($table) {
        case 'metas_incentivos':
            deleteRecord($conn, 'metas_incentivos', $id);
            break;
        default:
            echo json_encode(['error' => 'Tabla no válida']);
    }
}

// Función para obtener registros de una tabla
function getRecords($conn, $table) {
    $query = "SELECT * FROM $table";
    $result = $conn->query($query);

    // Verifica si la consulta fue exitosa
    if ($result) {
        $data = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode([$table => $data]);
    } else {
        echo json_encode(['error' => 'Error en la consulta SQL']);
    }
}

// Función para insertar un nuevo registro en una tabla
function insertRecord($conn, $table, $data) {
    // Implementa la lógica para insertar un nuevo registro en la tabla
    // Asegúrate de validar y filtrar adecuadamente los datos antes de realizar la inserción
    $meta_cantidad = $data['meta_cantidad'] ?? null;
    $empleado_id = $data['empleado_id'] ?? null;
    $filtro = $data['filtro'] ?? null;
    $incentivo = $data['incentivo'] ?? null;

    // Asegúrate de realizar la validación de datos antes de la inserción
    if ($meta_cantidad !== null && $empleado_id !== null && $filtro !== null && $incentivo !== null) {
        $query = "INSERT INTO $table (empleado_id, meta_cantidad, filtro, incentivo) VALUES ($empleado_id, $meta_cantidad, '$filtro', $incentivo)";
        $result = $conn->query($query);

        // Verifica si la inserción fue exitosa
        if ($result) {
            echo json_encode(['success' => 'Registro insertado correctamente']);
        } else {
            echo json_encode(['error' => 'Error al insertar el registro']);
        }
    } else {
        echo json_encode(['error' => 'Datos incompletos para la inserción']);
    }
}

// Función para actualizar un registro existente en una tabla
function updateRecord($conn, $table, $data) {
    // Implementa la lógica para actualizar un registro existente en la tabla
    // Asegúrate de validar y filtrar adecuadamente los datos antes de realizar la actualización
    $meta_id = $data['meta_id'] ?? null;
    $meta_cantidad = $data['meta_cantidad'] ?? null;
    $empleado_id = $data['empleado_id'] ?? null;
    $filtro = $data['filtro'] ?? null;
    $incentivo = $data['incentivo'] ?? null;

    // Asegúrate de realizar la validación de datos antes de la actualización
    if ($meta_id !== null && $meta_cantidad !== null && $empleado_id !== null && $filtro !== null && $incentivo !== null) {
        $query = "UPDATE $table SET empleado_id=$empleado_id, meta_cantidad=$meta_cantidad, filtro='$filtro', incentivo=$incentivo WHERE meta_id=$meta_id";
        $result = $conn->query($query);

        // Verifica si la actualización fue exitosa
        if ($result) {
            echo json_encode(['success' => 'Registro actualizado correctamente']);
        } else {
            echo json_encode(['error' => 'Error al actualizar el registro']);
        }
    } else {
        echo json_encode(['error' => 'Datos incompletos para la actualización']);
    }
}

// Función para eliminar un registro en una tabla según el ID
function deleteRecord($conn, $table, $id) {
    // Implementa la lógica para eliminar un registro en la tabla según el ID
    // Asegúrate de validar y filtrar adecuadamente los datos antes de realizar la eliminación
    if ($id !== null) {
        $query = "DELETE FROM $table WHERE meta_id=$id";
        $result = $conn->query($query);

        // Verifica si la eliminación fue exitosa
        if ($result) {
            echo json_encode(['success' => 'Registro eliminado correctamente']);
        } else {
            echo json_encode(['error' => 'Error al eliminar el registro']);
        }
    } else {
        echo json_encode(['error' => 'ID no proporcionado para la eliminación']);
    }
}

// Cierra la conexión a la base de datos
$conn->close();
?>
