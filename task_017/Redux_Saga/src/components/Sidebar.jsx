import React from 'react';
import { LayoutDashboard, Users, Calendar, Settings, Activity } from 'lucide-react';

const Sidebar = () => {
  return (
    <aside className="sidebar">
      <div className="sidebar-header">
        <Activity color="var(--primary-color)" size={28} />
        <span>HealthDash</span>
      </div>
      <nav className="sidebar-nav">
        <div className="nav-item active">
          <LayoutDashboard size={20} />
          <span>Dashboard</span>
        </div>
        <div className="nav-item">
          <Users size={20} />
          <span>Patients</span>
        </div>
        <div className="nav-item">
          <Calendar size={20} />
          <span>Appointments</span>
        </div>
        <div className="nav-item">
          <Settings size={20} />
          <span>Settings</span>
        </div>
      </nav>
    </aside>
  );
};

export default Sidebar;
