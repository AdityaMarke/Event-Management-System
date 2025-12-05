<?php
// admin.php - admin login and event management
require 'config.php';

$errors = []; $messages = [];
$isLoggedIn = isset($_SESSION['admin']) && $_SESSION['admin'] === true;

/* Handle login */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'login') {
    if (trim($_POST['password'] ?? '') === $adminPassword) {
        $_SESSION['admin'] = true;
        $isLoggedIn = true;
        $messages[] = "Login successful.";
    } else {
        $errors[] = "Invalid password.";
    }
}

/* Handle logout */
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit;
}

/* Handle event create */
if ($isLoggedIn && $_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create_event') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $event_date = trim($_POST['event_date'] ?? '');

    if ($title === '') $errors[] = "Title required.";
    if ($event_date === '') $errors[] = "Event date required.";

    // optional banner file
    $bannerKey = null; $bannerFilename = null; $bannerUrl = null;
    if (isset($_FILES['banner']) && $_FILES['banner']['error'] === UPLOAD_ERR_OK) {
        $orig = $_FILES['banner']['name'];
        $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExt, true)) $errors[] = "Invalid banner file type.";
        $sizeBytes = (int)$_FILES['banner']['size'];
        if ($sizeBytes > $maxFileMB * 1024 * 1024) $errors[] = "Banner too large.";

        if (!$errors) {
            $safe = sanitize_filename($orig);
            $bannerKey = 'events/banners/' . time() . '-' . $safe;
            try {
                $s3->putObject([
                    'Bucket' => $bucket,
                    'Key' => $bannerKey,
                    'SourceFile' => $_FILES['banner']['tmp_name'],
                    'ContentType' => $_FILES['banner']['type']
                ]);
                // presigned 7 days
                $cmd = $s3->getCommand('GetObject',['Bucket'=>$bucket,'Key'=>$bannerKey]);
                $req = $s3->createPresignedRequest($cmd, '+7 days');
                $bannerUrl = (string)$req->getUri();
                $bannerFilename = $orig;
            } catch (AwsException $e) {
                $errors[] = "S3 upload failed: " . htmlspecialchars($e->getAwsErrorMessage() ?: $e->getMessage());
            }
        }
    }

    if (!$errors) {
        // insert into DB
        $stmt = $conn->prepare("INSERT INTO events (title, description, event_date, banner_s3_key, banner_filename, banner_url) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $title, $description, $event_date, $bannerKey, $bannerFilename, $bannerUrl);
        if ($stmt->execute()) {
            $eventId = $stmt->insert_id;
            $messages[] = "Event created successfully.";

            // write JSON metadata to S3 also
            $meta = [
                'id'=>$eventId,
                'title'=>$title,
                'description'=>$description,
                'event_date'=>$event_date,
                'banner_s3_key'=>$bannerKey,
                'banner_filename'=>$bannerFilename,
                'banner_url'=>$bannerUrl,
                'created_at'=>date(DATE_ATOM)
            ];
            $metaKey = "events/metadata/event-{$eventId}-".time().".json";
            try {
                $s3->putObject([
                    'Bucket'=>$bucket,
                    'Key'=>$metaKey,
                    'Body'=>json_encode($meta, JSON_PRETTY_PRINT),
                    'ContentType'=>'application/json'
                ]);
            } catch (AwsException $e) {
                $messages[] = "Event created but failed to save metadata to S3: ".htmlspecialchars($e->getAwsErrorMessage() ?: $e->getMessage());
            }
        } else {
            $errors[] = "DB insert failed: " . $conn->error;
        }
    }
}

/* Handle event delete */
if ($isLoggedIn && $_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete_event') {
    $id = intval($_POST['id'] ?? 0);
    if ($id) {
        $res = $conn->query("SELECT banner_s3_key FROM events WHERE id=".$id);
        if ($row = $res->fetch_assoc()) {
            $s3Key = $row['banner_s3_key'];
            if ($s3Key) {
                try { $s3->deleteObject(['Bucket'=>$bucket,'Key'=>$s3Key]); } catch (AwsException $e) { /* ignore */ }
            }
            $conn->query("DELETE FROM events WHERE id=".$id);
            $messages[] = "Event deleted.";
        }
    }
}

/* fetch events for admin listing */
$events = [];
$res = $conn->query("SELECT * FROM events ORDER BY created_at DESC");
while ($r = $res->fetch_assoc()) $events[] = $r;
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin - Event Portal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <style>
    :root {
      --primary-color: #6366f1;
      --secondary-color: #8b5cf6;
      --danger-color: #ef4444;
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
      background: linear-gradient(135deg, var(--danger-color), #f97316);
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
    .admin-card {
      transition: all 0.3s ease;
      border: none;
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .admin-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
    .btn-danger-admin {
      background: linear-gradient(135deg, var(--danger-color), #dc2626);
      border: none;
      color: white;
      padding: 8px 20px;
      border-radius: 20px;
      transition: all 0.3s ease;
    }
    .btn-danger-admin:hover {
      transform: scale(1.05);
      box-shadow: 0 5px 15px rgba(239, 68, 68, 0.4);
    }
    .btn-success-admin {
      background: linear-gradient(135deg, #10b981, #059669);
      border: none;
      color: white;
    }
    .navbar-brand {
      font-weight: 700;
      font-size: 1.5rem;
      color: var(--danger-color) !important;
    }
    .login-card {
      background: white;
      border-radius: 20px;
      box-shadow: 0 10px 40px rgba(0,0,0,0.2);
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
      <i class="bi bi-shield-lock-fill"></i> Admin Panel
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
    <h1><i class="bi bi-shield-lock-fill"></i> Admin Panel</h1>
    <p>Manage events and registrations</p>
  </div>
  <?php if ($errors): ?><div class="alert alert-danger"><?php foreach ($errors as $e) echo htmlspecialchars($e)."<br>"; ?></div><?php endif; ?>
  <?php if ($messages): ?><div class="alert alert-success"><?php foreach ($messages as $m) echo htmlspecialchars($m)."<br>"; ?></div><?php endif; ?>

  <?php if (!$isLoggedIn): ?>
    <div class="card mx-auto mt-4" style="max-width:480px">
      <div class="card-body">
        <h5 class="card-title">Admin Login</h5>
        <form method="post">
          <input type="hidden" name="action" value="login">
          <input type="password" name="password" class="form-control mb-2" placeholder="Password" required>
          <button class="btn btn-primary w-100">Login</button>
        </form>
      </div>
    </div>
  <?php else: ?>
    <div class="card mt-3">
      <div class="card-body">
        <h5>Create Event</h5>
        <form method="post" enctype="multipart/form-data">
          <input type="hidden" name="action" value="create_event">
          <div class="mb-2"><input class="form-control" name="title" placeholder="Event title" required></div>
          <div class="mb-2"><textarea class="form-control" name="description" placeholder="Description"></textarea></div>
          <div class="mb-2"><input class="form-control" name="event_date" type="date" required></div>
          <div class="mb-2">
            <label class="form-label small">Upload banner (optional)</label>
            <input type="file" name="banner" class="form-control">
          </div>
          <button class="btn btn-success">Create Event</button>
        </form>
        <a href="admin.php?logout=1" class="btn btn-danger mt-3">Logout</a>
      </div>
    </div>

    <h3 class="mt-4">Existing Events</h3>
    <div class="row">
      <?php foreach($events as $ev): ?>
        <div class="col-md-6 mb-3">
          <div class="card h-100">
            <?php if ($ev['banner_url']): ?>
              <img src="<?= htmlspecialchars($ev['banner_url']) ?>" class="card-img-top" alt="banner">
            <?php endif; ?>
            <div class="card-body">
              <h5><?= htmlspecialchars($ev['title']) ?></h5>
              <p class="text-muted"><?= htmlspecialchars($ev['event_date']) ?></p>
              <p><?= nl2br(htmlspecialchars($ev['description'])) ?></p>
              <form method="post" onsubmit="return confirm('Delete?');">
                <input type="hidden" name="action" value="delete_event">
                <input type="hidden" name="id" value="<?= $ev['id'] ?>">
                <button class="btn btn-danger">Delete</button>
              </form>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
</body>
</html>
