# Proyecto de Informe de Ventas y Gestión de Metas e Incentivos

Este proyecto consta de dos partes: un backend desarrollado en PHP para manejar informes de ventas y gestión de metas e incentivos, y un frontend en React para interactuar con el backend y presentar los informes.

## Instrucciones de Ejecución

### Backend (PHP)

1. Asegúrate de tener un servidor web configurado con soporte para PHP.

2. Copia el contenido de la carpeta `backend` en tu directorio de servidor web.

3. Configura la base de datos editando el archivo `backend/db_connection.php` con los detalles de tu base de datos.

4. Ejecuta el servidor web y accede a la carpeta donde copiaste los archivos del backend.

5. importa el archivo ventas_sucursales.sql en la base de datos.

### Frontend (React)

1. Asegúrate de tener [Node.js](https://nodejs.org/) instalado en tu sistema.

2. Abre una terminal y navega hasta la carpeta `frontend`.

3. Ejecuta el siguiente comando para instalar las dependencias:

   ```bash
   npm install


Descripción del Problema

Este proyecto aborda el seguimiento de las ventas de diversas sedes y la gestión de metas e incentivos para los empleados. El backend en PHP proporciona puntos finales para obtener informes de ventas, detalles de productos más vendidos y datos de metas e incentivos. El frontend en React permite la interacción con estos informes, visualización de gráficos y gestión de metas e incentivos mediante operaciones CRUD.