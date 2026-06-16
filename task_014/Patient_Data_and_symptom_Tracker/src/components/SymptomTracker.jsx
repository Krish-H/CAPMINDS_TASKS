import { useState, useEffect, useRef } from 'react';

const SymptomTracker = () => {
  // useState manages the list of symptoms added
  const [symptoms, setSymptoms] = useState(["Headache", "Fever"]);
  
  // useRef provides a way to access the DOM element directly.
  // It persists between renders but does not trigger a re-render when its value changes.
  const inputRef = useRef(null);

  // useEffect runs after the component renders.
  // The empty dependency array [] means it runs only once on mount.
  // We use it here to automatically focus the input field when the component loads.
  useEffect(() => {
    if (inputRef.current) {
      inputRef.current.focus();
    }
  }, []);

  const handleAddSymptom = () => {
    const newSymptom = inputRef.current.value.trim();
    if (newSymptom) {
      // Add the new symptom to the array
      setSymptoms(prevSymptoms => [...prevSymptoms, newSymptom]);
      // Clear the input manually using the ref
      inputRef.current.value = "";
      // Keep focus on input after adding
      inputRef.current.focus();
    }
  };

  const handleKeyDown = (e) => {
    if (e.key === 'Enter') {
      handleAddSymptom();
    }
  };

  return (
    <div className="card section">
      <h3>3. Symptom Tracker (useRef + useState)</h3>
      <div className="input-group">
        <input 
          type="text" 
          ref={inputRef} 
          onKeyDown={handleKeyDown}
          placeholder="Enter a symptom..." 
          className="symptom-input"
        />
        <button onClick={handleAddSymptom} className="btn-primary">Add</button>
      </div>
      
      <div className="symptoms-list">
        <h4>Symptoms:</h4>
        {symptoms.length > 0 ? (
          <ul>
            {symptoms.map((symptom, index) => (
              <li key={index}>{symptom}</li>
            ))}
          </ul>
        ) : (
          <p>No symptoms recorded.</p>
        )}
      </div>
    </div>
  );
};

export default SymptomTracker;
