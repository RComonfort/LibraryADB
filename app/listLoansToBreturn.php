<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png" />
    <link rel="icon" type="image/png" href="../assets/img/favicon.png" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>Library</title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />
    <!-- Bootstrap core CSS     -->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet" />
    <!--  Material Dashboard CSS    -->
    <link href="../assets/css/material-dashboard.css?v=1.2.0" rel="stylesheet" />
    <!--     Fonts and icons     -->
    <link href="http://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" rel="stylesheet">
    <link href='http://fonts.googleapis.com/css?family=Roboto:400,700,300|Material+Icons' rel='stylesheet' type='text/css'>
</head>

<body>
    <?php
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        require_once "../models/Breturn.php";
        $db = new Database;
        $breturn = new Breturn($db);
        $breturns = $breturn->get(1);        
    ?>
    <div class="wrapper">
        <div class="sidebar" data-color="purple" data-image="../assets/img/sidebar-1.jpg">
            <!--
        Tip 1: You can change the color of the sidebar using: data-color="purple | blue | green | orange | red"

        Tip 2: you can also add an image using data-image tag
    -->
            <div class="logo">
                Library
            </div>
            <div class="sidebar-wrapper">
                <ul class="nav">
                    <li>
                        <a href="./splash.html">
                            <p>Home</p>
                        </a>
                    </li>
                    <li class="active">
                        <a href="./listLoans.php">
                            <p>Loans</p>
                        </a>
                    </li>
                    <li>
                        <a href="./listBreturns.php">
                            <p>Returns</p>
                        </a>
                    </li>
                    <li>
                        <a href="./listFines.php">
                            <p>Fines</p>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="main-panel">
            <nav class="navbar navbar-transparent navbar-absolute">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="#"> Library </a>
                    </div>
                </div>
            </nav>
            <div class="content">
                <div class="container-fluid">
                    <div class="col-lg-12">
                        <h2 class="text-center text-primary">Fines List</h2>
                        <?php
                            if( ! empty( $breturns ) ) {
                        ?>
                        <table class="table">
                            <thead class="text-primary">
                                <th>Loan Id</th>
                                <th>Loan Date</th>
                                <th>Client Id</th>
                                <th>Number of books</th>
                                <th>Return date</th>
                            </thead>
                            <tbody>
                                <?php foreach( $breturns as $loan )
                                {
                                ?>
                                <tr>
                                    <td><?php echo $loan->loanid ?></td>
                                    <td><?php echo $loan->loan_date ?></td>
                                    <td><?php echo $loan->clientid ?></td>
                                    <td><?php echo $loan->bookcount ?></td>
                                    <td><?php echo $loan->return_date ?></td>
                                    <td>
                                        <a class="btn btn-primary" href="<?php echo Breturn::baseurl() ?>app/saveBretuns.php?loan=<?php echo $loan->loanid ?>">Return</a> 
                                    </td>
                                </tr>
                                <?php
                                }
                                ?>
                            <tbody>
                        </table>
                        <?php
                        }
                        else
                        {
                        ?>
                        <div class="alert alert-danger" style="margin-top: 100px">There are 0 loans to return</div>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<!--   Core JS Files   -->
<script src="../assets/js/jquery-3.2.1.min.js" type="text/javascript"></script>
<script src="../assets/js/bootstrap.min.js" type="text/javascript"></script>
<script src="../assets/js/material.min.js" type="text/javascript"></script>
<!--  Charts Plugin -->
<script src="../assets/js/chartist.min.js"></script>
<!--  Dynamic Elements plugin -->
<script src="../assets/js/arrive.min.js"></script>
<!--  PerfectScrollbar Library -->
<script src="../assets/js/perfect-scrollbar.jquery.min.js"></script>
<!--  Notifications Plugin    -->
<script src="../assets/js/bootstrap-notify.js"></script>
<!-- Material Dashboard javascript methods -->
<script src="../assets/js/material-dashboard.js?v=1.2.0"></script>

</html>