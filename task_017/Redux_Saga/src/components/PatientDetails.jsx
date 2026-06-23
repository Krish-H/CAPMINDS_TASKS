import React from 'react';
import { useSelector, useDispatch } from 'react-redux';
import { FileText, Loader, X } from 'lucide-react';
import { clearSelectedPatient } from '../redux/patientSlice';

const PatientDetails = () => {
  const dispatch = useDispatch();
  const { selectedPatient, detailsLoading } = useSelector((state) => state.patients);

  if (detailsLoading) {
    return (
      <div className="card">
        <h2 className="card-title">
          <FileText size={20} />
          Patient Details
        </h2>
        <div className="details-empty">
          <Loader className="spinner" size={32} />
          <p style={{ marginTop: '1rem' }}>Loading details from remote server...</p>
          <small>Try clicking another patient quickly to test cancellation!</small>
        </div>
      </div>
    );
  }

  if (!selectedPatient) {
    return (
      <div className="card">
        <h2 className="card-title">
          <FileText size={20} />
          Patient Details
        </h2>
        <div className="details-empty">
          <FileText size={48} opacity={0.2} />
          <p style={{ marginTop: '1rem' }}>Select a patient from the list to view details</p>
        </div>
      </div>
    );
  }

  return (
    <div className="card details-content">
      <div className="card-title" style={{ justifyContent: 'space-between' }}>
        <div style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}>
          <FileText size={20} className="text-primary" />
          Patient Details
        </div>
        <button
          onClick={() => dispatch(clearSelectedPatient())}
          style={{ background: 'none', border: 'none', color: 'var(--text-secondary)', cursor: 'pointer' }}
        >
          <X size={20} />
        </button>
      </div>

      <div className="details-row">
        <span className="details-label">Full Name</span>
        <span className="details-value">{selectedPatient.name}</span>
      </div>
      <div className="details-row">
        <span className="details-label">Email</span>
        <span className="details-value">{selectedPatient.email}</span>
      </div>
      <div className="details-row">
        <span className="details-label">Phone</span>
        <span className="details-value">{selectedPatient.phone}</span>
      </div>
      <div className="details-row">
        <span className="details-label">Website</span>
        <span className="details-value">{selectedPatient.website}</span>
      </div>
      <div className="details-row">
        <span className="details-label">Company</span>
        <span className="details-value">{selectedPatient.company?.name}</span>
      </div>
    </div>
  );
};

export default PatientDetails;
