<?php
// index.php - show events and registration
require 'config.php';

$messages = []; $errors = [];

/* Fetch events */
$events = [];
$res = $conn->query("SELECT * FROM events ORDER BY event_date ASC, created_at DESC");
while ($row = $res->fetch_assoc()) $events[] = $row;
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Event Portal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <style>
    :root {
      --primary-color: #6366f1;
      --secondary-color: #8b5cf6;
      --accent-color: #ec4899;
    }
    body {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
    }
    .navbar-custom {
      background: rgba(255, 255, 255, 0.95) !important;
      backdrop-filter: blur(10px);
      box-shadow: 0 2px 20px rgba(0,0,0,0.1);
    }
    .main-container {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      border-radius: 20px;
      box-shadow: 0 10px 40px rgba(0,0,0,0.2);
      margin: 20px auto;
      padding: 30px;
      max-width: 1400px;
    }
    .page-header {
      text-align: center;
      margin-bottom: 40px;
      padding: 30px 0;
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      color: white;
      border-radius: 15px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.2);
    }
    .page-header h1 {
      font-size: 3rem;
      font-weight: 700;
      margin: 0;
      text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
    }
    .page-header p {
      font-size: 1.2rem;
      margin-top: 10px;
      opacity: 0.9;
    }
    .event-card {
      transition: all 0.3s ease;
      border: none;
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .event-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 15px 35px rgba(0,0,0,0.2);
    }
    .event-card img {
      height: 220px;
      object-fit: cover;
      transition: transform 0.3s ease;
    }
    .event-card:hover img {
      transform: scale(1.05);
    }
    .btn-primary {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      border: none;
      padding: 12px 30px;
      font-weight: 600;
      border-radius: 25px;
      transition: all 0.3s ease;
    }
    .btn-primary:hover {
      transform: scale(1.05);
      box-shadow: 0 5px 20px rgba(99, 102, 241, 0.4);
    }
    .btn-outline-admin {
      color: var(--primary-color);
      border: 2px solid var(--primary-color);
      padding: 10px 25px;
      font-weight: 600;
      border-radius: 25px;
      transition: all 0.3s ease;
    }
    .btn-outline-admin:hover {
      background: var(--primary-color);
      color: white;
      transform: scale(1.05);
    }
    .empty-state {
      text-align: center;
      padding: 60px 20px;
      color: #666;
    }
    .empty-state i {
      font-size: 5rem;
      color: #ddd;
      margin-bottom: 20px;
    }
    .no-events-card {
      background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
      color: white;
      border-radius: 20px;
      padding: 50px;
      text-align: center;
    }
    .navbar-brand {
      font-weight: 700;
      font-size: 1.5rem;
      color: var(--primary-color) !important;
    }
    .modal-content {
      border-radius: 15px;
      border: none;
    }
    .modal-header {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      color: white;
      border-radius: 15px 15px 0 0;
    }
    .form-control:focus {
      border-color: var(--primary-color);
      box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.25);
    }
  </style>
</head>

<body>
<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-custom mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">
      <i class="bi bi-calendar-event-fill"></i> Event Portal
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link" href="index.php">
            <i class="bi bi-house-fill"></i> Home
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="admin.php">
            <i class="bi bi-shield-lock-fill"></i> Admin
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="main-container">
  <div class="page-header">
    <h1><i class="bi bi-calendar-check-fill"></i> Upcoming Events</h1>
    <p>Discover exciting opportunities and register for events</p>
  </div>

  <?php if ($errors): ?><div class="alert alert-danger"><?php foreach ($errors as $e) echo htmlspecialchars($e)."<br>"; ?></div><?php endif; ?>
  <?php if ($messages): ?><div class="alert alert-success"><?php foreach ($messages as $m) echo htmlspecialchars($m)."<br>"; ?></div><?php endif; ?>

  <?php if (empty($events)): ?>
    <div class="no-events-card">
      <i class="bi bi-calendar-x" style="font-size: 5rem;"></i>
      <h3>No Events Available</h3>
      <p>Check back soon for exciting new events!</p>
    </div>
  <?php else: ?>
    <div class="row">
      <?php foreach($events as $ev): ?>
        <div class="col-lg-6 mb-4">
          <div class="card event-card h-100">
            <?php if (!empty($ev['banner_url'])): ?>
              <img src="<?= htmlspecialchars($ev['banner_url']) ?>" class="card-img-top" alt="banner">
            <?php else: ?>
              <div style="height: 220px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center;">
                <i class="bi bi-calendar-event" style="font-size: 4rem; color: white;"></i>
              </div>
            <?php endif; ?>
            <div class="card-body d-flex flex-column">
              <h4 class="card-title" style="color: var(--primary-color);">
                <i class="bi bi-tag-fill"></i> <?= htmlspecialchars($ev['title']) ?>
              </h4>
              <p class="card-text flex-grow-1"><?= nl2br(htmlspecialchars($ev['description'])) ?></p>
              <p class="text-muted mb-3">
                <i class="bi bi-calendar3"></i> Date: <?= htmlspecialchars($ev['event_date']) ?>
              </p>
              <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#regModal" data-eventid="<?= $ev['id'] ?>" data-eventtitle="<?= htmlspecialchars($ev['title']) ?>">
                <i class="bi bi-person-plus-fill"></i> Register Now
              </a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<!-- Registration Modal -->
<div class="modal fade" id="regModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="regForm" class="modal-content">
      <input type="hidden" name="action" value="register">
      <input type="hidden" name="event_id" id="event_id" value="">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitle">Register</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="regAlert" class="alert d-none"></div>
        <div class="mb-2"><input class="form-control" name="name" placeholder="Your full name" required></div>
        <div class="mb-2"><input class="form-control" name="email" type="email" placeholder="Email" required></div>
        <div class="mb-2"><input class="form-control" name="phone" placeholder="Phone (optional)"></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">
          <i class="bi bi-x-circle"></i> Cancel
        </button>
        <button class="btn btn-primary" type="submit">
          <i class="bi bi-check-circle"></i> Submit Registration
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Alert Modal for messages -->
<div class="modal fade" id="alertModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header" id="alertModalHeader">
        <h5 class="modal-title" id="alertModalTitle">Notification</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="alertModalBody">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
var regModal = document.getElementById('regModal');
regModal.addEventListener('show.bs.modal', function (event) {
  var button = event.relatedTarget;
  var eventId = button.getAttribute('data-eventid');
  var eventTitle = button.getAttribute('data-eventtitle');
  document.getElementById('event_id').value = eventId;
  document.getElementById('modalTitle').textContent = 'Register for: ' + eventTitle;
  // Reset form
  document.getElementById('regForm').reset();
  document.getElementById('event_id').value = eventId;
  document.getElementById('regAlert').classList.add('d-none');
});

// Handle form submission with AJAX
document.getElementById('regForm').addEventListener('submit', function(e) {
  e.preventDefault();
  
  var formData = new FormData(this);
  var alertDiv = document.getElementById('regAlert');
  
  fetch('register.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      // Success - close registration modal and show alert
      var bsRegModal = bootstrap.Modal.getInstance(regModal);
      bsRegModal.hide();
      
      // Show success alert
      showAlert('Success!', data.message, 'success');
      
      // Reload page after a short delay
      setTimeout(function() {
        window.location.reload();
      }, 1500);
    } else {
      // Error - show in alert div within the modal
      alertDiv.textContent = data.message;
      alertDiv.className = 'alert alert-danger';
      alertDiv.classList.remove('d-none');
    }
  })
  .catch(error => {
    alertDiv.textContent = 'An error occurred. Please try again.';
    alertDiv.className = 'alert alert-danger';
    alertDiv.classList.remove('d-none');
  });
});

// Function to show alert modal
function showAlert(title, message, type) {
  document.getElementById('alertModalTitle').textContent = title;
  document.getElementById('alertModalBody').textContent = message;
  
  var header = document.getElementById('alertModalHeader');
  var modal = new bootstrap.Modal(document.getElementById('alertModal'));
  
  // Set color based on type
  if (type === 'success') {
    header.className = 'modal-header bg-success text-white';
  } else {
    header.className = 'modal-header bg-danger text-white';
  }
  
  modal.show();
}

</script>
</body>
</html>
