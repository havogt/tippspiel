<? 
require_once "db.php";
global $db_table_suffix_glob;
$teams = db_get_more_array( "SELECT * FROM ".$db_table_suffix_glob."teams" ); 

$year = 2016;

if( $_POST["request"] == "submit" )
{
	$splittedtime = split( ":", $_POST["thetime"] );
	db_query( "INSERT INTO ".$db_table_suffix_glob."matches ( Gruppe, Team1, Team2, Datum) VALUES ( '".$_POST["group"]."', '".$_POST["team1"]."', '".$_POST["team2"]."', '".mktime( $splittedtime[0], $splittedtime[1],0,$_POST["month"],$_POST["day"],$year )."' )" );

	Echo( "Match submitted" );
}
?>

<html>
<head>
<title>Spieleintrag</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body bgcolor="#FFFFFF" text="#000000">
<form name="form1" method="post" action="">
  <table width="400" border="0" cellspacing="0" cellpadding="0">
    <tr> 
      <td>Team 1</td>
      <td> 
        <select name="team1">
		<? foreach($teams as $t ) { ?>
		<option value="<?=$t["TeamID"]?>"> 
		<?=$t["Name"]?>
		</option>
		<? } ?>
        </select>
      </td>
    </tr>
    <tr> 
      <td>Team 2</td>
      <td> 
        <select name="team2">
		<? foreach($teams as $t ) { ?>
		<option value="<?=$t["TeamID"]?>"> 
		<?=$t["Name"]?>
		</option>
		<? } ?>
        </select>
      </td>
    </tr>
    <tr> 
      <td>Datum (Tag Monat Stunde)</td>
      <td> 
        <select name="day">
          <? for($i=1;$i<=30;$i++){ ?>
          <option value="<?=$i?>"> 
          <?=$i?>
          </option>
		  <? } ?>
        </select>
        <select name="month">
          <option value="6" selected>Juni</option>
          <option value="7">Juli</option>
        </select>
        <select name="thetime">
          <option value="15:00">15:00</option>
          <option value="18:00">18:00</option>
		  <option value="21:00">21:00</option>
        </select>
      </td>
    </tr>
    <tr>
      <td>Gruppe</td>
      <td>
        <select name="group">
          <!--<option value="A">A</option>
          <option value="B">B</option>
          <option value="C">C</option>
          <option value="D">D</option>
		  <option value="E">E</option>
		  <option value="F">F</option>
		  <option value="G">G</option>
		  <option value="H">H</option>--> 
		  <option value="8">8</option>
          <option value="4">4</option>
          <option value="2">2</option>
          <option value="3">3</option>
          <option value="1">1</option>
        </select>
      </td>
    </tr>
    <tr> 
      <td> 
		<input type="hidden" name="request" value="submit">
        <input type="submit" name="Submit" value="Submit">
      </td>
      <td>&nbsp;</td>
    </tr>
	<tr>
		<td>
		</td>
	</tr>
  </table>
</form>
</body>
</html>
