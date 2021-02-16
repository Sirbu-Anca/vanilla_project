<?php

require_once 'common.php';
checkForAuthentication();
$connection = getDbConnection();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $editProductId = $_GET['editProductId'] ?? null;
} else {
    $editProductId = $_POST['editProductId'] ?? null;
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

$inputErrors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['title']) || !$_POST['title']) {
        $inputErrors['titleError'] = translate('Please enter a product title.');
    }

    if (!isset($_POST['description']) || !$_POST['description']) {
        $inputErrors['descriptionError'] = translate('Please enter a product description.');
    }

    if (isset($_POST['price']) && $_POST['price']) {
        if (!is_numeric($_POST['price'])) {
            $inputErrors['priceError'] = translate('Please enter a natural number for product price.');
        }
    } else {
        $inputErrors['priceError'] = translate('Please enter a number for product price.');
    }

    if (!$editProductId && (!isset($_FILES['image']['tmp_name']) || !$_FILES['image']['tmp_name'])) {
        $inputErrors['imageNameError'] = translate('Please choose an image.');
    }

    if (isset($_FILES['image']['tmp_name']) && $_FILES['image']['tmp_name']) {
        if ($_FILES['image']['error'] === 0) {
            if (isset($_FILES['image']['name']) && $_FILES['image']['name']) {
                $imageFileType = mime_content_type($_FILES['image']['tmp_name']);
                $image = [
                    'png' => 'image/png',
                    'jpe' => 'image/jpeg',
                    'jpeg' => 'image/jpeg',
                    'jpg' => 'image/jpeg',
                    'gif' => 'image/gif',
                    'bmp' => 'image/bmp',
                ];
                if (!in_array($imageFileType, $image)) {
                    $inputErrors['imageNameError'] = translate('Sorry, only JPG, JPEG, PNG, GIF, JPE, BMP files are allowed.');
                }
            }
            if ($_FILES['image']['size'] > 500000) {
                $inputErrors['imageNameError'] = translate('Sorry, your file is too large.');
            }
        } else {
            $uploadErrors = [
                1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
                2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
                3 => 'The uploaded file was only partially uploaded',
                4 => 'No file was uploaded',
                5 => 'Missing a temporary folder',
                6 => 'Failed to write file to disk.',
                7 => 'A PHP extension stopped the file upload. PHP does not provide a way to ascertain which extension caused the file upload to stop; examining the list of loaded extensions with phpinfo() may help.',
            ];
            $inputErrors['imageNameError'] = $uploadErrors[$_FILES['image']['error']] ?? 'Image upload error.';
        }
    }

    if (!count($inputErrors)) {
        $pathImage = 'uploads/' . time() . $_FILES['image']['name'];
        $sql = '';
        $parameters = [
            $_POST['title'],
            $_POST['description'],
            $_POST['price']
        ];
        if ($editProductId) {
            if (empty($_FILES['image']['name'])) {
                $sql = 'UPDATE products SET title= ?, description= ?, price= ? WHERE id= ?';
                array_push($parameters, $editProductId);

            } else {
                $sql = 'UPDATE products SET title= ?, description= ?, price= ?, image= ? WHERE id= ?';
                array_push($parameters, $pathImage, $editProductId);

                if (isset($product->image) && file_exists($product->image)) {
                    unlink($product->image);
                }
            }
        } else {
            $sql = 'INSERT INTO products (title, description, price, image) VALUES( ?, ?, ?, ?)';
            array_push($parameters, $pathImage);
        }
        move_uploaded_file($_FILES['image']['tmp_name'], $pathImage);
        $insertUpdateProduct = $connection->prepare($sql);
        $insertUpdateProduct->execute($parameters);
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
        <input type="hidden" name="editProductId" value="<?= $product->id ?? null ?>">
    <?php endif; ?>
    <input type="text" name="title" placeholder="<?= translate('Title') ?>"
           value="<?= $_POST['title'] ?? $product->title ?? ''; ?>">
    <span class="error">
        <?= $inputErrors['titleError'] ?? ''; ?>
    </span>
    <br><br>
    <input type="text" name="description" placeholder="<?= translate('Description') ?>"
           value="<?= $_POST['description'] ?? $product->description ?? '' ?>">
    <span class="error">
        <?= $inputErrors['descriptionError'] ?? ''; ?>
    </span>
    <br><br>
    <input type="number" name="price" placeholder="<?= translate('Price') ?> " min="0" step="any"
           value="<?= $_POST['price'] ?? $product->price ?? '' ?>">
    <span class="error">
        <?= $inputErrors['priceError'] ?? '' ?>
    </span>
    <br><br>
    <input type="file" id="image" name="image" placeholder="<?= translate('Image') ?>">
    <span class="error">
        <?= $inputErrors['imageNameError'] ?? '' ?>
    </span>
    <br><br>
    <a href="products.php"><?= translate('Products') ?></a>
    &nbsp;
    <input type="submit" name="save" value="<?= translate('Save') ?>">
</form>
</body>
</html>
