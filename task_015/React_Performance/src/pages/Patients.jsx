import React, { useState, useMemo, useCallback } from 'react';
import PatientCard from '../components/PatientCard';

import { patients } from '../data/patients';

function Patients() {
  const [search, setSearch] = useState("");

  // 7. Performance Learning: Observe when Patients component renders
  console.log('Patients page rendered');

  // 5. useCallback Practice: Optimize search handler
  // Prevent creating a new function instance on every render
  const handleSearch = useCallback((e) => {
    setSearch(e.target.value);
  }, []);

  // 5. useCallback Practice: Optimize button click handler
  // This stable callback prevents PatientCard (React.memo) from re-rendering
  // when the search state changes, because the onClick prop stays identical.
  const handleViewPatient = useCallback((id) => {
    console.log(`Viewing details for patient ID: ${id}`);
    alert(`View action triggered for Patient #${id}`);
  }, []);

  // 4. Patients Page: Use useMemo() for filtering
  // This computation only runs when 'search' or 'patients' changes
  const filteredPatients = useMemo(() => {
    console.log('Filtering patients...');
    return patients.filter(patient =>
      patient.name.toLowerCase().includes(search.toLowerCase())
    );
  }, [search, patients]);

  return (
    <div className="patients-page">
      <div className="page-header">
        <h1 className="page-title">Patient Records</h1>
      </div>

      <div className="search-container">
        <input 
          type="text" 
          className="search-input"
          placeholder="Search patients by name..." 
          value={search}
          onChange={handleSearch}
        />
      </div>

      <div className="patients-grid">
        {filteredPatients.length > 0 ? (
          filteredPatients.map(patient => (
            <PatientCard 
              key={patient.id} 
              patient={patient} 
              onClick={handleViewPatient} 
            />
          ))
        ) : (
          <p>No patients found matching your search.</p>
        )}
      </div>
    </div>
  );
}

export default Patients;
