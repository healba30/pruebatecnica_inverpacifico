import React, { useState, useEffect } from 'react';
import axios from 'axios';
import '../CRUD.css';  // Importa estilos CSS específicos para CrudComponent

const CrudComponent = () => {
  // Estado para almacenar los datos obtenidos del servidor
  const [data, setData] = useState([]);
  
  // Estado para almacenar los datos del formulario
  const [formData, setFormData] = useState({});

  // Función asincrónica para cargar los datos desde el servidor
  const loadData = async () => {
    try {
      // Realiza una solicitud GET al servidor para obtener datos de la tabla 'metas_incentivos'
      const response = await axios.get('http://localhost/proyecto/backend/crud.php?table=metas_incentivos');
      
      // Verifica si la respuesta del servidor contiene un array de datos
      if (Array.isArray(response.data.metas_incentivos)) {
        // Actualiza el estado con los datos obtenidos
        setData(response.data.metas_incentivos);
      } else {
        console.error('La respuesta del servidor no es un array:', response.data);
      }
    } catch (error) {
      // Maneja errores al cargar datos, establece el estado de datos como un array vacío
      console.error('Error al cargar los datos:', error.message);
      setData([]);
    }
  };

  // Función asincrónica para enviar datos al servidor
  const sendData = async () => {
    try {
      // Configuración de la solicitud para insertar o actualizar datos en la tabla 'metas_incentivos'
      let requestConfig = {
        method: 'POST',
        url: 'http://localhost/proyecto/backend/crud.php',
        data: { table: 'metas_incentivos', ...formData },
      };

      // Si formData.meta_id existe, se trata de una actualización, por lo que se agrega la operación 'update'
      // De lo contrario, es una inserción y se agrega la operación 'insert'
      if (formData.meta_id) {
        requestConfig.data.operation = 'update';
      } else {
        requestConfig.data.operation = 'insert';
      }

      // Realiza la solicitud al servidor
      await axios(requestConfig);

      // Recarga los datos después de la inserción o actualización y reinicia el estado del formulario
      loadData();
      setFormData({});
    } catch (error) {
      // Maneja errores al enviar datos al servidor
      console.error('Error al enviar datos:', error.message);
    }
  };

  // Función asincrónica para eliminar un elemento del servidor
  const deleteItem = async (meta_id) => {
    try {
      // Realiza una solicitud POST al servidor para eliminar un elemento de la tabla 'metas_incentivos'
      await axios.post('http://localhost/proyecto/backend/crud.php', {
        table: 'metas_incentivos',
        operation: 'delete',
        id: meta_id,
      });

      // Recarga los datos después de la eliminación
      loadData();
    } catch (error) {
      // Maneja errores al eliminar elementos del servidor
      console.error('Error al eliminar el elemento:', error.message);
    }
  };

  // Efecto que se ejecuta al montar el componente para cargar los datos iniciales
  useEffect(() => {
    loadData();
  }, []);

  return (
    <div>
      <h1>Gestionar Metas e Incentivos</h1>

      {/* Formulario para ingresar datos con campos controlados por el estado */}
      <form onSubmit={(e) => { e.preventDefault(); sendData(); }}>
        <label>
          Empleado ID:
          <input
            type="number"
            value={formData.empleado_id || ''}
            onChange={(e) => setFormData({ ...formData, empleado_id: e.target.value })}
          />
        </label>
        <label>
          Meta Cantidad:
          <input
            type="number"
            value={formData.meta_cantidad || ''}
            onChange={(e) => setFormData({ ...formData, meta_cantidad: e.target.value })}
          />
        </label>
        <label>
          Filtro:
          <input
            type="text"
            value={formData.filtro || ''}
            onChange={(e) => setFormData({ ...formData, filtro: e.target.value })}
          />
        </label>
        <label>
          Incentivo:
          <input
            type="number"
            value={formData.incentivo || ''}
            onChange={(e) => setFormData({ ...formData, incentivo: e.target.value })}
          />
        </label>
        
        {/* Botón para enviar el formulario */}
        <button type="submit">Guardar</button>
      </form>

      {/* Lista para mostrar los datos y botones de editar y eliminar */}
      <ul>
        {data.map(item => (
          <li key={item.meta_id}>
            <span>Empleado ID: {item.empleado_id}, Meta Cantidad: {item.meta_cantidad}, Filtro: {item.filtro}, Incentivo: {item.incentivo}</span>
            
            {/* Botones para editar y eliminar elementos */}
            <button onClick={() => setFormData(item)}>Editar</button>
            <button onClick={() => deleteItem(item.meta_id)}>Eliminar</button>
          </li>
        ))}
      </ul>
    </div>
  );
};

export default CrudComponent;
