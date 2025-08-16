<?php
require_once 'config/database.php';

echo "<h2>Database Setup</h2>";
echo "<p>Setting up the database and tables...</p>";

// Initialize the database with messages enabled
initializeDatabase(true);

echo "<h3>Setup completed successfully!</h3>";
echo "<p>You can now:</p>";
echo "<ul>";
echo "<li><a href='signup.php'>Create a new account</a></li>";
echo "<li><a href='login.php'>Login to existing account</a></li>";
echo "<li><a href='portfolio.html'>Go back to portfolio</a></li>";
echo "</ul>";

echo "<h3>Database Information:</h3>";
echo "<p><strong>Database Name:</strong> " . DB_NAME . "</p>";
echo "<p><strong>Host:</strong> " . DB_HOST . "</p>";
echo "<p><strong>Tables Created:</strong></p>";
echo "<ul>";
echo "<li>users - For storing user accounts</li>";
echo "<li>biodata - For storing biodata information</li>";
echo "</ul>";
?>
