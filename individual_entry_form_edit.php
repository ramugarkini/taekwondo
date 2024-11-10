<?php

$mode = 'Add';
$id = 0;

// Fetch users from the database
$stmtc = $conn->prepare("SELECT id, district_name FROM districts");
$stmtc->execute();
$resultc = $stmtc->get_result();
$districts = $resultc->fetch_all(MYSQLI_ASSOC);

// Check if an ID is passed in the URL for editing
if (isset($uri_segments[1]) && intval(decrypt($uri_segments[1], $key)) > 0) {
    $id = intval(decrypt($uri_segments[1], $key));
    $mode = 'Edit';

    // Fetch data from the database if editing
    $query = $conn->prepare("SELECT * FROM individual_entry_form WHERE id = ?");
    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();
    $row = $result->fetch_assoc();

    // If no data is found for the given ID, redirect or show an error
    if (!$row) {
        $_SESSION['error'] = "Record not found!";
        header("Location: /");
        exit();
    }
}

$uploadsDir = 'uploads/photo';

// Function to handle individual file upload
function uploadFile($file, $id, $uploadsDir) {
    if ($file['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $file['tmp_name'];
        $fileName = $file['name'];
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        $cleanFileName = $id . '.' . $fileExtension;

        // Create a nested directory structure based on the ID
        $nestedPath = $uploadsDir . '/' . implode('/', str_split($id));
        if (!is_dir($nestedPath)) {
            mkdir($nestedPath, 0777, true);
        }

        // Define the final destination for the uploaded file
        $destination = "$nestedPath/$cleanFileName";
        if (move_uploaded_file($fileTmpPath, $destination)) {
            return $destination; // Return the path if successful
        }
    }
    return null; // Return null if the upload failed
}

// Function to delete a file if it exists
function deleteFile($path) {
    if (file_exists($path)) {
        unlink($path);
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        // Collecting form data for adding
        $type = $_POST['type'];
        $category = $_POST['category'];
        $gender = $_POST['gender'];
        $weight = $_POST['weight'];
        $weight_category = $_POST['weight_category'];
        $name = $_POST['name'];
        $state_organization_name = $_POST['state_organization_name'];
        $date_of_birth = $_POST['date_of_birth'];
        $age = $_POST['age'];
        $parent_guardian_name = $_POST['parent_guardian_name'];
        $current_belt_grade = $_POST['current_belt_grade'];
        $tfi_id_no = $_POST['tfi_id_no'];
        $belt_certificate_no = $_POST['belt_certificate_no'];
        $academic_qualification = $_POST['academic_qualification'];
        $name_of_school = $_POST['name_of_school'];
        $board_university_name = $_POST['board_university_name'];
        $user_id = $_SESSION['user_details']['user_id'] ?? NULL;
        $district_id = $_POST['district_id'];

        $photo_path = '';
        $signature_parent_guardian_path = '';
        $signature_participant_path = '';
        $signature_president_secretary_path = '';
        $state_association_stamp_path = '';


        

        // Prepare the SQL statement
        $stmt = $conn->prepare("INSERT INTO individual_entry_form (
                    type, category, gender, weight, weight_category, name, photo_path, 
                    state_organization_name, date_of_birth, age, parent_guardian_name, 
                    current_belt_grade, tfi_id_no, belt_certificate_no, academic_qualification, 
                    name_of_school, board_university_name, signature_parent_guardian_path, 
                    signature_participant_path, signature_president_secretary_path, 
                    state_association_stamp_path, user_id, district_id
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");


        // Bind parameters
        $stmt->bind_param("sssssssssssssssssssssss", 
            $type, $category, $gender, $weight, $weight_category, $name, 
            $photo_path, $state_organization_name, $date_of_birth, $age, 
            $parent_guardian_name, $current_belt_grade, $tfi_id_no, 
            $belt_certificate_no, $academic_qualification, $name_of_school, 
            $board_university_name, $signature_parent_guardian_path, 
            $signature_participant_path, $signature_president_secretary_path, 
            $state_association_stamp_path, $user_id, $district_id
        );

        // Execute insert query
        if ($stmt->execute()) {
            $last_id = $stmt->insert_id; // Get the last inserted ID
            // File uploads
            $photo_path = uploadFile($_FILES['photo_path'], $last_id, 'uploads/photo');
            $signature_parent_guardian_path = uploadFile($_FILES['signature_parent_guardian_path'], $last_id, 'uploads/signatures/parent_guardian');
            $signature_participant_path = uploadFile($_FILES['signature_participant_path'], $last_id, 'uploads/signatures/participant');
            $signature_president_secretary_path = uploadFile($_FILES['signature_president_secretary_path'], $last_id, 'uploads/signatures/president_secretary');
            $state_association_stamp_path = uploadFile($_FILES['state_association_stamp_path'], $last_id, 'uploads/stamp');

            $sql = "UPDATE individual_entry_form SET 
                photo_path = '$photo_path', 
                signature_parent_guardian_path = '$signature_parent_guardian_path', 
                signature_participant_path = '$signature_participant_path', 
                signature_president_secretary_path = '$signature_president_secretary_path', 
                state_association_stamp_path = '$state_association_stamp_path' 
                WHERE id = '$last_id'";

            // Execute update query
            if ($conn->query($sql)) {
                $_SESSION['success'] = 'Entry Added Successfully';
                $enc_last_id = encrypt($last_id, $key);
                header("Location: /individual_entry_form/$enc_last_id");
                exit();
            }

        } else {
            $_SESSION['error'] = 'Something went wrong while adding: ' . $stmt->error;
        }

        $stmt->close(); // Close the statement
    } elseif (isset($_POST['update'])) {
        // Collect form data for updating
        $id = $_POST['id'];  // Ensure the ID is collected for updating
        $type = $_POST['type'];
        $category = $_POST['category'];
        $gender = $_POST['gender'];
        $weight = $_POST['weight'];
        $weight_category = $_POST['weight_category'];
        $name = $_POST['name'];
        $state_organization_name = $_POST['state_organization_name'];
        $date_of_birth = $_POST['date_of_birth'];
        $age = $_POST['age'];
        $parent_guardian_name = $_POST['parent_guardian_name'];
        $current_belt_grade = $_POST['current_belt_grade'];
        $tfi_id_no = $_POST['tfi_id_no'];
        $belt_certificate_no = $_POST['belt_certificate_no'];
        $academic_qualification = $_POST['academic_qualification'];
        $name_of_school = $_POST['name_of_school'];
        $board_university_name = $_POST['board_university_name'];
        $user_id = $_SESSION['user_details']['user_id'] ?? NULL;
        $district_id = $_POST['district_id'];

        $existing_photo_path = !empty($row['photo_path']) ? $row['photo_path'] : '';

        // Handle file uploads (check if new files were uploaded)
        $photo_path = !empty($_FILES['photo_path']['name']) 
                ? uploadFile($_FILES['photo_path'], $id, 'uploads/photo') 
                : (isset($_POST['photo_path']) && $_POST['photo_path'] !== '' ? $_POST['photo_path'] : $existing_photo_path);

        if ($_POST['remove_photo_path'] == '' && $_POST['photo_path'] == '') {
            $photo_path = '';
        }

        // Existing paths
        $existing_signature_parent_guardian_path = !empty($row['signature_parent_guardian_path']) ? $row['signature_parent_guardian_path'] : '';
        $existing_signature_participant_path = !empty($row['signature_participant_path']) ? $row['signature_participant_path'] : '';
        $existing_signature_president_secretary_path = !empty($row['signature_president_secretary_path']) ? $row['signature_president_secretary_path'] : '';
        $existing_state_association_stamp_path = !empty($row['state_association_stamp_path']) ? $row['state_association_stamp_path'] : '';

        // Handle file uploads for Parent/Guardian signature
        $signature_parent_guardian_path = !empty($_FILES['signature_parent_guardian_path']['name']) 
            ? uploadFile($_FILES['signature_parent_guardian_path'], $id, 'uploads/signatures/parent_guardian') 
            : (isset($_POST['signature_parent_guardian_path']) ? $_POST['signature_parent_guardian_path'] : $existing_signature_parent_guardian_path);

        // Handle file uploads for Participant signature
        $signature_participant_path = !empty($_FILES['signature_participant_path']['name']) 
            ? uploadFile($_FILES['signature_participant_path'], $id, 'uploads/signatures/participant') 
            : (isset($_POST['signature_participant_path']) ? $_POST['signature_participant_path'] : $existing_signature_participant_path);

        // Handle file uploads for President/Secretary signature
        $signature_president_secretary_path = !empty($_FILES['signature_president_secretary_path']['name']) 
            ? uploadFile($_FILES['signature_president_secretary_path'], $id, 'uploads/signatures/president_secretary') 
            : (isset($_POST['signature_president_secretary_path']) ? $_POST['signature_president_secretary_path'] : $existing_signature_president_secretary_path);

        // Handle file uploads for State Association stamp
        $state_association_stamp_path = !empty($_FILES['state_association_stamp_path']['name']) 
            ? uploadFile($_FILES['state_association_stamp_path'], $id, 'uploads/stamp') 
            : (isset($_POST['state_association_stamp_path']) ? $_POST['state_association_stamp_path'] : $existing_state_association_stamp_path);

        // Handle removals
        if (isset($_POST['remove_signature_parent_guardian_path']) && $_POST['remove_signature_parent_guardian_path'] === '') {
            $signature_parent_guardian_path = '';
        }
        if (isset($_POST['remove_signature_participant_path']) && $_POST['remove_signature_participant_path'] === '') {
            $signature_participant_path = '';
        }
        if (isset($_POST['remove_signature_president_secretary_path']) && $_POST['remove_signature_president_secretary_path'] === '') {
            $signature_president_secretary_path = '';
        }
        if (isset($_POST['remove_state_association_stamp_path']) && $_POST['remove_state_association_stamp_path'] === '') {
            $state_association_stamp_path = '';
        }


        // Prepare the SQL statement for updating
        $sql = "UPDATE individual_entry_form SET 
            type = '$type', 
            category = '$category', 
            gender = '$gender', 
            weight = '$weight', 
            weight_category = '$weight_category', 
            name = '$name', 
            photo_path = '$photo_path', 
            state_organization_name = '$state_organization_name', 
            date_of_birth = '$date_of_birth', 
            age = '$age', 
            parent_guardian_name = '$parent_guardian_name', 
            current_belt_grade = '$current_belt_grade', 
            tfi_id_no = '$tfi_id_no', 
            belt_certificate_no = '$belt_certificate_no', 
            academic_qualification = '$academic_qualification', 
            name_of_school = '$name_of_school', 
            board_university_name = '$board_university_name', 
            signature_parent_guardian_path = '$signature_parent_guardian_path', 
            signature_participant_path = '$signature_participant_path', 
            signature_president_secretary_path = '$signature_president_secretary_path', 
            state_association_stamp_path = '$state_association_stamp_path',
            user_id = '$user_id', 
            district_id = '$district_id'
        WHERE id = '$id'";

        // Execute update query
        if ($conn->query($sql)) {
            $_SESSION['success'] = 'Changes Updated Successfully';
            $enc_id = encrypt($id, $key);
            header("Location: /individual_entry_form/$enc_id");
            exit();
        } else {
            $_SESSION['error'] = 'Something went wrong while updating: ' . $conn->error;
        }
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];

        // Fetch the file paths from the database to delete associated files
        $stmt = $conn->prepare("SELECT photo_path, signature_parent_guardian_path, signature_participant_path, signature_president_secretary_path, state_association_stamp_path FROM individual_entry_form WHERE id = ?");
        $stmt->bind_param("i", $id); // Assuming $id is an integer
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();

        if ($data) {
            // Delete files associated with the entry
            deleteFile($data['photo_path']);
            deleteFile($data['signature_parent_guardian_path']);
            deleteFile($data['signature_participant_path']);
            deleteFile($data['signature_president_secretary_path']);
            deleteFile($data['state_association_stamp_path']);

            // Prepare the delete statement
            $deleteStmt = $conn->prepare("DELETE FROM individual_entry_form WHERE id = ?");
            $deleteStmt->bind_param("i", $id); // Assuming $id is an integer

            // Execute delete query
            if ($deleteStmt->execute()) {
                // $_SESSION['success'] = 'Entry Deleted Successfully';
                header("Location: /");
                exit();
            } else {
                $_SESSION['error'] = 'Something went wrong in deleting the entry: ' . $deleteStmt->error;
            }
            
            $deleteStmt->close(); // Close the delete statement
        } else {
            $_SESSION['error'] = 'No entry found for the provided ID.';
        }

        $stmt->close(); // Close the select statement
    }
}

?>

<style>
    /*body {
        font-family: Arial, sans-serif;
        margin: 20px;
    }*/
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        border: 1px solid black;
        padding: 8px;
        text-align: left;
    }
    th {
        background-color: #f2f2f2;
    }
    .header {
        text-align: center;
        font-weight: bold;
        font-size: 18px;
        margin-bottom: 20px;
    }
    .sub-header {
        text-align: center;
        margin-bottom: 10px;
    }
    h2.blue-header {
        background-color: #e0e0e0;
        text-align: center;
        color: #007bff;
        font-weight: bold;
        padding: 10px; /* Optional */
        margin: 0; /* Optional */
    }

    .declaration {
        margin-top: 20px;
        font-size: smaller;
    }
    .footer-table {
        margin-top: 20px;
        width: 100%;
        border: none;
    }
    .footer-table td {
        border: none;
    }
    .no-right-border {
        border-right: none; /* Remove right border from the first td */
    }

    .no-left-border {
        border-left: none; /* Remove left border from the second td */
    }

</style>
<style>
    /* Custom styles for the file input */
    .custom-file {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center; /* Center content horizontally */
        margin: 0 auto; /* Center the file input container */
        max-width: 300px; /* Limit maximum width for better responsiveness */
    }

    .custom-file-input {
        opacity: 0; /* Hide the default file input */
        position: absolute;
        z-index: 1;
        cursor: pointer; /* Change cursor to pointer */
        width: 100%; /* Make the file input cover the entire label */
        height: 100%; /* Make the file input cover the entire label */
    }

    .custom-file-label {
        border: 1px solid #ccc;
        border-radius: 4px;
        padding: 12px 20px; /* Add padding for better spacing */
        background-color: #f8f9fa;
        color: #495057;
        transition: background-color 0.3s, color 0.3s, border-color 0.3s; /* Add border color transition */
        width: 100%; /* Ensure the label takes full width */
        text-align: center; /* Center the text */
    }

    .custom-file-label:hover {
        background-color: #e2e6ea;
    }

    .custom-file-input:focus + .custom-file-label {
        border-color: #80bdff; /* Change border color on focus */
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25); /* Add shadow on focus */
    }

    /* Optional: Style for the label when a file is selected */
    .custom-file-input:valid + .custom-file-label::after {
        content: "✓"; /* Add a checkmark icon or text */
        color: green; /* Change color to indicate success */
        margin-left: 10px; /* Space between text and checkmark */
    }
</style>
<style type="text/css">
    /* Basic styling for the modal overlay */
    .custom-modal {
        display: none; /* Hidden by default */
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent background */
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    /* Modal box styling */
    .modal-content {
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        max-width: 400px;
        width: 90%;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        position: relative;
        text-align: center;
    }

    /* Header styling */
    .modal-header h4 {
        margin: 0;
    }

    /* Close button styling */
    .close-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 24px;
        cursor: pointer;
    }

    /* Footer button styling */
    .modal-footer {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
    }

    .btn {
        padding: 8px 16px;
        cursor: pointer;
        border: none;
        border-radius: 4px;
    }

    .btn-secondary {
        background-color: #ccc;
        color: #000;
    }

    .btn-danger {
        background-color: #d9534f;
        color: #fff;
    }

</style>

<div class="container">
    <br>
    <div class="d-flex align-items-center justify-content-between mb-3" style="display: flex; align-items: center;">
        <!-- Back Button -->
        <a href="javascript:void(0);" onclick="window.history.back();" class="btn btn-primary" style="margin-right: auto;">
            <span class="glyphicon glyphicon-arrow-left"></span> Back
        </a>

        <!-- Center Title -->
        <h2 class="page-header text-center" style="margin: 0 auto; flex-grow: 1; text-align: center;">
            <?php echo $mode; ?> Individual Entry Form
        </h2>

        <!-- New Button -->
        <!-- <a href="/individual_entry_form/0" class="btn btn-primary" style="margin-left: auto;">
            <span class="glyphicon glyphicon-plus"></span> New
        </a> -->
    </div>


    <?php
    // Display success or error messages
        if(isset($_SESSION['success'])){
            echo "<div class='alert alert-success text-center'>
                <button class='close'>&times;</button>
                ".$_SESSION['success']."
            </div>";
            unset($_SESSION['success']);
        }
        if(isset($_SESSION['error'])){
            echo "<div class='alert alert-danger text-center'>
                <button class='close'>&times;</button>
                ".$_SESSION['error']."
            </div>";
            unset($_SESSION['error']);
        }
    ?>
    <br>

    <div class="header">2023 NATIONAL OPEN KYORUGI & POOMSAE TAEKWONDO CHAMPIONSHIPS</div>
    <div class="sub-header">5th to 8th October 2023<br>Noida Indoor Stadium, Sector 21A, Noida, Uttar Pradesh-201301<br>Organizer: Uttar Pradesh Taekwondo Association<br>Promoter: Taekwondo Federation of India</div>

    <h2 class="blue-header">INDIVIDUAL ENTRY FORM</h2>

    <form action="" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
	    <table>
	        <tr>
	            <th colspan="4">Mark (X) on the appropriate boxes.</th>
	        </tr>
	        <tr>
	        	<td>
                    <label>
	        	    <input type="radio" name="type" value="sub_junior" <?php echo ($row['type'] ?? '') == 'sub_junior' ? 'checked' : ''; ?>>
	        	    Sub Junior
                    </label>
	        	</td>
	        	<td>
                    <label>
	        	    <input type="radio" name="type" value="cadet" <?php echo ($row['type'] ?? '') == 'cadet' ? 'checked' : ''; ?>>
	        	    Cadet
                    </label>
	        	</td>
	        	<td>
                    <label>
	        	    <input type="radio" name="type" value="junior" <?php echo ($row['type'] ?? '') == 'junior' ? 'checked' : ''; ?>>
	        	    Junior
                    </label>
	        	</td>
	        	<td>
                    <label>
	        	    <input type="radio" name="type" value="senior" <?php echo ($row['type'] ?? '') == 'senior' ? 'checked' : ''; ?>>
	        	    Senior
                    </label>
	        	</td>
	        </tr>
	        <tr>
	            <td colspan="4">
	                <table style="width: 100%; border: none;">
	                    <tr>
	                        <td>
                                <label>Category <span style="color: red; font-size: 1.3em;">*</span></label>
	                        	<label>
						            <input type="radio" name="category" value="Individual" <?php echo ($row['category'] ?? '') == 'Individual' ? 'checked' : ''; ?>>
						            Individual
						        </label>
						        <label>
						            <input type="radio" name="category" value="Pair" <?php echo ($row['category'] ?? '') == 'Pair' ? 'checked' : ''; ?>>
						            Pair
						        </label>
						        <label>
						            <input type="radio" name="category" value="Group" <?php echo ($row['category'] ?? '') == 'Group' ? 'checked' : ''; ?>>
						            Group
						        </label>
						    </td>
	                        <td>
                                <label>Gender <span style="color: red; font-size: 1.3em;">*</span></label>
	                        	<label>
	                	            <input type="radio" name="gender" value="Male" <?php echo ($row['gender'] ?? '') == 'Male' ? 'checked' : ''; ?>>
	                	            Male
	                	        </label>
	                	        <label>
	                	            <input type="radio" name="gender" value="Female" <?php echo ($row['gender'] ?? '') == 'Female' ? 'checked' : ''; ?>>
	                	            Female
	                	        </label>
	                        </td>
	                        <td>
                                <label>Weight <span style="color: red; font-size: 1.3em;">*</span></label>
	                        	<input type="number" name="weight" class="form-control" value="<?php echo $row['weight'] ?? ''; ?>" required>
	                        </td>
	                        <td>
                                <label>Weight Category<span style="color: red; font-size: 1.3em;">*</span></label>
	                        	<input type="text" name="weight_category" class="form-control" value="<?php echo $row['weight_category'] ?? ''; ?>" required>
	                        </td>
	                        
	                    </tr>
	                </table>
	            </td>
	        </tr>
	        <tr>
		        <td colspan="4">
		            <table style="width: 100%; border: none;">
		                <tr>
		                    <td class="no-right-border">
                                <label>Name <span style="color: red; font-size: 1.3em;">*</span></label>
                            </td>
	            			<td class="no-left-border">
	            				<input type="text" name="name" class="form-control" value="<?php echo $row['name'] ?? ''; ?>" required>
		                    </td>
		                    <td class="no-right-border">
                                <label>State/Organization Name <span style="color: red; font-size: 1.3em;">*</span></label>
                            </td>
		                    <td class="no-left-border">
		                    	<input type="text" name="state_organization_name" class="form-control" value="<?php echo $row['state_organization_name'] ?? ''; ?>" required>
		                    </td>
        			        <td rowspan="5" style="text-align: center;">
                                <label>Attach One Passport Size Photo</label>
                                <!-- File Input Wrapper -->
                                <div class="custom-file mb-3" style="text-align: center; margin: 0 auto;">
                                    <input type="file" name="photo_path" id="photo_path" accept="image/*" class="custom-file-input">
                                    <label class="custom-file-label" for="photo_path">Choose file</label>
                                </div>

        	                	<!-- Check if the photo_path is not empty and display the image -->
                                <div id="current_photo_display" style="display: <?php echo empty($row['photo_path']) ? 'none' : 'block'; ?>; margin-bottom: 10px;">
                                    <img src="/<?php echo htmlspecialchars($row['photo_path']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" style="max-width: 200px; max-height: 200px; display: inline-block;">
                                </div>

                                <!-- Optional: Add a preview area for the uploaded image -->
                                <div id="image_preview" style="display: none; margin-bottom: 10px;">
                                    <img id="preview_img" src="#" alt="Image Preview" style="max-width: 200px; max-height: 200px; display: inline-block;">
                                </div>

                                <!-- Button to remove the photo -->
                                <button type="button" id="remove_photo" class="btn btn-danger mt-2">Remove Photo</button>
                                <input type="hidden" id="remove_photo_path" name="remove_photo_path" value="<?php echo $row['photo_path'] ?? ''; ?>">

        	                </td>
		                </tr>
		                <tr>
		                    <td class="no-right-border">
                                <label>Date of Birth <span style="color: red; font-size: 1.3em;">*</span></label>
                            </td>
		                    <td class="no-left-border">
		                        <input type="date" name="date_of_birth" id="date_of_birth" class="form-control" value="<?php echo $row['date_of_birth'] ?? ''; ?>" onkeyup="calculateAge()" required>
		                    </td>
		                    <td class="no-right-border">
                                <label>Age </label>
                            </td>
		                    <td class="no-left-border">
		                        <input type="number" name="age" id="age" class="form-control" value="<?php echo $row['age'] ?? ''; ?>" readonly>
		                    </td>
		                </tr>
		                <tr>
		                    <td class="no-right-border">
                                <label>Parent/Guardian Name <span style="color: red; font-size: 1.3em;">*</span></label>
                            </td>
		                    <td class="no-left-border">
		                    	<input type="text" name="parent_guardian_name" class="form-control" value="<?php echo $row['parent_guardian_name'] ?? ''; ?>" required>
		                    </td>
		                    <td class="no-right-border"></td>
		                    <td class="no-left-border"></td>
		                </tr>
		                <tr>
		                    <td class="no-right-border">
                                <label>Current Belt Grade <span style="color: red; font-size: 1.3em;">*</span></label>
                            </td>
		                    <td class="no-left-border">
		                    	<input type="text" name="current_belt_grade" class="form-control" value="<?php echo $row['current_belt_grade'] ?? ''; ?>" required>
		                    </td>
		                    <td class="no-right-border">
                                <label>TFI ID No. <span style="color: red; font-size: 1.3em;">*</span></label>
                            </td>
		                    <td class="no-left-border">
		                    	<input type="text" name="tfi_id_no" class="form-control" value="<?php echo $row['tfi_id_no'] ?? ''; ?>" required>
		                    </td>
		                </tr>
		                <tr>
		                    <td class="no-right-border">
                                <label>Belt Certificate No. <span style="color: red; font-size: 1.3em;">*</span></label>
                            </td>
		                    <td class="no-left-border">
		                    	<input type="text" name="belt_certificate_no" class="form-control" value="<?php echo $row['belt_certificate_no'] ?? ''; ?>" required>
		                    </td>
		                    <td class="no-right-border"></td>
		                    <td class="no-left-border"></td>
		                </tr>
		            </table>
		        </td>
		        
		    </tr>
		    <tr>
		        <td colspan="4" style="font-size: smaller;">
		            <strong>Note:</strong> Xerox copy of TFI ID Card, Belt Grade Certificate, Birth Certificate should be enclosed compulsorily.
		        </td>
		    </tr>
		    <tr>
		        <td colspan="4">
		            <table style="width: 100%; border: none;">
		                <tr>
		                    <td class="no-right-border">
                                <label>Academic Qualification <span style="color: red; font-size: 1.3em;">*</span></label>
                            </td>
		                    <td class="no-left-border">
		                    	<input type="text" name="academic_qualification" class="form-control" value="<?php echo $row['academic_qualification'] ?? ''; ?>" required>
		                    </td>
		                    <td class="no-right-border">
                                <label>Name of School <span style="color: red; font-size: 1.3em;">*</span></label>
                            </td>
		                    <td class="no-left-border">
		                    	<input type="text" name="name_of_school" class="form-control" value="<?php echo $row['name_of_school'] ?? ''; ?>" required>
		                    </td>
		                </tr>
		                <tr>
		                    <td class="no-right-border">
                                <label>Name of Board/University <span style="color: red; font-size: 1.3em;">*</span></label>
                            </td>
		                    <td class="no-left-border">
		                    	<input type="text" name="board_university_name" class="form-control" value="<?php echo $row['board_university_name'] ?? ''; ?>" required>
		                    </td>
		                    <td class="no-right-border">
                                <label>District Name <span class="text-danger">*</span></label>
                            </td>
		                    <td class="no-left-border">
                                <select name="district_id" id="districtDropdown" class="form-control" required>
                                    <option value="">Select District</option>
                                    <?php $row_district_id = $row['district_id'] ?? $_SESSION['user_details']['district_id'] ?? ''; ?>

                                    <?php foreach ($districts as $district): ?>
                                        <option value="<?php echo $district['id']; ?>"
                                            <?php if ($district['id'] == $row_district_id) echo 'selected'; ?>>
                                            <?php echo $district['district_name']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
		                </tr>
		            </table>
		        </td>
		    </tr>
		</table>

		<div class="declaration">
		    <h4>DECLARATION</h4>
		    <p>I, the undersigned do hereby solemnly affirm, declare and confirm for myself, my heirs, executors & administrators that I indemnify the Promoters/Organizers/Sponsors & its Members, Officials, Participants etc., holding myself personally responsible for all damages, injuries or accidents, claims, demands etc., waiving all prerogative rights, whatsoever related to the above set forth event.</p>
		</div>

		<table hidden>
            <tr>
                <td style="text-align: center;">
                    Signature of Parent/Guardian
                    <div class="custom-file mb-3" style="text-align: center; margin: 0 auto;">
                        <input type="file" name="signature_parent_guardian_path" id="signature_parent_guardian_path" accept="image/*" class="custom-file-input">
                        <label class="custom-file-label" for="signature_parent_guardian_path">Choose file</label>
                    </div>
                    <div id="current_signature_parent_guardian_display" style="display: <?php echo empty($row['signature_parent_guardian_path']) ? 'none' : 'block'; ?>; margin-bottom: 10px;">
                        <img src="/<?php echo htmlspecialchars($row['signature_parent_guardian_path']); ?>" alt="Signature of Parent/Guardian" style="max-width: 200px; max-height: 200px; display: inline-block;">
                    </div>
                    <div id="image_preview_parent_guardian" style="display: none; margin-bottom: 10px;">
                        <img id="preview_signature_parent_guardian" src="#" alt="Image Preview" style="max-width: 200px; max-height: 200px; display: inline-block;">
                    </div>
                    <button type="button" id="remove_signature_parent_guardian" class="btn btn-danger mt-2">Remove Photo</button>
                    <input type="hidden" id="remove_signature_parent_guardian_path" name="remove_signature_parent_guardian_path" value="<?php echo htmlspecialchars($row['signature_parent_guardian_path']); ?>">
                </td>
                <td style="text-align: center;">
                    Signature of Participant
                    <div class="custom-file mb-3" style="text-align: center; margin: 0 auto;">
                        <input type="file" name="signature_participant_path" id="signature_participant_path" accept="image/*" class="custom-file-input">
                        <label class="custom-file-label" for="signature_participant_path">Choose file</label>
                    </div>
                    <div id="current_signature_participant_display" style="display: <?php echo empty($row['signature_participant_path']) ? 'none' : 'block'; ?>; margin-bottom: 10px;">
                        <img src="/<?php echo htmlspecialchars($row['signature_participant_path']); ?>" alt="Signature of Participant" style="max-width: 200px; max-height: 200px; display: inline-block;">
                    </div>
                    <div id="image_preview_participant" style="display: none; margin-bottom: 10px;">
                        <img id="preview_signature_participant" src="#" alt="Image Preview" style="max-width: 200px; max-height: 200px; display: inline-block;">
                    </div>
                    <button type="button" id="remove_signature_participant" class="btn btn-danger mt-2">Remove Photo</button>
                    <input type="hidden" id="remove_signature_participant_path" name="remove_signature_participant_path" value="<?php echo htmlspecialchars($row['signature_participant_path']); ?>">
                </td>
            </tr>
            <tr>
                <td style="text-align: center;">
                    Signature of President/Secretary
                    <div class="custom-file mb-3" style="text-align: center; margin: 0 auto;">
                        <input type="file" name="signature_president_secretary_path" id="signature_president_secretary_path" accept="image/*" class="custom-file-input">
                        <label class="custom-file-label" for="signature_president_secretary_path">Choose file</label>
                    </div>
                    <div id="current_signature_president_secretary_display" style="display: <?php echo empty($row['signature_president_secretary_path']) ? 'none' : 'block'; ?>; margin-bottom: 10px;">
                        <img src="/<?php echo htmlspecialchars($row['signature_president_secretary_path']); ?>" alt="Signature of President/Secretary" style="max-width: 200px; max-height: 200px; display: inline-block;">
                    </div>
                    <div id="image_preview_president_secretary" style="display: none; margin-bottom: 10px;">
                        <img id="preview_signature_president_secretary" src="#" alt="Image Preview" style="max-width: 200px; max-height: 200px; display: inline-block;">
                    </div>
                    <button type="button" id="remove_signature_president_secretary" class="btn btn-danger mt-2">Remove Photo</button>
                    <input type="hidden" id="remove_signature_president_secretary_path" name="remove_signature_president_secretary_path" value="<?php echo htmlspecialchars($row['signature_president_secretary_path']); ?>">
                </td>
                
                <td style="text-align: center;">
                    State Association with stamp
                    <div class="custom-file mb-3" style="text-align: center; margin: 0 auto;">
                        <input type="file" name="state_association_stamp_path" id="state_association_stamp_path" accept="image/*" class="custom-file-input">
                        <label class="custom-file-label" for="state_association_stamp_path">Choose file</label>
                    </div>
                    <div id="current_state_association_stamp_display" style="display: <?php echo empty($row['state_association_stamp_path']) ? 'none' : 'block'; ?>; margin-bottom: 10px;">
                        <img src="/<?php echo htmlspecialchars($row['state_association_stamp_path']); ?>" alt="State Association Stamp" style="max-width: 200px; max-height: 200px; display: inline-block;">
                    </div>
                    <div id="image_preview_state_association_stamp" style="display: none; margin-bottom: 10px;">
                        <img id="preview_state_association_stamp" src="#" alt="Image Preview" style="max-width: 200px; max-height: 200px; display: inline-block;">
                    </div>
                    <button type="button" id="remove_state_association_stamp" class="btn btn-danger mt-2">Remove Photo</button>
                    <input type="hidden" id="remove_state_association_stamp_path" name="remove_state_association_stamp_path" value="<?php echo htmlspecialchars($row['state_association_stamp_path']); ?>">
                </td>
            </tr>
        </table>


		<hr>

        <div class="form-group">
            <button type="submit" name="<?php echo $mode == 'Edit' ? 'update' : 'add'; ?>" class="btn btn-success"><span class="glyphicon glyphicon-floppy-disk"></span>
                <?php echo $mode == 'Edit' ? 'Update' : 'Save'; ?>
            </button>

            <?php if ($mode == 'Edit') : ?>
                <a href="javascript:void(0);" onclick="openModal('delete_<?php echo $row['id']; ?>')" class="btn btn-danger pull-right">
                    <i class="glyphicon glyphicon-trash"></i> Delete
                </a>
                <!-- Delete -->
                <div id="delete_<?php echo $row['id']; ?>" class="custom-modal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <span class="close-btn" onclick="closeModal('delete_<?php echo $row['id']; ?>')">&times;</span>
                            <h4>Confirm Delete</h4>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to delete ?</p>
                            <h4 class="text-danger"><?php echo $row['name']; ?></h4>
                        </div>
                        <div class="modal-footer">
                            <button onclick="closeModal('delete_<?php echo $row['id']; ?>')" class="btn btn-secondary">Cancel</button>
                            <form action="" method="POST">
                                <input type="hidden" name="id" value="<?php echo $id; ?>">
                                <button type="submit" name="delete" class="btn btn-danger">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>

                <a href="/individual_entry_form_pdf_1/<?php echo encrypt(decrypt($uri_segments[1], $key), $key); ?>" class="btn btn-danger" target="_blank"><span class="glyphicon glyphicon-file"></span> PDF 1</a>
                <a href="/individual_entry_form_pdf_2/<?php echo encrypt(decrypt($uri_segments[1], $key), $key); ?>" class="btn btn-danger" target="_blank"><span class="glyphicon glyphicon-file"></span> PDF 2</a>
                <a href="/individual_entry_form_pdf_3/<?php echo encrypt(decrypt($uri_segments[1], $key), $key); ?>" class="btn btn-danger" target="_blank"><span class="glyphicon glyphicon-file"></span> PDF 3</a>
                <!-- <a href="/individual_entry_form" class="btn btn-default">Cancel</a> -->
            <?php endif; ?>
        </div>
    </form>
</div>

<script>
    function calculateAge() {
        const dob = document.getElementById("date_of_birth").value;
        const ageField = document.getElementById("age");

        if (dob) {
            const dobDate = new Date(dob);
            const today = new Date();
            let age = today.getFullYear() - dobDate.getFullYear();
            const monthDifference = today.getMonth() - dobDate.getMonth();

            // Adjust age if the birthday hasn't occurred yet this year
            if (monthDifference < 0 || (monthDifference === 0 && today.getDate() < dobDate.getDate())) {
                age--;
            }

            ageField.value = age;
        } else {
            ageField.value = '';
        }
    }

    // Attach calculateAge function to keyup and change events
    document.getElementById("date_of_birth").addEventListener("keyup", calculateAge);
    document.getElementById("date_of_birth").addEventListener("change", calculateAge);

    // JavaScript to handle the file input and preview
    document.getElementById('photo_path').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const previewImg = document.getElementById('preview_img');
        const imagePreview = document.getElementById('image_preview');
        const currentPhotoDisplay = document.getElementById('current_photo_display');
        document.getElementById('remove_photo_path').value = document.getElementById('photo_path').value;

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.style.display = 'block'; // Show the preview
                currentPhotoDisplay.style.display = 'none'; // Hide current photo display
            };
            reader.readAsDataURL(file);
        } else {
            imagePreview.style.display = 'none'; // Hide preview if no file is selected
            currentPhotoDisplay.style.display = 'block'; // Show current photo display if no new file is selected
        }
    });

    // JavaScript to clear the photo and hide the preview
    document.getElementById('remove_photo').addEventListener('click', function() {
        document.getElementById('photo_path').value = ""; // Clear the file input
        document.getElementById('remove_photo_path').value = ""; // Clear the file input
        document.getElementById('image_preview').style.display = 'none'; // Hide the preview
        document.getElementById('current_photo_display').style.display = 'none'; // Show the current photo display
    });

    // Function to handle file input change and preview for signatures
    function setupFileInputPreview(inputId, previewImgId, currentDisplayId, removeButtonId, removePathId) {
        document.getElementById(inputId).addEventListener('change', function(event) {
            const file = event.target.files[0];
            const previewImg = document.getElementById(previewImgId);
            const imagePreview = document.getElementById(previewImgId).parentElement; // Get the parent div
            const currentPhotoDisplay = document.getElementById(currentDisplayId);

            // Set the remove path value
            document.getElementById(removePathId).value = document.getElementById(inputId).value;

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    imagePreview.style.display = 'block'; // Show the preview
                    currentPhotoDisplay.style.display = 'none'; // Hide current photo display
                };
                reader.readAsDataURL(file);
            } else {
                imagePreview.style.display = 'none'; // Hide preview if no file is selected
                currentPhotoDisplay.style.display = 'block'; // Show current photo display if no new file is selected
            }
        });

        // Clear the photo and hide the preview
        document.getElementById(removeButtonId).addEventListener('click', function() {
            document.getElementById(inputId).value = ""; // Clear the file input
            document.getElementById(removePathId).value = ""; // Clear the remove path
            document.getElementById(previewImgId).parentElement.style.display = 'none'; // Hide the preview
            document.getElementById(currentDisplayId).style.display = 'none'; // Show the current photo display
        });
    }

    // Setup event listeners for each signature input
    setupFileInputPreview('signature_parent_guardian_path', 'preview_signature_parent_guardian', 'current_signature_parent_guardian_display', 'remove_signature_parent_guardian', 'remove_signature_parent_guardian_path');
    setupFileInputPreview('signature_participant_path', 'preview_signature_participant', 'current_signature_participant_display', 'remove_signature_participant', 'remove_signature_participant_path');
    setupFileInputPreview('signature_president_secretary_path', 'preview_signature_president_secretary', 'current_signature_president_secretary_display', 'remove_signature_president_secretary', 'remove_signature_president_secretary_path');
    setupFileInputPreview('state_association_stamp_path', 'preview_state_association_stamp', 'current_state_association_stamp_display', 'remove_state_association_stamp', 'remove_state_association_stamp_path');
</script>

<script type="text/javascript">
    // Function to open the modal
    function openModal(modalId) {
        document.getElementById(modalId).style.display = 'flex';
    }

    // Function to close the modal
    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }

</script>