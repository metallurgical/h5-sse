# HTML5-server-sent-event
A server-sent event is when a web page automatically gets updates from a server.  This was also possible before, but the web page would have to ask if any updates were available. With server-sent events, the updates come automatically.  Examples: Facebook/Twitter updates, stock price updates, news feeds, sport results, etc.

CLIENT
=======================

    var source = new EventSource("demo_sse.php");
      source.onmessage = function(event) {
      document.getElementById("result").innerHTML += event.data + "<br>";
    };

SERVER
==============================

    <?php
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');

    $time = date('r');
    echo "data: The server time is: {$time}\n\n";
    flush();
    ?>

Code explained
================================
 - Set the "Content-Type" header to "text/event-stream"
 - Specify that the page should not cache
 - Output the data to send (Always start with "data: ")
 - Flush the output data back to the web page

Example
=============================
This example are used PHP and MYSQL and can be found on "From-db" folder. Consists of :
 - index.html
 - hello.php

To test, copy both index.html and hello.php into webserver folder and run index.html in your web page(or download this repo). Open database(in example were used Xampp - phpmyadmin), and insert data into table user and save. At the same time, see the changes inside web page displaying newly inserted data from database. Have fun!!
 
**Index.html**

``` html
    <!DOCTYPE html>  
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Server Sent Event</title>
        <script type="text/javascript" src="http://code.jquery.com/jquery-2.1.4.js"></script>
    </head>
    <body>
        <div id="result"></div>
        <script type="text/javascript">

        ( function ( $ ) {

            $( function () {

              // check for browser support
              // for even source features
              if ( typeof( EventSource ) !== "undefined" ) {
                // create event source 
                var src = new EventSource("hello.php");
                // trigger message handler
                // if new data received
                src.addEventListener( 'message', function ( e ) {
                  // anyway append into page
                  // for user view
                  $( '#result' ).append( e.data + '<br/>' );
                });
        
              }
              else {

                alert( 'Sooorry, your browser did\'t support EventSource Features, Please use Firefox or Chrome' );
                // if did't support 
                // create an AJAX request 
                // to replace EventSource

              }
            });

        })( jQuery )
  
    </script>
    </body>
    </html>
```

**hello.php**

```php
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
    	// never remove data in front of below statement
    	echo "data: The name is: {$name}\n\n";
    	ob_flush();
    	flush();		
    }
    // initial calling function
    checkExistingData();
    
    ?>
