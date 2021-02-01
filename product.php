<?php

require_once 'common.php';
//checkForAuthentication();
$connection = getDbConnection();

$editProduct = (!isset($_GET['editProduct']) ? 0 : $_GET['editProduct']);

$stm = $connection->prepare('SELECT * FROM products WHERE id= ?');
$stm->execute([$editProduct]);
$product = $stm->fetch(PDO::FETCH_OBJ);

$inputData = [
    'title' => (isset($product->title) && isset($editProduct)) ? $product->title : '',
    'description' => (isset($product->description) && isset($editProduct)) ? $product->description : '',
    'price' => (isset($product->price) && isset($editProduct)) ? $product->price : '',
];

$inputErrors = [];

if (isset($_POST['save'])) {
    if (isset($_POST['title']) && $_POST['title']) {
        $inputData['title'] = $_POST['title'];
    } else {
        $inputErrors['titleError'] = translate('Please enter a title product.');
    }
    if (isset($_POST['description']) && $_POST['description']) {
        $inputData['description'] = $_POST['description'];
    } else {
        $inputErrors['descriptionError'] = translate('Please enter a description product.');
    }
    if (isset($_POST['price']) && $_POST['price']) {
        $inputData['price'] = $_POST['price'];
    } else {
        $inputErrors['priceError'] = translate('Please enter a price.');
    }

    if (isset($_FILES['image']) && $_FILES['image']['tmp_name']) {
        $inputData['imageLocation'] = $_FILES['image']['tmp_name'];
        $inputData['imageName'] = $_FILES['image']['name'];
    } else {
        $inputErrors['imageNameError'] = translate('Please choose an image.');
    }

    if (!count($inputErrors)) {
        if (!$editProduct) {
            $image_path = 'uploads/' . time() . "." . $inputData['imageName'];
            $sql = $connection->prepare('INSERT INTO products (title, description, price, image) VALUES( ?, ?, ?, ?)');
            $sql->execute([$inputData['title'], $inputData['description'], $inputData['price'], $image_path]);
            move_uploaded_file($inputData['imageLocation'], $image_path);
            header('Location: products.php');
            die();
        } else {
            if (isset($product->image) && ($inputData['imageName'] == pathImage($product->image))) {
                $sql = 'UPDATE products SET title= ? description= ? price= ? WHERE id= '.$editProduct;
                $params = [
                    $inputData['title'],
                    $inputData['description'],
                    $inputData['price'],
                ];

            } else {
                $sql = 'UPDATE products SET title= ? description= ? price= ? image= ? WHERE id= '.$editProduct;
                $image_path = 'uploads/' . time() . "." . $inputData['imageName'];
                $params = [
                    $inputData['title'],
                    $inputData['description'],
                    $inputData['price'],
                    $image_path,
                ];
            }
            $updateProduct = $connection->prepare($sql);
            $updateProduct->execute($params);
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
    <title>Add product</title>
</head>
<body>
<form action="" method="post" enctype="multipart/form-data">
    <input type="text" name="title" placeholder="<?= translate('Title') ?>"
           value="<?= $inputData['title'] ?>">
    <span>
        <?= isset($inputErrors['titleError']) ? $inputErrors['titleError'] : ''; ?>
    </span>
    <br><br>
    <input type="text" name="description" placeholder="<?= translate('Description') ?>"
           value="<?= $inputData['description'] ?>">
    <span>
        <?= isset($inputErrors['descriptionError']) ? $inputErrors['descriptionError'] : ''; ?>
    </span>
    <br><br>
    <input type="number" name="price" placeholder="<?= translate('Price') ?> " min="0" step="any"
           value="<?= $inputData['price'] ?>">
    <span>
        <?= isset($inputErrors['priceError']) ? $inputErrors['priceError'] : '' ?>
    </span>
    <br><br>
    <label for="image" >
        <?=isset($product->image) ? $product->image : translate('Choose an image!') ?>
    </label>
    <input type="file" id="image" name="image" placeholder="<?= translate('Image') ?>">
    <span>
        <?= isset($inputErrors['imageNameError']) ? $inputErrors['imageNameError'] : '' ?>
    </span>
    <br><br>
    <a href="products.php">Products</a>
    &nbsp;
    <input type="submit" name="save" value="Save">
</form>

</body>
</html>
