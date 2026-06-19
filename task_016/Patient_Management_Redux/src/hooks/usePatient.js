import { useSelector, useDispatch } from "react-redux";
import { addPatient, deletePatient } from "../redux/patientSlice";

export const usePatient = () => {
  const patients = useSelector((state) => state.patients.patients);
  const dispatch = useDispatch();

  const addPatientHandler = (patientData) => {
    dispatch(addPatient(patientData));
  };

  const deletePatientHandler = (id) => {
    dispatch(deletePatient(id));
  };

  return {
    patients,
    addPatient: addPatientHandler,
    deletePatient: deletePatientHandler,
  };
};