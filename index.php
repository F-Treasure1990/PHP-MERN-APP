<?php
//establish a connection to DB
$pdo = new PDO('mysql:host=localhost;port=41062;dbname=products_crud', 'Fenton', '123456');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$search = $_GET['search'] ?? "";
if ($search) {
  $statement = $pdo->prepare('SELECT * FROM products WHERE title LIKE :title ORDER BY create_date DESC');
  $statement->bindValue(':title', "%$search%");
} else {
  $statement = $pdo->prepare('SELECT * FROM products ORDER BY create_date DESC');
}
$statement->execute();
$products = $statement->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Products CRUD</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
  <link rel="stylesheet" href="./app.css">
</head>

<body>
  <h1>Products CRUD!</h1>
  <p>
    <a href="create.php" class="btn btn-success">Create Product</a>
  </p>
  <form>
    <div class="input-group mb-3">
      <input type="text" class="form-control" placeholder="Search for Products" name="search" value="<?php echo $search ?>">
      <button class="btn btn-outline-secondary" type="submit">Search</button>
    </div>
  </form>
  <table class="table table-dark table-striped">
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
      <?php
      foreach ($products as $i => $product) : ?>
      <tr>
        <th scope="row"><?php echo $i + 1 ?></th>
        <td><img class="thumb-img" src="<?php echo $product['image'] ?>" alt="product image"></td>
        <td><?php echo $product['title'] ?></td>
        <td>$<?php echo $product['price'] ?></td>
        <td><?php echo $product['create_date'] ?></td>
        <td>
          <a href="update.php?id=<?php echo $product['id'] ?>" type="button" class="btn btn-sm btn-outline-primary">Edit</a>
          <form style="display:inline-block" method="post" action="delete.php">
            <input type="hidden" name="id" value="<?php echo $product['id'] ?>">
            <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
          </form>
        </td>
      </tr>
      <?php endforeach ?>
    </tbody>
  </table>


</body>

</html>