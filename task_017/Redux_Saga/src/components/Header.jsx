import React from 'react';
import NetworkStatus from './NetworkStatus';
import { Bell, Search } from 'lucide-react';

const Header = () => {
  return (
    <header className="header">
      <div style={{ display: 'flex', alignItems: 'center', gap: '1rem', flex: 1 }}>
        <div style={{ position: 'relative', width: '300px' }}>
          <Search size={18} style={{ position: 'absolute', left: '10px', top: '50%', transform: 'translateY(-50%)', color: 'var(--text-secondary)' }} />
          <input
            type="text"
            placeholder="Search patients..."
            className="form-input"
            style={{ paddingLeft: '2.5rem', background: 'rgba(255, 255, 255, 0.05)', border: 'none' }}
          />
        </div>
      </div>
      
      <div style={{ display: 'flex', alignItems: 'center', gap: '1.5rem' }}>
        <NetworkStatus />
        <div style={{ position: 'relative', cursor: 'pointer', color: 'var(--text-secondary)' }}>
          <Bell size={20} />
          <span style={{ position: 'absolute', top: '-4px', right: '-4px', width: '8px', height: '8px', backgroundColor: 'var(--danger-color)', borderRadius: '50%' }}></span>
        </div>
        <div style={{ display: 'flex', alignItems: 'center', gap: '0.75rem', cursor: 'pointer' }}>
          <div style={{ width: '36px', height: '36px', borderRadius: '50%', background: 'var(--primary-color)', display: 'flex', alignItems: 'center', justifyContent: 'center', fontWeight: 'bold' }}>
            DR
          </div>
          <div style={{ display: 'flex', flexDirection: 'column' }}>
            <span style={{ fontSize: '0.875rem', fontWeight: 500 }}>Dr. Roberts</span>
            <span style={{ fontSize: '0.75rem', color: 'var(--text-secondary)' }}>Cardiologist</span>
          </div>
        </div>
      </div>
    </header>
  );
};

export default Header;
