<?php
// Establece permisos CORS para permitir solicitudes desde cualquier origen
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Incluye el archivo de conexión a la base de datos
include('db_connection.php');

// Obtiene las fechas de inicio y fin de ventas desde los parámetros de la URL
$fecha_ventas_inicio = $_GET['fecha_venta_inicio'] ?? null;
$fecha_ventas_fin = $_GET['fecha_venta_fin'] ?? null;

// Verifica si ambos parámetros están presentes
if ($fecha_ventas_inicio === null || $fecha_ventas_fin === null) {
    echo json_encode(['error' => 'Falta uno o ambos parámetros']);
    exit();
}

// Consulta SQL para obtener las ventas totales por sede en un rango de fechas
$query = "SELECT s.sede_id, s.ubicacion, SUM(v.cantidad_vendida) as totalVentas FROM sedes s
          LEFT JOIN ventas v ON s.sede_id = v.sede_id AND v.fecha_venta BETWEEN ? AND ?
          GROUP BY s.sede_id";
$stmt = $conn->prepare($query);

// Verifica si la preparación de la consulta fue exitosa
if ($stmt) {
    // Enlaza los parámetros a la consulta preparada
    $stmt->bind_param('ss', $fecha_ventas_inicio, $fecha_ventas_fin);

    // Ejecuta la consulta
    $stmt->execute();
    $result = $stmt->get_result();

    // Verifica si la ejecución de la consulta fue exitosa
    if ($result) {
        // Obtiene los datos de ventas por sede
        $data = $result->fetch_all(MYSQLI_ASSOC);

        // Consulta SQL para obtener el producto más vendido en un rango de fechas
        $queryProduct = "SELECT p.nombre_producto as producto_mas_vendido, SUM(v.cantidad_vendida) as totalVentas
                         FROM ventas v
                         LEFT JOIN productos p ON v.producto_id = p.producto_id
                         WHERE v.fecha_venta BETWEEN ? AND ?
                         GROUP BY p.producto_id
                         ORDER BY totalVentas DESC
                         LIMIT 1";

        $stmtProduct = $conn->prepare($queryProduct);

        // Verifica si la preparación de la consulta para el producto más vendido fue exitosa
        if ($stmtProduct) {
            // Enlaza los parámetros a la consulta preparada
            $stmtProduct->bind_param('ss', $fecha_ventas_inicio, $fecha_ventas_fin);

            // Ejecuta la consulta para el producto más vendido
            $stmtProduct->execute();
            $resultProduct = $stmtProduct->get_result();

            // Verifica si la ejecución de la consulta para el producto más vendido fue exitosa
            if ($resultProduct) {
                // Obtiene los datos del producto más vendido
                $dataProduct = $resultProduct->fetch_assoc();

                // Consulta SQL para obtener las metas e incentivos de los empleados
                $queryMetas = "SELECT e.empleado_id, e.nombre_empleado, mi.meta_cantidad, mi.filtro, mi.incentivo FROM metas_incentivos mi
                               INNER JOIN empleados e ON mi.empleado_id = e.empleado_id";
                $resultMetas = $conn->query($queryMetas);

                // Verifica si la consulta para las metas e incentivos fue exitosa
                if ($resultMetas) {
                    // Obtiene los datos de metas e incentivos
                    $metasIncentivos = $resultMetas->fetch_all(MYSQLI_ASSOC);
                    $incentivosData = [];

                    // Calcular incentivos para cada empleado
                    foreach ($metasIncentivos as $meta) {
                        // Obtiene la cantidad de ventas del empleado en el período
                        $cantidadVentasEmpleado = obtenerCantidadVentasEmpleado($conn, $meta['empleado_id'], $fecha_ventas_inicio, $fecha_ventas_fin);

                        // Verifica si se pudo obtener la cantidad de ventas del empleado
                        if ($cantidadVentasEmpleado !== null) {
                            // Verifica si se cumple la meta
                            $cumpleMeta = cumpleMeta($cantidadVentasEmpleado, $meta['meta_cantidad'], $meta['filtro']);

                            // Agrega los datos de incentivos al array
                            $incentivosData[] = [
                                'empleado_id' => $meta['empleado_id'],
                                'nombre_empleado' => $meta['nombre_empleado'],
                                'cumple_meta' => $cumpleMeta,
                                'incentivo' => $cumpleMeta ? $meta['incentivo'] : null,
                            ];
                        } else {
                            // Maneja el caso en el que no se pudo obtener la cantidad de ventas del empleado
                            $incentivosData[] = [
                                'empleado_id' => $meta['empleado_id'],
                                'nombre_empleado' => $meta['nombre_empleado'],
                                'error' => 'Error al obtener la cantidad de ventas del empleado',
                            ];
                        }
                    }

                    // Devuelve los resultados como JSON
                    echo json_encode(['salesBySede' => $data, 'productData' => $dataProduct, 'incentivos' => $incentivosData]);
                } else {
                    // Maneja el caso en el que la consulta SQL para las metas e incentivos no fue exitosa
                    echo json_encode(['error' => 'Error en la consulta SQL para obtener metas e incentivos']);
                }

                // Cierra la consulta preparada para el producto más vendido
                $stmtProduct->close();
            } else {
                // Maneja el caso en el que la consulta SQL para el producto más vendido no fue exitosa
                echo json_encode(['error' => 'Error en la consulta SQL para obtener el producto más vendido']);
            }
        } else {
            // Maneja el caso en el que la preparación de la consulta SQL para el producto más vendido no fue exitosa
            echo json_encode(['error' => 'Error en la preparación de la consulta SQL para obtener el producto más vendido', 'errno' => $conn->errno, 'error_message' => $conn->error]);
        }
    } else {
        // Maneja el caso en el que la consulta SQL para las ventas por sede no fue exitosa
        echo json_encode(['error' => 'Error en la consulta SQL']);
    }

    // Cierra la consulta preparada para las ventas por sede
    $stmt->close();
} else {
    // Maneja el caso en el que la preparación de la consulta SQL no fue exitosa
    echo json_encode(['error' => 'Error en la preparación de la consulta SQL', 'errno' => $conn->errno, 'error_message' => $conn->error]);
}

// Cierra la conexión a la base de datos
$conn->close();

// Función para obtener la cantidad de ventas de un empleado en un período
function obtenerCantidadVentasEmpleado($conn, $empleadoId, $fechaInicio, $fechaFin) {
    // Consulta SQL para obtener la cantidad de ventas de un empleado en un período
    $queryVentasEmpleado = "SELECT SUM(cantidad_vendida) as totalVentas FROM ventas WHERE empleado_id = ? AND fecha_venta BETWEEN ? AND ?";
    $stmtVentasEmpleado = $conn->prepare($queryVentasEmpleado);

    // Verifica si la preparación de la consulta fue exitosa
    if ($stmtVentasEmpleado) {
        // Enlaza los parámetros a la consulta preparada
        $stmtVentasEmpleado->bind_param('iss', $empleadoId, $fechaInicio, $fechaFin);
        // Ejecuta la consulta
        $stmtVentasEmpleado->execute();

        // Obtiene el resultado de la consulta
        $resultVentasEmpleado = $stmtVentasEmpleado->get_result();

        // Verifica si la ejecución de la consulta fue exitosa
        if ($resultVentasEmpleado) {
            // Obtiene los datos de la consulta
            $dataVentasEmpleado = $resultVentasEmpleado->fetch_assoc();
            return $dataVentasEmpleado['totalVentas'];
        } else {
            return null;
        }

        // Cierra la consulta preparada para las ventas del empleado
        $stmtVentasEmpleado->close();
    } else {
        return null;
    }
}

// Función para verificar si se cumple una meta
function cumpleMeta($cantidadVentas, $metaCantidad, $filtro) {
    // Compara la cantidad de ventas con la meta según el filtro
    switch ($filtro) {
        case '<':
            return $cantidadVentas < $metaCantidad;
        case '<=':
            return $cantidadVentas <= $metaCantidad;
        case '>':
            return $cantidadVentas > $metaCantidad;
        case '>=':
            return $cantidadVentas >= $metaCantidad;
        case '=':
            return $cantidadVentas == $metaCantidad;
        default:
            return false;
    }
}
?>
