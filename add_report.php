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
                echo $_POST["add_report"];
                if(preg_match('/^[_A-z0-9]*((-|\s)*[_A-z0-9])*$/',$_POST["add_report"])){
                    $found_dup = true;
                    while($found_dup){
                        $found_dup = false;
                        $new_id = mt_rand(100001, 999999);
                        $result = mysqli_query($conn,'select id from test.reports_tree');
                        $all_ids = array();
                        while ($id = mysqli_fetch_array($result)["id"]){
                            array_push($all_ids,$id);
                        }
                        if (in_array($new_id,$all_ids)){
                            $found_dup = true;
                        }
                    }
                    mysqli_query($conn,"insert into test.reports_tree 
                        values (".$new_id.",'".$_POST["add_report"]."',
                        'report',".$_COOKIE["selected_folder_id"].")");
                    mysqli_query($conn,"insert into test.report_detail
                        values (".$new_id.",'".$_POST["description"]."')");
                    header("Location: main_admin.php"); 
                    exit();
                } else {
                    echo "<p>Report name not allowed. Redirecting...</p>";
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
