import axios from 'axios';

export const patientApi = {
  getPatients: async () => {
    const response = await axios.get('https://jsonplaceholder.typicode.com/users');
    const mappedData = response.data.map(user => ({
      id: user.id,
      name: user.name,
      age: Math.floor(Math.random() * 30) + 20, // dynamic fallback age between 20 and 49
      disease: 'Unknown',
      doctor: 'Not Assigned'
    }));
    return { data: mappedData };
  },

  getPatientDetails: async (id, signal) => {
    // delay added to ensure cancellation is visible in UI and network tab
    await new Promise(resolve => setTimeout(resolve, 800));
    return await axios.get(`https://jsonplaceholder.typicode.com/users/${id}`, { signal });
  },

  createPatient: async (data) => {
    await new Promise(resolve => setTimeout(resolve, 500));
    return { data: { ...data, id: Date.now() } };
  }
};
