import React, { useState } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { submitPatientForm } from '../redux/actions';
import { UserPlus, Save, AlertCircle } from 'lucide-react';

const PatientForm = () => {
  const dispatch = useDispatch();
  const { offlineQueue, loading } = useSelector((state) => state.patients);
  const [formData, setFormData] = useState({
    name: '',
    age: '',
    disease: '',
    doctor: '',
  });

  const handleChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    if (!formData.name || !formData.age || !formData.disease || !formData.doctor) return;

    dispatch(submitPatientForm(formData));
    setFormData({ name: '', age: '', disease: '', doctor: '' });
  };

  return (
    <div className="card">
      <h2 className="card-title">
        <UserPlus size={20} />
        Register Patient
      </h2>

      {offlineQueue.length > 0 && (
        <div className="offline-queue-alert">
          <AlertCircle size={18} />
          You are offline. {offlineQueue.length} form(s) are queued and will be submitted automatically when connection is restored.
        </div>
      )}

      <form onSubmit={handleSubmit}>
        <div className="form-group">
          <label className="form-label">Patient Name</label>
          <input
            type="text"
            name="name"
            value={formData.name}
            onChange={handleChange}
            className="form-input"
            placeholder="John Doe"
            disabled={loading}
          />
        </div>

        <div className="form-group">
          <label className="form-label">Age</label>
          <input
            type="number"
            name="age"
            value={formData.age}
            onChange={handleChange}
            className="form-input"
            placeholder="45"
            disabled={loading}
          />
        </div>

        <div className="form-group">
          <label className="form-label">Disease</label>
          <input
            type="text"
            name="disease"
            value={formData.disease}
            onChange={handleChange}
            className="form-input"
            placeholder="Diabetes"
            disabled={loading}
          />
        </div>

        <div className="form-group">
          <label className="form-label">Doctor Assigned</label>
          <input
            type="text"
            name="doctor"
            value={formData.doctor}
            onChange={handleChange}
            className="form-input"
            placeholder="Dr. Smith"
            disabled={loading}
          />
        </div>

        <button type="submit" className="btn btn-primary" disabled={loading} style={{ width: '100%' }}>
          <Save size={16} />
          {loading ? 'Submitting...' : 'Register Patient'}
        </button>
      </form>
    </div>
  );
};

export default PatientForm;
