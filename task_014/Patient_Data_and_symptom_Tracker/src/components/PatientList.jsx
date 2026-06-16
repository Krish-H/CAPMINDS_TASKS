import { useState, useEffect } from 'react';
import axios from 'axios';

const PatientList = () => {

  const [patients, setPatients] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  const API_URL = 'https://jsonplaceholder.typicode.com/users';

  useEffect(() => {
    axios.get(API_URL)
      .then(response => {
        setPatients(response.data);
        setLoading(false);
      })
      .catch(err => {
        console.error("Error fetching patients", err);
        setError("Failed to load patient list.");
        setLoading(false);
      });
  }, []);



  return (
    <div className="card section">
      <h3>2. API Patient List (useEffect + Axios)</h3>
      {loading && <p>Loading patients...</p>}
      {error && <p className="error">{error}</p>}

      {!loading && !error && (
        <ul className="patient-list">
          {patients.slice(0, 5).map(patient => (
            <li key={patient.id}>
              {patient.name} ({patient.email})
            </li>
          ))}
        </ul>
      )}
    </div>
  );
};

export default PatientList;
