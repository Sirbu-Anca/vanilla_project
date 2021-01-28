<?php
session_start();
require_once('common.php');
$connection = getDbConnection();

if (isset($_GET['add_id'])) {
    $_SESSION['cart'][$_GET['add_id']] = $_GET['add_id'];
    header('location:index.php');
    die();
}

if (isset($_GET['remove_id'])) {
    unset($_SESSION['cart'][$_GET['remove_id']]);
}

$cart = array_values($_SESSION['cart']);
if (empty($cart)) {
    echo "You don't have any product in your cart!<br>";
    echo '<a href="index.php">Go to index</a>';
    die();
}
$ids_arr = str_repeat('?,', count($cart) - 1) . '?';

$stm = $connection->prepare("SELECT * FROM products WHERE id IN (" . $ids_arr . ")");
$stm->execute($cart);
$cart_products = $stm->fetchAll(PDO::FETCH_OBJ);
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
        <th>id</th>
        <th>Image</th>
        <th>Title</th>
        <th>Description</th>
        <th>Price</th>
        <th>Action</th>
    </tr>
    <?php foreach ($cart_products as $product) { ?>
        <tr>
            <td><?php echo $product->id ?></td>
            <td><img src="" alt="image"></td>
            <td><?php echo $product->title ?></td>
            <td><?php echo $product->description ?></td>
            <td><?php echo $product->price ?></td>
            <td><a href="cart.php?remove_id=<?php echo $product->id ?>">Remove</a></td>
        </tr>
        <?php
    }
    ?>
</table>
<br>
<form action="" method="post">
    <input type="text" name="name" placeholder="Name"><br><br>
    <textarea name="contact_details" id="" cols="22" rows="2" placeholder="Contact details"></textarea><br><br>
    <textarea name="comments" id="comm" cols="22" rows="3" placeholder="Comments"></textarea><br><br>
    <a href="index.php">Go to index</a><input type="button" name="button" value="Checkout">
</form>
</body>
</html>

