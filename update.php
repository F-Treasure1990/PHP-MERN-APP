<?php
//establish a connection to DB
$pdo = new PDO('mysql:host=localhost;port=41062;dbname=products_crud', 'Fenton', '123456');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$id = $_GET['id'] ?? null;

if (!$id) {
  header('Location: index.php');
  exit;
};

$statement = $pdo->prepare('SELECT * FROM products WHERE id =:id');
$statement->bindValue(':id', $id);
$statement->execute();
$product = $statement->fetch(pdo::FETCH_ASSOC);

$errors = [];
$title = $product['title'];
$price = $product['price'];
$description = $product['description'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $title = $_POST['title'];
  $description = $_POST['description'];
  $price = $_POST['price'];

  if (!$title) {
    $errors[] = "Product Title is Required";
  };

  if (!$price) {
    $errors[] = "Product Price is Required";
  }

  if (!is_dir('images')) mkdir('images');

  if (empty($errors)) {
    $image = $_FILES['image'] ?? null;
    $imagePath = '';

    if ($image && $image['tmp_name']) {

      if ($product['image']) {
        unlink($product['image']);
      };

      $imagePath = 'images/' . randomString(8) . '/' . $image['name'];
      mkdir(dirname($imagePath));
      move_uploaded_file($image['tmp_name'], $imagePath);
    }

    $statement = $pdo->prepare("UPDATE products SET title= :title, image=:image, description = :description, price = :price WHERE id = :id");
    $statement->bindValue(':title', $title);
    $statement->bindValue(':image', $imagePath);
    $statement->bindValue(':description', $description);
    $statement->bindValue(':price', $price);
    $statement->bindValue(':id', $id);
    $statement->execute();
    header('Location: index.php');
  }
};

function randomString($n)
{
  $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIjKLMNOPQRSTUVWXYZ';
  $str = '';
  for ($i = 0; $i < $n; $i++) {
    $index = rand(0, strlen($characters) - 1);
    $str .= $characters[$index];
  }
  return $str;
};
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
  <p><a class="btn btn-secondary" href="index.php">Go Back To Products</a></p>
  <h1>Update Product <b><?php echo $product['title'] ?></b></h1>
  <?php if (!empty($errors)) : ?>
  <div class="alert alert-danger">
    <?php foreach ($errors as $error) : ?>
    <div><?php echo $error ?></div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data">
    <?php if ($product['image']) : ?>
    <img style="height:300px" src="<?php echo $product['image'] ?>" alt="">
    <?php endif; ?>
    <div class="mb-3">
      <label class="form-label">Product Image</label>
      <input type="file" class="form-control" name="image">
    </div>
    <div class="mb-3">
      <label class="form-label">Product Title</label>
      <input type="text" class="form-control" name="title" value="<?php echo $title ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Product Description</label>
      <textarea class="form-control" name="description"><?php echo $description ?></textarea>
    </div>
    <div class="mb-3">
      <label class="form-label">Product Price</label>
      <input type="number" class="form-control" step=".01" name="price" value="<?php echo $price ?>">
    </div>
    <button type="submit" class="btn btn-primary">Update</button>
  </form>

</body>

</html>