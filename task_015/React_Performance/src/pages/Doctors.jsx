import React, { useState } from 'react';

const dummyDoctors = [
  { id: 101, name: "Dr. Alice Morgan", specialty: "Cardiology", experience: "15 years" },
  { id: 102, name: "Dr. Brian Lee", specialty: "Neurology", experience: "12 years" },
  { id: 103, name: "Dr. Catherine Woods", specialty: "Pediatrics", experience: "8 years" },
  { id: 104, name: "Dr. Daniel Kim", specialty: "Orthopedics", experience: "20 years" },
  { id: 105, name: "Dr. Evelyn Ross", specialty: "Dermatology", experience: "10 years" }
];

function Doctors() {
  const [doctors] = useState(dummyDoctors);

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
