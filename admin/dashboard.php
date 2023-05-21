<?php
    $pageTitle = 'Dashboard';
    session_start();

    if (isset($_SESSION['Username'])) {
        
        include 'init.php'; ?>

        <div class="container home-stats text-center">
            <h1>Dashboard</h1>
            <div class="row">
                <div class="col-md-3">
                    <a href="members.php">
                    <div class="stat st-mem">
                        Total Members
                        <span><?php echo countItem('UserID', 'users') ?></span>
                    </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <div class="stat st-pen">
                        Pending Members
                        <span><?php echo countItem('RegStatus', 'users', 0) ?></span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat st-it">
                        Total Items
                        <span>200</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat st-com">
                        Total Comments
                        <span>200</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="container latest">
            <div class="row">
                <div class="col-sm-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-users"></i> Latest Registerd Users
                        </div>
                        <div class="panel-body">
                            Test
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-tag"></i> Latest Items
                        </div>
                        <div class="panel-body">
                            Test
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <?php include $tpl . "footer.php";

    } else {

        header('Location: index.php');

        exit();

    }