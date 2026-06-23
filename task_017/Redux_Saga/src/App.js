import React, { useEffect } from 'react';
import { useDispatch } from 'react-redux';
import { setNetworkStatus } from './redux/patientSlice';
import { processQueue } from './redux/actions';
import Sidebar from './components/Sidebar';
import Header from './components/Header';
import PatientList from './components/PatientList';
import PatientForm from './components/PatientForm';
import PatientDetails from './components/PatientDetails';

function App() {
  const dispatch = useDispatch();

  useEffect(() => {
    const handleOnline = () => {
      dispatch(setNetworkStatus(true));
      // Try to process queue when network restores
      dispatch(processQueue());
    };

    const handleOffline = () => {
      dispatch(setNetworkStatus(false));
    };

    window.addEventListener('online', handleOnline);
    window.addEventListener('offline', handleOffline);

    // Initial check just in case
    if (navigator.onLine) {
      dispatch(processQueue());
    }

    return () => {
      window.removeEventListener('online', handleOnline);
      window.removeEventListener('offline', handleOffline);
    };
  }, [dispatch]);

  return (
    <div className="dashboard-container">
      <Sidebar />
      <div className="main-content">
        <Header />
        <div className="content-grid">
          <div style={{ display: 'flex', flexDirection: 'column', gap: '2rem' }}>
            <PatientList />
          </div>
          <div style={{ display: 'flex', flexDirection: 'column', gap: '2rem' }}>
            <PatientDetails />
            <PatientForm />
          </div>
        </div>
      </div>
    </div>
  );
}

export default App;
