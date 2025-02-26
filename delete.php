<?php
include 'db.php'; // Database connection

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);

    // Check if record exists before deleting
    $check_sql = "SELECT * FROM personal_info WHERE id=?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        // If record exists, proceed with delete
        $sql = "DELETE FROM personal_info WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            header("Location: submit.php?success=deleted");
            exit();
        } else {
            echo "<h3 style='color:red; text-align:center;'>Error deleting record.</h3>";
        }
    } else {
        echo "<h3 style='color:red; text-align:center;'>Record not found!</h3>";
    }
} else {
    echo "<h3 style='color:red; text-align:center;'>Invalid request!</h3>";
}
?>
