<?php

require_once 'common.php';
$connection = getDbConnection();
$productId = $_GET['showProduct'] ?? null;

if (!$productId || !is_numeric($productId)) {
    header('Location: index.php');
    die();
}

$sql = $connection->prepare('SELECT * FROM products WHERE id = ?');
$sql->execute([$productId]);
$product = $sql->fetch(PDO::FETCH_OBJ);

$sql = $connection->prepare('SELECT comment, rating, creation_date FROM reviews WHERE product_id = ? ORDER BY creation_date');
$sql->execute([$productId]);
$reviews = $sql->fetchAll(PDO::FETCH_OBJ);

$inputData['comments'] = strip_tags($_POST['comments'] ?? '');
$inputData['rate'] = strip_tags($_POST['rate'] ?? null);
$inputErrors = [];
if (isset($_POST['save'])) {
    if (empty($inputData['comments'])) {
        $inputErrors['commentsError'] = translate('Please enter your review.');
    } else {
        if (strlen($inputData['comments']) < 5) {
            $inputErrors['commentsError'] = translate('Your review should have more then 5 letters.');
        }
    }

    if (empty($inputData['rate'])) {
        $inputErrors['rateError'] = translate('On a scale of 1 to 5, how do you rate this product?');
    }

    if (!count($inputErrors)) {
        $creationDate = date('Y-m-d H:i:s');
        $sql = $connection->prepare('INSERT INTO reviews (product_id, comment, rating , creation_date) VALUES( ?, ?, ?, ?)');
        $sql->execute([$productId, $inputData['comments'], $inputData['rate'], $creationDate,]);
        header('Location: show_product.php?showProduct=' . $productId);
    }
}
?>

<html lang="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= translate('Product') ?></title>
    <link rel='stylesheet' href='http://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css'>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<table>
    <tr>
        <td>
            <img src="<?= $product->image ?>" alt="image">
        </td>
        <td>
            <?= $product->title ?><br>
            <?= $product->description ?><br>
            <?= $product->price ?><?= translate(' eur') ?> <br><br>
        </td>
    </tr>
</table>
<p><a href="index.php"><?= translate('Go back products') ?></a></p>
<?php if (count($reviews)) : ?>
    <h4><?= translate('Reviews') ?></h4>
<?php endif; ?>
<?php foreach ($reviews as $review) : ?>
    <ul>
        <li>
            <?= date('Y-m-d', strtotime($review->creation_date)) ?>
            <?= translate('Rate:') ?><?= $review->rating ?> <?= '/5' ?>
            <i class="fa fa-star" style="font-size:20px;color:orange;"></i>
            <br>
            <?= $review->comment ?>
        </li>
    </ul>
<?php endforeach; ?>
<p><?= translate('Leave a review') ?></p>
<form action="" method="post">
    <textarea name="comments" cols="22" rows="3"
              placeholder="<?= translate('Comments') ?>"><?= $inputData['comments'] ?></textarea>
    <span class="error">
        <?= $inputErrors['commentsError'] ?? ''; ?>
    </span>
    <p><?= translate('Rate the product:') ?></p>
    <input type="radio" id="rate1" name="rate" title="Don't recommend" value="1">
    <label for="rate1">1</label>
    <input type="radio" id="rate2" name="rate" title="Poor" value="2">
    <label for="rate2">2</label>
    <input type="radio" id="rate3" name="rate" title="Acceptable" value="3">
    <label for="rate3">3</label>
    <input type="radio" id="rate3" name="rate" title="Good" value="4">
    <label for="rate3">4</label>
    <input type="radio" id="rate3" name="rate" title="Excellent" value="5">
    <label for="rate3">5</label>
    <span class="error">
        <?= $inputErrors['rateError'] ?? ''; ?>
    </span>
    <br><br>
    <button type="submit" name="save"><?= translate('Save') ?> </button>
</form>
</body>
</html>
