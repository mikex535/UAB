<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Report</title>
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
        if(count($_GET) != 0){
            $query = "SELECT a.id, a.name, b.description FROM test.reports_tree A 
                LEFT JOIN test.report_detail B on b.id = a.id
                where a.type = 'report'
                and a.id=".$_GET["id"];
            $result = mysqli_query($conn,$query);
            $row =  mysqli_fetch_array($result);
            $r_name = $row["name"];
            $r_desc = $row["description"];
            $query = "SELECT count(*) subscribed from test.subscriptions WHERE
                usr = '".$_COOKIE["user"]."' and report_id = ".$_GET["id"]." and end_date > current_date";
            $result = mysqli_query($conn,$query);
            $subscribed = mysqli_fetch_array($result)["subscribed"];
        }
        else {
            header("Location: main_admin.php"); 
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
    <h1><b>Report: </b> <?php echo $r_name;?></h1>
    <div style="width:30%">
        <p><b>Description: </b> <?php echo $r_desc;?></p>
        <p><b>Current Status: </b> <?php if($subscribed==0)echo "Not ";?> Subscribed</p>
        <form action="remove_sub.php" method="post">
            <input type="hidden" name="id" value=<?php echo '"'.$_GET["id"].'"'?>>
            <button type="submit" name="delete" value=<?php echo '"'.$_GET["id"].'"'?>>Unsubscribe</button>
        </form>
    </div>
    <form action="subscribe.php" method="post">
        <span style="padding:0px">Scheduled Days:</span>
        <label><input type="checkbox" name="days[]" value="U" /> Sun</label>
        <label><input type="checkbox" name="days[]" value="M" /> Mon</label>
        <label><input type="checkbox" name="days[]" value="T" /> Tue</label>
        <label><input type="checkbox" name="days[]" value="W" /> Wed</label>
        <label><input type="checkbox" name="days[]" value="R" /> Thu</label>
        <label><input type="checkbox" name="days[]" value="F" /> Fri</label>
        <label><input type="checkbox" name="days[]" value="S" /> Sat</label><br>
        <label> Hours (ex: "0,12", "16-18"):<input type="text" name="hours" style="margin-left:15px" required/></label><br>
        <label> End Date: <input type="date" name="end_date"required></label><br>
        <input type="hidden" name="id" value=<?php echo '"'.$_GET["id"].'"'?>>
        <input type="submit" value="Subscribe">
    </form>
</body>
</html>