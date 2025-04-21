<?php
// This script starts the PHP built-in server on the correct port

// Get the port from the environment variable (Railway assigns this automatically)
$port = getenv('PORT') ?: 52319; // Use the Railway PORT environment variable, fallback to 8080 if not available

// Start the PHP server on the correct port
echo "Starting PHP server on port $port\n";
shell_exec("php -S 0.0.0.0:$port"); // Start the server on the assigned port
