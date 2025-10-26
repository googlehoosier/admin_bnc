import React from 'react';
import { useNavigate, useLocation } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import styles from './Header.module.css';

const Header = () => {
  const navigate = useNavigate();
  const location = useLocation();
  const { logout } = useAuth();

  const handleLogout = () => {
    logout();
    navigate('/');
  };

  return (
    <header className={styles.header}>
      <div className={styles.container}>
        <div className={styles.brand}>
          <div className={styles.logo}>
            <img src="/logo.png" alt="BINGE'N Logo" />
          </div>
          <h1 className={styles.title}>BINGE'N CELEBRATIONS</h1>
        </div>
        <nav className={styles.nav}>
          <button
            onClick={() => navigate('/booking-form')}
            className={location.pathname === '/booking-form' ? styles.active : ''}
          >
            Booking Form
          </button>
          <button
            onClick={() => navigate('/view-bookings')}
            className={location.pathname === '/view-bookings' ? styles.active : ''}
          >
            View Bookings
          </button>
          <button onClick={handleLogout} className={styles.logoutBtn}>
            Logout
          </button>
        </nav>
      </div>
    </header>
  );
};

export default Header;
