import React, { useState, useEffect } from 'react';
import Header from '../components/Header';
import { supabase } from '../lib/supabase';
import styles from './ViewBookings.module.css';

const ViewBookings = () => {
  const [bookings, setBookings] = useState([]);
  const [loading, setLoading] = useState(true);
  const [dateFilter, setDateFilter] = useState('');
  const [editingBooking, setEditingBooking] = useState(null);
  const [editForm, setEditForm] = useState({});

  useEffect(() => {
    loadBookings();
  }, [dateFilter]);

  const loadBookings = async () => {
    setLoading(true);
    try {
      let query = supabase
        .from('bookings')
        .select('*')
        .order('created_at', { ascending: false })
        .limit(50);

      if (dateFilter) {
        query = query.eq('booking_date', dateFilter);
      }

      const { data, error } = await query;

      if (error) throw error;
      setBookings(data || []);
    } catch (error) {
      alert('Error loading bookings: ' + error.message);
    } finally {
      setLoading(false);
    }
  };

  const handleEdit = (booking) => {
    setEditingBooking(booking);
    setEditForm(booking);
  };

  const handleEditInputChange = (e) => {
    const { name, value, type } = e.target;
    setEditForm(prev => ({
      ...prev,
      [name]: type === 'select-one' && (name === 'advance_paid')
        ? value === 'true'
        : value
    }));
  };

  const handleSaveEdit = async () => {
    try {
      const updateData = { ...editForm };
      delete updateData.id;
      delete updateData.created_at;

      const { error } = await supabase
        .from('bookings')
        .update(updateData)
        .eq('id', editingBooking.id);

      if (error) throw error;

      alert('Booking updated successfully!');
      setEditingBooking(null);
      loadBookings();
    } catch (error) {
      alert('Error updating booking: ' + error.message);
    }
  };

  const handleFilterByDate = () => {
    loadBookings();
  };

  const handleRemoveFilter = () => {
    setDateFilter('');
  };

  return (
    <div className={styles.page}>
      <Header />
      <div className={styles.container}>
        <div className={styles.topBar}>
          <h2>All Bookings</h2>
          <div className={styles.filterSection}>
            <input
              type="date"
              value={dateFilter}
              onChange={(e) => setDateFilter(e.target.value)}
              className={styles.dateInput}
            />
            <button onClick={handleFilterByDate} className={styles.filterBtn}>
              Filter
            </button>
            <button onClick={handleRemoveFilter} className={styles.removeFilterBtn}>
              Remove Filter
            </button>
          </div>
        </div>

        {loading ? (
          <div className={styles.loading}>Loading bookings...</div>
        ) : (
          <div className={styles.tableContainer}>
            <table className={styles.table}>
              <thead>
                <tr>
                  <th>Booking Name</th>
                  <th>Date</th>
                  <th>Persons</th>
                  <th>WhatsApp</th>
                  <th>Email</th>
                  <th>Decoration</th>
                  <th>Advance Paid</th>
                  <th>Event Type</th>
                  <th>Total</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                {bookings.length === 0 ? (
                  <tr>
                    <td colSpan="10" style={{ textAlign: 'center', padding: '30px' }}>
                      No bookings found
                    </td>
                  </tr>
                ) : (
                  bookings.map(booking => (
                    <tr key={booking.id}>
                      <td>{booking.booking_name}</td>
                      <td>{booking.booking_date}</td>
                      <td>{booking.persons}</td>
                      <td>{booking.whatsapp}</td>
                      <td>{booking.email}</td>
                      <td>{booking.decoration ? 'Yes' : 'No'}</td>
                      <td>{booking.advance_paid ? 'Yes' : 'No'}</td>
                      <td>{booking.event_type}</td>
                      <td>₹{booking.total_amount}</td>
                      <td>
                        <button
                          onClick={() => handleEdit(booking)}
                          className={styles.editBtn}
                        >
                          Edit
                        </button>
                      </td>
                    </tr>
                  ))
                )}
              </tbody>
            </table>
          </div>
        )}
      </div>

      {editingBooking && (
        <div className={styles.modal} onClick={() => setEditingBooking(null)}>
          <div className={styles.modalContent} onClick={(e) => e.stopPropagation()}>
            <h3>Edit Booking</h3>
            <form className={styles.editForm}>
              <div className={styles.formGroup}>
                <label>Booking Name</label>
                <input
                  type="text"
                  name="booking_name"
                  value={editForm.booking_name || ''}
                  onChange={handleEditInputChange}
                />
              </div>

              <div className={styles.formGroup}>
                <label>Date</label>
                <input
                  type="date"
                  name="booking_date"
                  value={editForm.booking_date || ''}
                  onChange={handleEditInputChange}
                />
              </div>

              <div className={styles.formGroup}>
                <label>Persons</label>
                <input
                  type="number"
                  name="persons"
                  value={editForm.persons || ''}
                  onChange={handleEditInputChange}
                />
              </div>

              <div className={styles.formGroup}>
                <label>WhatsApp</label>
                <input
                  type="text"
                  name="whatsapp"
                  value={editForm.whatsapp || ''}
                  onChange={handleEditInputChange}
                />
              </div>

              <div className={styles.formGroup}>
                <label>Email</label>
                <input
                  type="email"
                  name="email"
                  value={editForm.email || ''}
                  onChange={handleEditInputChange}
                />
              </div>

              <div className={styles.formGroup}>
                <label>Decoration</label>
                <input
                  type="text"
                  name="decoration"
                  value={editForm.decoration || ''}
                  onChange={handleEditInputChange}
                />
              </div>

              <div className={styles.formGroup}>
                <label>Advance Paid</label>
                <select
                  name="advance_paid"
                  value={editForm.advance_paid ? 'true' : 'false'}
                  onChange={handleEditInputChange}
                >
                  <option value="true">Yes</option>
                  <option value="false">No</option>
                </select>
              </div>

              <div className={styles.formGroup}>
                <label>Event Type</label>
                <input
                  type="text"
                  name="event_type"
                  value={editForm.event_type || ''}
                  onChange={handleEditInputChange}
                />
              </div>

              <div className={styles.formGroup}>
                <label>Cake Selection</label>
                <input
                  type="text"
                  name="cake_selection"
                  value={editForm.cake_selection || ''}
                  onChange={handleEditInputChange}
                />
              </div>

              <div className={styles.formGroup}>
                <label>Selected Addons</label>
                <textarea
                  name="selected_addons"
                  value={editForm.selected_addons || ''}
                  onChange={handleEditInputChange}
                  rows="3"
                />
              </div>

              <div className={styles.formGroup}>
                <label>Total Amount</label>
                <input
                  type="number"
                  name="total_amount"
                  value={editForm.total_amount || ''}
                  onChange={handleEditInputChange}
                />
              </div>

              <div className={styles.modalActions}>
                <button
                  type="button"
                  onClick={() => setEditingBooking(null)}
                  className={styles.closeBtn}
                >
                  Cancel
                </button>
                <button
                  type="button"
                  onClick={handleSaveEdit}
                  className={styles.saveBtn}
                >
                  Save Changes
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
};

export default ViewBookings;
