import React from 'react';
import Button from '@mui/material/Button';
import Menu from '@mui/material/Menu';
import MenuItem from '@mui/material/MenuItem';
import PopupState, { bindTrigger, bindMenu } from 'material-ui-popup-state';
import { Link } from 'react-router-dom';

// Componente funcional que representa la barra de navegación
const NavComponent = () => {
  return (
    // Utiliza el componente PopupState para manejar el estado del menú emergente
    <PopupState variant="popover" popupId="demo-popup-menu">
      {(popupState) => (
        <>
          {/* Botón que activa el menú emergente */}
          <Button variant="contained" {...bindTrigger(popupState)}>
            Menu
          </Button>

          {/* Menú emergente */}
          <Menu {...bindMenu(popupState)}>
            {/* Elemento de menú que enlaza a la ruta "/Components/SalesReport" */}
            <MenuItem component={Link} to="/Components/SalesReport" onClick={popupState.close}>
              Reporte de Ventas
            </MenuItem>

            {/* Elemento de menú que enlaza a la ruta "/Components/CrudComponent" */}
            <MenuItem component={Link} to="/Components/CrudComponent" onClick={popupState.close}>
              Registro
            </MenuItem>
          </Menu>
        </>
      )}
    </PopupState>
  );
};

// Exporta el componente para su uso en otras partes de la aplicación
export default NavComponent;
