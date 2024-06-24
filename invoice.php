<?php
session_start();

if(isset($_POST['process'])):
	//membuat session array dengan variabel - variabel POST
	$_SESSION['pos']=$_POST;
endif;

if(isset($_SESSION['pos'])):
	$uangmasuk=$_SESSION['pos']['uangmasuk'];
	$biayatrf =$_SESSION['pos']['bytrf'];
	$txt_pemb =$_SESSION['pos']['txt_pemb'];
	$invamount=$_SESSION['pos']['amount'];
else:
	$uangmasuk	='';
	$biayatrf	='';
	$txt_pemb	='';
	$invamount	='';
endif;

session_destroy();
?>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="invoice.css">
	<script src="invoice.js"></script>
	<title>Looking for paid off Invoice</title>

	<style>
		/* Chrome, Safari, Edge, Opera */
		input::-webkit-outer-spin-button,
		input::-webkit-inner-spin-button {
		  -webkit-appearance: none;
		  margin: 0;
		}

		/* Firefox */
		input[type=number] {
		  -moz-appearance:textfield;
		}
		
		/* table, th, td {border: 1px solid black;} */
}
	</style>
	
</head>
<body>
	<form method="post" action="">  
	<fieldset style="width:1000px">
	<legend><b>Lengkapi kolom berikut</b></legend>
	<br>
	<table>
	<tr>
	<td><font face="arial" size="2"><b>Jumlah uang masuk:</td>
	<td><input class="box" type="number" name="uangmasuk" placeholder="Uang masuk" value="<?php echo $uangmasuk; ?>" /></td><td></td><td></td><td></td>
	<td><font face="arial" size="2"><b>Biaya transfer:</td>
	<td><input class="boxx" type="number" name="bytrf" placeholder="0" value="<?php echo $biayatrf; ?>" /></td>
	</tr>
	<tr>
	<td><font face="arial" size="2"><b>Paste inv amount here:</td>
	<td><textarea class="area" type="number" name="amount" placeholder="1000
2000
3000
...
..."><?php echo $invamount;?></textarea></td><td></td><td></td><td></td>
	</td> <!-- </td> -->
	<!-- <td colspan="3" style="vertical-align:top;"><input type="checkbox" name="chk_pembulatan" checked="checked"><font face="arial" size="2">Cari pembulatan (opsional) max [-/+]<input class="box2" name="txt_pemb" size="5" placeholder="0" value="<?php echo $txt_pemb; ?>" /></font></input> </td> -->
	<td style="vertical-align:top;"><font face="arial" size="2"><b>Pembulatan (max): </b></td>
	<td style="vertical-align:top;"><font face="arial" size="2"><input class="boxx" type="number" name="txt_pemb" placeholder="0" value="<?php echo $txt_pemb; ?>" />[-/+]</font></input> </td>

	</tr>
	<tr>
	<td></td>
	<td><input class="button" type="submit" name="process" value="Process" /></td>
	</tr>
	
	</table>
	</fieldset>
	</form>
		<b>Result:</b><br/><br/>
</body>
</html>
<?php
$executionStartTime = microtime(true);
$array = isset($_POST['amount'])?$_POST['amount']:"";
$amount = explode(" ", str_replace("\r", " ", $array));
//I dont check for empty() incase your app allows a 0 as ID.
if (strlen($array)==0) {
  echo '-no input-';
  exit;
}

$uangmasuk=$_POST['uangmasuk'];
if ($_POST['uangmasuk']=="") {$uangmasuk=0;} //set def value 0 if uangmasuk box is null

$bytrf=$_POST['bytrf'];
if ($_POST['bytrf']=="") {$bytrf=0;} //set def value 0 if bytrf box is null

$txt_pemb=$_POST['txt_pemb'];
if ($_POST['txt_pemb']=="") {$txt_pemb=0;} //set def value 0 if pembulatan box is null

$totum = $uangmasuk+$bytrf;

$num = count($amount); 
$total = pow(2, $num); //The total number of possible combinations 
$hasil = array();
//for ($process=0; $process<$total; $process++) {echo "$process";}
//for ($i = 1; $i < $total; $i++) {
for ($i = 1; $i < $total; $i++) { //Loop through each possible combination 
		
	$datainv = array();
    for ($j = 0; $j < $num; $j++) {	//For each combination check if each bit is set
        if (pow(2, $j) & $i) //Is bit $j set in $i? 
		{			
			$datainv[] = "$amount[$j]";
		}
	}
	
	$selisih = $totum-array_sum($datainv);
	switch (true) {
		case ($selisih==0) :
			echo "<table><td style='width:auto; text-align:right; font-size:13'>";
			for($k=0; $k < count($datainv); $k++){
					echo number_format($datainv[$k])."<br />";
			}
			echo "<tr><td style='width:auto; text-align:right; font-size:13'><b> <hr style='width:auto;' />" .number_format(array_sum($datainv)). "</td>";
			if ($biayatrf!=0) {
			echo "<td style='width:auto; text-align:right; font-size:13'><b> <hr style='width:1; border-top:1px solid; color:#ffffd7;' /> ==> <font color=\"red\"> (trf " .number_format($uangmasuk). " + by trf " .number_format($bytrf). ")</font></b></td></table>";			
			}
			else {
			echo "<td style='width:auto; text-align:right; font-size:13'><b> <hr style='width:1; border-top:1px solid; color:#ffffd7;' /> ==> <font color=\"red\"> (trf " .number_format($uangmasuk). ")</font></b></td></table>";
			}
			echo "<br/><br/>";
			$hasil[] = array_sum($datainv);
			break 2;
			
		//pembulatan lebih bayar
		case ($selisih > 0 && $selisih <= $txt_pemb) :
			if ($txt_pemb == 0){ break; }
			$pemb= ($totum-array_sum($datainv));
			echo "<table><td style='width:auto; text-align:right; font-size:13'>";
			for($k=0; $k < count($datainv); $k++){
					echo number_format($datainv[$k])."<br />";
			}
			echo "<tr><td style='width:auto; text-align:right; font-size:13'><b> <hr style='width:auto;' />" .number_format(array_sum($datainv)). "</td>";
			if ($biayatrf!=0) {
			echo "<td style='width:auto; text-align:right; font-size:13'><b> <hr style='width:1; border-top:1px solid; color:#ffffd7;' /> ==> <font color=\"red\"> (trf " .number_format($uangmasuk). " + by trf " .number_format($bytrf). " - pembulatan " .number_format($pemb). ")</font></b></td></table>";	
			}
			else {
			echo "<td style='width:auto; text-align:right; font-size:13'><b> <hr style='width:1; border-top:1px solid; color:#ffffd7;' /> ==> <font color=\"red\"> (trf " .number_format($uangmasuk). " - pembulatan " .number_format($pemb). ")</font></b></td></table>";
			}
			echo "<br/><br/>";
			$hasil[] = array_sum($datainv);
			break 2;
			
		//pembulatan kurang bayar
		case ($selisih >= -$txt_pemb && $selisih < 0) :
			if ($txt_pemb == 0){ break; }
			$pemb= (array_sum($datainv)-$totum);
			echo "<table><td style='width:auto; text-align:right; font-size:13'>";
			for($k=0; $k < count($datainv); $k++){
					echo number_format($datainv[$k])."<br />";
			}
			echo "<tr><td style='width:auto; text-align:right; font-size:13'><b> <hr style='width:auto;' />" .number_format(array_sum($datainv)). "</td>";
			if ($biayatrf!=0) {
			echo "<td style='width:auto; text-align:right; font-size:13'><b> <hr style='width:1; border-top:1px solid; color:#ffffd7;' /> ==> <font color=\"red\"> (trf " .number_format($uangmasuk). " + by trf " .number_format($bytrf). " + pembulatan " .number_format($pemb). ")</font></b></td></table>";
			}
			else {
			echo "<td style='width:auto; text-align:right; font-size:13'><b> <hr style='width:1; border-top:1px solid; color:#ffffd7;' /> ==> <font color=\"red\"> (trf " .number_format($uangmasuk). " + pembulatan " .number_format($pemb). ")</font></b></td></table>";
			}
			echo "<br/><br/>";
			$hasil[] = array_sum($datainv);
			break 2;
			
		default :
			break;
	}
	unset($datainv);
}

if ($hasil==array()) { echo "<font color=\"red\">-pencarian tidak ketemu-</font>"; }

$executionEndTime = microtime(true);
$seconds = $executionEndTime - $executionStartTime;
echo "<br/><br/><i>[This script took " .number_format($seconds,2). " seconds to execute]";
?>