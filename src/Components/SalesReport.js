import React, { useState, useEffect } from 'react';
import axios from 'axios';
import { Doughnut, Bar } from 'react-chartjs-2';
import Chart from 'chart.js/auto';  // Importa la biblioteca Chart.js
import '../SalesReport.css';  // Importa estilos CSS específicos para SalesReport

// Componente funcional que representa el informe de ventas
const SalesReport = () => {
  // Estados para gestionar la fecha de inicio, fecha de fin y datos del informe
  const [startDate, setStartDate] = useState('');
  const [endDate, setEndDate] = useState('');
  const [totalSales, setTotalSales] = useState(null);
  const [salesBySede, setSalesBySede] = useState(null);
  const [productData, setProductData] = useState(null);
  const [incentivosData, setIncentivosData] = useState(null);
  const [pieChart, setPieChart] = useState(null);
  const [barChart, setBarChart] = useState(null);
  const [pieChartId, setPieChartId] = useState(null);
  const [barChartId, setBarChartId] = useState(null);

  // Efecto para destruir los gráficos anteriores al cambiar los datos de ventas
  useEffect(() => {
    // Función para destruir un gráfico por su ID
    const destroyChart = (chartId, setChartId) => {
      if (chartId !== null) {
        const existingChart = Chart.getChart(chartId);
        existingChart.destroy();
        setChartId(null);
      }
    };

    // Destruir los gráficos anteriores
    destroyChart(pieChartId, setPieChartId);
    destroyChart(barChartId, setBarChartId);

    // Verificar si hay datos de ventas por sede
    if (salesBySede !== null) {
      // Crear datos para el gráfico de Pie
      const pieChartData = {
        labels: salesBySede.map((sede) => `Sede ${sede.sede_id}`),
        datasets: [
          {
            data: salesBySede.map((sede) => sede.totalVentas),
            backgroundColor: [
              'rgba(255, 99, 132, 0.7)',
              'rgba(54, 162, 235, 0.7)',
              'rgba(255, 206, 86, 0.7)',
              'rgba(75, 192, 192, 0.7)',
              'rgba(153, 102, 255, 0.7)',
            ],
          },
        ],
      };

      // Crear datos para el gráfico de Barras
      const barChartData = {
        labels: salesBySede.map((sede) => `Sede ${sede.sede_id}`),
        datasets: [
          {
            label: 'Ventas por Sede',
            data: salesBySede.map((sede) => sede.totalVentas),
            backgroundColor: 'rgba(75, 192, 192, 0.7)',
          },
        ],
      };

      // Establecer los nuevos datos para los gráficos
      setPieChart(pieChartData);
      setBarChart(barChartData);
    }
  }, [salesBySede, pieChartId, barChartId]);

  // Función para manejar la obtención de las ventas
  const handleGetSales = async () => {
    try {
      // Realizar una solicitud al backend para obtener los datos de ventas
      const response = await axios.post(
        'http://localhost/proyecto/backend/getSales.php',
        null,
        {
          params: {
            fecha_venta_inicio: startDate,
            fecha_venta_fin: endDate,
          },
        }
      );

      console.log('Respuesta completa del servidor:', response);

      // Verificar si la respuesta contiene datos de ventas por sede
      if (response.data && response.data.salesBySede && response.data.salesBySede.length > 0) {
        // Calcular el total de las ventas
        const newTotalSales = response.data.salesBySede.reduce(
          (total, sede) => total + Number(sede.totalVentas),
          0
        );
        setTotalSales(newTotalSales);
        setSalesBySede(response.data.salesBySede);
      } else {
        console.error('La respuesta del servidor no contiene datos de ventas');
      }

      // Verificar si la respuesta contiene datos del producto más vendido
      if (response.data && response.data.productData) {
        setProductData(response.data.productData);
      } else {
        console.error('La respuesta del servidor no contiene datos del producto más vendido');
      }

      // Verificar si la respuesta contiene datos de incentivos para empleados
      if (response.data && response.data.incentivos) {
        setIncentivosData(response.data.incentivos);
      } else {
        console.error('La respuesta del servidor no contiene datos de incentivos');
      }
    } catch (error) {
      console.error('Error al obtener las ventas', error);
    }
  };

  // Renderizar el componente
  return (
    <div>
      <h2>Informe de Ventas</h2>
      {/* Sección para seleccionar las fechas y obtener las ventas */}
      <div>
        <label>
          Fecha de Inicio:
          <input
            type="date"
            value={startDate}
            onChange={(e) => setStartDate(e.target.value)}
          />
        </label>
        <label>
          Fecha de Fin:
          <input
            type="date"
            value={endDate}
            onChange={(e) => setEndDate(e.target.value)}
          />
        </label>
        <button onClick={handleGetSales}>Obtener Ventas</button>
      </div>

      {/* Sección para mostrar el total de ventas */}
      {totalSales !== null && (
        <div>
          <p>Ventas Totales: {totalSales}</p>
          <p>
            Fechas Seleccionadas: Desde {startDate} hasta {endDate}
          </p>
        </div>
      )}

      {/* Sección para mostrar las ventas por sede */}
      {salesBySede !== null && (
        <div>
          <h3>Ventas por Sede:</h3>
          <ul>
            {salesBySede.map((sede) => (
              <li key={sede.sede_id}>
                Sede {sede.sede_id} ({sede.ubicacion}): {sede.totalVentas}
              </li>
            ))}
          </ul>
        </div>
      )}

      {/* Sección para mostrar el producto más vendido */}
      {productData !== null && (
        <div>
          <h3>Producto más vendido entre todas las sedes:</h3>
          <p>{productData.producto_mas_vendido}</p>
        </div>
      )}

      {/* Sección para mostrar los incentivos para empleados */}
      {incentivosData !== null && (
        <div>
          <h3>Incentivos para Empleados:</h3>
          <ul>
            {incentivosData.map((incentivo) => (
              <li key={incentivo.empleado_id}>
                Empleado ID: {incentivo.empleado_id}, Nombre: {incentivo.nombre_empleado},{' '}
                {incentivo.cumple_meta ? `Cumple Meta, Incentivo: ${incentivo.incentivo}` : 'No Cumple Meta'}
              </li>
            ))}
          </ul>
        </div>
      )}

      {/* Sección para mostrar el gráfico de Pie */}
      {pieChart !== null && (
        <div>
          <h3>Gráfico de Pie (Ventas por Sede):</h3>
          <Doughnut data={pieChart} />
        </div>
      )}

      {/* Sección para mostrar el gráfico de Barras */}
      {barChart !== null && (
        <div>
          <h3>Gráfico de Barras (Ventas por Sede):</h3>
          <Bar data={barChart} />
        </div>
      )}
    </div>
  );
};

// Exporta el componente para su uso en otras partes de la aplicación
export default SalesReport;
