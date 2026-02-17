<?php
require_once "auth_session.php";
require_once "../db_config.php";

header('Content-Type: application/json');

// Check if request is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['id']) || !isset($input['table']) || !isset($input['status'])) {
        echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
        exit;
    }

    $id = intval($input['id']);
    $table = $input['table'];
    $status = $input['status'];

    // Validate table name to prevent SQL injection
    $allowed_tables = ['consultations', 'internships', 'job_applications'];
    if (!in_array($table, $allowed_tables)) {
        echo json_encode(['success' => false, 'message' => 'Invalid table name']);
        exit;
    }

    // Validate status
    $allowed_statuses = ['pending', 'completed'];
    if (!in_array($status, $allowed_statuses)) {
        echo json_encode(['success' => false, 'message' => 'Invalid status']);
        exit;
    }

    try {
        $sql = "UPDATE $table SET status = :status WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update status']);
        }
    } catch (PDOException $e) {
        error_log("Database Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error occurred']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
