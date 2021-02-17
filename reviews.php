<?php

require_once 'common.php';
checkForAuthentication();
$connection = getDbConnection();

if (isset($_POST['deleteReview'])) {
    $reviewToBeDeleted = $_POST['deleteReview'];
    $sql = $connection->prepare('SELECT comment FROM reviews WHERE id= ?');
    $sql->execute([$reviewToBeDeleted]);
    $review = $sql->fetch(PDO::FETCH_OBJ);

    $sql = $connection->prepare('DELETE FROM reviews WHERE id= ?');
    $sql->execute([$reviewToBeDeleted]);

    header('Location: reviews.php');
    die();
}

$sql = $connection->prepare('SELECT r.id, r.comment, r.rating, p.title 
    FROM reviews r 
        INNER JOIN products p ON r.product_id = p.id
    ORDER BY rating');
$sql->execute();
$reviews = $sql->fetchAll(PDO::FETCH_OBJ);

?>

<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= translate('Reviews') ?></title>
</head>
<body>
<?php if (count($reviews)) :?>
<table>
    <tr>
        <th><?= translate('Id') ?></th>
        <th><?= translate('Product') ?></th>
        <th><?= translate('Comment') ?></th>
        <th><?= translate('Rating') ?></th>
        <th><?= translate('Action') ?></th>
    </tr>
    <?php foreach ($reviews as $review) : ?>
        <tr>
            <td>
                <?= $review->id ?>
            </td>
            <td>
                <?= $review->title ?>
            </td>
            <td>
                <?= $review->comment ?>
            </td>
            <td>
                <?= $review->rating ?>
            </td>
            <td>
                <form action="reviews.php" method="post">
                    <input type="hidden" name="deleteReview" value="<?= $review->id ?>">
                    <button type="submit" name="delete"><?= translate('Delete') ?></button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
</body>
</html>
<?php else :?>
<p><?= translate('No reviews!')?> </p>
<?php endif;?>
