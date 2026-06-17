import React from 'react';

// 6. React.memo Practice: 
// Separate component wrapped in React.memo
function PatientCard({ patient, onClick }) {
  // 7. Performance Learning: Observe when PatientCard renders
  console.log(`PatientCard rendered for: ${patient.name}`);

  return (
    <div className="patient-card">
      <div className="patient-header">
        <div className="patient-name">{patient.name}</div>
        <div className="patient-id">ID: #{patient.id}</div>
      </div>
      
      <div className="patient-details">
        <div className="patient-detail-row">
          <span className="detail-label">Age:</span>
          <span className="detail-value">{patient.age} years</span>
        </div>
        <div className="patient-detail-row">
          <span className="detail-label">Condition:</span>
          <span className="detail-value">{patient.disease}</span>
        </div>
      </div>

      <button 
        className="btn btn-primary"
        onClick={() => onClick(patient.id)}
      >
        View Patient
      </button>
    </div>
  );
}

// React.memo prevents re-rendering if props (patient object and onClick callback) haven't changed
export default React.memo(PatientCard);
