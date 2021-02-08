<?php

require_once 'common.php';

$inputData = [];
$inputErrors = [];

if (isset($_POST['submit'])) {
	if ($_POST['username']) {
		$inputData['username'] = strip_tags($_POST['username']);
	} else {
		$inputErrors['usernameError'] = translate('Enter a username');
	}
	if ($_POST['password']) {
		$inputData['password'] = strip_tags($_POST['password']);
	} else {
		$inputErrors['passwordError'] = translate('Enter a password');
	}
	if (!count($inputErrors)) {
		if ($inputData['username'] === ADMIN_CREDENTIALS['username'] && $inputData['password'] === ADMIN_CREDENTIALS['password']) {
			$_SESSION['username'] = $inputData['username'];
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
           value="<?= isset($inputData['username']) ? $inputData['username'] : ''; ?>">
    <span class="error">
        <?= isset($inputErrors['usernameError']) ? $inputErrors['usernameError'] : ''; ?>
    </span>
    <br><br>
    <input type="password" name="password" placeholder="<?= translate('Password') ?>"
           value="<?= isset($inputData['password']) ? $inputData['password'] : ""; ?>">
    <span class="error">
        <?= isset($inputErrors['passwordError']) ? $inputErrors['passwordError'] : ''; ?>
    </span>
    <br><br>
    <button type="submit" name="submit"><?= translate('Login') ?></button>
    <span class="error">
        <?= isset($inputErrors['failedMessage']) ? $inputErrors['failedMessage'] : ''; ?>
    </span>
</form>

</body>
</html>
