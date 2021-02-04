<?php

require_once 'common.php';
checkForAuthentication();
$connection = getDbConnection();

if (isset($_GET['logout'])) {
	unset($_SESSION['username']);
	header('Location: login.php');
	die();
}

if (isset($_POST['deleteItem'])) {
	$productToBeDeleted = $_POST['deleteItem'];
	$stm = $connection->prepare('SELECT image FROM products WHERE id= ?');
	$stm->execute([$productToBeDeleted]);
	$product = $stm->fetch(PDO::FETCH_OBJ);

	$sql = $connection->prepare('DELETE FROM products WHERE id= ?');
	$sql->execute([$productToBeDeleted]);

	if (isset($product->image) && file_exists($product->image)) {
		unlink($product->image);
	}
	header('Location: products.php');
	die();
}

$stm = $connection->prepare("SELECT * FROM products");
$stm->execute();
$products = $stm->fetchAll(PDO::FETCH_OBJ);
$connection = null;

?>

<html lang="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= translate('Products') ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h3><?= translate('All products') ?></h3>
<table>
	<?php foreach ($products as $product) : ?>
        <tr>
            <td>
                <img src="<?= $product->image ?>" alt="image">
            </td>
            <td>
				<?= $product->title ?><br>
				<?= $product->description ?><br>
				<?= $product->price . ' ' . translate('eur') ?><br>
            </td>
            <td><a href="product.php?editProduct=<?= $product->id ?>"><?= translate('Edit') ?></a></td>
            <td>
                <form action="products.php" method="post">
                    <input type="hidden" name="deleteItem" value="<?= $product->id ?>">
                    <input type="submit" value="Delete">
                </form>
            </td>
        </tr>
	<?php
	endforeach;
	?>
    <tr>
        <td id="bottom" colspan="4">
            <a href="product.php"><?= translate('Add') ?></a>
            <a href="products.php?logout=logout"><?= translate('Logout') ?></a>
        </td>
    </tr>
</table>
</body>
</html>
