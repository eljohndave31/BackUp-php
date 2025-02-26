<?php
include 'db.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    function clean_input($data) {
        return htmlspecialchars(stripslashes(trim($data)));
    }

    $errors = [];

    $required_fields = [
        'last_name', 'first_name', 'middle_name', 'dob', 'sex', 'civil_status', 'nationality',
        'birth_street', 'birth_city', 'birth_province', 'birth_country',
        'home_street', 'home_city', 'home_province', 'home_country', 'home_zip_code', 'mobile'
    ];

    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $errors[] = ucfirst(str_replace("_", " ", $field)) . " is required.";
        }
    }

    // Specific field validations
    if (!empty($_POST['last_name']) && !preg_match("/^[A-Za-z\s]+$/", $_POST['last_name'])) {
        $errors[] = "Last name should only contain letters and spaces.";
    }
    if (!empty($_POST['first_name']) && !preg_match("/^[A-Za-z\s]+$/", $_POST['first_name'])) {
        $errors[] = "First name should only contain letters and spaces.";
    }
    if (!empty($_POST['middle_name']) && !preg_match("/^[A-Za-z\s]+$/", $_POST['middle_name'])) {
        $errors[] = "Middle name should only contain letters and spaces.";
    }
    if (!empty($_POST['mobile']) && !preg_match("/^\d{10,}$/", $_POST['mobile'])) {
        $errors[] = "Mobile Number should contain at least 10 digits.";
    }
    if (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // Age validation
    if (!empty($_POST['dob'])) {
        $dob_date = new DateTime($_POST['dob']);
        $today = new DateTime();
        $age = $today->diff($dob_date)->y;

        if ($age < 18) {
            $errors[] = "You must be at least 18 years old.";
        }
    }

    if (!empty($errors)) {
        // If there are validation errors, display them
        echo "<h2 style='color: red;'>Please fix the following errors:</h2>";
        echo "<ul style='color: red;'>";
        foreach ($errors as $error) {
            echo "<li>$error</li>";
        }
        echo "</ul>";
        echo "<br><a href='javascript:history.back()'>Back to Previous Page</a>";
        exit();
    }

    // Prepare the data for insertion
    $last_name = clean_input($_POST['last_name']);
    $first_name = clean_input($_POST['first_name']);
    $middle_name = clean_input($_POST['middle_name']);
    $dob = clean_input($_POST['dob']);
    $sex = clean_input($_POST['sex']);
    $civil_status = clean_input($_POST['civil_status']);
    $tin = !empty($_POST['tin']) ? clean_input($_POST['tin']) : NULL;
    $nationality = clean_input($_POST['nationality']);
    $religion = !empty($_POST['religion']) ? clean_input($_POST['religion']) : NULL;
    $birth_street = clean_input($_POST['birth_street']);
    $birth_city = clean_input($_POST['birth_city']);
    $birth_province = clean_input($_POST['birth_province']);
    $birth_country = clean_input($_POST['birth_country']);
    $birth_zip_code = !empty($_POST['birth_zip_code']) ? clean_input($_POST['birth_zip_code']) : NULL;
    $home_street = clean_input($_POST['home_street']);
    $home_city = clean_input($_POST['home_city']);
    $home_province = clean_input($_POST['home_province']);
    $home_country = clean_input($_POST['home_country']);
    $home_zip_code = clean_input($_POST['home_zip_code']);
    $mobile = clean_input($_POST['mobile']);
    $email = !empty($_POST['email']) ? clean_input($_POST['email']) : NULL;
    $telephone = !empty($_POST['telephone']) ? clean_input($_POST['telephone']) : NULL;
    $father_last_name = clean_input($_POST['father_last_name']);
    $father_first_name = clean_input($_POST['father_first_name']);
    $father_middle_name = clean_input($_POST['father_middle_name']);
    $mother_last_name = clean_input($_POST['mother_last_name']);
    $mother_first_name = clean_input($_POST['mother_first_name']);
    $mother_middle_name = clean_input($_POST['mother_middle_name']);

    $sql = "INSERT INTO personal_info 
    (last_name, first_name, middle_name, dob, sex, civil_status, 
    nationality, religion, birth_street, birth_city, birth_province, birth_country, birth_zip_code, 
    home_street, home_city, home_province, home_country, home_zip_code,  -- ✅ DITO NADAGDAG
    mobile, email, telephone, father_last_name, father_first_name, father_middle_name, 
    mother_last_name, mother_first_name, mother_middle_name, tin) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

// ✅ Siguraduhin na 28 values ang nakasama sa bind_param()
$stmt->bind_param(
    "ssssssssssssssssssssssssssss", 
    $last_name, $first_name, $middle_name, $dob, $sex, $civil_status, 
    $nationality, $religion, $birth_street, $birth_city, $birth_province, $birth_country, $birth_zip_code, 
    $home_street, $home_city, $home_province, $home_country, $home_zip_code,  // ✅ DITO NADAGDAG
    $mobile, $email, $telephone, $father_last_name, $father_first_name, $father_middle_name, 
    $mother_last_name, $mother_first_name, $mother_middle_name, $tin
);


if ($stmt->execute()) {
    header("Location: submit.php?success=1");
    exit();
} else {
    echo "Error: " . $stmt->error;
}

}

// Fetch all records AFTER redirection (safe from form resubmission)
include 'db.php'; // Reconnect to database after redirect
$sql = "SELECT * FROM personal_info ORDER BY id DESC";
$result = $conn->query($sql);

function calculate_age($dob) {
    $dob_date = new DateTime($dob);
    $today = new DateTime();
    return $today->diff($dob_date)->y;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Submission</title>
    <link rel="stylesheet" href="submit.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<?php if (isset($_GET['success'])): ?>
    <h2 style="color: green;">Submitted Successfully!</h2>
    <?php elseif (isset($_GET['success']) && $_GET['success'] == 'updated'): ?>
        <h2 style="color: green;"> Updated Successfully!</h2>
<?php endif; ?>

<!-- <h2>All Records</h2> -->
<table border="1">
    <tr>
        <th>ID</th>
        <th>Full Name</th>
        <!-- <th>Date of Birth</th> -->
        <th>Age</th>
        <th>Sex</th>
        <th>Civil Status</th>
        <th>Nationality</th>
        <!-- <th>Religion</th> -->
        <th>Place of Birth</th>
        <th>Home Address</th>
        <th>Mobile</th>
        <th>Email</th>
        <!-- <th>Telephone</th> -->
        <th>Father's Name</th>
        <th>Mother's Name</th>
        <th>Actions</th>
    </tr>

    <?php while ($user = $result->fetch_assoc()): ?>
    <tr>
        <td><?= htmlspecialchars($user['id']) ?></td>
        <td><?= htmlspecialchars($user['last_name'] . " " . $user['first_name'] . " " . $user['middle_name']) ?></td>
        <!-- <td><?= htmlspecialchars($user['dob']) ?></td> -->
        <td><?= calculate_age($user['dob']) ?></td>
        <td><?= htmlspecialchars($user['sex']) ?></td>
        <td><?= htmlspecialchars($user['civil_status']) ?></td>
        <td><?= htmlspecialchars($user['nationality']) ?></td>
        <!-- <td><?= htmlspecialchars($user['religion']) ?></td> -->
        <td><?= htmlspecialchars($user['birth_street'] . ", " . $user['birth_city'] . ", " . $user['birth_province'] . ", " . $user['birth_country'] . ", " . $user['birth_zip_code']) ?></td>
        <td><?= htmlspecialchars($user['home_street'] . ", " . $user['home_city'] . ", " . $user['home_province'] . ", " . $user['home_country'] . ", " . $user['home_zip_code']) ?></td>
        <td><?= htmlspecialchars($user['mobile']) ?></td>
        <td><?= htmlspecialchars($user['email']) ?></td>
        <!-- <td><?= htmlspecialchars($user['telephone']) ?></td> -->
        <td><?= htmlspecialchars($user['father_first_name'] . " " . $user['father_middle_name'] . " " . $user['father_last_name']) ?></td>
        <td><?= htmlspecialchars($user['mother_first_name'] . " " . $user['mother_middle_name'] . " " . $user['mother_last_name']) ?></td>
        <td class="action-buttons">
            <a href="edit.php?id=<?= $user['id'] ?>" class="edit"><i class="fas fa-edit"></i> Edit</a> | 
            <a href="delete.php?id=<?= $user['id'] ?>" class="delete" onclick="return confirm('Are you sure?')"><i class="fas fa-trash-alt"></i> Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<br><br>
<a href="index.php" class="btn btn-primary">Add More</a>

</body>
</html>

<?php 
$conn->close();
?>