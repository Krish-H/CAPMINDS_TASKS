import React, { Suspense } from 'react';
import { BrowserRouter as Router, Routes, Route, NavLink } from 'react-router-dom';
import Dashboard from './pages/Dashboard';

// 2. Code splitting / Lazy Loading:
// Lazy load ONLY Patients and Doctors components
const Patients = React.lazy(() => import('./pages/Patients'));
const Doctors = React.lazy(() => import('./pages/Doctors'));

function App() {
  return (
    <Router>
      <div className="app-container">
        {/* Sidebar Navigation */}
        <aside className="sidebar">
          <div className="sidebar-title">
            MediAdmin
          </div>
          <nav className="nav-links">
            <NavLink 
              to="/" 
              className={({ isActive }) => isActive ? "nav-link active" : "nav-link"}
              end
            >
              Dashboard
            </NavLink>
            <NavLink 
              to="/patients" 
              className={({ isActive }) => isActive ? "nav-link active" : "nav-link"}
            >
              Patients
            </NavLink>
            <NavLink 
              to="/doctors" 
              className={({ isActive }) => isActive ? "nav-link active" : "nav-link"}
            >
              Doctors
            </NavLink>
          </nav>
        </aside>

        {/* Main Content Area */}
        <main className="main-content">
          <Routes>
            <Route path="/" element={<Dashboard />} />
            
            <Route path="/patients" element={
              // Show a loading fallback while components load
              <Suspense fallback={
                <div className="loading-fallback">
                  <div className="spinner"></div>
                  Loading Patients Data...
                </div>
              }>
                <Patients />
              </Suspense>
            } />
            
            <Route path="/doctors" element={
              <Suspense fallback={
                <div className="loading-fallback">
                  <div className="spinner"></div>
                  Loading Doctors Data...
                </div>
              }>
                <Doctors />
              </Suspense>
            } />
          </Routes>
        </main>
      </div>
    </Router>
  );
}

export default App;