<?php

require_once 'common.php';

if (isset($_SESSION['username']) && $_SESSION['username'] == ADMIN_CREDENTIALS['USERNAME']) {
    header('Location: products.php');
    die();
}
$inputData = [];
$inputErrors = [];

if (isset($_POST['submit'])) {
    if ($_POST['username']) {
        $inputData['username'] = strip_tags($_POST['username']);
    } else {
        $inputErrors['usernameError'] = 'Enter a username';
    }
    if ($_POST['password']) {
        $inputData['password'] = strip_tags($_POST['password']);
    } else {
        $inputErrors['passwordError'] = 'Enter a password';
    }
    if (!count($inputErrors)) {
        if ($inputData['username'] == ADMIN_CREDENTIALS['USERNAME'] && $inputData['password'] == ADMIN_CREDENTIALS['PASSWORD']) {
            $_SESSION['username'] = $inputData['username'];
            header('Location: products.php');
            die();
        }
        $inputErrors['failedMessage'] = 'Wrong username and password';
    }
}

?>

<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login page</title>
</head>
<body>
<form action="login.php" method="post">
    <input type="text" name="username" placeholder="Username"
           value="<?= isset($inputData['username']) ? $inputData['username'] : ''; ?>">
    <span><?= isset($inputErrors['usernameError']) ? $inputErrors['usernameError'] : ''; ?></span>
    <br><br>
    <input type="password" name="password" placeholder="Password"
           value="<?= isset($inputData['password']) ? $inputData['password'] : ""; ?>">
    <span><?= isset($inputErrors['passwordError']) ? $inputErrors['passwordError'] : ''; ?></span>
    <br><br>
    <input type="submit" name="submit" value="Login">
    <span><?= isset($inputErrors['failedMessage']) ? $inputErrors['failedMessage'] : ''; ?></span>
</form>

</body>
</html>
