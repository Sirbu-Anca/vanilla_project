<?php

require_once 'common.php';
checkForAuthentication();
$connection = getDbConnection();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $editProductId = $_GET['editProductId'] ?? null;
} else {
    $editProductId = $_POST['editProductId'] ?? null;
}

$product = [
    'id' => '',
    'title' => '',
    'description' => '',
    'price' => '',
    'image' => '',
];

if (!empty($editProductId)) {
    if (!is_numeric($editProductId)) {
        header('Location: products.php');
        die();
    }
    $stm = $connection->prepare('SELECT * FROM products WHERE id= ?');
    $stm->execute([$editProductId]);
    $product = $stm->fetch();
    if (!$product) {
        header('Location: products.php');
        die();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    unset($_SESSION['inputErrors']);
    unset($_SESSION['oldInputs']);
    $_SESSION['oldInputs'] = $_POST;

    if (!isset($_POST['title']) || !$_POST['title']) {
        $_SESSION['inputErrors']['titleError'] = translate('Please enter a product title.');
    }
    if (!isset($_POST['description']) || !$_POST['description']) {
        $_SESSION['inputErrors']['descriptionError'] = translate('Please enter a product description.');
    }

    if (isset($_POST['price']) && $_POST['price']) {
        if (!is_numeric($_POST['price'])) {
            $_SESSION['inputErrors']['priceError'] = translate('Please enter a natural number for product price.');
        }
    } else {
        $_SESSION['inputErrors']['priceError'] = translate('Please enter a natural number for product price.');
    }

    if (!$editProductId && (!isset($_FILES['image']['tmp_name']) || !$_FILES['image']['tmp_name'])) {
        $_SESSION['inputErrors']['imageNameError'] = translate('Please choose an image.');
    }

    if (isset($_SESSION['inputErrors']) && count($_SESSION['inputErrors'])) {
        $location = 'Location: product.php';
        if ($editProductId) {
            $location .= '?editProductId=' . $editProductId;
        }
        header($location);
        die();
    }

    $pathImage = 'uploads/' . time() . $_FILES['image']['name'];
    if ($editProductId) {
        if (empty($_FILES['image']['name'])) {
            $sql = 'UPDATE products SET title= ?, description= ?, price= ? WHERE id= ?';
            $parameters = [
                $_POST['title'],
                $_POST['description'],
                $_POST['price'],
                $editProductId,
            ];
        } else {
            move_uploaded_file($_FILES['image']['tmp_name'], $pathImage);
            $sql = 'UPDATE products SET title= ?, description= ?, price= ?, image= ? WHERE id= ?';
            $parameters = [
                $_POST['title'],
                $_POST['description'],
                $_POST['price'],
                $pathImage,
                $editProductId,
            ];
            if (isset($product['image']) && file_exists($product['image'])) {
                unlink($product['image']);
            }
        }
        $updateProduct = $connection->prepare($sql);
        $updateProduct->execute($parameters);
        header('Location: products.php');
        die();

    } else {
        move_uploaded_file($_FILES['image']['tmp_name'], $pathImage);
        $sql = $connection->prepare(
            'INSERT INTO products (title, description, price, image) VALUES( ?, ?, ?, ?)');
        $sql->execute([$_POST['title'], $_POST['description'], $_POST['price'], $pathImage]);
        header('Location: products.php');
        die();
    }
}
?>

    <html lang="">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title><?= translate('Add product') ?></title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
    <h3><?= translate('Add your product details') ?></h3>
    <form action="product.php" method="post" enctype="multipart/form-data">
        <?php if ($editProductId) : ?>
            <input type="hidden" name="editProductId" value="<?= $product['id'] ?>">
        <?php endif; ?>
        <input type="text" name="title" placeholder="<?= translate('Title') ?>"
               value="<?= isset($oldInputs['title']) ? $oldInputs['title'] : $product['title'] ?>">
        <span class="error">
        <?= isset($inputErrors['titleError']) ? $inputErrors['titleError'] : ''; ?>
    </span>
        <br><br>
        <input type="text" name="description" placeholder="<?= translate('Description') ?>"
               value="<?= isset($oldInputs['description']) ? $oldInputs['description'] : $product['description'] ?>">
        <span class="error">
        <?= isset($inputErrors['descriptionError']) ? $inputErrors['descriptionError'] : ''; ?>
    </span>
        <br><br>
        <input type="number" name="price" placeholder="<?= translate('Price') ?> " min="0" step="any"
               value="<?= isset($oldInputs['price']) ? $oldInputs['price'] : $product['price'] ?>">
        <span class="error">
        <?= isset($inputErrors['priceError']) ? $inputErrors['priceError'] : '' ?>
    </span>
        <br><br>
        <input type="file" id="image" name="image" placeholder="<?= translate('Image') ?>">
        <span class="error">
        <?= isset($inputErrors['imageNameError']) ? $inputErrors['imageNameError'] : '' ?>
    </span>
        <br><br>
        <a href="products.php"><?= translate('Products') ?></a>
        &nbsp;
        <input type="submit" name="save" value="<?= translate('Save') ?>">
    </form>

    </body>
    </html>
<?php
unset($_SESSION['inputErrors']);
unset($_SESSION['oldInputs']);