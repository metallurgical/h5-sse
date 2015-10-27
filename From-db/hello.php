<?php
/**
 *  Respone Header
 */
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
/**
 * Connection to database
 * @return [connection]
 */
function connect() {
	// set connection
	// use localhost as for testing only
	$con = mysqli_connect( 'localhost','root','' );
	mysqli_select_db( $con,'test' );

	return $con;

}
/**
 * Get data from database
 * @return [array] [data fetch from database and rows of data]
 */
function getData () {
	// call connection function
	$con    = connect();
	// fetching data from database
	$select = mysqli_query( $con, "SELECT *FROM user order by user_id DESC" );
	$data   = mysqli_fetch_array( $select );
	$row    = mysqli_num_rows( $select );
	// return data and rows in database
	// return array( $data --> results, $row --> number of rows of results );
	return array( $data, $row );
}

/**
 * Check existing data
 * Store old id into session
 * and matched the new data inserted with session variable
 * @return [type] [description]
 */
function checkExistingData () {
	
	session_start();
	// if session old id is exist & not empty
	if ( isset( $_SESSION['oldId'] ) && $_SESSION['oldId'] != "" ) {
		// we check data from database
		$data = getData();
		// if data exist in database
		// $data[0] == data
		// $data[1] == rows
		if ( $data[1] > 0 ) {
			// if old session NOT matched with
			// the newly inserted data from database
			if ( $_SESSION['oldId'] != $data[0]['user_id'] ) {
				// call send message function
				// to send message into client-side
				// only send simple data
				sendMsg( $data[0]['name'] );
				// then we store newly inserted data 
				// into session(SAVED)
				$_SESSION['oldId'] = $data[0]['user_id'];
			}
		}
		// if all data empty from database
		// or no data matched with the query
		else {
			// destroy old session
			session_destroy();
		}

	} 
	// this is for initial start of event source
	// at start, we dont create any session
	// matched with the query
	else {		
		// call the function
		$data = getData();
		// if data in database more than 0
		if ( $data[1] > 0 ) {
			// create first session using user id
			$_SESSION['oldId'] = $data[0]['user_id'];
			// send message to client
			sendMsg( $data[0]['name'] );
		}
		
		
	}

}
// create event source
// send to client side
function sendMsg ( $name ) {
	echo "data: The name is: {$name}\n\n";
	ob_flush();
	flush();		
}
// initial calling function
checkExistingData();

?>