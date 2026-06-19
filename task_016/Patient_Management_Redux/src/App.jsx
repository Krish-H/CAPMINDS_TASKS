import AddPatient from "./components/AddPatient";
import PatientList from "./components/PatientList";
import "./index.css";

function App() {
  console.log("App Component rendered");

  return (
    <div className="app-container">
      <header className="app-header">

        <h1>Patient Dashboard</h1>
        <p>Manage healthcare records efficiently</p>
      </header>
      <main className="main-content">
        <AddPatient />
        <PatientList />
      </main>
    </div>
  );
}

export default App;
