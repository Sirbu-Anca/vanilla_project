<?php
session_start();
require_once 'common.php';
$connection = getDbConnection();
if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
    $cart = array_values($_SESSION['cart']);
    $ids_arr = str_repeat('?,', count($cart) - 1) . '?';

    $stm = $connection->prepare("SELECT * FROM products WHERE id  NOT IN (" . $ids_arr . ")");
    $stm->execute($cart);
    $products = $stm->fetchAll(PDO::FETCH_OBJ);
}
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
            <th>image</th>
            <th>Title</th>
            <th>Description</th>
            <th>Price</th>
            <th>Action</th>
        </tr>
        <?php foreach ($products as $product) { ?>
            <tr>
                <td><?php echo $product->id ?></td>
                <td><img src="" alt="image"></td>
                <td><?php echo $product->title ?></td>
                <td><?php echo $product->description ?></td>
                <td><?php echo $product->price ?></td>
                <td><a href="cart.php?add_id=<?php echo $product->id ?>">Add</a></td>
            </tr>
            <?php
        }
        ?>
    </table>
    <a href="cart.php">Go to cart</a>
    </body>
    </html>



