<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Listado de usuarios</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" media="screen" title="no title" charset="utf-8">
    </head>
    <body>
        <?php
            require_once "../models/User.php";
            $db = new Database;
            $user = new User($db);
            $users = $user->get();        
        ?>
        <div class="container">
            <div class="col-lg-12">
                <h2 class="text-center text-primary">Users List</h2>
                <div class="col-lg-1 pull-right" style="margin-bottom: 10px">
                    <a class="btn btn-info" href="<?php echo User::baseurl() ?>/app/add.php">Add user</a>
                </div>
                <?php
                if( ! empty( $users ) ) {
                ?>
                <table class="table table-striped">
                    <tr>
                        <th>Id</th>
                        <th>Username</th>
                        <th>Password</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                    <?php foreach( $users as $user )
                    {
                    ?>
                        <tr>
                            <td><?php echo $user->id ?></td>
                            <td><?php echo $user->username ?></td>
                            <td><?php echo $user->password ?></td>
                            <td><?php echo $user->created_at ?></td>
                            <td>
                                <a class="btn btn-info" href="<?php echo User::baseurl() ?>app/edit.php?user=<?php echo $user->id ?>">Edit</a> 
                                <a class="btn btn-info" href="<?php echo User::baseurl() ?>app/delete.php?user=<?php echo $user->id ?>">Delete</a>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </table>
                <?php
                }
                else
                {
                ?>
                <div class="alert alert-danger" style="margin-top: 100px">There are 0 registered users</div>
                <?php
                }
                ?>
            </div>
        </div>
    </body>
</html>