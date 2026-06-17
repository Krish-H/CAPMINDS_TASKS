import React from 'react';
import { doctors } from '../data/doctors';

function Doctors() {

  // 7. Performance Learning: Observe when Doctors component renders
  console.log('Doctors page rendered');

  return (
    <div className="doctors-page">
      <div className="page-header">
        <h1 className="page-title">Medical Staff</h1>
      </div>

      <div className="patients-grid">
        {doctors.map(doctor => (
          <div key={doctor.id} className="patient-card">
            <div className="patient-header">
              <div className="patient-name">{doctor.name}</div>
              <div className="patient-id">ID: #{doctor.id}</div>
            </div>
            
            <div className="patient-details">
              <div className="patient-detail-row">
                <span className="detail-label">Specialty:</span>
                <span className="detail-value">{doctor.specialty}</span>
              </div>
              <div className="patient-detail-row">
                <span className="detail-label">Experience:</span>
                <span className="detail-value">{doctor.experience}</span>
              </div>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}

export default Doctors;
