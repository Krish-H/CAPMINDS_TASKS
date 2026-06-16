import { useContext } from 'react';
import { PatientContext } from '../context/PatientContext.jsx';

const Navbar = () => {
  // useContext accepts a context object and returns the current context value.
  // We use this to get the global patient data without passing props.
  const patientData = useContext(PatientContext);

  return (
    <nav className="navbar">
      <h2>Patient Dashboard</h2>
      <div className="welcome-message">
        Welcome {patientData ? patientData.name : 'Guest'}
      </div>
    </nav>
  );
};

export default Navbar;
