<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $errorMessage = ""; // Default error message

    // Validate email format
    if (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $username)) {
        $errorMessage = '<span style="color: red;">Invalid email format. Please enter a valid email address.</span>';
    }

    // Validate password format
    elseif (strlen($password) < 8) {
        $errorMessage = '<span style="color: red;">Password must have at least 8 characters.</span>';
    } elseif (!preg_match("/[A-Z]/", $password)) {
        $errorMessage = '<span style="color: red;">Password must contain at least one uppercase letter.</span>';
    } elseif (!preg_match("/\d/", $password)) {
        $errorMessage = '<span style="color: red;">Password must contain at least one number.</span>';
    }

    // If there were no validation errors, proceed with account creation
    if ($errorMessage === "") {
        // Simulated account creation (replace this with proper database insertion)
        $host = "localhost";
        $db_user = "root";
        $db_password = "";
        $db_name = "users";

        $conn = new mysqli($host, $db_user, $db_password, $db_name);

        if ($conn->connect_error) {
            $errorMessage = '<span style="color: red;">Connection failed: ' . $conn->connect_error . '</span>';
        } else {
            // Check if the account already exists
            $checkSql = "SELECT * FROM users WHERE usermail = '$username'";
            $result = $conn->query($checkSql);

            if ($result->num_rows > 0) {
                $errorMessage = '<span style="color: red;">An account with this email already exists. Please use a different email address.</span>';
            } else {
                // Insert username (email) and password into the database
                $insertSql = "INSERT INTO users (usermail, password) VALUES ('$username', '$password')";

                if ($conn->query($insertSql) === TRUE) {
                    $errorMessage = '<span style="color:green;">Account created successfully. Click on <a href="index.html">Login</a></span>';
                } else {
                    $errorMessage = '<span style="color: red;">Error creating account: ' . $conn->error . '</span>';
                }

                $conn->close();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8" />
    <title>Create Account Form | CodeLab</title>
    <link rel="stylesheet" href="style.css" />
  </head>
  <body>
    <div class="wrapper">
      <div class="title">Create Account</div>
      <form
        action="create_account.php"
        method="post"
        onsubmit="return validateForm()"
      >
        <div class="field">
          <input type="text" name="username" id="email-input" required />
          <label>Email Address</label>
        </div>
        <div class="field">
          <input type="password" name="password" id="password-input" required />
          <label>Password</label>
        </div>
        <div class="field">
          <input type="submit" value="Create Account" />
        </div>
        <div class="login-link">
          Already have an account? <a href="index.html">Login</a>
        </div>
      </form>
      <div id="output">
                <?php echo $errorMessage; ?>
	</div>
    </div>
  </body>
</html>
