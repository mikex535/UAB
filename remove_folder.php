<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<?php
    if(!isset($_COOKIE["user"])) {
        header("Location: login.html?redirect=".basename($_SERVER['PHP_SELF'])); 
		exit();
    } else {
        setcookie("user",$_COOKIE["user"], time() + (3600/4), "/");
        $conn = mysqli_connect("127.0.0.1","root");
        $result = mysqli_query($conn,"SELECT role from test.users where usr='".$_COOKIE["user"]."'");
        
        $role = mysqli_fetch_array($result)["role"];
        if($role != "Admin"){
            header("Location: main.php"); 
            exit();
        } else {
            if(count($_POST) != 0) {
                if ((int)$_POST["remove_folder"] != 100000){
                    mysqli_query($conn,"delete from test.reports_tree 
                        where id=".$_POST["remove_folder"]." or parent_id = ".$_POST["remove_folder"]);
                    mysqli_query($conn,"delete from test.report_detail 
                        where id not in (select id from reports_tree)");
                    mysqli_query($conn,"delete from test.subscriptions 
                        where report_id not in (select id from reports_tree)");
                    setcookie("selected_folder_id", $row["id"],time() - (3600/4), "/");
                    header("Location: main_admin.php"); 
                    exit();
                } else {
                    echo "<p>Cannot delete root. Redirecting...</p>";
                    header("Refresh: 3; url= http://127.0.0.1/project/main_admin.php"); 
                    exit();
                }
            } else {
                header("Location: main_admin.php"); 
                exit();
            }
        }
    }
?>    
</body>
</html>
