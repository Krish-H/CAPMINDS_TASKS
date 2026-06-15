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
    <div style={{ border: "1px solid #333", padding: "10px", marginTop: "10px" }}>
      <h2>Props Showcase</h2>
      <p><strong>String:</strong> {stringProp}</p>
      <p><strong>Number:</strong> {numberProp}</p>
      <p><strong>Boolean:</strong> {booleanProp ? "True" : "False"}</p>
      <p><strong>Array:</strong> {arrayProp.join(", ")}</p>
      <p><strong>Object:</strong> {objectProp.name} (ID: {objectProp.id})</p>
      <button onClick={functionProp}>Call Function Prop</button>
      <p><strong>Null:</strong> {String(nullProp)}</p>
      <p><strong>Undefined:</strong> {String(undefinedProp)}</p>
    </div>
  );
}

export default DataTypesDemo;
