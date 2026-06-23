import React, { useEffect } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { fetchPatients, fetchPatientDetails } from '../redux/actions';
import { changePage } from '../redux/patientSlice';
import { Users, ChevronLeft, ChevronRight, Loader } from 'lucide-react';

const PatientList = () => {
  const dispatch = useDispatch();
  const { patients, loading, currentPage, pageSize, selectedPatient } = useSelector(
    (state) => state.patients
  );

  useEffect(() => {
    dispatch(fetchPatients());
  }, [dispatch]);

  // Pagination optimization: slice data from Redux store locally
  const indexOfLastItem = currentPage * pageSize; // 1*5
  const indexOfFirstItem = indexOfLastItem - pageSize; // 0
  const currentPatients = patients.slice(indexOfFirstItem, indexOfLastItem); // [0, 5] for page 1, [5, 10] for page 2
  const totalPages = Math.ceil(patients.length / pageSize);

  const handleNextPage = () => {
    if (currentPage < totalPages) {
      dispatch(changePage(currentPage + 1));
    }
  };

  const handlePrevPage = () => {
    if (currentPage > 1) {
      dispatch(changePage(currentPage - 1));
    }
  };

  const handlePatientClick = (id) => {
    dispatch(fetchPatientDetails(id));
  };

  return (
    <div className="card">
      <h2 className="card-title">
        <Users size={20} className="text-primary" />
        Patient List
        {loading && patients.length === 0 && <Loader size={16} className="spinner ml-2" />}
      </h2>

      {loading && patients.length === 0 ? (
        <div style={{ padding: '2rem', textAlign: 'center', color: 'var(--text-secondary)' }}>
          Loading patients from API...
        </div>
      ) : (
        <>
          <div className="patient-list">
            {currentPatients.map((patient) => (
              <div
                key={patient.id}
                className={`patient-card ${selectedPatient?.id === patient.id ? 'active' : ''}`}
                onClick={() => handlePatientClick(patient.id)}
              >
                <div className="patient-info">
                  <h3>{patient.name}</h3>
                  <p>Age: {patient.age} | {patient.doctor}</p>
                </div>
                <div className="patient-meta">
                  <span className="badge">{patient.disease}</span>
                </div>
              </div>
            ))}
          </div>

          {patients.length > 0 && (
            <div className="pagination">
              <button
                className="pagination-btn"
                onClick={handlePrevPage}
                disabled={currentPage === 1}
              >
                <ChevronLeft size={16} /> Prev
              </button>
              <span style={{ fontSize: '0.875rem', color: 'var(--text-secondary)' }}>
                Page {currentPage} of {totalPages}
              </span>
              <button
                className="pagination-btn"
                onClick={handleNextPage}
                disabled={currentPage === totalPages}
              >
                Next <ChevronRight size={16} />
              </button>
            </div>
          )}
        </>
      )}
    </div>
  );
};

export default PatientList;
