<?php

require_once 'common.php';
checkForAuthentication();
$connection = getDbConnection();
if (isset($_POST['logout'])) {
    unset($_SESSION['isAuthenticated']);
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
                <?= $product->price ?> <?= translate(' eur') ?><br>
            </td>
            <td><a href="product.php?editProductId=<?= $product->id ?>"><?= translate('Edit') ?></a></td>
            <td>
                <form action="products.php" method="post">
                    <input type="hidden" name="deleteItem" value="<?= $product->id ?>">
                    <button type="submit" name="delete"><?= translate('Delete') ?></button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    <tr>
        <td class="bottom" colspan="2">
            <a href="product.php"><?= translate('Add') ?></a>
        </td>
        <td colspan="2">
            <form action="products.php" method="post">
                <button type="submit" name="logout"><?= translate('Logout') ?> </button>
            </form>
        </td>
    </tr>
</table>
</body>
</html>
