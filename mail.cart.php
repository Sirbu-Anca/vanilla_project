<?php

require_once 'common.php';
?>

<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= translate('Email') ?></title>
    <style>
        table {
            width: 10%;
            border: 1px solid black;
        }

        td {
            text-align: left;
        }
    </style>
</head>
<body>
<table>
    <?php foreach ($cartProducts as $product) : ?>
        <tr>
            <td>
                <img src="<?= $product->image?>" alt="image" width="100" height="100">
            </td>
            <td>
                <?= $product->title ?><br>
                <?= $product->description ?><br>
                <?= $product->price . ' ' . translate('eur') ?> <br><br>
            </td>
        </tr>
    <?php
    endforeach;
    ?>
    <tr>
        <td>
            <p>
                <?= translate('Name') ?>
                <?= translate($inputData['name']) ?>
            </p>
        </td>
    </tr>
    <tr>
        <td>
            <p>
                <?= translate('Contact details') ?>
                <?= translate($inputData['contactDetails']) ?>
            </p>
        </td>
    </tr>
    <tr>
        <td>
            <p>
                <?= translate('Comments') ?>
                <?= translate($inputData['comments']) ?>
            </p>
        </td>
    </tr>
</table>
</body>
</html>
