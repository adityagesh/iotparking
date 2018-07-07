<?php
$flag=[];
$bookflag=[];
$username="root";
$password="root";
$database="PARKING";
$conn=mysql_connect('localhost',$username,$password) or die("Connection problem");
$db_select=mysql_select_db($database,$conn) or die("Unable to select DB");
$query="SELECT * FROM STATUS ORDER BY i;" ;
$result=mysql_query($query,$conn) or die("QUERY ERROR");
$num=mysql_num_rows($result);
$i=0;

while($i<$num)
	{
	$id=mysql_result($result,$i,"id");
	$flag[$i+1]=intval(mysql_result($result,$i,"flag"));  	      //i represents an id.CHECK DB STATUS FOR RELATION
	$bookflag[$i+1]=intval(mysql_result($result,$i,"bookflag"));
	$i++;
	}

	
//auto deletion from db after specific time

$curr_time=time();
$query="SELECT time,pid FROM BOOKING;";
$result=mysql_query($query,$conn) or die(mysql_error());
$num=mysql_num_rows($result);
$i=0;
while($i<$num)
	{
	$time=intval(mysql_result($result,$i,"time"));
	$pid=mysql_result($result,$i,"pid");	
	$i++;
	$tempquery="SELECT flag FROM STATUS WHERE id LIKE '$pid';"; 	//check if vehicle is present
	$tempresult=mysql_query($tempquery);
	$clear=mysql_result($tempresult,0,"flag");
	if($curr_time-$time >= 20 && $clear==0)			//vechicle is not present and booking time exceeded
	{
	$query="UPDATE STATUS SET flag=0, bookflag=0 WHERE id LIKE '$pid';"; 	//release the slot
	mysql_query($query);
	$query="DELETE FROM BOOKING WHERE pid LIKE '$pid';";		//delete the booking
	mysql_query($query);
	}
	
	}

mysql_close();
?>
<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="refresh" content="7">
  <meta charset="UTF-8">
  <title>Parking Slot</title>
  <meta name="viewport" content="width=device-width">
  <link rel="stylesheet" href="css/normalize.min.css">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1 align="center">Car Parking</h1>
<div class="road">
  <div class="exit exit--front fuselage">
  </div>
  <form method="POST" action="" id="slot">
  <ol class="cabin fuselage">
    <li class="row row--1">
      <ol class="cars" type="A">
        <li class="car">
          <input type="checkbox" id="3A" name="check" value="3A" <?php if($flag[3] || $bookflag[3]) echo 'disabled'; ?>/>
          <label for="3A">3A</label>
        </li>
        
        <li class="car">
          <input type="checkbox" id="1B" value="1B" name="check" <?php if($flag[6] || $bookflag[6]) echo 'disabled'; ?> />
          <label for="1B">1B</label>
        </li>
      </ol>
    </li>
    <li class="row row--2">
      <ol class="cars" type="A">
        <li class="car">
          <input type="checkbox" id="2A" name="check" value="2A" <?php if($flag[2] || $bookflag[2]) echo 'disabled'; ?>/>
          <label for="2A">2A</label>
        </li>
        <li class="car">
          <input type="checkbox" id="2B" name="check" value="2B" <?php if($flag[7] || $bookflag[7]) echo 'disabled'; ?> />
          <label for="2B">2B</label>
        </li>
      </ol>
    </li>
    <li class="row row--3">
      <ol class="cars" type="A">
        <li class="car">
          <input type="checkbox" id="1A" name="check" value="1A" <?php if($flag[1] || $bookflag[1]) echo 'disabled'; ?>/>
          <label for="1A">1A</label>
        </li>
        <li class="car">
          <input type="checkbox" id="3B" name="check" value="3B" <?php if($flag[8] || $bookflag[8]) echo 'disabled'; ?>/>
          <label for="3B">3B</label>
        </li>
      </ol>
    </li>

  </ol>
   <div class="exit exit--back fuselage">
  </div>
</div>
<input type="textbox" name="uid">
<input type="submit" name="submit">
 </form>
<?php

if(isset($_POST['submit'])){ $uid=$_POST['uid']; $id=$_POST['check']; $time=time();         // if submitted
	if($id=="" || $uid=="" ){ echo "ERROR"; } 					//if any field is empty
else	{
	
	$conn=mysql_connect('localhost',$username,$password) or die("Connection problem");
	$db_select=mysql_select_db($database,$conn) or die("Unable to select DB");
	$query="INSERT INTO BOOKING VALUES('$uid',$time,'$id');";		//add booking
	$result=mysql_query($query,$conn);
	if($result){$query="UPDATE STATUS SET bookflag=1 WHERE id LIKE '$id';";		//block the slot
	$result=mysql_query($query,$conn);
	echo "SLOT BOOKED : $id";}
	else echo "ERROR. Only one booking per user";
	mysql_close();
	echo "<meta http-equiv='refresh' content='0'>";
	} 
}

?>
<script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
</body>
</html>
