<?php

$pdo = new PDO('mysql:host=localhost;port=3306;dbname=products_crud', 'root', ''); //DSN String + user +password
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$errors = [];
$name = '';
$description = '';
$price = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $date = date("Y-m-d H:i:s");

    if (!$name) {
        $errors[] = "Product name is required!";
    }
    if (!$price) {
        $errors[] = "Product price is required!";
    }

    if (!is_dir('images')) {
        mkdir('images');
    }

    if (empty($errors)) {
        // save the image
        $image = $_FILES['image'] ?? null;
        $imagePath = '';
        if ($image) {
            $imagePath = 'images/' . randomString(8) . '/' . $image['name'];
            mkdir(dirname($imagePath));
            move_uploaded_file($image['tmp_name'], $imagePath);
        }


        // Insert into database

        $statement = $pdo->prepare("INSERT INTO products (title, description, image, price, create_date) VALUES (:name, :description, :image, :price, :date)");
        $statement->bindValue(':name', $name);
        $statement->bindValue(':image', $imagePath);
        $statement->bindValue(':description', $description);
        $statement->bindValue(':price', $price);
        $statement->bindValue(':date', $date);
        $statement->execute();
        header('Location: index.php');
    }
}

function randomString($n)
{
    $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $str = "";
    for ($i = 0; $i < $n; $i++) {
        $index = rand(0, strlen($characters) - 1);
        $str .= $characters[$index];
    }
    return $str;
}
?>

<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
    <link rel="stylesheet" href="app.css">
    <title>Crud Application</title>
</head>

<body>
    <h1>Add new product</h1>
    <?php if (!empty($errors)) : ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $error) : ?>
        <div><?php echo $error; ?></div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <form action="" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label>Product Image</label>
            <input type="file" name="image" class="form-control">
        </div>
        <div class="form-group">
            <label>Product Name</label>
            <input type="text" name="name" value="<?php echo $name; ?>" class="form-control">
        </div>
        <div class="form-group">
            <label>Product Description</label>
            <textarea class="form-control" name="description"><?php echo $description; ?></textarea>
        </div>
        <div class="form-group">
            <label>Product Price</label>
            <input type="number" step=".01" name="price" value="<?= $price; ?>" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>

</body>

</html>