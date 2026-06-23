import { all } from 'redux-saga/effects';
import {
  watchFetchPatients,
  watchFetchPatientDetails,
  watchSubmitPatientForm,
  watchProcessQueue
} from './saga';

export default function* rootSaga() {
  yield all([
    watchFetchPatients(),
    watchFetchPatientDetails(),
    watchSubmitPatientForm(),
    watchProcessQueue()
  ]);
}
