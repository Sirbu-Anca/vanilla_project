<?php

require_once('common.php');
$connection = getDbConnection();

$inputData = [
    'name' => '',
    'contactDetails' => '',
    'comments' => '',
];
if (isset($_POST['add_id'])) {
    $_SESSION['cart'][$_POST['add_id']] = $_POST['add_id'];
    header('Location: index.php');
    die();
}

if (isset($_GET['remove_id'])) {
    unset($_SESSION['cart'][$_GET['remove_id']]);
}

if (!isset($_SESSION['cart'])) {
    header('Location: index.php');
    die();
}

$cartProducts = [];

if (count($_SESSION['cart']) > 0) {
    $cart = array_values($_SESSION['cart']);
    $ids_arr = str_repeat('?, ', count($cart) - 1) . '?';
    $stm = $connection->prepare('SELECT * FROM products WHERE id IN (' . $ids_arr . ')');
    $stm->execute($cart);
    $cartProducts = $stm->fetchAll(PDO::FETCH_OBJ);
}
$inputErrors = [];

if (isset($_POST['submit'])) {
    if (isset($_POST['name']) && $_POST['name']) {
        $inputData['name'] = strip_tags($_POST['name']);
    } else {
        if (strlen($inputData['name']) < 3) {
            $inputErrors['nameError'] = translate('The name should have more then 2 letters.');
        }
    }

    if (isset($_POST['contactDetails']) && $_POST['contactDetails']) {
        $inputData['contactDetails'] = strip_tags($_POST['contactDetails']);
    } else {
        if (!filter_var($inputData['contactDetails'], FILTER_VALIDATE_EMAIL)) {
            $inputErrors['contactDetailsError'] = translate('Invalid email address!');
        }
    }
    $inputData['comments'] = strip_tags($_POST['comments']);
    if (!count($inputErrors)) {
        ob_start();
        include 'mail.cart.php';
        $emailPage = ob_get_clean();
        $to = 'test@gmail.com';
        $subject = 'HTML email-order';
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type:text/html;charset=UTF-8' . "\r\n";
        $headers .= 'From: ' . SHOP_MANAGER_EMAIL . "\r\n";
        $creationDate = date('Y-m-d H:i:s');

        $sql = $connection->prepare('INSERT INTO orders (creation_date, name, address, comments) VALUES( ?, ?, ?, ?)');
        $sql->execute([$creationDate, $inputData['name'], $inputData['contactDetails'], $inputData['comments']]);
        $lastId = $connection->lastInsertId();

        foreach ($cartProducts as $cartProduct) {
            $sql = $connection->prepare('INSERT INTO order_products (order_id, product_id, product_price) VALUES (?, ?, ?)');
            $sql->execute([$lastId, $cartProduct->id, $cartProduct->price]);
        }
        mail($to, $subject, $emailPage, $headers);
        unset($_SESSION['cart']);
        header('Location: index.php');
    }
}

?>

<html lang="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= translate('Cart') ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h4><?= translate('Your cart') ?></h4>
<table>
    <?php foreach ($cartProducts as $product) : ?>
        <tr>
            <td>
                <img src="<?= $product->image ?>" alt="image">
            </td>
            <td>
                <?= $product->title ?><br>
                <?= $product->description ?><br>
                <?= $product->price ?> <?= translate(' eur') ?> <br><br>
            </td>
            <td>
                <a href="cart.php?remove_id=<?= $product->id ?>"><?= translate('Remove') ?></a>
            </td>
        </tr>
    <?php endforeach; ?>
    <br>
    <tr>
        <td colspan="3">
            <form action="" method="post">
                <input type="text" name="name" placeholder="Name" value="<?= $inputData['name'] ?>"><br>
                <span class="error">
                    <?= isset($inputErrors['nameError']) ? $inputErrors['nameError'] : ''; ?>
                </span>
                <br>
                <input type="text" name="contactDetails" placeholder="<?= translate('Contact details') ?>"
                       value="<?= $inputData['contactDetails'] ?>"><br>
                <span class="error">
                    <?= isset($inputErrors['contactDetailsError']) ? $inputErrors['contactDetailsError'] : ''; ?>
                </span>
                <br>
                <textarea name="comments" cols="22" rows="3" placeholder="<?= translate('Comments') ?>"></textarea>
                <br>
                <a href="index.php"><?= translate('Go to index') ?></a>
                <button type="submit" name="submit"> <?= translate('Checkout') ?></button>
            </form>
        </td>
    </tr>
</table>
</body>
</html>

