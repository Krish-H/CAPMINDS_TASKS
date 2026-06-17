import React, { useState, useMemo, useCallback } from 'react';
import PatientCard from '../components/PatientCard';

// 4. Patients Page: Dummy patient data with 10-15 objects
const initialPatients = [
  { id: 1, name: "John Doe", age: 45, disease: "Diabetes" },
  { id: 2, name: "Jane Smith", age: 32, disease: "Hypertension" },
  { id: 3, name: "Robert Johnson", age: 58, disease: "Asthma" },
  { id: 4, name: "Emily Davis", age: 29, disease: "Migraine" },
  { id: 5, name: "Michael Wilson", age: 64, disease: "Arthritis" },
  { id: 6, name: "Sarah Brown", age: 41, disease: "Thyroid Disorder" },
  { id: 7, name: "David Miller", age: 53, disease: "High Cholesterol" },
  { id: 8, name: "Lisa Taylor", age: 37, disease: "Anemia" },
  { id: 9, name: "James Anderson", age: 71, disease: "Coronary Artery Disease" },
  { id: 10, name: "Mary Thomas", age: 25, disease: "Eczema" },
  { id: 11, name: "William Jackson", age: 49, disease: "Gerd" },
  { id: 12, name: "Linda White", age: 62, disease: "Osteoporosis" },
  { id: 13, name: "Richard Harris", age: 34, disease: "Allergies" },
  { id: 14, name: "Susan Martin", age: 55, disease: "Glaucoma" }
];

function Patients() {
  const [patients] = useState(initialPatients);
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
