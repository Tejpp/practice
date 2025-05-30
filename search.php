<?php
// Database connection
require 'db_connect.php';

// Admit logic 
$successMessage = '';

if (isset($_GET['admit_student_id'])) {
    $admitId = mysqli_real_escape_string($conn, $_GET['admit_student_id']);

    // Fetch student's full name before updating
    $fetchQuery = "SELECT first_name, last_name FROM studentsdata WHERE student_id = '$admitId'";
    $result = mysqli_query($conn, $fetchQuery);

    if ($row = mysqli_fetch_assoc($result)) {
        $fullName = trim($row['first_name'] . ' ' . $row['last_name']);

        // Update admission status
        $updateQuery = "UPDATE studentsdata SET admission_status = 'Admitted' WHERE student_id = '$admitId'";
        if (mysqli_query($conn, $updateQuery)) {
            $successMessage = "$fullName Admitted Successfully";
        }
    }
}


// search student 
$searcheddata = null; // SearchedStudent is the variable defined below by string I have changed it as searcheddata
if (isset($_POST['search'])) {
    $searchTerm = mysqli_real_escape_string($conn, $_POST['search_term']); // $_POST stands for post search to the search bar to requiest result
    $searchParts = explode(' - ', $searchTerm);
    $searchID = isset($searchParts[0]) ? trim($searchParts[0]) : '';
// SQL Query 
    $searchQuery = "
        SELECT * FROM studentsdata
        WHERE student_id = '$searchID'
        OR CONCAT(first_name, ' ', last_name ) LIKE '%$searchTerm%' 
        LIMIT 1
    ";
    // if only one row was there in database table = OR student_name (CONCAT(first_name, ' ', last_name )) LIKE '%$searchTerm%'
    $result = mysqli_query($conn, $searchQuery);
    if (mysqli_num_rows($result) > 0) {
        $searcheddata = mysqli_fetch_assoc($result); // this is the string to define variable
    }
}
?>
<!--  HTML part for search label and to show the searched result  -->  
<!DOCTYPE html>
<html>
<head>
    <title>Search Student</title>
  
</head>
<body>

<h2>Search Student</h2>

<!-- this whole part is for search lable and suggest student name and id starts -->

<form method="POST">
<!-- Using method="POST" because the search form data is being handled with $_POST in PHP -->
<input type="text" name="search_term" placeholder="Enter student ID or name" list="suggestion" required>
    <datalist id="suggestion">
<!-- datalist id "suggestion" should match the list = "suggestion" this will track id -->
    <!-- name = "search_term" because this is applied in search field -->
        <?php
        $suggest = mysqli_query($conn, "SELECT student_id, first_name, last_name FROM studentsdata"); // this is string to define what it will do
        while ($row = mysqli_fetch_assoc($suggest)) {
            $fullName = trim($row['first_name'] . ' ' . $row['last_name']); // full name is in concat with string
            echo "<option value='{$row['student_id']} - {$fullName}'>"; // full name has been used as variable
        }
        ?>
    </datalist>
    <button type="submit" name="search">Search</button>
    <button onclick="window.location.href='studentform.php'">Add Student</button>
</form>

<!-- this whole part is for search lable and suggest student name and id ends -->


<!-- after search what will be displayed ? -->

<?php if ($searcheddata): ?>
     <h3>Student Information</h3>
     <table border="1" cellpadding="2">
        <!-- cellpadding can increase and decrease the cell width and height -->
        <!-- this is html table where data will be displayed -->
        <tr>
            <th>ID</th>
            <th>Student ID</th>
            <th>Full Name</th>
            <th>Gender</th>
            <th>Date of Birth</th>
            <th>Class</th>
            <th>Address</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Guardian</th>
            <th>Admission Status</th>
            <th>Created At</th>
        </tr>
        <!-- this is php code to fetch data to the html table (above) -->
        <tr>
            <td><?= htmlspecialchars($searcheddata['id']); ?></td>
            <td><?= htmlspecialchars($searcheddata['student_id']); ?></td>
            <td><?= htmlspecialchars($searcheddata['first_name'] . ' ' . $searcheddata['last_name']); ?></td>
            <!-- concat first name and last to show to the html full name column and searched data is the string for variable here -->
            <td><?= htmlspecialchars($searcheddata['gender']); ?></td>
            <td><?= htmlspecialchars($searcheddata['date_of_birth']); ?></td>
            <td><?= htmlspecialchars($searcheddata['class']); ?></td>
            <td><?= htmlspecialchars($searcheddata['address']); ?></td>
            <td><?= htmlspecialchars($searcheddata['phone']); ?></td>
            <td><?= htmlspecialchars($searcheddata['email']); ?></td>
            <td><?= htmlspecialchars($searcheddata['guardian_name']); ?></td>
            <td>
    <?php if ($searcheddata['admission_status'] === 'Admitted'): ?>
        <span style="background-color: #d4edda; color: #155724; padding: 3px 8px; border-radius: 4px;">
            <?= htmlspecialchars($searcheddata['admission_status']); ?>
        </span>
    <?php else: ?>
        <?= htmlspecialchars($searcheddata['admission_status']); ?><br>
        <a href="?admit_student_id=<?= urlencode($searcheddata['student_id']); ?>" onclick="return confirm('Admit this student?')">Admit Now</a>
    <?php endif; ?>
</td>

            <td><?= htmlspecialchars($searcheddata['created_at']); ?></td>
        </tr>
    </table>
                
<?php endif; ?>
 
<!-- after search what will be displayed ? endif logic for the what will be displayed after search -->

</body>
</html>
