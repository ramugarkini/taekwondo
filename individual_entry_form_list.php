<div class="container">
    <!-- <h1 class="page-header text-center">Individual Entry Form</h1> -->
    <br>
    <div class="d-flex align-items-center justify-content-between mb-3" style="display: flex; align-items: center;">

        <!-- Center Title -->
        <h1 class="page-header text-center" style="margin: 0 auto; flex-grow: 1; text-align: center;"> Individual Entry Form</h1>

        <!-- New Button -->
        <a href="/individual_entry_form/<?php echo encrypt(0, $key); ?>" class="btn btn-primary" style="margin-left: auto;">
            <span class="glyphicon glyphicon-plus"></span> New
        </a>
    </div>
    <div class="row">
        <!-- <div class="col-sm-8 col-sm-offset-2"> -->
        <div>
            <div class="row">
                <?php
                    if(isset($_SESSION['error'])){
                        echo "<div class='alert alert-danger text-center'>
                            <button class='close'>&times;</button>
                            ".$_SESSION['error']."
                        </div>";
                        unset($_SESSION['error']);
                    }
                    if(isset($_SESSION['success'])){
                        echo "<div class='alert alert-success text-center'>
                            <button class='close'>&times;</button>
                            ".$_SESSION['success']."
                        </div>";
                        unset($_SESSION['success']);
                    }
                ?>
            </div>
            <!-- <div class="row"> -->
                <!-- <a href="individual_entry_form/0" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> New</a> -->
                <!-- <a href="print_pdf.php" class="btn btn-success pull-right"><span class="glyphicon glyphicon-print"></span> PDF</a> -->
            <!-- </div> -->
            <div class="height10"></div>
            <div class="row">
                <table id="myTable" class="table table-bordered table-striped">
                    <thead>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Category</th>
                        <th>Gender</th>
                        <th>Weight</th>
                        <th>Weight Category</th>
                        <th>State/Organization</th>
                        <th>Action</th>
                    </thead>
                    <tbody>
                        <?php
                            $sql = "SELECT * FROM individual_entry_form";
                            $query = $conn->query($sql);
                            while($row = $query->fetch_assoc()){
                                echo "<tr>
                                    <td>".$row['id']."</td>
                                    <td>".$row['name']."</td>
                                    <td>".$row['type']."</td>
                                    <td>".$row['category']."</td>
                                    <td>".$row['gender']."</td>
                                    <td>".$row['weight']."</td>
                                    <td>".$row['weight_category']."</td>
                                    <td>".$row['state_organization_name']."</td>
                                    <td>
                                        <a href='/individual_entry_form/".encrypt($row['id'], $key)."' class='btn btn-success' data-toggle='modal'><span class='glyphicon glyphicon-edit'></span> Edit</a>
                                    </td>
                                </tr>";
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>