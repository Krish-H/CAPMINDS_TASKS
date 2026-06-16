import { useContext } from 'react';
import { PatientContext } from '../context/PatientContext.jsx';

const PatientInfo = () => {
  // useContext accesses the PatientContext to get the global patientData.
  const patientData = useContext(PatientContext);

  return (
    <div className="card section">
      <h3>1. Patient Information (useContext)</h3>
      {patientData ? (
        <div className="info-box">
          <p><strong>Patient Name:</strong> {patientData.name}</p>
          <p><strong>Patient Email:</strong> {patientData.email}</p>
        </div>
      ) : (
        <p>No patient data found.</p>
      )}
    </div>
  );
};

export default PatientInfo;
