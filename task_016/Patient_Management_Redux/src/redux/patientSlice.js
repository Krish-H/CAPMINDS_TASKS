import { createSlice } from "@reduxjs/toolkit";

const initialState = {
  patients: [],
};

export const patientSlice = createSlice({
  name: "patients",
  initialState,
  reducers: {
    addPatient: (state, action) => {
      console.log("Patient added");
      state.patients.push(action.payload);
    },
    deletePatient: (state, action) => {
      console.log("Patient deleted");
      state.patients = state.patients.filter(
        (patient) => patient.id !== action.payload
      );
    },
  },
});

export const { addPatient, deletePatient } = patientSlice.actions;

export default patientSlice.reducer;
