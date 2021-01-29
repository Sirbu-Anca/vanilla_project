<?php
session_start();
require_once('common.php');
$connection = getDbConnection();
$inputErrors = [];
$inputData = [
    'name' => '',
    'contactDetails' => '',
    'comments' => '',
];

if (isset($_GET['add_id'])) {
    $_SESSION['cart'][$_GET['add_id']] = $_GET['add_id'];
    header('Location: index.php');
    die();
}

if (isset($_GET['remove_id'])) {
    unset($_SESSION['cart'][$_GET['remove_id']]);
}


if (!isset($_SESSION['cart'])) {
    echo 'Your cart is empty!<br>';
    echo '<a href="index.php">' . translate('Go to index') . '</a>';
    die();
}

$cart_products = [];

if (count($_SESSION['cart']) > 0) {
    $cart = array_values($_SESSION['cart']);
    $ids_arr = str_repeat('?,', count($cart) - 1) . '?';
    $stm = $connection->prepare("SELECT * FROM products WHERE id IN (" . $ids_arr . ")");
    $stm->execute($cart);
    $cart_products = $stm->fetchAll(PDO::FETCH_OBJ);
}

$to = "test@gmail.com";
$subject = "HTML email";

if (count($cart_products) > 0) {
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
        if (!count($inputErrors)) {
            ob_start();
            include 'mail.cart.php';
            $emailPage = ob_get_clean();
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: " . SHOP_MANAGER_EMAIL . "\r\n";
            mail($to, $subject, $emailPage, $headers);
            unset($_SESSION['cart']);
            header('Location: index.php');
        }
    }
}
?>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo translate('Cart') ?></title>
    <style>
        table {
            border: 1px solid #000000;
        }

        td {
            text-align: left;
        }
    </style>
</head>
<body>
<table>
    <?php foreach ($cart_products as $product) { ?>
        <tr>
            <td>
                <img src="" alt="image">
            </td>
            <td>
                <?php echo $product->title ?><br>
                <?php echo $product->description ?><br>
                <?php echo $product->price . ' ' . translate('eur') ?> <br><br>
            </td>
            <td>
                <a href="cart.php?remove_id=<?php echo $product->id ?>"><?php echo translate('Remove') ?></a>
            </td>
        </tr>
        <?php
    }
    ?>
    <br>
    <tr>
        <td colspan="3">
            <form action="" method="post">
                <input type="text" name="name" placeholder="Name" value="<?php echo $inputData['name'] ?>">
                <span>
                <?php echo isset($inputErrors['nameError']) ? $inputErrors['nameError'] : ''; ?>
            </span>
                <br><br>
                <input type="text" name="contactDetails" placeholder="<?php echo translate('Contact details') ?>"
                       value="<?php echo $inputData['contactDetails'] ?>">
                <span>
                <?php echo isset($inputErrors['contactDetailsError']) ? $inputErrors['contactDetailsError'] : ''; ?>
            </span>
                <br><br>
                <textarea name="comments" id="comm" cols="22" rows="3"
                          placeholder="<?php echo translate('Comments') ?>">
            </textarea><br><br>
                <a href="index.php"><?php echo translate('Go to index') ?></a>
                <input type="submit" name="button" value="Checkout">
            </form>
        </td>
    </tr>
</table>
</body>
</html>

