<?php
require_once __DIR__ . '/../config.php';
$errors = [];
$success = '';

$pageTitle = 'Library Book Borrowing';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        
$sid=post_value('student_id'); $isbn=strtoupper(str_replace(['-',' '],'',post_value('isbn'))); $title=post_value('book_title');
$borrow=post_value('borrow_date'); $due=post_value('due_date'); $duration=post_value('duration'); $purpose=post_value('purpose');
$ok=false;
if(preg_match('/^[0-9]{9}[0-9X]$/',$isbn)){ $sum=0; for($i=0;$i<10;$i++){$sum+=(($isbn[$i]==='X'?10:(int)$isbn[$i])*(10-$i));} $ok=($sum%11===0);}
elseif(preg_match('/^[0-9]{13}$/',$isbn)){ $sum=0;for($i=0;$i<12;$i++)$sum+=(int)$isbn[$i]*($i%2?3:1);$ok=((10-$sum%10)%10)===(int)$isbn[12];}
if($sid===''||$title===''||$borrow===''||$due==='')$errors[]='Student ID, ISBN, title and dates are required.';
if(!$ok)$errors[]='Enter a valid ISBN-10 or ISBN-13.';
if(!ctype_digit($duration)|| (int)$duration<1)$errors[]='Duration must be a positive whole number.';
if($borrow>date('Y-m-d'))$errors[]='Borrow date cannot be in the future.';
if($due<=$borrow)$errors[]='Due date must be after borrow date.';
if($purpose==='')$errors[]='Borrowing purpose is required.';
if(!isset($_POST['rules']))$errors[]='You must agree to the library rules.';
if(!$errors){$q=$pdo->prepare("SELECT id FROM loans WHERE student_id=? AND isbn=? AND status='Active'");$q->execute([$sid,$isbn]);
if($q->fetch())$errors[]='This student already has an active loan for this book.';
else{$q=$pdo->prepare('INSERT INTO loans(student_id,isbn,book_title,borrow_date,due_date,loan_duration,purpose) VALUES(?,?,?,?,?,?,?)');$q->execute([$sid,$isbn,$title,$borrow,$due,(int)$duration,$purpose]);$success='Loan saved. Loan ID: '.$pdo->lastInsertId();}}
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
<form method="post" >

<div class="field"><label>Student ID</label><input name="student_id" value="<?= old('student_id') ?>" required></div>
<div class="field"><label>Book ISBN</label><input name="isbn" value="<?= old('isbn') ?>" required></div>
<div class="field"><label>Book title</label><input name="book_title" value="<?= old('book_title') ?>" required></div>
<div class="field"><label>Borrow date</label><input type="date" name="borrow_date" value="<?= old('borrow_date') ?>" required></div>
<div class="field"><label>Return due date</label><input type="date" name="due_date" value="<?= old('due_date') ?>" required></div>
<div class="field"><label>Loan duration (days)</label><input type="number" min="1" name="duration" value="<?= old('duration') ?>" required></div>
<div class="field"><label>Borrowing purpose</label><textarea name="purpose" required><?= old('purpose') ?></textarea></div>
<div class="field"><label class="check"><input type="checkbox" name="rules" value="1"> I agree to the library rules</label></div>
<div class="field"><button type="submit">Submit Form</button> <button type="reset" style="background:#667085">Reset</button></div>
</form>
<h2>Current active loans</h2><?php $rows=$pdo->query("SELECT id,student_id,book_title,due_date FROM loans WHERE status='Active' ORDER BY due_date")->fetchAll(); ?><ul><?php foreach($rows as $r): ?><li>Loan #<?=e($r['id'])?> — <?=e($r['book_title'])?> (<?=e($r['student_id'])?>), due <?=e($r['due_date'])?></li><?php endforeach;?></ul>
</main></body></html>
