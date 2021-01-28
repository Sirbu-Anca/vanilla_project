<?php
session_start();
require_once('common.php');
$connection = getDbConnection();
$inputErrors = [];
$inputData = [
    'name'            => '',
    'contactDetails'  => '',
    'comments'        => '',
];

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

if (!empty($cart_products)) {
    if (isset($_POST['name']) && isset($_POST['contactDetails'])) {
        $inputData['name'] = strip_tags($_POST['name']);
        if (strlen($inputData['name']) < 3) {
            $inputErrors['nameError'] = 'The name should have more then 2 letters.';
        }
        $inputData['contactDetails'] = strip_tags($_POST['contactDetails']);
        if (!filter_var($inputData['contactDetails'], FILTER_VALIDATE_EMAIL)) {
            $inputErrors['contactDetailsError'] = 'Invalid email address!';
        }
        $inputData['comments'] = strip_tags($_POST['comments']);
    }
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
        <th>Image</th>
        <th>Title</th>
        <th>Description</th>
        <th>Price</th>
        <th>Action</th>
    </tr>
    <?php foreach ($cart_products as $product) { ?>
        <tr>
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
    <input type="text" name="name" placeholder="Name" value="<?php echo $inputData['name'] ?>">
    <span><?php echo isset($inputErrors['nameError']) ? $inputErrors['nameError'] : '';?></span>
    <br><br>
    <input type="text" name="contactDetails" placeholder="Contact details" value="<?php echo $inputData['contactDetails']?>">
    <span><?php echo isset($inputErrors['contactDetailsError']) ? $inputErrors['contactDetailsError'] : '';?></span>
    <br><br>
    <textarea name="comments" id="comm" cols="22" rows="3" placeholder="Comments"> </textarea><br><br>
    <a href="index.php">Go to index</a><input type="submit" name="button" value="Checkout">
</form>
</body>
</html>

