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
    <div className="app-shell">
      <header className="app-hero">
        <div>
          <p className="eyebrow">React Demo</p>
          <h1>React useState + Props</h1>
          <p className="app-subtitle">
            Interactive props showcase for strings, numbers, booleans, arrays,
            objects, functions, null, and undefined.
          </p>
        </div>
      </header>

      <section className="form-panel">
        <h2>Update prop values</h2>
        <div className="form-grid">
          <label className="field-group">
            <span>String</span>
            <input
              type="text"
              value={stringVal}
              onChange={(e) => setStringVal(e.target.value)}
            />
          </label>

          <label className="field-group">
            <span>Number</span>
            <input
              type="number"
              value={numberVal}
              onChange={(e) => setNumberVal(Number(e.target.value))}
            />
          </label>

          <label className="field-group">
            <span>Boolean</span>
            <select
              value={booleanVal}
              onChange={(e) => setBooleanVal(e.target.value === "true")}
            >
              <option value="true">True</option>
              <option value="false">False</option>
            </select>
          </label>

          <label className="field-group">
            <span>Array</span>
            <input
              type="text"
              value={arrayVal.join(",")}
              onChange={(e) => setArrayVal(e.target.value.split(","))}
            />
          </label>

          <label className="field-group">
            <span>Object Name</span>
            <input
              type="text"
              value={objectVal.name}
              onChange={(e) => setObjectVal({ ...objectVal, name: e.target.value })}
            />
          </label>

          <label className="field-group">
            <span>Null</span>
            <input
              type="text"
              value={nullVal === null ? "" : nullVal}
              onChange={(e) => setNullVal(e.target.value === "null" ? null : e.target.value)}
            />
          </label>

          <label className="field-group">
            <span>Undefined</span>
            <input
              type="text"
              value={undefinedVal === undefined ? "" : undefinedVal}
              onChange={(e) => setUndefinedVal(e.target.value === "undefined" ? undefined : e.target.value)}
            />
          </label>
        </div>
      </section>

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
