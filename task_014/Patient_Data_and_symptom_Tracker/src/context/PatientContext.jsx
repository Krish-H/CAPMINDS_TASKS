import { createContext, useState } from 'react';

export const PatientContext = createContext();

export const PatientProvider = ({ children }) => {

  const [patientData] = useState({
    name: "John Doe",
    email: "patient@test.com"
  });

  return (
    <PatientContext.Provider value={patientData}>
      {children}
    </PatientContext.Provider>
  );
};
