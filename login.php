<?php

require_once 'common.php';

if (isAuthenticated()) {
    header('Location: products.php');
    die();
}

$inputData['username'] = strip_tags($_POST['username'] ?? '');
$inputData['password'] = strip_tags($_POST['password'] ?? '');
$inputErrors = [];

if (isset($_POST['submit'])) {
    if (!$inputData['username']) {
        $inputErrors['usernameError'] = translate('Enter a username');
    }

    if (!$inputData['password']) {
        $inputErrors['passwordError'] = translate('Enter a password');
    }

    if (!count($inputErrors)) {
        if ($inputData['username'] === ADMIN_CREDENTIALS['username'] && $inputData['password'] === ADMIN_CREDENTIALS['password']) {
            $_SESSION['isAuthenticated'] = true;
            header('Location: products.php');
            die();
        }
        $inputErrors['failedMessage'] = translate('Wrong username and password');
    }
}

?>

<html lang="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= translate('Login') ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<form action="login.php" method="post">
    <input type="text" name="username" placeholder="<?= translate('Username') ?>"
           value="<?= $inputData['username'] ?? ''; ?>">
    <span class="error">
        <?= $inputErrors['usernameError'] ?? ''; ?>
    </span>
    <br><br>
    <input type="password" name="password" placeholder="<?= translate('Password') ?>"
           value="<?= $inputData['password'] ?? ''; ?>">
    <span class="error">
        <?= $inputErrors['passwordError'] ?? ''; ?>
    </span>
    <br><br>
    <button type="submit" name="submit"><?= translate('Login') ?></button>
    <span class="error">
        <?= $inputErrors['failedMessage'] ?? ''; ?>
    </span>
</form>

</body>
</html>
