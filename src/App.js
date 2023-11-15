import './App.css';
import { BrowserRouter as Router, Route, Routes } from 'react-router-dom';
import NavComponent from './Components/NavComponent';
import SalesReport from './Components/SalesReport';
import CrudComponent from './Components/CrudComponent';

function App() {
  return (
    <div className="App">
      <header className="App-header">
        <Router>
          <div>
            <NavComponent />
            <Routes>
              <Route path="/Components/SalesReport" element={<SalesReport />} />
              <Route path="/Components/CrudComponent" element={<CrudComponent />} />
            </Routes>
          </div>
        </Router>
      </header>
    </div>
  );
}

export default App;
