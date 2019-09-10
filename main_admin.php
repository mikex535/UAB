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
        if($role != "Admin"){
            header("Location: main.php"); 
            exit();
        }
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
    <script>
        var curr = document.getElementById("curr_usr");
        curr.innerHTML = getCookie("user") + " " + curr.innerHTML;
    </script>
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
    <?php
        if(count($_POST) == 0) {
            if(!isset($_COOKIE["selected_folder_id"])){
                $selected_folder = 100000;
            } else {
                $selected_folder = (int)$_COOKIE["selected_folder_id"];
            }
        } 
        else {
            $selected_folder = $_POST["selected_folder"];
        }
        $query = "select * from test.reports_tree where id = ".$selected_folder;
        $result = mysqli_query($conn,$query);
        $row = mysqli_fetch_array($result);
        $folder_name = $row["name"];
        $folder_id = $row["id"];
        $folder_parent_id = (int)$row["parent_id"]; 
        setcookie("selected_folder_id", $row["id"],time() + (3600/4), "/");
    ?>
    <div id="admin_ctls">
        <p><b> Selected Folder:</b> <span style="font-family: 'Courier New', Courier, monospace">
            <?php 
            echo $folder_name;
            ?>
        </span></p>
        <form action="remove_folder.php" method="post">
            <button type="submit" name="remove_folder" value=<?php echo '"'.$folder_id.'"'?>>
                Delete Selected Folder
            </button>
        </form><br><br>
        <form action="main_admin.php" method="post">
            <label for="selected_folder">Select Folder: </label>
            <select name="selected_folder" id="dd1">
                <?php
                $query = "Select id, name from test.reports_tree 
                        where (id = ".$folder_parent_id." or parent_id = ".$folder_id." ) and type = 'folder'";
                $result = mysqli_query($conn,$query);
                while($row = mysqli_fetch_array($result)) {
                    if ($row["id"] ==  $folder_parent_id){
                        echo "\n<option value=\"".$row["id"]."\">".$row["name"]." (parent)</option>\n";
                    } else {
                       echo "\n<option value=\"".$row["id"]."\">".$row["name"]."</option>\n"; 
                    }
                }
                ?>
            </select>
            <input type="submit" value="Select">
        </form>
        <br><br>
        <form action="add_folder.php" method="post">
            <label for="add_folder">Add Folder: </label>
            <input type="text" name="add_folder" required>
            <input type="submit" value="Add">
        </form>
        
        <br><br>
        <div>
            <form action="add_report.php" method="post" id="report_form">
                <label for="add_report">Add Report: <br> Name:</label>
                <input type="text" name="add_report" style="width:100%" required><br>
                <label for="description">&Tab;Description:</label><br>
                <textarea name="description" style="height:40px;width:100%;overflow:auto" form="report_form"></textarea><br>
                <input type="submit" value="Add">
            </form>
        </div><br><br>
        <form action="remove_report.php" method="post">
            <label for="remove_report">Delete Report: </label>
            <select name="remove_report">
                <?php
                $query = "Select id, name from test.reports_tree 
                        where parent_id = ".$folder_id." and type = 'report'";
                $result = mysqli_query($conn,$query);
                while($row = mysqli_fetch_array($result)) {
                    echo "\n<option value=\"".$row["id"]."\">".$row["name"]."</option>\n"; 
                }
                ?>
            </select>
            <input type="submit" value="Delete">
        </form>
    </div>
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