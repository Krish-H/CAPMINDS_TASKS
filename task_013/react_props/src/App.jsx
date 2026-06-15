import React, { useState } from "react";
import DataTypesDemo from "./components/DataTypesDemo.jsx";

function App() {
  const [stringVal, setStringVal] = useState("Hello ");
  const [numberVal, setNumberVal] = useState(42);
  const [booleanVal, setBooleanVal] = useState(true);
  const [arrayVal, setArrayVal] = useState(["apple", "banana"]);
  const [objectVal, setObjectVal] = useState({ id: 1, name: "Mark" });
  const [functionVal] = useState(() => () => alert("Function called!"));
  const [nullVal, setNullVal] = useState(null);
  const [undefinedVal, setUndefinedVal] = useState(undefined);

  return (
    <div style={{ padding: "20px" }}>
      <h1>React useState + Props</h1>

      {/* Input fields to update state */}
      <div style={{ marginBottom: "20px" }}>
        <label>
          String: 
          <input 
            type="text" 
            value={stringVal} 
            onChange={(e) => setStringVal(e.target.value)} 
          />
        </label>
        <br />

        <label>
          Number: 
          <input 
            type="number" 
            value={numberVal} 
            onChange={(e) => setNumberVal(Number(e.target.value))} 
          />
        </label>
        <br />

        <label>
          Boolean: 
          <select 
            value={booleanVal} 
            onChange={(e) => setBooleanVal(e.target.value === "true")}
          >
            <option value="true">True</option>
            <option value="false">False</option>
          </select>
        </label>
        <br />

        <label>
          Array (comma separated): 
          <input 
            type="text" 
            value={arrayVal.join(",")} 
            onChange={(e) => setArrayVal(e.target.value.split(","))} 
          />
        </label>
        <br />

        <label>
          Object Name: 
          <input 
            type="text" 
            value={objectVal.name} 
            onChange={(e) => setObjectVal({ ...objectVal, name: e.target.value })} 
          />
        </label>
        <br />

        <label>
          Null (type "null" to set): 
          <input 
            type="text" 
            value={nullVal === null ? "" : nullVal} 
            onChange={(e) => setNullVal(e.target.value === "null" ? null : e.target.value)} 
          />
        </label>
        <br />

        <label>
          Undefined (type "undefined" to set): 
          <input 
            type="text" 
            value={undefinedVal === undefined ? "" : undefinedVal} 
            onChange={(e) => setUndefinedVal(e.target.value === "undefined" ? undefined : e.target.value)} 
          />
        </label>
      </div>

  
      <DataTypesDemo
        stringProp={stringVal}
        numberProp={numberVal}
        booleanProp={booleanVal}
        arrayProp={arrayVal}
        objectProp={objectVal}
        functionProp={functionVal}
        nullProp={nullVal}
        undefinedProp={undefinedVal}
      />
    </div>
  );
}

export default App;
