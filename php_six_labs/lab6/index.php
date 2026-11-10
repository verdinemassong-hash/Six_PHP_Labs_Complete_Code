<?php
require_once __DIR__ . '/../config.php';
$errors = [];
$success = '';

$pageTitle = 'Customer Support Ticket';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        
$name=post_value('customer_name');$email=post_value('email');$cat=post_value('category');$priority=post_value('priority');$subject=post_value('subject');$desc=post_value('description');
if(!valid_length($name,2,100))$errors[]='Name must be 2–100 characters.';
if(!filter_var($email,FILTER_VALIDATE_EMAIL))$errors[]='Enter a valid email.';
if(!in_array($cat,['Hardware','Software','Network','Account','Other'],true))$errors[]='Choose an issue category.';
if(!in_array($priority,['Normal','High'],true))$errors[]='Choose a priority.';
if(!valid_length($subject,5,120))$errors[]='Subject must be 5–120 characters.';
if(!valid_length($desc,20,2000))$errors[]='Description must be 20–2000 characters.';
if(!isset($_POST['confirmation']))$errors[]='Confirm that the details are accurate.';
if(!$errors){$img=upload_image('screenshot','uploads/tickets',2*1024*1024);$q=$pdo->prepare("INSERT INTO support_tickets(customer_name,email,category,priority,subject,description,screenshot_path,status) VALUES(?,?,?,?,?,?,?,'Pending')");$q->execute([$name,$email,$cat,$priority,$subject,$desc,$img]);$success='Ticket created. Reference: TKT-'.$pdo->lastInsertId();}
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

<div class="field"><label>Customer name</label><input name="customer_name" value="<?=old('customer_name')?>" required></div>
<div class="field"><label>Email</label><input type="email" name="email" value="<?=old('email')?>" required></div>
<div class="field"><label>Issue category</label><select name="category" required><option value="">Choose</option><?php foreach(['Hardware','Software','Network','Account','Other'] as $v):?><option><?=e($v)?></option><?php endforeach;?></select></div>
<div class="field inline"><strong>Priority</strong><br><label><input type="radio" name="priority" value="Normal">Normal</label><label><input type="radio" name="priority" value="High">High</label></div>
<div class="field"><label>Subject (5–120 characters)</label><input name="subject" value="<?=old('subject')?>" required></div>
<div class="field"><label>Issue description (20–2000 characters)</label><textarea name="description" required><?=old('description')?></textarea></div>
<div class="field"><label>Screenshot (optional PNG/JPEG, under 2 MB)</label><input type="file" name="screenshot" accept=".jpg,.jpeg,.png"></div>
<div class="field"><label class="check"><input type="checkbox" name="confirmation" value="1"> I confirm these details are accurate</label></div>
<div class="field"><button type="submit">Submit Form</button> <button type="reset" style="background:#667085">Reset</button></div>
</form>
<h2>Tickets by status</h2><?php $rows=$pdo->query("SELECT status,COUNT(*) total FROM support_tickets GROUP BY status")->fetchAll();?><ul><?php foreach($rows as $r):?><li><?=e($r['status'])?>: <?=e($r['total'])?></li><?php endforeach;?></ul>
</main></body></html>
