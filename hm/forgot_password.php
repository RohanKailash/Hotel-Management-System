<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $newPassword = $_POST["new_password"];
    $confirmNewPassword = $_POST["confirm_new_password"];

    $errorMessage = ""; // Default error message

    // Validate email format
    if (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email)) {
        $errorMessage = '<span style="color: red;">Invalid email format. Please enter a valid email address.</span>';
    }

    // Validate password format
    elseif (strlen($newPassword) < 8 || !preg_match("/[A-Z]/", $newPassword) || !preg_match("/\d/", $newPassword)) {
        $errorMessage = '<span style="color: red;">Password must have at least 8 characters and contain at least one uppercase letter and one number.</span>';
    } elseif ($newPassword !== $confirmNewPassword) {
        $errorMessage = '<span style="color: red;">New Password and Confirm New Password do not match.</span>';
    }

    // If there were no validation errors, proceed with password reset
    if ($errorMessage === "") {
        // Database connection (Replace with your database credentials)
        $host = "localhost";
        $db_user = "root";  // Replace with your database user
        $db_password = "";   // Replace with your database password
        $db_name = "users";  // Replace with your database name

        $conn = new mysqli($host, $db_user, $db_password, $db_name);

        if ($conn->connect_error) {
            $errorMessage = '<span style="color: red;">Connection failed: ' . $conn->connect_error . '</span>';
        } else {
            // Check if the email exists in the database
            $checkSql = "SELECT * FROM users WHERE usermail = '$email'";
            $result = $conn->query($checkSql);

            if ($result->num_rows > 0) {
                // Update the password for the user (without hashing)
                $updateSql = "UPDATE users SET password = '$newPassword' WHERE usermail = '$email'";
                if ($conn->query($updateSql) === TRUE) {
                    $errorMessage = '<span style="color:green;">Password reset successfully.</span>';
                } else {
                    $errorMessage = '<span style="color: red;">Error resetting password: ' . $conn->error . '</span>';
                }
            } else {
                // Email not found in the database
                $errorMessage = '<span style="color: red;">Username does not exist.</span>';
            }

            $conn->close();
        }
    }

}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8" />
    <title>Forgot Password | CodeLab</title>
    <link rel="stylesheet" href="style.css" />
  </head>
  <body>
    <div class="wrapper">
      <div class="title">Forgot Password</div>
      <form
        action="forgot_password.php"
        method="post"
        onsubmit="return validateForm()"
      >
        <div class="field">
          <input type="text" name="email" id="email-input" required />
          <label>Email Address</label>
        </div>
        <div class="field">
          <input
            type="password"
            name="new_password"
            id="new-password-input"
            required
          />
          <label>New Password</label>
        </div>
        <div class="field">
          <input
            type="password"
            name="confirm_new_password"
            id="confirm-new-password-input"
            required
          />
          <label>Confirm New Password</label>
        </div>
        <div class="field">
          <input type="submit" value="Reset Password" />
        </div>
        <div class="login-link">
          Remember your password? <a href="index.html">Login</a>
        </div>
      </form>
      <div id="output">
                <?php echo $errorMessage; ?>
	</div>
    </div>
  </body>
</html>
