<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Hello test</h1> 
    <h2>PHP version</h2>
    <?php
        echo $_GET['page'];
        //php_info
    ?>
    <?php
        phpinfo();
    ?>
</body>
</html>