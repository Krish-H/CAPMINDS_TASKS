import React, { useMemo } from 'react';

import { patients } from '../data/patients';
import { doctors } from '../data/doctors';

function Dashboard() {
  // 7. Performance Learning: Observe when components render
  console.log('Dashboard rendered');

  // 3. Dashboard Page: Use useMemo() for calculating totals
  // In a real app, this might be a more complex computation over large arrays
  const totalPatients = useMemo(() => {
    console.log('Calculating total patients...');
    return patients.length;
  }, []); // Empty dependency array as dummy data doesn't change here

  const totalDoctors = useMemo(() => {
    console.log('Calculating total doctors...');
    return doctors.length;
  }, []);

  return (
    <div className="dashboard">
      <div className="page-header">
        <h1 className="page-title">Dashboard Overview</h1>
      </div>

      <div className="stats-grid">
        <div className="stat-card">
          <div className="stat-title">Total Patients</div>
          <div className="stat-value">{totalPatients}</div>
        </div>

        <div className="stat-card">
          <div className="stat-title">Active Doctors</div>
          <div className="stat-value">{totalDoctors}</div>
        </div>
      </div>

      <div className="dashboard-content">
        <p>Welcome to the Healthcare Performance Optimization Dashboard.</p>
        <p>This page loaded synchronously, while other pages are lazy-loaded to optimize initial bundle size.</p>
      </div>
    </div>
  );
}

export default Dashboard;
