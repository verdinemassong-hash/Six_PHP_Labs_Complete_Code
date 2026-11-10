<?php
require_once __DIR__ . '/../config.php';
$errors = [];
$success = '';

$pageTitle = 'E-Commerce Product Entry';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        
$name=post_value('product_name');$sku=post_value('sku');$cat=post_value('category');$price=post_value('price');$qty=post_value('quantity');$desc=post_value('description');
if(!valid_length($name,3,100))$errors[]='Name must be 3–100 characters.';
if($sku==='')$errors[]='SKU is required.';
if(!in_array($cat,['Electronics','Clothing','Books','Food','Home','Other'],true))$errors[]='Choose a category.';
if(!is_numeric($price)||(float)$price<=0)$errors[]='Price must be greater than zero.';
if(!preg_match('/^[0-9]+$/',$qty))$errors[]='Quantity must be a non-negative integer.';
if(!valid_length($desc,0,1000))$errors[]='Description maximum is 1000 characters.';
if(!$errors){$img=upload_image('product_image','uploads/products',2*1024*1024);$q=$pdo->prepare('INSERT INTO products(product_name,sku,category,unit_price,quantity,description,image_path,available) VALUES(?,?,?,?,?,?,?,?)');$q->execute([$name,$sku,$cat,(float)$price,(int)$qty,$desc?:null,$img,isset($_POST['available'])?1:0]);$success='Product saved. Product ID: '.$pdo->lastInsertId();}
    } catch (PDOException $ex) {
        if ($ex->getCode() === '23000') {
            $errors[] = 'This record conflicts with an existing record. Check unique fields or booking availability.';
        } else {
            $errors[] = 'A database error occurred. Please try again.';
        }
    } catch (Throwable $ex) {
        $errors[] = $ex->getMessage();
    }
}
function old($key) { return e($_POST[$key] ?? ''); }
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($pageTitle) ?></title><link rel="stylesheet" href="../assets/style.css"></head><body>
<main class="container"><p class="nav"><a href="../index.php">All laboratories</a></p><h1><?= e($pageTitle) ?></h1>
<p class="muted">Complete the form. Fields are checked again by PHP before anything is saved.</p>
<?php if ($success): ?><div class="message success"><?= e($success) ?></div><?php endif; ?>
<?php if ($errors): ?><div class="message error"><strong>Please correct the following:</strong><ul class="errors"><?php foreach($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="post" enctype="multipart/form-data">

<div class="field"><label>Product name</label><input name="product_name" value="<?=old('product_name')?>" required></div>
<div class="field"><label>SKU</label><input name="sku" value="<?=old('sku')?>" required></div>
<div class="field"><label>Category</label><select name="category" required><option value="">Choose</option><?php foreach(['Electronics','Clothing','Books','Food','Home','Other'] as $v):?><option><?=e($v)?></option><?php endforeach;?></select></div>
<div class="field"><label>Unit price (XAF)</label><input type="number" min="0.01" step="0.01" name="price" required></div>
<div class="field"><label>Quantity</label><input type="number" min="0" step="1" name="quantity" required></div>
<div class="field"><label>Description (maximum 1000 characters)</label><textarea name="description"><?=old('description')?></textarea></div>
<div class="field"><label>Product image (JPEG/PNG, maximum 2 MB)</label><input type="file" name="product_image" accept=".jpg,.jpeg,.png"></div>
<div class="field"><label class="check"><input type="checkbox" name="available" value="1"> Available for sale</label></div>
<div class="field"><button type="submit">Submit Form</button> <button type="reset" style="background:#667085">Reset</button></div>
</form>
<h2>Inventory by category</h2><?php $rows=$pdo->query("SELECT product_name,sku,category,quantity,unit_price FROM products ORDER BY category,product_name")->fetchAll();?><ul><?php foreach($rows as $r):?><li><?=e($r['category'])?> — <?=e($r['product_name'])?> (SKU <?=e($r['sku'])?>), <?=e($r['quantity'])?> in stock, <?=e($r['unit_price'])?> XAF</li><?php endforeach;?></ul>
</main></body></html>
