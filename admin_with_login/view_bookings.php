<?php
require_once 'config.php';
require_login();
// Connect to Supabase
$ch = curl_init();
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 50;
$offset = ($page - 1) * $limit;

$dateFilter = isset($_GET['date']) ? $_GET['date'] : null;
$url = "$BOOKINGS_ENDPOINT?order=created_at.desc&limit=$limit&offset=$offset";

if ($dateFilter) {
  $url = "$BOOKINGS_ENDPOINT?booking_date=eq.$dateFilter&order=created_at.desc";
}

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "apikey: $SUPABASE_SERVICE_ROLE_KEY",
    "Authorization: Bearer $SUPABASE_SERVICE_ROLE_KEY",
    "Content-Type: application/json",
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
$bookings = json_decode($result, true);
curl_close($ch);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Bookings</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      padding: 20px;
      background: #f5f5f5;
    }
    h2 {
      text-align: center;
      margin-bottom: 20px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      background: #fff;
      border-radius: 10px;
      overflow: hidden;
    }
    th, td {
      padding: 10px;
      border-bottom: 1px solid #ddd;
      text-align: left;
    }
    th {
      background: #007BFF;
      color: white;
    }
    tr:hover {
      background: #f1f1f1;
    }
    .top-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
    }
    .back-btn {
      background: #007BFF;
      color: white;
      border: none;
      padding: 8px 12px;
      border-radius: 5px;
      cursor: pointer;
    }
    .pagination {
      text-align: center;
      margin-top: 20px;
    }
    .pagination a {
      padding: 8px 12px;
      border: 1px solid #007BFF;
      color: #007BFF;
      margin: 0 2px;
      text-decoration: none;
      border-radius: 5px;
    }
    .pagination a.active {
      background: #007BFF;
      color: white;
    }
    .filter-section {
      text-align: center;
      margin-bottom: 20px;
    }
    .filter-section input, .filter-section button {
      padding: 6px 10px;
      border-radius: 5px;
      border: 1px solid #ccc;
      font-size: 14px;
    }
    .filter-btn {
      background: #28a745;
      color: white;
      border: none;
      cursor: pointer;
    }
    .remove-filter-btn {
      background: #dc3545;
      color: white;
      border: none;
      cursor: pointer;
      margin-left: 5px;
    }
    .edit-btn {
      background: #28a745;
      color: white;
      padding: 5px 10px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    /* Modal */
    .modal {
      display: none;
      position: fixed;
      z-index: 10;
      padding-top: 100px;
      left: 0; top: 0;
      width: 100%; height: 100%;
      background-color: rgba(0,0,0,0.6);
    }
    .modal-content {
      background: white;
      margin: auto;
      padding: 20px;
      border-radius: 10px;
      width: 50%;
      max-height: 80%;
      overflow-y: auto;
    }
    .modal input, .modal textarea, .modal select {
      width: 100%;
      margin: 5px 0 10px;
      padding: 8px;
    }
    .modal button {
      padding: 8px 12px;
      margin-right: 10px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    .save-btn { background: #28a745; color: white; }
    .close-btn { background: #dc3545; color: white; }
  </style>
</head>
<body>

  <div class="top-bar">
    <button class="back-btn" onclick="window.location.href='booking_form.php'">← Back</button>
    <h2>All Bookings</h2>
    <div class="filter-section">
      <input type="date" id="filterDate" value="<?= htmlspecialchars($dateFilter ?? '') ?>">
      <button class="filter-btn" onclick="filterByDate()">Filter</button>
      <button class="remove-filter-btn" onclick="removeFilter()">Remove Filter</button>
    </div>
  </div>

  <table id="bookingsTable">
    <thead>
      <tr>
        <th>ID</th>
        <th>Venue ID</th>
        <th>Slot ID</th>
        <th>Date</th>
        <th>Name</th>
        <th>Persons</th>
        <th>WhatsApp</th>
        <th>Email</th>
        <th>Decoration</th>
        <th>Advance Paid</th>
        <th>created at</th>
        <th>Event Type</th>
        <th>Cake</th>
        <th>Addons</th>
        <th>Payment ID</th>
        <th>Total</th>
        <th>Extra</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($bookings as $b): ?>
      <tr>
        <?php foreach ($b as $col => $val): ?>
          <td><?= htmlspecialchars($val) ?></td>
        <?php endforeach; ?>
        <td><button class="edit-btn" onclick='openEditModal(<?= json_encode($b) ?>)'>Edit</button></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div class="pagination">
    <?php if ($page > 1): ?>
      <a href="?page=<?= $page - 1 ?>">← Prev</a>
    <?php endif; ?>
    <a class="active" href="#"><?= $page ?></a>
    <a href="?page=<?= $page + 1 ?>">Next →</a>
  </div>

  <!-- Modal -->
  <div id="editModal" class="modal">
    <div class="modal-content">
      <h3>Edit Booking</h3>
      <form id="editForm">
        <input type="hidden" name="id" id="edit_id">
        <label>Venue ID</label>
        <input type="text" name="venue_id" id="edit_venue_id">
        <label>Slot ID</label>
        <input type="text" name="slot_id" id="edit_slot_id">
        <label>Date</label>
        <input type="date" name="booking_date" id="edit_booking_date">
        <label>Name</label>
        <input type="text" name="booking_name" id="edit_booking_name">
        <label>Persons</label>
        <input type="number" name="persons" id="edit_persons">
        <label>WhatsApp</label>
        <input type="text" name="whatsapp" id="edit_whatsapp">
        <label>Email</label>
        <input type="text" name="email" id="edit_email">
        <label>Decoration</label>
        <input type="text" name="decoration" id="edit_decoration">
        <label>Advance Paid</label>
        <select name="advance_paid" id="edit_advance_paid">
          <option value="true">Yes</option>
          <option value="false">No</option>
        </select>
        <label>Event Type</label>
        <input type="text" name="event_type" id="edit_event_type">
        <label>Cake Selection</label>
        <input type="text" name="cake_selection" id="edit_cake_selection">
        <label>Addons</label>
        <textarea name="selected_addons" id="edit_selected_addons"></textarea>
        <label>Total Amount</label>
        <input type="number" name="total_amount" id="edit_total_amount">
        <div style="text-align:right;">
          <button type="button" class="close-btn" onclick="closeEditModal()">Back</button>
          <button type="button" class="save-btn" onclick="saveEdit()">Save</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function filterByDate() {
      const date = document.getElementById('filterDate').value;
      if (!date) return;
      window.location.href = `view_bookings.php?date=${date}`;
    }

    function removeFilter() {
      window.location.href = 'view_bookings.php';
    }

    function openEditModal(data) {
      document.getElementById('editModal').style.display = 'block';
      for (const key in data) {
        const input = document.getElementById('edit_' + key);
        if (input) {
          if (key === 'advance_paid') {
            input.value = data[key] ? 'true' : 'false';
          } else {
            input.value = data[key];
          }
        }
      }
    }

    function closeEditModal() {
      document.getElementById('editModal').style.display = 'none';
    }

    async function saveEdit() {
      const formData = new FormData(document.getElementById('editForm'));
      const payload = Object.fromEntries(formData.entries());
      payload.advance_paid = payload.advance_paid === 'true';
      const res = await fetch('update_booking.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      const result = await res.json();
      if (result.success) {
        alert('Booking updated successfully!');
        location.reload();
      } else {
        alert('Error updating booking: ' + result.error);
      }
    }
  </script>
</body>
</html>
