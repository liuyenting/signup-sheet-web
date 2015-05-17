<!DOCTYPE html>
<html>
<head>
<title> Displaying DB </title>
<meta http-equiv="refresh" content="5">
<style type="text/css">
#left_form {
	width:50%;
	float:left;
	background-color:#FFFFCC;	/* 淡黃色 */
}
#right_table {
	width:50%;
	float:right;
	top:300px;
	background-color:#99FFFF;	/* 淺藍色 */
}
</style>

<script>
	function whenSubmit{
		document.form.date.value = '<?=$date?>';
		document.form.track.value = '<?=$track?>';
	}
</script>

</head>

<body>
	
	<center>
	<?php
		ob_start();
		
		if(isset($_POST['date'])){
			$inputDate = $_POST['date'];
			setcookie("date",$inputDate);
		}
		else if(isset($_COOKIE['date'])){
			$inputDate = $_COOKIE['date'];
		}
		else{
			$inputDate = "";
		}
		if(isset($_POST['track'])){
			$inputTrack = $_POST['track'];
			setcookie("track",$inputTrack);
		}
		else if(isset($_COOKIE['track'])){
			$inputTrack = $_COOKIE['track'];
		}
		else{
			$inputTrack = "";
		}
		if(isset($_POST['date']) && isset($_POST['track'])){
			$isSubmit = false;
			if(strcasecmp($_POST['date'],"") != 0 && strcasecmp($_POST['track'],"") != 0){
				$isSubmit = true;
				$tempString = $_POST['date'];
				$inputTotal = "d-2015-05-" . $tempString[2] . $tempString[3] . "-" . $_POST['track'];
			}
		}
		else if(isset($_COOKIE['date']) && isset($_COOKIE['track'])){
			$isSubmit = false;
			if(strcasecmp($_COOKIE['date'],"") != 0 && strcasecmp($_COOKIE['track'],"") != 0){
				$isSubmit = true;
				$tempString = $_COOKIE['date'];
				$inputTotal = "d-2015-05-" . $tempString[2] . $tempString[3] . "-" . $_COOKIE['track'];
			}
		}
		else{
			$isSubmit = false;
		}		
		
		$dbname = "signup-sheet-db";
		$cName = ($isSubmit)? $inputTotal:"blank";
		
		ob_end_flush();
		
		echo '<h1>Date : ' . $inputDate . '&nbsp &nbsp Track : ' . $inputTrack . '</h1>';
	
		$connection = new MongoClient();
		$db = $connection->$dbname;
		$collection = $db->$cName;
		$user = "user";
		$userCollection = $db->$user;

		$cursor = $collection->find();
		
		echo '<table border="1" width="500" cellpadding="10" style="font-size:18px;" align="center"';
			echo '<tr>';
				echo '<td align="center">Name</td>';
				echo '<td align="center">Signed</td>';
				echo '<td align="center">Date</td>';
			echo '</tr>';
			
		$countSigned = 0;
		foreach($cursor as $obj){
			$userCursor = $userCollection->find(array('card_id'=>$obj["card_id"]));
			foreach($userCursor as $username){
				if($obj["signed"] == true){
					$countSigned = $countSigned + 1;
				}
				echo '<tr>';
					echo '<td align="center">' . $username["name"] . '</td>';
					echo '<td align="center">' . $obj["signed"] . '</td>';
					echo '<td align="center">' . $obj["date"] . '</td>';
				echo "</tr>";
			}
		}
		
		echo '<tr>
			<td align="center">總簽到人數</td>
			<td align="center">' . $countSigned . '</td>
			</tr>';
		echo '<br/></table><br/><br/>';
	
			
			echo '<form name="form" method="post" action="signup-sheet.php" onSubmit="whenSubmit()"><br/>
			<Select name="date" method="get" style="font-size:18px;" onChange="this.form.submit()">
				<Option Value="' . $inputDate . '">' . $inputDate . '</Option>
				<Option Value="0513">0513</Option>
				<Option Value="0514">0514</Option>
				<Option Value="0515">0515</Option>
				<Option Value="0516">0516</Option>
				<Option Value="0517">0517</Option>
			</Select>  ';
			if(isset($_POST['date'])){
				$searchTrack = $collection->find(array('Time'=>$_POST['date']));
			}
				echo '<Select name="track" method="get" style="font-size:18px;">';
				echo '<Option Value="' . $inputTrack . '">' . $inputTrack . '</Option>';
				echo '<Option Value="track1">Track 1</Option>
				<Option Value="track2">Track 2</Option>
				<Option Value="track3">Track 3</Option>
				<Option Value="track4">Track 4</Option>
				<Option Value="track5">Track 5</Option>'; 
				
			echo '</Select><br/><br/>
			<input type="submit" value="送出表單">
			</form><br/>';
		?>
		
	</center>
</body>
</html>