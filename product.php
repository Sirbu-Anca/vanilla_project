<?php

require_once 'common.php';
checkForAuthentication();
$connection = getDbConnection();
$inputErrors = [];
$editProduct = (!isset($_GET['editProduct']) ? 0 : $_GET['editProduct']);

$stm = $connection->prepare('SELECT * FROM products WHERE id= ?');
$stm->execute([$editProduct]);
$product = $stm->fetch(PDO::FETCH_OBJ);

$inputData = [
    'title' => (isset($product->title) && isset($editProduct)) ? $product->title : '',
    'description' => (isset($product->description) && isset($editProduct)) ? $product->description : '',
    'price' => (isset($product->price) && isset($editProduct)) ? $product->price : '',
    'imageName' => (isset($product->image) && isset($editProduct)) ? imagePath($product->image) : '',
];

if (isset($_POST['save'])) {
    if (isset($_POST['title']) && $_POST['title']) {
        $inputData['title'] = $_POST['title'];
    } else {
        $inputErrors['titleError'] = translate('Please enter a product title.');
    }

    if (isset($_POST['description']) && $_POST['description']) {
        $inputData['description'] = $_POST['description'];
    } else {
        $inputErrors['descriptionError'] = translate('Please enter a product description.');
    }

    if (isset($_POST['price'])) {
        $inputData['price'] = $_POST['price'];
        if (!is_numeric($_POST['price']) || !intval($_POST['price'])) {
            $inputErrors['priceError'] = translate('Please enter a natural number for product price.');
        }
    }

    if (isset($_FILES['image']) && $_FILES['image']['tmp_name']) {
        $inputData['imageLocation'] = $_FILES['image']['tmp_name'];
        $inputData['imageName'] = $_FILES['image']['name'];
    } else {
        if (!isset($product->image)) {
            $inputErrors['imageNameError'] = translate('Please choose an image.');
        }
    }

    $pathImage = 'uploads/' . time() . $inputData['imageName'];
    if (!count($inputErrors)) {
        if (!$editProduct) {
            $sql = $connection->prepare(
                    'INSERT INTO products (title, description, price, image) VALUES( ?, ?, ?, ?)');
            $sql->execute([$inputData['title'], $inputData['description'], $inputData['price'], $pathImage]);
            move_uploaded_file($inputData['imageLocation'], $pathImage);
            header('Location: products.php');
            die();
        } else {
            if (isset($product->image) && $inputData['imageName'] == imagePath($product->image)) {
                $sql = 'UPDATE products SET title= ?, description= ?, price= ? WHERE id= ?';
                $parameters = [
                    $inputData['title'],
                    $inputData['description'],
                    $inputData['price'],
                    $editProduct,
                ];
            } else {
                $sql = 'UPDATE products SET title= ?, description= ?, price= ?, image= ? WHERE id= ?';
                $parameters = [
                    $inputData['title'],
                    $inputData['description'],
                    $inputData['price'],
                    $pathImage,
                    $editProduct,
                ];
            }
            $updateProduct = $connection->prepare($sql);
            $updateProduct->execute($parameters);

            if (!(isset($product->image) && $inputData['imageName'] == imagePath($product->image))) {
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

<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?=translate('Add product')?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h3><?= translate('Add your product details')?></h3>
<form action="" method="post" enctype="multipart/form-data">
    <input type="text" name="title" placeholder="<?= translate('Title') ?>"
           value="<?= $inputData['title'] ?>">
    <span id="error">
        <?= isset($inputErrors['titleError']) ? $inputErrors['titleError'] : ''; ?>
    </span>
    <br><br>
    <input type="text" name="description" placeholder="<?= translate('Description') ?>"
           value="<?= $inputData['description'] ?>">
    <span id="error">
        <?= isset($inputErrors['descriptionError']) ? $inputErrors['descriptionError'] : ''; ?>
    </span>
    <br><br>
    <input type="number" name="price" placeholder="<?= translate('Price') ?> " min="0" step="any"
           value="<?= $inputData['price'] ?>">
    <span id="error">
        <?= isset($inputErrors['priceError']) ? $inputErrors['priceError'] : '' ?>
    </span>
    <br><br>
    <input type="file" id="image" name="image" placeholder="<?= translate('Image') ?>">
    <span id="error">
        <?= isset($inputErrors['imageNameError']) ? $inputErrors['imageNameError'] : '' ?>
    </span>
    <br><br>
    <a href="products.php"><?= translate('Products') ?></a>
    &nbsp;
    <input type="submit" name="save" value="<?= translate('Save')?>">
</form>

</body>
</html>
