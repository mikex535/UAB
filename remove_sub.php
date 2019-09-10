<?php
if(!isset($_COOKIE["user"])) {
    header("Location: login.html?redirect=".basename($_SERVER['PHP_SELF'])); 
    exit();
} else {
    setcookie("user",$_COOKIE["user"], time() + (3600/4), "/");
    $conn = mysqli_connect("127.0.0.1","root");
    if(count($_POST) != 0){
        $query = "delete from test.subscriptions where usr = '".$_COOKIE["user"]."' and report_id = ".$_POST["id"];
        $result = mysqli_query($conn,$query);
        header("Location: main_admin.php"); 
        exit();
    } else {
        header("Location: main_admin.php"); 
        exit();
    }
}
?>
