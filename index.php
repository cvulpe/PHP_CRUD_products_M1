<?php

$pdo = new PDO('mysql:host=localhost;port=3306;dbname=products_crud', 'root', ''); //DSN String + iser +password
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$search = $_GET['search'] ?? "";
if ($search) {
    $statement = $pdo->prepare('SELECT * FROM products WHERE title LIKE :name ORDER BY create_date ASC;');
    $statement->bindValue(':name', "%$search%");
} else {
    $statement = $pdo->prepare('SELECT * FROM products ORDER BY create_date ASC;');
}

$statement->execute();
$products = $statement->fetchAll(PDO::FETCH_ASSOC); // each ellement will be fetched as an associative array


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
    <h1>Crud Application</h1>
    <p>
        <a href="add.php" class="btn btn-success">Add Product</a>
    </p>
    <form action="">
        <div class="input-group">
            <input type="text" class="form-control" placeholder="Search for products:" name="search"
                value="<?= $search ?>">
            <button class="btn btn-outline-secondary" type="submit">Search</button>
        </div>
    </form>
    <table class="table">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Image</th>
                <th scope="col">Title</th>
                <th scope="col">Price</th>
                <th scope="col">Create Date</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $i => $product) : ?>
            <tr>
                <th scope="row"><?php echo $i + 1; ?></th>
                <td>
                    <img src="<?php echo $product['image']; ?>" class="thumb-image" alt="">
                </td>
                <td><?php echo $product['title']; ?></td>
                <td><?php echo $product['price']; ?></td>
                <td><?php echo $product['create_date']; ?></td>
                <td>
                    <a href="update.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-primary">Update</a>
                    <form style="display:inline-block" method="post" action="delete.php">
                        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>



</body>

</html>