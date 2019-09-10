<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Subscription Manager</title>
    <link href="tree.css" rel="stylesheet">
    <style>
        body{
            background-color : rgb(224,224,224);
        }
        #user_corner{
            float: right;
        }
        #logo{
            width:100px;
            height:100px;
        }
        #admin_ctls {
            float: left;
            margin-left : 30px;
        }
    </style>
</head>
<?php
    if(!isset($_COOKIE["user"])) {
        header("Location: login.html?redirect=".basename($_SERVER['PHP_SELF'])); 
		exit();
    } else {
        setcookie("user",$_COOKIE["user"], time() + (3600/4), "/");
        $conn = mysqli_connect("127.0.0.1","root");
        $result = mysqli_query($conn,"SELECT role from test.users where usr='".$_COOKIE["user"]."'");
        $role = mysqli_fetch_array($result)["role"];
    }
?>
<script>
    function getCookie(cname) {
        var name = cname + "=";
        var decodedCookie = decodeURIComponent(document.cookie);
        var ca = decodedCookie.split(';');
        for(var i = 0; i <ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') {
            c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
            }
        } 
        return "";
    }
</script>
<?php
function get_children($conn,$id){
    $query = "select * from test.reports_tree where parent_id =".$id." order by type";
    return mysqli_query($conn,$query);
}
function create_branches($conn,$result,$html_code){
    while ($row=mysqli_fetch_array($result)){
        if($row["type"]=="folder"){
            $insertion = "<li><span class=\"caret\">".$row["name"]."</span>
                            <ul class=\"nested\">
                            <!--".$row["id"]."-->
                            </ul>
                            </li>";
            $html_code = substr_replace($html_code, $insertion, strpos($html_code,"<!--".$row["parent_id"]."-->"), 0);
            $html_code = create_branches($conn, get_children($conn,$row["id"]),$html_code);
        } else {
            $insertion = "<li><a href=\"report.php?id=".$row["id"]."\">".$row["name"]."</a></li>";
            $html_code = substr_replace($html_code, $insertion, strpos($html_code,"<!--".$row["parent_id"]."-->"), 0);
        }
    } return $html_code;
}
?>
<body>
    <div id="user_corner">
        <p style="text-align: right" id="curr_usr">
            - <a href="logout.php"> Logout</a>
        </p>
    </div>
    <div id=logo>
        <img src="logo.png" style="width:100px;height:100px">
    </div>
    <h2>Reports:</h2>
    <div id="tree_box">
        <ul id="myUL">
            <?php
            $query = "select * from test.reports_tree where parent_id is null order by type";
            $result = mysqli_query($conn,$query);
            $row =  mysqli_fetch_array($result);
            $html_code = "<li><span class=\"caret\">".$row["name"]."</span>
                        <ul class=\"nested\">
                        <!--".$row["id"]."-->
                        </ul>
                        </li>";
            $html_code = create_branches($conn,get_children($conn,$row["id"]),$html_code);
            $query = "select * from test.reports_tree where parent_id =".$row["id"]." order by type";
            echo $html_code ;           
            ?>
        </ul>
    </div>
    
    <script>
    var curr = document.getElementById("curr_usr");
    curr.innerHTML = getCookie("user") + " " + curr.innerHTML;
    </script>
    <script>
        var toggler = document.getElementsByClassName("caret");
        var i;
        function tgl() {
            this.parentElement.querySelector(".nested").classList.toggle("active");
            this.classList.toggle("caret-down");
        }
        function tglx(elmnt) {
            elmnt.parentElement.querySelector(".nested").classList.toggle("active");
            elmnt.classList.toggle("caret-down");
        }
        for (i = 0; i < toggler.length; i++) {
            tglx(toggler[i]);
        }
        for (i = 0; i < toggler.length; i++) {
        toggler[i].addEventListener("click", tgl);
        }
    </script>
</body>
</html>