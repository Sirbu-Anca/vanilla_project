<?php

require_once 'common.php';
checkForAuthentication();
$connection = getDbConnection();

if (isset($_GET['editProductId'])) {
    $editProductId = $_GET['editProductId'];
} else {
    $editProductId = null;
}

$product = new stdClass();
if (!empty($editProductId)) {
    if (!is_numeric($editProductId)) {
        header('Location: products.php');
        die();
    }
    $stm = $connection->prepare('SELECT * FROM products WHERE id= ?');
    $stm->execute([$editProductId]);
    $product = $stm->fetch(PDO::FETCH_OBJ);
    if (!$product) {
        header('Location: products.php');
        die();
    }
}

$inputData = [
    'title' => isset($product->title) ? $product->title : '',
    'description' => isset($product->description) ? $product->description : '',
    'price' => isset($product->price) ? $product->price : '',
    'imageName' => isset($product->image) ? imagePath($product->image) : '',
];

unset($_SESSION['oldInputs']);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    unset($_SESSION['inputErrors']);
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
        $_SESSION['inputErrors']['priceError'] = translate('Please enter a number for product price.');
    }

    if (isset($_FILES['image']) && $_FILES['image']['tmp_name']) {
        $inputData['imageLocation'] = $_FILES['image']['tmp_name'];
        $inputData['imageName'] = $_FILES['image']['name'];
    } else {
        if (!isset($product->image)) {
            $_SESSION['inputErrors']['imageNameError'] = translate('Please choose an image.');
        }
    }

    $pathImage = 'uploads/' . time() . $inputData['imageName'];
    if (empty($_SESSION['inputErrors'])) {
        if (!$editProductId) {
            $sql = $connection->prepare(
                'INSERT INTO products (title, description, price, image) VALUES( ?, ?, ?, ?)');
            $sql->execute([$_POST['title'], $_POST['description'], $_POST['price'], $pathImage]);
            move_uploaded_file($inputData['imageLocation'], $pathImage);
            header('Location: products.php');
            die();
        } else {
            if (isset($product->image) && $inputData['imageName'] === imagePath($product->image)) {
                $sql = 'UPDATE products SET title= ?, description= ?, price= ? WHERE id= ?';
                $parameters = [
                    $_POST['title'],
                    $_POST['description'],
                    $_POST['price'],
                    $editProductId,
                ];
            } else {
                $sql = 'UPDATE products SET title= ?, description= ?, price= ?, image= ? WHERE id= ?';
                $parameters = [
                    $_POST['title'],
                    $_POST['description'],
                    $_POST['price'],
                    $pathImage,
                    $editProductId,
                ];
            }
            $updateProduct = $connection->prepare($sql);
            $updateProduct->execute($parameters);

            if (!(isset($product->image) && $inputData['imageName'] === imagePath($product->image))) {
                move_uploaded_file($inputData['imageLocation'], $pathImage);
                if (isset($product->image) && file_exists($product->image)) {
                    unlink($product->image);
                }
            }
            header('Location: products.php');
            die();
        }
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
<form action="" method="post" enctype="multipart/form-data">
    <input type="text" name="title" placeholder="<?= translate('Title') ?>"
           value="<?= isset($_SESSION['oldInputs']['title']) ? $_SESSION['oldInputs']['title'] : $inputData['title'] ?>">
    <span class="error">
        <?= isset($_SESSION['inputErrors']['titleError']) ? $_SESSION['inputErrors']['titleError'] : ''; ?>
    </span>
    <br><br>
    <input type="text" name="description" placeholder="<?= translate('Description') ?>"
           value="<?= isset($_SESSION['oldInputs']['description']) ? $_SESSION['oldInputs']['description'] : $inputData['description'] ?>">
    <span class="error">
        <?= isset($_SESSION['inputErrors']['descriptionError']) ? $_SESSION['inputErrors']['descriptionError'] : ''; ?>
    </span>
    <br><br>
    <input type="number" name="price" placeholder="<?= translate('Price') ?> " min="0" step="any"
           value="<?= isset($_SESSION['oldInputs']['price']) ? $_SESSION['oldInputs']['price'] : $inputData['price'] ?>">
    <span class="error">
        <?= isset($_SESSION['inputErrors']['priceError']) ? $_SESSION['inputErrors']['priceError'] : '' ?>
    </span>
    <br><br>
    <input type="file" name="image" placeholder="<?= translate('Image') ?>">
    <span class="error">
        <?= isset($_SESSION['inputErrors']['imageNameError']) ? $_SESSION['inputErrors']['imageNameError'] : '' ?>
    </span>
    <br><br>
    <a href="products.php"><?= translate('Products') ?></a>
    &nbsp;
    <input type="submit" name="save" value="<?= translate('Save') ?>">
</form>

</body>
</html>


