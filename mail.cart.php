<html lang="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= translate('Email') ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<table>
    <?php foreach ($cartProducts as $product) : ?>
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
    <?php endforeach; ?>
    <tr>
        <td>
            <p>
                <?= translate('Name') ?>
                <?= $inputData['name'] ?>
            </p>
        </td>
    </tr>
    <tr>
        <td>
            <p>
                <?= translate('Contact details') ?>
                <?= $inputData['contactDetails'] ?>
            </p>
        </td>
    </tr>
    <tr>
        <td>
            <p>
                <?= translate('Comments') ?>
                <?= $inputData['comments'] ?>
            </p>
        </td>
    </tr>
</table>
</body>
</html>
