<?php
require_once 'common.php';
session_start();
checkForAuthentication();
$connection = getDbConnection();

if (isset($_GET['logout'])) {
    unset($_SESSION['username']);
    header('Location: login.php');
    die();
}

if (isset($_POST['deleteItem'])) {
    $productToBeDeleted = $_POST['deleteItem'];
    $sql = $connection->prepare('DELETE FROM products where id=:id');
    $sql->bindParam(':id', $productToBeDeleted, PDO::PARAM_INT);
    $sql->execute();
    header('Location: products.php');
    die();
}

$stm = $connection->prepare("SELECT * FROM products");
$stm->execute();
$products = $stm->fetchAll(PDO::FETCH_OBJ);
$connection = null;

?>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo translate('Products') ?></title>
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
    <?php foreach ($products as $product) { ?>
        <tr>
            <td>
                <img src="" alt="image">
            </td>
            <td>
                <?php echo $product->title ?><br>
                <?php echo $product->description ?><br>
                <?php echo $product->price . ' ' . translate('eur') ?><br>
            </td>
            <td><a href="">Edit</a></td>
            <td>
                <form action="products.php" method="post"
                ">
                <input type="hidden" name="deleteItem" value="<?php echo $product->id; ?>">
                <input type="submit" value="Delete">
                </form>
            </td>
        </tr>
        <?php
    }
    ?>
    <tr>
        <td id="bottom" colspan="4">
            <a href="product.php"><?php echo translate('Add') ?></a>
            <a href="products.php?logout=logout"><?php echo translate('Logout') ?></a>
        </td>
    </tr>
</table>
</body>
</html>
