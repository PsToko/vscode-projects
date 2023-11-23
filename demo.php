<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <title>Login to our system</title>

<style>

/* login.css */

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f2f2f2;
    margin: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.container {
    background-color: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    width: 300px;
    text-align: center;
}

h1 {
    color: #4c4eaf;
}

label {
    display: block;
    margin: 15px 0 5px;
    font-weight: bold;
}

input {
    width: 100%;
    padding: 10px;
    margin: 5px 0 20px;
    box-sizing: border-box;
    border: 1px solid #ccc;
    border-radius: 5px;
}

button {
    background-color: #4c4eaf;
    color: white;
    padding: 12px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

button:hover {
    background-color: #2f3188;
}

.psw {
    margin-top: 15px;
}

.psw a {
    text-decoration: none;
    color: #007bff;
    font-weight: bold;
}

.psw a:hover {
    text-decoration: underline;
    color: #0056b3;
}

</style>


</head>

<body>

    <form action="login.php" method="post">
        <div class="container">
            <h1>Login form</h1>

            <label for="uname"><b>Username</b></label>
            <input type="text" placeholder="Enter Username" name="uname" required>

            <label for="psw"><b>Password</b></label>
            <input type="password" placeholder="Enter Password" name="psw" required>

            <?php
              if(isset($_GET['error'])==true){
                 echo '<font colour="#FF0000"><p align="center">Invalid username/password</p></font>';
                }
            ?>

            <button type="submit">Login</button>
            
        </div>
        

        <div class="signup"><a href="#">You don't have an account?</a></div>
    </form>




</body>


</html>

