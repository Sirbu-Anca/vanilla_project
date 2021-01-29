<?php
require_once 'common.php';
?>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo translate('Email') ?></title>
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
        </tr>
        <?php
    }
    ?>
    <tr>
        <td>
            <p>
                <?php echo translate('Name') ?>
                <?php echo translate($inputData['name']) ?>
            </p>
        </td>
    </tr>
    <tr>
        <td>
            <p>
                <?php echo translate('Contact details') ?>
                <?php echo translate($inputData['contactDetails']) ?>
            </p>
        </td>
    </tr>
    <tr>
        <td>
            <p>
                <?php echo translate('Comments') ?>
                <?php echo translate($inputData['comments']) ?>
            </p>
        </td>
    </tr>
</table>
</body>
</html>
