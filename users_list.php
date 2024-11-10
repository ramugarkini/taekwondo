<div class="container">
    <!-- <h1 class="page-header text-center">Individual Entry Form</h1> -->
    <!-- <br> -->
    <div class="d-flex align-items-center justify-content-between mb-3" style="display: flex; align-items: center;">

        <!-- Center Title -->
        <h2 class="page-header text-center" style="margin: 0 auto; flex-grow: 1; text-align: center;"> Users</h2>

        <!-- New Button -->
        <a href="/users/<?php echo encrypt(0, $key); ?>" class="btn btn-primary" style="margin-left: auto;">
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
            <div class="height10"></div>
            <div class="row">
                <table id="myTable" class="table table-bordered table-striped">
                    <thead>
                        <th>ID</th>
                        <th>Username</th>
                        <th>District Name</th>
                        <th>Action</th>
                    </thead>
                    <tbody>
                        <?php
                            $sql = "SELECT u.id, u.username, u.password, d.district_name
                                    FROM users u
                                    INNER JOIN districts d ON d.id = u.district_id";
                            $query = $conn->query($sql);
                            while($row = $query->fetch_assoc()){
                                echo "<tr>
                                    <td>".$row['id']."</td>
                                    <td>".$row['username']."</td>
                                    <td>".$row['district_name']."</td>
                                    <td>
                                        <a href='/users/".encrypt($row['id'], $key)."' class='btn btn-success' data-toggle='modal'><span class='glyphicon glyphicon-edit'></span> Edit</a>
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
