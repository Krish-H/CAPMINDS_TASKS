export const FETCH_PATIENTS = 'FETCH_PATIENTS';
export const FETCH_PATIENT_DETAILS = 'FETCH_PATIENT_DETAILS';
export const SUBMIT_PATIENT_FORM = 'SUBMIT_PATIENT_FORM';
export const PROCESS_QUEUE = 'PROCESS_QUEUE';

export const fetchPatients = () => ({ type: FETCH_PATIENTS });
export const fetchPatientDetails = (id) => ({ type: FETCH_PATIENT_DETAILS, payload: id });
export const submitPatientForm = (data) => ({ type: SUBMIT_PATIENT_FORM, payload: data });
export const processQueue = () => ({ type: PROCESS_QUEUE });
