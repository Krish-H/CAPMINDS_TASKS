import { PatientProvider } from './context/PatientContext.jsx';
import Navbar from './components/Navbar.jsx';
import PatientInfo from './components/PatientInfo.jsx';
import PatientList from './components/PatientList.jsx';
import SymptomTracker from './components/SymptomTracker.jsx';
import HookExperiment from './components/HookExperiment.jsx';



function App() {
  return (
    // Wrap the application with PatientProvider to make context available to all children
    <PatientProvider>
      <div className="app-container">
        <Navbar />

        <main className="dashboard">
          <header className="dashboard-header">
            <h1>Patient Data Dashboard</h1>
            <p>React Hooks Demonstration (useContext, useEffect, useRef, useState)</p>
          </header>

          <div className="dashboard-grid">
            <div className="dashboard-column">
              <PatientInfo />
              <PatientList />
            </div>

            <div className="dashboard-column">
              <SymptomTracker />
              <HookExperiment />
            </div>
          </div>
        </main>
      </div>
      
      
    </PatientProvider>

  );
}

export default App;