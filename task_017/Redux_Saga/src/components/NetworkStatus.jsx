import React from 'react';
import { useSelector } from 'react-redux';
import { Wifi, WifiOff } from 'lucide-react';

const NetworkStatus = () => {
  const isOnline = useSelector((state) => state.patients.isOnline);

  return (
    <div className={`network-status ${isOnline ? 'online' : 'offline'}`}>
      <div className="status-dot"></div>
      {isOnline ? (
        <>
          <Wifi size={16} />
          <span>Online</span>
        </>
      ) : (
        <>
          <WifiOff size={16} />
          <span>Offline</span>
        </>
      )}
    </div>
  );
};

export default NetworkStatus;
