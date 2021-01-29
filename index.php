<?php

require_once 'common.php';
$connection = getDbConnection();
$cart = null;
$sql = 'SELECT * FROM products';

if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
    $cart = array_values($_SESSION['cart']);
    $ids_arr = str_repeat('?,', count($cart) - 1) . '?';
    $sql = $sql . ' WHERE id NOT IN (' . $ids_arr . ')';
    $stm = $connection->prepare($sql);
    $stm->execute($cart);
    $products = $stm->fetchAll(PDO::FETCH_OBJ);
}
$stm = $connection->prepare($sql);
$stm->execute($cart);
$products = $stm->fetchAll(PDO::FETCH_OBJ);
?>

<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= translate('Products') ?></title>
    <style>
        table {
            border: 1px solid black;
        }

        td {
            text-align: left;
        }

        #bottom {
            text-align: center;
        }
    </style>
</head>
<body>

<table>
    <?php foreach ($products as $product) : ?>
        <tr>
            <td>
                <img src="" alt="image">
            </td>
            <td>
                <?= $product->title ?><br>
                <?= $product->description ?><br>
                <?= $product->price . ' ' . translate('eur') ?> <br><br>
            </td>
            <td>
                <a href="cart.php?add_id=<?= $product->id ?>"><?= translate('Add') ?></a>
            </td>
        </tr>
    <?php
    endforeach;
    ?>
    <tr>
        <td id="bottom" colspan="3">
            <a href="cart.php"><?= translate('Go to cart') ?></a>
        </td>
    </tr>
</table>
</body>
</html>



