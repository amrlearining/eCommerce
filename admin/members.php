<?php 

session_start();
$pageTitle = 'Members';

if (isset($_SESSION['Username'])) {
    include 'init.php';

    $do = isset($_GET['do']) ? $_GET['do'] : 'Manage';

    if ($do == 'Manage') { // Manage page 
        
        $stmt = $con->prepare("SELECT * FROM users WHERE GroupID != 1");
        $stmt->execute();
        $rows = $stmt->fetchAll();
        ?>
                
        <h1 class="text-center">Manage Members</h1>
        <div class="container">
            <div class="table-resposive">
                <table class="main-table text-center table table-bordered">
                    <tr>
                        <td>#ID</td>
                        <td>Username</td>
                        <td>Email</td>
                        <td>Full Name</td>
                        <td>Registerd Date</td>
                        <td>Control</td>
                    </tr>
                    <?php
                    
                    foreach($rows as $row) {
                        echo '<tr>';
                            echo '<td>' . $row['UserID'] . '</td>';
                            echo '<td>' . $row['Username'] . '</td>';
                            echo '<td>' . $row['Email'] . '</td>';
                            echo '<td>' . $row['FullName'] . '</td>';
                            echo '<td>' . $row['Date'] . '</td>';
                            echo '<td> <a href="members.php?do=edit&userid=' . $row['UserID'] . '" class="btn btn-success"><i class="fa fa-edit"></i>Edit</a>
                            <a href="members.php?do=delete&userid=' . $row['UserID'] . '" class="btn btn-danger confirm"><i class="fa fa-close"></i>Delete</a> </td>';
                        echo '</tr>';
                    } ?>
                </table>
            </div>
            <a href="members.php?do=add" class="btn btn-primary"><i class="fa fa-plus"></i> Insert Member</a>
        </div>
    <?php } elseif ($do == 'add'){ //2 ?>

        <h1 class="text-center">Add New Member</h1>
        <div class="container">
            <form class="form-horizontal" action="?do=Insert" method="POST">
                <!--start Username Field-->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Username</label>
                    <div class="col-sm-10 col-md-4">    
                        <input type="text" name="username" class="form-control" autocomplete="off" required="required" placeholder="" />
                    </div>
                </div>
                <!--start Username Field-->
                <!--start Password Field-->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Password</label>
                    <div class="col-sm-10 col-md-4">
                        <input type="Password" name="password" class="password form-control" autocomplete="new-password" required="required" placeholder=""/>
                        <i class="show-pass fa-solid fa-eye fa-2x"></i>
                    </div>
                </div>
                <!--start Password Field-->
                <!--start email Field-->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Email</label>
                    <div class="col-sm-10 col-md-4">
                        <input type="email" name="email" class="form-control" required="required" placeholder="" />
                    </div>
                </div>
                <!--start email Field-->
                <!--start Full name Field-->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Full name</label>
                    <div class="col-sm-10 col-md-4">
                        <input type="text" name="Full-name" class="form-control" required="required" placeholder="" />
                    </div>
                </div>
                <!--start Full name Field-->
                <!--start Submit Field-->
                <div class="form-group form-group-lg">
                    <div class="col-sm-offset-2 col-sm-10">
                        <input type="submit" value="Insert" class="btn btn-primary btn-lg" />
                    </div>
                </div>
                <!--start Submit Field-->
            </form>
        </div>
    <?php } elseif ($do == 'Insert') {
                
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            echo "<h1 class='text-center'>Update Member</h1>";
            echo "<div class='container'>";

            $user = $_POST['username'];
            $pass = $_POST['password'];
            $email = $_POST['email'];
            $name = $_POST['Full-name'];

            $hashpass = sha1($_POST['password']);

            $formErrors = array();
            if (empty($user)){
                $formErrors[] = 'Username cant be <strong> empty </strong>';
            }
            if (strlen($user) < 4){
                $formErrors[] = 'Username cant be <strong> less than 4 Charachter </strong>';
            }
            
            if (strlen($user) > 20){
                $formErrors[] = 'Username cant be <strong> more than 20 Charachter </strong>';
            }
            if (empty($email)){
                $formErrors[] = 'Email cant be <strong> empty </strong>';
            }
            if (empty($name)){
                $formErrors[] = 'Full Name cant be <strong> empty </strong>';
            }
            
            if (empty($pass)){
                $formErrors[] = 'Password cant be <strong> empty </strong>';
            }
            if (strlen($_POST['password']) < 6 && strlen($_POST['password']) > 0){
                $formErrors[] = ' Password cant be less than <strong> 6 charachters </strong> ';
            }

            if (checkItem("Username", "users", $user) == 1) {
                $formErrors[] = 'Sorry This user is <strong>exist <strong>';
            }

            foreach($formErrors as $error){
                $theMsg = '<div class="alert alert-danger">' . $error . '</div>';
                redirectHome($theMsg, 'back', 5);
            }

            if (empty($formErrors)) {
                
                $stmt = $con->prepare("INSERT INTO 
                                        users(Username, Password, Email, FullName, RegStatus, Date) 
                                        VALUES(:un, :pw, :e, :fn, 1, now())");
                $stmt->execute(array(
                    ':un' => $user,
                    ':pw' => $hashpass,
                    ':e'  => $email,
                    ':fn' => $name
                ));

                $theMsg = '<div class="alert alert-success">' . $stmt->rowCount() . ' One record Inserted </div>';
                
                redirectHome($theMsg, 'back', 6);
            }
        } else {
            $errorMsg = '<div class="alert alert-danger">Sorry you cant Browse This Page Directly</div>';

            redirectHome($errorMsg, 'back', 6);
        }                
        
    } elseif ($do == 'edit') {// 4 Edit page 
        
        //check if the GET Requist userid Is numeric $ GET the Integar vlue of it
        $userid = isset($_GET['userid']) && is_numeric($_GET['userid']) ? intval($_GET['userid']) : 0;
        
        //select all data depend on this id
        $stmt = $con->prepare("SELECT * FROM users WHERE UserID = ? LIMIT 1");
        
        //execute quiry
        $stmt->execute(array($userid));
        
        // fetch the data
        $row = $stmt->fetch();
        
        // the row count
        $count = $stmt->rowCount();
        
        if($count > 0) { ?>
        
        <h1 class="text-center">Edit Member</h1>
        <div class="container">
            <form class="form-horizontal" action="?do=Update" method="POST">
                <input type="hidden" name="userid" value="<?php echo $userid ?>" />
                <!--start Username Field-->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Username</label>
                    <div class="col-sm-10 col-md-4">    
                        <input type="text" name="username" class="form-control" value="<?php echo $row['Username'] ?>" autocomplete="off" required="required"/>
                    </div>
                </div>
                <!--start Username Field-->
                <!--start Password Field-->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Password</label>
                    <div class="col-sm-10 col-md-4">
                        <input type="hidden" name="oldPassword" value="<?php echo $row['Password'] ?>"/>
                        <input type="Password" name="newPassword" class="form-control" autocomplete="new-password" placeholder="Leave Blank If You Don't want to change"/>
                    </div>
                </div>
                <!--start Password Field-->
                <!--start email Field-->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Email</label>
                    <div class="col-sm-10 col-md-4">
                        <input type="email" name="email" class="form-control" value="<?php echo $row['Email'] ?>" required="required"/>
                    </div>
                </div>
                <!--start email Field-->
                    <!--start Full name Field-->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Full name</label>
                        <div class="col-sm-10 col-md-4">
                            <input type="text" name="Full-name" class="form-control"  value="<?php echo $row['FullName'] ?>" required="required"/>
                        </div>
                    </div>
                    <!--start Full name Field-->
                    <!--start Submit Field-->
                    <div class="form-group form-group-lg">
                        <div class="col-sm-offset-2 col-sm-10">
                            <input type="submit" value="Save" class="btn btn-primary btn-lg" />
                        </div>
                    </div>
                    <!--start Submit Field-->
                </form>
            </div>
            <?php } else {

                $theMsg = '<div class="alert alert-danger">Theres no such ID</div>';

                redirectHome($theMsg, 'back', 4);
            }
    } elseif ($do == 'Update') {
        
        echo "<h1 class='text-center'>Update Member</h1>";
        echo "<div class='container'>";
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            $id = $_POST['userid'];
            $user = $_POST['username'];
            $email = $_POST['email'];
            $name = $_POST['Full-name'];
            
            $pass = empty($_POST['newPassword']) ? $_POST['oldPassword'] : sha1($_POST['newPassword']);  
            
            $formErrors = array();
            if (empty($user)){
                $formErrors[] = 'Username cant be <strong> empty </strong>';
        }
        if (empty($email)){
            $formErrors[] = 'Email cant be <strong> empty </strong>';
        }
        if (empty($name)){
            $formErrors[] = 'Full Name cant be <strong> empty </strong>';
        }
        if (strlen($_POST['newPassword']) < 6 && strlen($_POST['newPassword']) > 0){
            $formErrors[] = ' Password cant be less than <strong> 6 charachters </strong> ';
        }
        
        foreach($formErrors as $error){
            echo '<div class="alert alert-danger">' . $error . '</div>';
        }
        
        if (empty($formErrors)) {
            
            $stmt = $con->prepare("Update users SET Username = ?, Email = ?, FullName = ?, Password = ? WHERE UserID = ?");
            $stmt->execute(array($user, $email, $name, $pass, $id));    
            $theMsg = '<div class="alert alert-success">' . $stmt->rowCount() . ' Record Update </div>';
            redirectHome($theMsg, 'back', 6);
        }
        
        } else {
            $theMsg = '<div class="alert alert-danger">Sorry you cant Browse This Page Directly</div>';

            redirectHome($theMsg, 'back', 6);
        }
        echo "</div>";
    } elseif ($do == 'delete') { // 6
        
        echo "<h1 class='text-center'>Delete Member</h1>";
        echo "<div class='container'>";
        $userid = isset($_GET['userid']) && is_numeric($_GET['userid']) ? intval($_GET['userid']) : 0;
        
        //select all data depend on this id

        $check = checkItem('UserID', 'users', $userid);     
            
            if($check > 0) {
                $stmt = $con->prepare("DELETE FROM users WHERE UserID = :zuser");
                $stmt->bindParam(":zuser", $userid);
                $stmt->execute();
                $theMsg = '<div class="alert alert-success">' . $stmt->rowCount() . ' One record Inserted </div>';
                redirectHome($theMsg, 'back', 5);
            } else {
                $theMsg = '<div class="alert alert-danger"> No user with same ID </div>';
                redirectHome($theMsg);
            }
            echo "</div>";
            
    } else {
        $errorMsg = '<div class="alert alert-danger">erorr page</div>';

        redirectHome($errorMsg);
    }
        
        include $tpl . 'footer.php';
        
    } else {
        
        header('Location: index.php');
        
        exit();
    }