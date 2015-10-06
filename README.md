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
Set the "Content-Type" header to "text/event-stream"
Specify that the page should not cache
Output the data to send (Always start with "data: ")
Flush the output data back to the web page
