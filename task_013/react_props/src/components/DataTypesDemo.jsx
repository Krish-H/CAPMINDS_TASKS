import React from "react";

function DataTypesDemo({
  stringProp,
  numberProp,
  booleanProp,
  arrayProp,
  objectProp,
  functionProp,
  nullProp,
  undefinedProp,
}) {
  return (
    <section className="demo-card">
      <h2>Props Showcase</h2>
      <div className="demo-grid">
        <div className="demo-row">
          <span>String</span>
          <span>{stringProp}</span>
        </div>
        <div className="demo-row">
          <span>Number</span>
          <span>{numberProp}</span>
        </div>
        <div className="demo-row">
          <span>Boolean</span>
          <span>{booleanProp ? "True" : "False"}</span>
        </div>
        <div className="demo-row">
          <span>Array</span>
          <span>{arrayProp.join(", ")}</span>
        </div>
        <div className="demo-row">
          <span>Object</span>
          <span>{objectProp.name} (ID: {objectProp.id})</span>
        </div>
        <div className="demo-row">
          <span>Null</span>
          <span>{String(nullProp)}</span>
        </div>
        <div className="demo-row">
          <span>Undefined</span>
          <span>{String(undefinedProp)}</span>
        </div>
      </div>
      <button className="action-button" onClick={functionProp}>
        Call Function Prop
      </button>
    </section>
  );
}

export default DataTypesDemo;
