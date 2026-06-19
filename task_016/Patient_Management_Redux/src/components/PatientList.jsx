import { usePatient } from "../hooks/usePatient";

// Helper to get initials for the avatar
const getInitials = (name) => {
  return name
    .split(' ')
    .map(word => word[0])
    .join('')
    .substring(0, 2)
    .toUpperCase();
};

const PatientList = () => {
  const { patients, deletePatient } = usePatient();

  console.log("PatientList Component rendered");

  // useSelector detects change -> Component re-render
  return (
    <div className="patient-list-container card">
      <h2 className="section-header">
        Patient Records
      </h2>
      
      {patients.length === 0 ? (
        <div className="empty-message">
          <p>No patients have been registered yet.<br/>Add a patient above to get started.</p>
        </div>
      ) : (
        <div className="patient-cards">
          {patients.map((patient) => (
            <div key={patient.id} className="patient-card">
              <div className="patient-info-wrapper">
                <div className="patient-avatar">
                  {getInitials(patient.name)}
                </div>
                <div className="patient-info">
                  <span className="patient-name">{patient.name}</span>
                  <span className="patient-id">ID: {patient.id.toString().slice(-6)}</span>
                </div>
              </div>
              <button
                onClick={() => deletePatient(patient.id)}
                className="btn btn-danger"
                title="Remove Patient"
              >
                Delete
              </button>
            </div>
          ))}
        </div>
      )}
    </div>
  );
};

export default PatientList;
