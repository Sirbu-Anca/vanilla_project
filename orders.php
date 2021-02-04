<?php

require_once 'common.php';
checkForAuthentication();
$connection = getDbConnection();

$sql = $connection->prepare(
	'SELECT o_d.id, SUM(o_p.product_price) totalAmount
        FROM order_details o_d
            INNER JOIN order_products o_p ON o_d.id = o_p.id_order
            INNER JOIN products p ON o_p.id_product = p.id
        GROUP BY o_d.id');
$sql->execute();
$orders = $sql->fetchAll(PDO::FETCH_OBJ);

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
    <title><?= translate('Orders list') ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h4><?= translate('Order list') ?></h4>
<table>
    <tr>
        <th><?= translate('Order id') ?></th>
        <th><?= translate('Total amount') ?></th>
        <th><?= translate('Action') ?></th>
    </tr>
	<?php foreach ($orders as $order) : ?>

        <tr>
            <td>
				<?= $order->id ?>
            </td>
            <td>
				<?= $order->totalAmount ?>
				<?= translate("eur") ?>
            </td>

            <td>
                <a href="order.php?orderId=<?= $order->id ?>"> <?= translate('Show order details') ?> </a>
            </td>
        </tr>
	<?php endforeach; ?>
</table>
</body>
</html>
