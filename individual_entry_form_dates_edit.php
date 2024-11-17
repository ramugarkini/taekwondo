<?php

$mode = 'Add';
$id = 0;

// Check if an ID is passed in the URL for editing
if (isset($uri_segments[1]) && intval(decrypt($uri_segments[1], $key)) > 0) {
    $id = intval(decrypt($uri_segments[1], $key));
    $mode = 'Edit';

    // Fetch data from the database if editing
    $query = $conn->prepare("SELECT * FROM individual_entry_form_dates WHERE id = ?");
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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        // Collecting form data for adding
        $date_range = $_POST['date_range'];
        $year       = $_POST['year'];

        // Prepare the SQL statement
        $stmt = $conn->prepare("INSERT INTO individual_entry_form_dates (
                    date_range, year
                ) VALUES (?, ?)");


        // Bind parameters
        $stmt->bind_param("ss", 
            $date_range, $year
        );

        // Execute insert query
        if ($stmt->execute()) {
            $last_id = $stmt->insert_id; // Get the last inserted ID
            $_SESSION['success'] = 'Entry Added Successfully';
            $enc_last_id = encrypt($last_id, $key);
            header("Location: /individual_entry_form_dates/$enc_last_id");
            exit();

        } else {
            $_SESSION['error'] = 'Something went wrong while adding: ' . $stmt->error;
        }

        $stmt->close(); // Close the statement
    } elseif (isset($_POST['update'])) {
        // Collect form data for updating
        $id = $_POST['id'];  // Ensure the ID is collected for updating
        $date_range = $_POST['date_range'];
        $year = $_POST['year'];

        // Prepare the SQL statement for updating
        $sql = "UPDATE individual_entry_form_dates SET 
            date_range = '$date_range', 
            year = '$year'
        WHERE id = '$id'";

        // Execute update query
        if ($conn->query($sql)) {
            $_SESSION['success'] = 'Changes Updated Successfully';
            $enc_id = encrypt($id, $key);
            header("Location: /individual_entry_form_dates/$enc_id");
            exit();
        } else {
            $_SESSION['error'] = 'Something went wrong while updating: ' . $conn->error;
        }
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];

        // Fetch the file paths from the database to delete associated files
        $stmt = $conn->prepare("SELECT id FROM individual_entry_form_dates WHERE id = ?");
        $stmt->bind_param("i", $id); // Assuming $id is an integer
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();

        if ($data) {
            // Prepare the delete statement
            $deleteStmt = $conn->prepare("DELETE FROM individual_entry_form_dates WHERE id = ?");
            $deleteStmt->bind_param("i", $id); // Assuming $id is an integer

            // Execute delete query
            if ($deleteStmt->execute()) {
                // $_SESSION['success'] = 'Entry Deleted Successfully';
                header("Location: /individual_entry_form_dates");
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

<div class="container mt-5">
    <!-- Header and Back Button -->
    <div class="d-flex align-items-center justify-content-between mb-3" style="display: flex; align-items: center;">

        <!-- Back Button -->
        <a href="/individual_entry_form_dates" class="btn btn-primary">
            <i class="glyphicon glyphicon-arrow-left"></i> Back
        </a>

        <!-- Center Title -->
        <h2 class="page-header text-center" style="margin: 0 auto; flex-grow: 1; text-align: center;"> <?php echo $mode; ?> Entry Form Dates</h2>

        <!-- New Button -->
        <a href="/individual_entry_form_dates/<?php echo encrypt(0, $key); ?>" class="btn btn-primary" style="margin-left: auto;">
            <span class="glyphicon glyphicon-plus"></span> New
        </a>
    </div>

    <!-- Success/Error Messages -->
    <?php if (isset($_SESSION['success'])) : ?>
        <div class="alert alert-success text-center">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])) : ?>
        <div class="alert alert-danger text-center">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    <br>

    <!-- Entry Form Dates Form -->
    <form action="" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $id; ?>">

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Entry Form Year <span class="text-danger">*</span></label>
                    <input type="number" name="year" class="form-control" value="<?php echo $row['year'] ?? ''; ?>" id="yearInput" required>
                </div>
            </div>
            <div class="col-md-8">
                <div class="form-group">
                    <label>Entry Form Date Range <span class="text-danger">*</span></label>
                    <input type="text" name="date_range" class="form-control" value="<?php echo $row['date_range'] ?? ''; ?>" required>
                </div>
            </div>
        </div>
        <hr>

        <div class="form-group d-flex justify-content-between">
            <!-- Save or Update Button -->
            <button type="submit" name="<?php echo $mode == 'Edit' ? 'update' : 'add'; ?>" class="btn btn-success">
                <i class="glyphicon glyphicon-floppy-disk"></i> <?php echo $mode == 'Edit' ? 'Update' : 'Save'; ?>
            </button>

            <!-- Delete Button (Only for Edit Mode) -->
            <?php if ($mode == 'Edit') : ?>
                <!-- <a href="#delete_<?php echo $row['id']; ?>" class="btn btn-danger" data-toggle="modal">
                    <i class="glyphicon glyphicon-trash"></i> Delete
                </a> -->
                <a href="javascript:void(0);" onclick="openModal('delete_<?php echo $row['id']; ?>')" class="btn btn-danger">
                    <i class="glyphicon glyphicon-trash"></i> Delete
                </a>
            <?php endif; ?>
        </div>
    </form>

    <!-- Delete Confirmation Modal (Only for Edit Mode) -->
    <?php if ($mode == 'Edit') : ?>
        <div id="delete_<?php echo $row['id']; ?>" class="custom-modal">
            <div class="modal-content">
                <div class="modal-header">
                    <span class="close-btn" onclick="closeModal('delete_<?php echo $row['id']; ?>')">&times;</span>
                    <h4>Confirm Delete</h4>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete ?</p>
                    <h4 class="text-danger"><?php echo $row['date_range']; ?></h4>
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
    <?php endif; ?>
</div>

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
