<?php
require 'config.php';

header('Content-Type: application/json');

$errors=[]; $result = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'register') {
    $event_id = intval($_POST['event_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if (!$event_id) $errors[] = "Invalid event.";
    if ($name === '') $errors[] = "Name required.";
    if ($email === '') $errors[] = "Email required.";

    if (!$errors) {
        // Check if email already registered for this event
        $chkStmt = $conn->prepare("SELECT id FROM registrations WHERE event_id = ? AND email = ?");
        $chkStmt->bind_param("is", $event_id, $email);
        $chkStmt->execute();
        $chkResult = $chkStmt->get_result();
        
        if ($chkResult->num_rows > 0) {
            $result['message'] = "You are already registered for this event!";
            echo json_encode($result);
            exit;
        } else {
            $stmt = $conn->prepare("INSERT INTO registrations (event_id, name, email, phone, extra, s3_key) VALUES (?, ?, ?, ?, ?, ?)");
            $extra = '';
            $s3_key = null;
            $stmt->bind_param("isssss", $event_id, $name, $email, $phone, $extra, $s3_key);
            if ($stmt->execute()) {
                $regId = $stmt->insert_id;
                // write JSON to S3
                $regData = [
                    'id'=>$regId,
                    'event_id'=>$event_id,
                    'name'=>$name,
                    'email'=>$email,
                    'phone'=>$phone,
                    'created_at'=>date(DATE_ATOM)
                ];
                $metaKey = "registrations/metadata/reg-{$regId}-".time().".json";
                try {
                    $s3->putObject([
                        'Bucket'=>$bucket,
                        'Key'=>$metaKey,
                        'Body'=>json_encode($regData, JSON_PRETTY_PRINT),
                        'ContentType'=>'application/json'
                    ]);
                } catch (AwsException $e) {
                    // not critical: registration succeeded but metadata save failed
                }
                $result['success'] = true;
                $result['message'] = "Registration successful!";
                $result['redirect'] = true;
                echo json_encode($result);
                exit;
            } else {
                $result['message'] = "DB insert failed: " . $conn->error;
                echo json_encode($result);
                exit;
            }
        }
    } else {
        $result['message'] = implode(" ", $errors);
        echo json_encode($result);
        exit;
    }
}

$result['message'] = "Invalid request.";
echo json_encode($result);
exit;
