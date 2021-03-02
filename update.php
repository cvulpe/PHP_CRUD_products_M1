<?php

$pdo = new PDO('mysql:host=localhost;port=3306;dbname=products_crud', 'root', ''); //DSN String + user +password
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit();
}

$statement = $pdo->prepare('SELECT * FROM products WHERE id= :id');
$statement->bindValue(':id', $id);
$statement->execute();
$product = $statement->fetch(PDO::FETCH_ASSOC);

$errors = [];
$name = $product['title'];
$description = $product['description'];
$price = $product['price'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];

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
        $imagePath = $product['image'];

        if ($image && $image['tmp_name']) {
            if ($product['image']) {
                unlink($product['image']);
            }
            $imagePath = 'images/' . randomString(8) . '/' . $image['name'];
            mkdir(dirname($imagePath));
            move_uploaded_file($image['tmp_name'], $imagePath);
        }


        // Insert into database

        $statement = $pdo->prepare("UPDATE products SET title = :name, image = :image, description = :description,
        price = :price WHERE id = :id");
        $statement->bindValue(':name', $name);
        $statement->bindValue(':image', $imagePath);
        $statement->bindValue(':description', $description);
        $statement->bindValue(':price', $price);
        $statement->bindValue(':id', $id);
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
    <p>
        <a href="index.php" class="btn btn-secondary">Return to Products</a>
    </p>
    <h3>Update product <b><?= $product['title'] ?></b></h3>
    <?php if (!empty($errors)) : ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $error) : ?>
        <div><?php echo $error; ?></div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <form action="" method="post" enctype="multipart/form-data">
        <?php if ($product['image']) : ?>
        <img src="<?= $product['image'] ?>" class="update-image" alt="">
        <?php endif; ?>
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