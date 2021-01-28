<?php
require_once 'common.php';
session_start();
if (!isset($_SESSION['username'])) {
    header('location:login.php');
    die();
}
$connection = getDbConnection();
$stm = $connection->prepare("SELECT * FROM products");
$stm->execute();
$products = $stm->fetchAll(PDO::FETCH_OBJ);
$connection = null;
unset($_SESSION['username']);
?>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Products</title>
</head>
<body>
<table border="1px solid black">
    <tr>
        <th>Image</th>
        <th>Title</th>
        <th>Description</th>
        <th>Price</th>
        <th colspan="2">Action</th>
    </tr>
    <?php foreach ($products as $product) { ?>
        <tr>
            <td><?php echo $product->image ?></td>
            <td><?php echo $product->title ?></td>
            <td><?php echo $product->description ?></td>
            <td><?php echo $product->price ?></td>
            <td><a href="">Edit</a></td>
            <td><a href="">Delete</a></td>
        </tr>
        <?php
    }
    ?>
</table>
<a href="product.php">Add</a> &nbsp &nbsp <a href="products.php">Logout</a>
</body>
</html>