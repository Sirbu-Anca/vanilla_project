<?php

require_once 'common.php';
checkForAuthentication();
$connection = getDbConnection();
$orderId = isset($_GET['orderId']) ? $_GET['orderId'] : 0;

$sql = $connection->prepare(
    'SELECT
                   p.id, 
                   p.title, 
                   p.description, 
                   p.image, 
                   o.name, 
                   o.address,
                   o.comments,
                   o.creation_date,
                   o_p.product_price
        FROM order_products o_p 
            INNER JOIN products p ON o_p.product_id = p.id 
            INNER JOIN orders o ON o_p.order_id = o.id
        WHERE o.id = ?');
$sql->execute([$orderId]);
$orders = $sql->fetchAll();

if (!count($orders)) {
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
                <?= isset($orders[0]['creation_date']) ? $orders[0]['creation_date'] : '' ?>
            </p>
        </td>
    </tr>
    <tr>
        <td>
            <p>
                <?= translate('Name: ') ?>
                <?= isset($orders[0]['name']) ? $orders[0]['name'] : '' ?>
            </p>
        </td>
    </tr>
    <tr>
        <td>
            <p>
                <?= translate('Address: ') ?>
                <?= isset($orders[0]['address']) ? $orders[0]['address'] : '' ?>
            </p>
        </td>
    </tr>
    <tr>
        <td>
            <p>
                <?= translate('Comments: ') ?>
                <?= isset($orders[0]['comments']) ? $orders[0]['comments'] : '' ?>
            </p>
        </td>
    </tr>
    <?php for ($i = 0; $i < count($orders); $i++) : ?>
        <tr>
            <td>
                <img src="<?= $orders[$i]['image'] ?>" alt="image">
            </td>
            <td>
                <?= $orders[$i]['title'] ?><br>
                <?= $orders[$i]['description'] ?><br>
                <?= $orders[$i]['product_price'] ?><?= translate(' eur') ?> <br><br>
            </td>
        </tr>
    <?php endfor; ?>
</table>
<a href="orders.php"><?= translate('Go to order list') ?></a>
</body>
</html>
