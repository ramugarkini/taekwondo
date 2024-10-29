<?php


$mode = 'Add';
$id = 0;

// Check if an ID is passed in the URL for editing
if (isset($uri_segments[1]) && intval($uri_segments[1]) > 0) {
    $id = intval($uri_segments[1]);
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
        header("Location: /individual_entry_form");
        exit();
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

        // Handle file uploads (if applicable)
        $photo_path = $_FILES['photo_path']['name'] ?? null;
        $signature_parent_guardian_path = $_FILES['signature_parent_guardian_path']['name'] ?? null;
        $signature_participant_path = $_FILES['signature_participant_path']['name'] ?? null;
        $signature_president_secretary_path = $_FILES['signature_president_secretary_path']['name'] ?? null;
        $state_association_stamp_path = $_FILES['state_association_stamp_path']['name'] ?? null;

        // Here, you should implement file upload handling
        // Example: move_uploaded_file($_FILES['photo']['tmp_name'], 'uploads/' . $photo);

        // Prepare the SQL statement for inserting
        $sql = "INSERT INTO individual_entry_form (
            type, 
            category, 
            gender, 
            weight, 
            weight_category, 
            name, 
            photo_path, 
            state_organization_name, 
            date_of_birth, 
            age, 
            parent_guardian_name, 
            current_belt_grade, 
            tfi_id_no, 
            belt_certificate_no, 
            academic_qualification, 
            name_of_school, 
            board_university_name, 
            signature_parent_guardian_path, 
            signature_participant_path, 
            signature_president_secretary_path, 
            state_association_stamp_path
        ) VALUES (
            '$type', 
            '$category', 
            '$gender', 
            '$weight', 
            '$weight_category', 
            '$name', 
            '$photo_path', 
            '$state_organization_name', 
            '$date_of_birth', 
            '$age', 
            '$parent_guardian_name', 
            '$current_belt_grade', 
            '$tfi_id_no', 
            '$belt_certificate_no', 
            '$academic_qualification', 
            '$name_of_school', 
            '$board_university_name', 
            '$signature_parent_guardian_path', 
            '$signature_participant_path', 
            '$signature_president_secretary_path', 
            '$state_association_stamp_path'
        )";

        // Execute insert query
        if ($conn->query($sql)) {
            $_SESSION['success'] = 'Entry Added Successfully';
            header("Location: /individual_entry_form/$conn->insert_id");
            exit();
        } else {
            $_SESSION['error'] = 'Something went wrong while adding: ' . $conn->error;
        }
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

        // Handle file uploads (if applicable)
        $photo_path = $_FILES['photo_path']['name'] ?? null;
        $signature_parent_guardian_path = $_FILES['signature_parent_guardian_path']['name'] ?? null;
        $signature_participant_path = $_FILES['signature_participant_path']['name'] ?? null;
        $signature_president_secretary_path = $_FILES['signature_president_secretary_path']['name'] ?? null;
        $state_association_stamp_path = $_FILES['state_association_stamp_path']['name'] ?? null;

        // File upload handling (if the file is uploaded, move it to the desired directory)
        // Example: move_uploaded_file($_FILES['photo']['tmp_name'], 'uploads/' . $photo);

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
            state_association_stamp_path = '$state_association_stamp_path' 
        WHERE id = '$id'";

        // Execute update query
        if ($conn->query($sql)) {
            $_SESSION['success'] = 'Changes Updated Successfully';
            header("Location: /individual_entry_form/$uri_segments[1]");
            exit();
        } else {
            $_SESSION['error'] = 'Something went wrong while updating: ' . $conn->error;
        }
    } elseif (isset($_POST['delete'])) {
        $sql = "DELETE FROM individual_entry_form WHERE id = '".$_POST['id']."'";

        //use for MySQLi OOP
        if($conn->query($sql)){
            $_SESSION['success'] = 'Entry Deleted Successfully';
            header("Location: /individual_entry_form");
            exit();
        } else {
            $_SESSION['error'] = 'Something went wrong in deleting the entry';
        }
    }
}

?>

<style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
    }
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

<div class="container">
    <br>
    <div class="d-flex align-items-center justify-content-between mb-3" style="display: flex; align-items: center;">
        <!-- Back Button -->
        <a href="/individual_entry_form" class="btn btn-primary" style="margin-right: auto;">
            <span class="glyphicon glyphicon-arrow-left"></span> Back
        </a>

        <!-- Center Title -->
        <h1 class="page-header text-center" style="margin: 0 auto; flex-grow: 1; text-align: center;">
            <?php echo $mode; ?> Individual Entry Form
        </h1>

        <!-- New Button -->
        <a href="individual_entry_form/0" class="btn btn-primary" style="margin-left: auto;">
            <span class="glyphicon glyphicon-plus"></span> New
        </a>
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
	        	    <input type="radio" name="type" value="sub_junior" <?php echo ($row['type'] ?? '') == 'sub_junior' ? 'checked' : ''; ?>>
	        	    Sub Junior
	        	</td>
	        	<td>
	        	    <input type="radio" name="type" value="cadet" <?php echo ($row['type'] ?? '') == 'cadet' ? 'checked' : ''; ?>>
	        	    Cadet
	        	</td>
	        	<td>
	        	    <input type="radio" name="type" value="junior" <?php echo ($row['type'] ?? '') == 'junior' ? 'checked' : ''; ?>>
	        	    Junior
	        	</td>
	        	<td>
	        	    <input type="radio" name="type" value="senior" <?php echo ($row['type'] ?? '') == 'senior' ? 'checked' : ''; ?>>
	        	    Senior
	        	</td>
	        </tr>
	        <tr>
	            <td colspan="4">
	                <table style="width: 100%; border: none;">
	                    <tr>
	                        <td>Category: 
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
	                        <td>Gender: 
	                        	<label>
	                	            <input type="radio" name="gender" value="Male" <?php echo ($row['gender'] ?? '') == 'Male' ? 'checked' : ''; ?>>
	                	            Male
	                	        </label>
	                	        <label>
	                	            <input type="radio" name="gender" value="Female" <?php echo ($row['gender'] ?? '') == 'Female' ? 'checked' : ''; ?>>
	                	            Female
	                	        </label>
	                        </td>
	                        <td>Weight
	                        	<input type="number" name="weight" class="form-control" value="<?php echo $row['weight'] ?? ''; ?>" required>
	                        </td>
	                        <td>Weight Category
	                        	<input type="number" name="weight_category" class="form-control" value="<?php echo $row['weight_category'] ?? ''; ?>" required>
	                        </td>
	                        
	                    </tr>
	                </table>
	            </td>
	        </tr>
	        <tr>
		        <td colspan="4">
		            <table style="width: 100%; border: none;">
		                <tr>
		                    <td class="no-right-border">Name:</td>
	            			<td class="no-left-border">
	            				<input type="text" name="name" class="form-control" value="<?php echo $row['name'] ?? ''; ?>" required>
		                    </td>
		                    <td class="no-right-border">State Organization Name:</td>
		                    <td class="no-left-border">
		                    	<input type="text" name="state_organization_name" class="form-control" value="<?php echo $row['state_organization_name'] ?? ''; ?>">
		                    </td>
            			        <td rowspan="5" style="text-align: center;">Attach One Passport Size Photo
            	                	<input type="file" name="photo_path">
            			            <?php if (!empty($row['photo_path'])): ?>
            			                <p>Current: <?php echo $row['photo_path']; ?></p>
            			            <?php endif; ?>
            	                </td>
		                </tr>
		                <tr>
		                    <td class="no-right-border">Date of Birth:</td>
		                    <td class="no-left-border">
		                    	<input type="date" name="date_of_birth" class="form-control" value="<?php echo $row['date_of_birth'] ?? ''; ?>">
		                    </td>
		                    <td class="no-right-border">Age:</td>
		                    <td class="no-left-border">
		                    	<input type="number" name="age" class="form-control" value="<?php echo $row['age'] ?? ''; ?>">
		                    </td>
		                </tr>
		                <tr>
		                    <td class="no-right-border">Parent/Guardian Name:</td>
		                    <td class="no-left-border">
		                    	<input type="text" name="parent_guardian_name" class="form-control" value="<?php echo $row['parent_guardian_name'] ?? ''; ?>">
		                    </td>
		                    <td class="no-right-border"></td>
		                    <td class="no-left-border"></td>
		                </tr>
		                <tr>
		                    <td class="no-right-border">Current Belt Grade:</td>
		                    <td class="no-left-border">
		                    	<input type="text" name="current_belt_grade" class="form-control" value="<?php echo $row['current_belt_grade'] ?? ''; ?>">
		                    </td>
		                    <td class="no-right-border">TFI ID No.:</td>
		                    <td class="no-left-border">
		                    	<input type="text" name="tfi_id_no" class="form-control" value="<?php echo $row['tfi_id_no'] ?? ''; ?>">
		                    </td>
		                </tr>
		                <tr>
		                    <td class="no-right-border">Belt Certificate No.:</td>
		                    <td class="no-left-border">
		                    	<input type="text" name="belt_certificate_no" class="form-control" value="<?php echo $row['belt_certificate_no'] ?? ''; ?>">
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
		                    <td class="no-right-border">Academic Qualification:</td>
		                    <td class="no-left-border">
		                    	<input type="text" name="academic_qualification" class="form-control" value="<?php echo $row['academic_qualification'] ?? ''; ?>">
		                    </td>
		                    <td class="no-right-border">Name of School:</td>
		                    <td class="no-left-border">
		                    	<input type="text" name="name_of_school" class="form-control" value="<?php echo $row['name_of_school'] ?? ''; ?>">
		                    </td>
		                </tr>
		                <tr>
		                    <td class="no-right-border">Name of Board/University:</td>
		                    <td class="no-left-border">
		                    	<input type="text" name="board_university_name" class="form-control" value="<?php echo $row['board_university_name'] ?? ''; ?>">
		                    </td>
		                    <td class="no-right-border"></td>
		                    <td class="no-left-border"></td>
		                </tr>
		            </table>
		        </td>
		    </tr>
		</table>

		<div class="declaration">
		    <h4>DECLARATION</h4>
		    <p>I, the undersigned do hereby solemnly affirm, declare and confirm for myself, my heirs, executors & administrators that I indemnify the Promoters/Organizers/Sponsors & its Members, Officials, Participants etc., holding myself personally responsible for all damages, injuries or accidents, claims, demands etc., waiving all prerogative rights, whatsoever related to the above set forth event.</p>
		</div>

		<table>
		    <tr>
		        <td>Signature of Parent/Guardian
		        	<input type="file" name="signature_parent_guardian_path">
		        	<?php if (!empty($row['signature_parent_guardian_path'])): ?>
		        	    <p>Current: <?php echo $row['signature_parent_guardian_path']; ?></p>
		        	<?php endif; ?>
		        </td>
		        <td>Signature of Participant
		        	<input type="file" name="signature_participant_path">
		        	<?php if (!empty($row['signature_participant_path'])): ?>
		        	    <p>Current: <?php echo $row['signature_participant_path']; ?></p>
		        	<?php endif; ?>
		        </td>
		    </tr>
		    <tr>
		        <td>Signature of President/Secretary
		        	<input type="file" name="signature_president_secretary_path">
		        	<?php if (!empty($row['signature_president_secretary_path'])): ?>
		        	    <p>Current: <?php echo $row['signature_president_secretary_path']; ?></p>
		        	<?php endif; ?>
		        </td>
		        <td>State Association with stamp
		        	<input type="file" name="state_association_stamp_path">
		        	<?php if (!empty($row['state_association_stamp_path'])): ?>
		        	    <p>Current: <?php echo $row['state_association_stamp_path']; ?></p>
		        	<?php endif; ?>
		        </td>
		    </tr>
		</table>
		<hr>

        <div class="form-group">
            <button type="submit" name="<?php echo $mode == 'Edit' ? 'update' : 'add'; ?>" class="btn btn-primary"><span class="glyphicon glyphicon-floppy-disk"></span>
                <?php echo $mode == 'Edit' ? 'Update' : 'Save'; ?>
            </button>

            <?php if ($mode == 'Edit') : ?>
                <?php echo "<a href='#delete_".$row['id']."' class='btn btn-danger pull-right' data-toggle='modal'><span class='glyphicon glyphicon-trash'></span> Delete</a>"; ?>
                <!-- Delete -->
                <div class="modal fade" id="delete_<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <center><h4 class="modal-title" id="myModalLabel">Delete</h4></center>
                            </div>
                            <div class="modal-body">    
                                <p class="text-center">Are you sure you want to Delete</p>
                                <h2 class="text-center"><?php echo $row['name']; ?></h2>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancel</button>
                                <!-- <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span> Yes</a> -->
                                <button type="submit" name="delete" class="btn btn-danger">Delete</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <a href="/individual_entry_form_pdf/<?php echo $uri_segments[1]; ?>" class="btn btn-success" target="_blank"><span class="glyphicon glyphicon-file"></span> PDF</a>
            <a href="/individual_entry_form_pdf_2/<?php echo $uri_segments[1]; ?>" class="btn btn-success" target="_blank"><span class="glyphicon glyphicon-file"></span> PDF 2</a>
            <!-- <a href="/individual_entry_form" class="btn btn-default">Cancel</a> -->
        </div>
    </form>
</div>