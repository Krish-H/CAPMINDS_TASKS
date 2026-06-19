import { useState } from "react";
import { usePatient } from "../hooks/usePatient";

const AddPatient = () => {
  const [name, setName] = useState("");
  const { addPatient } = usePatient();

  const handleSubmit = (e) => {
    e.preventDefault();
    if (!name.trim()) return;

    // Component -> dispatch(action) -> createSlice reducer -> Redux store update
    addPatient({
      id: Date.now(),
      name: name.trim(),
    });

    setName("");
  };

  console.log("AddPatient Component rendered");

  return (
    <div className="add-patient-container card">
      <h2 className="section-header">
        Register Patient
      </h2>
      <form onSubmit={handleSubmit} className="add-patient-form">
        <div className="input-wrapper">
          <input
            type="text"
            value={name}
            onChange={(e) => setName(e.target.value)}
            placeholder="Enter full name..."
            className="patient-input"
          />
        </div>
        <button type="submit" className="btn btn-primary" disabled={!name.trim()}>
          Add Record
        </button>
      </form>
    </div>
  );
};

export default AddPatient;
