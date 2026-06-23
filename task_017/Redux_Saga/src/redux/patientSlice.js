import { createSlice } from '@reduxjs/toolkit';

const initialState = {
  patients: [],
  selectedPatient: null,
  loading: false,
  detailsLoading: false,
  currentPage: 1,
  pageSize: 5,
  offlineQueue: [],
  isOnline: navigator.onLine,
  error: null,
};

const patientSlice = createSlice({
  name: 'patients',
  initialState,
  reducers: {
    setLoading: (state, action) => {
      state.loading = action.payload;
    },
    setDetailsLoading: (state, action) => {
      state.detailsLoading = action.payload;
    },
    setPatients: (state, action) => {
      state.patients = action.payload;
      state.loading = false;
      state.error = null;
    },
    setError: (state, action) => {
      state.error = action.payload;
      state.loading = false;
      state.detailsLoading = false;
    },
    changePage: (state, action) => {
      state.currentPage = action.payload;
    },
    setSelectedPatient: (state, action) => {
      state.selectedPatient = action.payload;
      state.detailsLoading = false;
    },
    clearSelectedPatient: (state) => {
      state.selectedPatient = null;
    },
    queuePatientForm: (state, action) => {
      state.offlineQueue.push(action.payload);
    },
    removeFromQueue: (state, action) => {
      state.offlineQueue = state.offlineQueue.filter(
        item => item.queueId !== action.payload
      );
    },
    setNetworkStatus: (state, action) => {
      state.isOnline = action.payload;
    },
    addPatientLocally: (state, action) => {
      state.patients.unshift(action.payload);
    }
  }
});

export const {
  setLoading,
  setDetailsLoading,
  setPatients,
  setError,
  changePage,
  setSelectedPatient,
  clearSelectedPatient,
  queuePatientForm,
  removeFromQueue,
  setNetworkStatus,
  addPatientLocally
} = patientSlice.actions;

export default patientSlice.reducer;
