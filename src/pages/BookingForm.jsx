import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import Header from '../components/Header';
import { supabase } from '../lib/supabase';
import styles from './BookingForm.module.css';

const VENUES = {
  '399e2ade-5d6d-4535-81a6-93ae43a637a5': { name: 'Aura', maxGuests: 12, includedGuests: 6, extraCharge: 250 },
  '18c00f9e-21d0-4d77-ad5a-d831aa4ede07': { name: 'Lunar', maxGuests: 8, includedGuests: 4, extraCharge: 250 },
  'fdb9e954-7810-4ab5-9b7d-1483ec53669a': { name: 'Minimax', maxGuests: 20, includedGuests: 8, extraCharge: 250 },
  '771c4da3-851e-431c-a490-8bb6bf93aa77': { name: 'Couple', maxGuests: 2, includedGuests: 2, extraCharge: 0 },
};

const EVENT_TYPES = [
  'Birthday', 'Anniversary', 'Romantic Date', 'Marriage Proposal',
  'Groom to Be', 'Bride to Be', 'Baby Shower', 'Private Party', 'none'
];

const CAKE_OPTIONS = [
  'Vanilla', 'Strawberry', 'Butterscotch', 'Pineapple', 'Blueberry',
  'Pistamalai', 'Choco Truffle', 'Choco Kitkat', 'White Forest', 'Black Forest', 'none'
];

const ADDONS = [
  { name: 'LED HBD', price: 119 },
  { name: 'Fog Entry', price: 700 },
  { name: 'Fog Entry + Cold Fire (2)', price: 1400 },
  { name: 'Photo Props', price: 189 },
  { name: 'LED Name Letters', price: 299 },
  { name: 'Table Décor', price: 299 },
  { name: 'Candles', price: 199 },
  { name: 'Photoshoot (30 mins)', price: 600 },
  { name: 'Photoshoot (60 mins)', price: 1200 },
  { name: 'Sash & Crown', price: 199 },
  { name: 'Cold Fire', price: 700 },
  { name: 'Candle Faith', price: 199 },
  { name: 'Fog in Room', price: 499 },
  { name: 'LOVE', price: 99 },
  { name: 'LED Numbers', price: 99 },
  { name: 'Bubble Entry', price: 200 },
  { name: 'none', price: 0 },
];

const BookingForm = () => {
  const navigate = useNavigate();
  const [slots, setSlots] = useState([]);
  const [formData, setFormData] = useState({
    venue_id: '',
    slot_id: '',
    booking_date: '',
    booking_name: '',
    persons: '',
    whatsapp: '',
    email: '',
    decoration: false,
    advance_paid: false,
    event_type: '',
    cake_selection: '',
    selected_addons: [],
    total_amount: '',
  });
  const [extraCharges, setExtraCharges] = useState(0);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    if (formData.venue_id) {
      loadSlots(formData.venue_id);
    }
  }, [formData.venue_id]);

  useEffect(() => {
    calculateExtraCharges();
  }, [formData.venue_id, formData.persons]);

  const loadSlots = async (venueId) => {
    const { data, error } = await supabase
      .from('slots')
      .select('*')
      .eq('venue_id', venueId)
      .order('start_time');

    if (!error && data) {
      setSlots(data);
    }
  };

  const calculateExtraCharges = () => {
    if (!formData.venue_id || !formData.persons) {
      setExtraCharges(0);
      return;
    }

    const venue = VENUES[formData.venue_id];
    if (!venue) {
      setExtraCharges(0);
      return;
    }

    const extraPersons = Math.max(0, parseInt(formData.persons) - venue.includedGuests);
    setExtraCharges(extraPersons * venue.extraCharge);
  };

  const handleInputChange = (e) => {
    const { name, value, type, checked } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: type === 'checkbox' ? checked : value
    }));
  };

  const handleAddonChange = (addon) => {
    setFormData(prev => {
      const addons = prev.selected_addons.includes(addon)
        ? prev.selected_addons.filter(a => a !== addon)
        : [...prev.selected_addons, addon];
      return { ...prev, selected_addons: addons };
    });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);

    try {
      const addonsString = formData.selected_addons
        .map(addon => {
          const addonData = ADDONS.find(a => a.name === addon);
          return `${addon}|${addonData.price}`;
        })
        .join(',');

      const bookingData = {
        venue_id: formData.venue_id,
        slot_id: formData.slot_id,
        booking_date: formData.booking_date,
        booking_name: formData.booking_name,
        persons: parseInt(formData.persons),
        whatsapp: formData.whatsapp,
        email: formData.email,
        decoration: formData.decoration,
        advance_paid: formData.advance_paid,
        event_type: formData.event_type,
        cake_selection: formData.cake_selection,
        selected_addons: addonsString,
        payment_id: formData.advance_paid ? 'admin_manual_advance' : 'admin_manual',
        total_amount: parseFloat(formData.total_amount) || 0,
        extra_person_charges: extraCharges,
      };

      const { error: bookingError } = await supabase
        .from('bookings')
        .insert([bookingData]);

      if (bookingError) throw bookingError;

      if (formData.advance_paid) {
        await supabase
          .from('slots')
          .update({ status: 'booked', is_booked: true })
          .eq('id', formData.slot_id);
      }

      alert('✅ Booking saved successfully' + (formData.advance_paid ? '. Slot frozen (advance paid).' : ''));
      navigate('/view-bookings');
    } catch (error) {
      alert('Error: ' + error.message);
    } finally {
      setLoading(false);
    }
  };

  const selectedVenue = VENUES[formData.venue_id];

  return (
    <div className={styles.page}>
      <Header />
      <div className={styles.container}>
        <h2>Admin Panel - Add Booking</h2>
        <form onSubmit={handleSubmit} className={styles.form}>
          <div className={styles.formGroup}>
            <label htmlFor="venue_id">Venue:</label>
            <select
              id="venue_id"
              name="venue_id"
              value={formData.venue_id}
              onChange={handleInputChange}
              required
            >
              <option value="">-- Select Venue --</option>
              {Object.entries(VENUES).map(([id, venue]) => (
                <option key={id} value={id}>{venue.name}</option>
              ))}
            </select>
          </div>

          <div className={styles.formGroup}>
            <label htmlFor="slot_id">Slot:</label>
            <select
              id="slot_id"
              name="slot_id"
              value={formData.slot_id}
              onChange={handleInputChange}
              required
              disabled={!formData.venue_id}
            >
              <option value="">-- Select Slot --</option>
              {slots.map(slot => (
                <option key={slot.id} value={slot.id}>
                  {slot.start_time} - {slot.end_time}
                </option>
              ))}
            </select>
          </div>

          <div className={styles.formGroup}>
            <label htmlFor="booking_date">Booking Date:</label>
            <input
              type="date"
              id="booking_date"
              name="booking_date"
              value={formData.booking_date}
              onChange={handleInputChange}
              required
            />
          </div>

          <div className={styles.formGroup}>
            <label htmlFor="booking_name">Booking Name:</label>
            <input
              type="text"
              id="booking_name"
              name="booking_name"
              value={formData.booking_name}
              onChange={handleInputChange}
              required
            />
          </div>

          <div className={styles.formGroup}>
            <label htmlFor="persons">Number of Persons:</label>
            <select
              id="persons"
              name="persons"
              value={formData.persons}
              onChange={handleInputChange}
              required
              disabled={!formData.venue_id}
            >
              <option value="">-- Select Guest Count --</option>
              {selectedVenue && Array.from({ length: selectedVenue.maxGuests }, (_, i) => i + 1).map(num => (
                <option key={num} value={num}>{num}</option>
              ))}
            </select>
          </div>

          <div className={styles.formGroup}>
            <label htmlFor="whatsapp">WhatsApp Number:</label>
            <input
              type="text"
              id="whatsapp"
              name="whatsapp"
              value={formData.whatsapp}
              onChange={handleInputChange}
              required
            />
          </div>

          <div className={styles.formGroup}>
            <label htmlFor="email">Email:</label>
            <input
              type="email"
              id="email"
              name="email"
              value={formData.email}
              onChange={handleInputChange}
              required
            />
          </div>

          <div className={styles.checkboxGroup}>
            <label>
              <input
                type="checkbox"
                name="decoration"
                checked={formData.decoration}
                onChange={handleInputChange}
              />
              Decoration
            </label>
          </div>

          <div className={styles.checkboxGroup}>
            <label>
              <input
                type="checkbox"
                name="advance_paid"
                checked={formData.advance_paid}
                onChange={handleInputChange}
              />
              Advance Payment
            </label>
          </div>

          <div className={styles.formGroup}>
            <label htmlFor="event_type">Event Type:</label>
            <select
              id="event_type"
              name="event_type"
              value={formData.event_type}
              onChange={handleInputChange}
              required
            >
              <option value="">-- Select Event Type --</option>
              {EVENT_TYPES.map(type => (
                <option key={type} value={type}>{type}</option>
              ))}
            </select>
          </div>

          <div className={styles.formGroup}>
            <label htmlFor="cake_selection">Cake Selection:</label>
            <select
              id="cake_selection"
              name="cake_selection"
              value={formData.cake_selection}
              onChange={handleInputChange}
            >
              <option value="">-- Select Cake --</option>
              {CAKE_OPTIONS.map(cake => (
                <option key={cake} value={cake}>{cake}</option>
              ))}
            </select>
          </div>

          <div className={styles.formGroup}>
            <label>Selected Addons:</label>
            <div className={styles.addonsGrid}>
              {ADDONS.map(addon => (
                <label key={addon.name} className={styles.addonLabel}>
                  <input
                    type="checkbox"
                    checked={formData.selected_addons.includes(addon.name)}
                    onChange={() => handleAddonChange(addon.name)}
                  />
                  {addon.name} {addon.price > 0 && `(₹${addon.price})`}
                </label>
              ))}
            </div>
          </div>

          <div className={styles.formGroup}>
            <label htmlFor="total_amount">Total Amount:</label>
            <input
              type="number"
              id="total_amount"
              name="total_amount"
              value={formData.total_amount}
              onChange={handleInputChange}
            />
          </div>

          <div className={styles.formGroup}>
            <label htmlFor="extra_person_charges">Extra Person Charges (₹):</label>
            <input
              type="number"
              id="extra_person_charges"
              value={extraCharges}
              readOnly
            />
          </div>

          <button type="submit" className={styles.submitBtn} disabled={loading}>
            {loading ? 'Submitting...' : 'Submit Booking'}
          </button>
        </form>
      </div>
    </div>
  );
};

export default BookingForm;
