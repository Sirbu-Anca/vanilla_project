<?php

require_once('common.php');
checkForAuthentication();
$connection = getDbConnection();

$orderId = $_GET['orderId'] ?? null;
if (!$orderId || !is_numeric($orderId)) {
    header('Location: orders.php');
    die();
}

$sql = $connection->prepare(
    'SELECT p.id,
                  p.title,
                  p.description,
                  p.image,
                  op.product_price
           FROM order_products op
                 INNER JOIN products p ON op.product_id = p.id
                 INNER JOIN orders o ON op.order_id = o.id
           WHERE o.id = ?');
$sql->execute([$orderId]);
$products = $sql->fetchAll(PDO::FETCH_OBJ);

$sql = $connection->prepare(
    'SELECT o.name,
                  o.address,
                  o.comments,
                  o.creation_date
            FROM order_products op 
                  INNER JOIN orders o ON op.order_id = o.id
            WHERE o.id = ?');
$sql->execute([$orderId]);
$order = $sql->fetch(PDO::FETCH_OBJ);

if (!count($products)) {
    die();
}
?>
<html lang="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="style.css">
    <title><?= translate('Order details') ?></title>
</head>
<body>
<h4><?= translate('Order details') ?></h4>
<table>
    <tr>
        <td>
            <p>
                <?= translate('Date: ') ?>
                <?= isset($order->creation_date) ? $order->creation_date : '' ?>
            </p>
        </td>
    </tr>
    <tr>
        <td>
            <p>
                <?= translate('Name: ') ?>
                <?= isset($order->name) ? $order->name : '' ?>
            </p>
        </td>
    </tr>
    <tr>
        <td>
            <p>
                <?= translate('Address: ') ?>
                <?= isset($order->address) ? $order->address : '' ?>
            </p>
        </td>
    </tr>
    <tr>
        <td>
            <p>
                <?= translate('Comments: ') ?>
                <?= isset($order->comments) ? $order->comments : '' ?>
            </p>
        </td>
    </tr>
    <?php foreach ($products as $product) : ?>
        <tr>
            <td>
                <img src="<?= $product->image ?>" alt="image">
            </td>
            <td>
                <?= $product->title ?><br>
                <?= $product->description ?><br>
                <?= $product->product_price ?><?= translate(' eur') ?> <br><br>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
<a href="orders.php"><?= translate('Go to order list') ?></a>
</body>
</html>
