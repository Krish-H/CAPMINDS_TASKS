import { call, put, takeLatest, select, cancelled, delay } from 'redux-saga/effects';
import axios from 'axios';
import { patientApi } from '../api/patientApi';
import {
  FETCH_PATIENTS,
  FETCH_PATIENT_DETAILS,
  SUBMIT_PATIENT_FORM,
  PROCESS_QUEUE
} from './actions';
import {
  setLoading,
  setDetailsLoading,
  setPatients,
  setError,
  setSelectedPatient,
  queuePatientForm,
  removeFromQueue,
  addPatientLocally
} from './patientSlice';
import { v4 as uuidv4 } from 'uuid';

function* workerFetchPatients() {
  try {
    yield put(setLoading(true));
    const response = yield call(patientApi.getPatients);
    // Store all 10 records. UI will slice them.
    yield put(setPatients(response.data));
  } catch (error) {
    yield put(setError('Failed to fetch patients.'));
  }
}

function* workerFetchPatientDetails(action) {
  const abortController = new AbortController();
  try {
    yield put(setDetailsLoading(true));
    // Pass signal to the API call
    const response = yield call(patientApi.getPatientDetails, action.payload, abortController.signal);
    yield put(setSelectedPatient(response.data));
  } catch (error) {
    // If request was cancelled, we don't dispatch an error
    if (!axios.isCancel(error)) {
      yield put(setError('Failed to fetch patient details.'));
    }
  } finally {
    if (yield cancelled()) {
      // Saga was cancelled (e.g., due to takeLatest firing again), abort the network request
      abortController.abort();
    }
  }
}

function* workerSubmitPatientForm(action) {
  const isOnline = yield select(state => state.patients.isOnline);
  const formData = action.payload;

  if (isOnline) {
    try {
      yield put(setLoading(true));
      const response = yield call(patientApi.createPatient, formData);
      yield put(addPatientLocally(response.data));
      yield put(setLoading(false));
      // Display success toast ideally
    } catch (error) {
      yield put(setError('Failed to submit form.'));
    }
  } else {
    // Offline queueing
    const queuedItem = { ...formData, queueId: uuidv4() };
    yield put(queuePatientForm(queuedItem));
  }
}

function* workerProcessQueue() {
  const offlineQueue = yield select(state => state.patients.offlineQueue);
  
  if (offlineQueue.length === 0) return;

  yield put(setLoading(true));
  for (const item of offlineQueue) {
    try {
      // Remove queueId before sending to API
      const { queueId, ...formData } = item;
      const response = yield call(patientApi.createPatient, formData);
      yield put(addPatientLocally(response.data));
      yield put(removeFromQueue(item.queueId));
    } catch (error) {
      console.error('Failed to process queued item:', error);
      // Keep in queue if it fails, or handle differently
    }
  }
  yield put(setLoading(false));
}

export function* watchFetchPatients() {
  yield takeLatest(FETCH_PATIENTS, workerFetchPatients);
}

export function* watchFetchPatientDetails() {
  // takeLatest handles the cancellation of the previous worker automatically
  yield takeLatest(FETCH_PATIENT_DETAILS, workerFetchPatientDetails);
}

export function* watchSubmitPatientForm() {
  yield takeLatest(SUBMIT_PATIENT_FORM, workerSubmitPatientForm);
}

export function* watchProcessQueue() {
  yield takeLatest(PROCESS_QUEUE, workerProcessQueue);
}
