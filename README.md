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
 
Index.html

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
