<?php
$outputMessage = ""; // Variable to store the output message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Validate if fields are empty
    if (empty($username) || empty($password)) {
        $outputMessage = '<span style="color: red;">Please fill in both fields</span>';
    } else {
        $host = "localhost";
        $db_user = "root";
        $db_password = "";
        $db_name = "users"; // Replace with your database name

        $conn = new mysqli($host, $db_user, $db_password, $db_name);

        if ($conn->connect_error) {
            $outputMessage = '<span style="color: red;">Database connection failed.</span>';
        } else {
            $sql = "SELECT usermail, password FROM users WHERE usermail = '$username'";
            $result = $conn->query($sql);

            if ($result === false) {
                $outputMessage = '<span style="color: red;">An error occurred while logging in.</span>';
            } else {
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $stored_username = $row["usermail"];
                    $stored_password = $row["password"];

                    if ($password === $stored_password) {
                        // Successful login
                        // Redirect to the Flask file
                        header("Location: http://127.0.0.1:5000");
                        exit();
                    } else {
                        $outputMessage = '<span style="color: red;">Incorrect password</span>';
                    }
                } else {
                    $outputMessage = '<span style="color: red;">User does not exist.</span>';
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
    <title>Login Form Design | CodeLab</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <div class="wrapper">
        <div class="title">Login Form</div>
        <form action="login.php" method="post">
            <div class="field">
                <input type="text" name="username" id="email-input" required />
                <label>Email Address</label>
            </div>
            <div class="field">
                <input type="password" name="password" id="password-input" required />
                <label>Password</label>
            </div>
            <div class="field">
                <input type="submit" value="Login" />
            </div>
            <div class="signup-link">
                Not a member? <a href="create_account.html">Signup now</a>
                <div class="forgot-password-link">
                    Reset password?<a href="forgot_password.html"> Forgot password</a>
                </div>
                <div id="output">
                <?php echo $outputMessage; ?>
            </div>
            </div>
        </form>
    </div>
</body>
</html>
