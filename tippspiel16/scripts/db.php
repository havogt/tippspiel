<?

$db_host = "db628583980.db.1and1.com";
$db_user = "dbo628583980";
$db_pw = "xPAfzyETtAgTOOJupJOY";
$db_name = "db628583980";

$db_table_suffix_glob = "tipp2016_";

/**
 * Stellt die Verbindung zur Datenbank her.
 * @return int
 * @author Hadschi
 * @date 18.7.2004
 */
function connect_db()
{
	global $db_host, $db_user, $db_pw, $db_name;
	$db_connection = mysql_connect( $db_host, $db_user, $db_pw ) or
		die( "Konnte keine Verbindung zur Datenbank herstellen" );
	mysql_select_db( $db_name, $db_connection ) or
		die( "Konnte Datenbank nicht finden" );
	return $db_connection;
}

/**
 * Gibt einen Wert aus der Datenbank zurück.
 * Ubergebenes Select-Query sollte nur einen Wert zurückliefern,
 * sonst wird nur der erste Wert des Results zurückgegeben.
 * @return DB-Eintrag
 * @author Hadschi
 * @date 18.7.2004
 */
function db_get( $query )
{
	$result = db_query( $query );
	return mysql_result( $result, 0 );
}

/**
 * Gibt einzeilige Select-Ergebnisse zurück.
 * Zugriff über $r["<Spaltenname>"]
 * @return array[]
 * @author Hadschi
 * @date 18.7.2004
 */
function db_get_array( $query )
{
	$result = db_query( $query );
	$r = mysql_fetch_array( $result );
	return $r;
}

/**
 * Gibt mehrzeilige Select-Ergebnisse zurück.
 * Zugriff über $results[<Zeilenindex>]["<Spaltenname>"]
 * @return array[][]
 * @author Hadschi
 * @date 18.7.2004
 */
function db_get_more_array( $query )
{
	$result = db_query( $query );
	while( $r = mysql_fetch_array( $result ) )
	{
		$results[] = $r;
	}
	return $results;
}

/**
 * Führt ein mysql_query() mit übergebenem Query aus.
 * @author Hadschi
 * @date 18.7.2004
 */
function db_query( $query )
{
	$result = mysql_query( $query ) or die( "DB-Fehler: "
											."<li>errorno=".mysql_errno()
											."<li>error=".mysql_error()
											."<li>query=".$query
										);
	return $result;
}

/**
 * Führt ein mysql_query() mit übergebenem Query aus und liefert als Return die
 * ID des per autoincrement generierten Primärschlüssel.
 * @return int insert_id
 * @author Hadschi
 * @date 18.7.2004
 */
function db_query_insert_id( $query )
{
	$result = mysql_query( $query ) or die( "DB-Fehler: "
											."<li>errorno=".mysql_errno()
											."<li>error=".mysql_error()
											."<li>query=".$query
										);
	return mysql_insert_id();
}

connect_db();

?>
