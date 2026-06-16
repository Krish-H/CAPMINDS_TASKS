import { useState, useRef, useEffect } from 'react';

const HookExperiment = () => {
  // useState manages a state variable. Updating it causes the component to re-render.
  const [stateValue, setStateValue] = useState("");
  
  // useRef holds a mutable value that persists across renders.
  // Updating the .current property does NOT trigger a component re-render.
  const refValue = useRef("");

  // This will log every time the component renders
  useEffect(() => {
    console.log("Component Rendered");
  });

  const handleStateChange = (e) => {
  
    setStateValue(e.target.value);
  };

  const handleRefChange = (e) => {
    refValue.current = e.target.value;
    // We log here because the change won't trigger a re-render to show up in UI immediately
    console.log("Ref value changed to:", refValue.current);
  };

  const checkRefValue = () => {
    alert(`Current Ref Value is: ${refValue.current}`);
  };

  return (
    <div className="card section">
      <h3>4. Hook Experiment (useState vs useRef)</h3>
      <p className="description">Check the console to see when the component re-renders.</p>
      
      <div className="experiment-grid">
        <div className="experiment-box">
          <h4>Input 1: useState</h4>
          <p>Typing here triggers re-renders</p>
          <input 
            type="text" 
            value={stateValue} 
            onChange={handleStateChange}
            placeholder="Type here..."
          />
          <p>State Value: <strong>{stateValue}</strong></p>
        </div>

        <div className="experiment-box">
          <h4>Input 2: useRef</h4>
          <p>Typing here DOES NOT trigger re-renders</p>
          <input 
            type="text" 
            onChange={handleRefChange}
            placeholder="Type here..."
          />
          <button onClick={checkRefValue} className="btn-secondary mt-2">Check Ref Value</button>
        </div>
      </div>
    </div>
  );
};

export default HookExperiment;
