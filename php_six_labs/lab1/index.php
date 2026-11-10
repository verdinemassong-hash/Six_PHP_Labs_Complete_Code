<?php
require_once __DIR__ . '/../config.php';
$errors = [];
$success = '';

$pageTitle = 'Student Registration';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        
$name=post_value('full_name'); $email=post_value('email'); $phone=post_value('telephone');
$dob=post_value('dob'); $programme=post_value('programme'); $level=post_value('level'); $gender=post_value('gender');
if (!valid_length($name,3,80) || !preg_match("/^[\p{L} .'-]+$/u",$name)) $errors[]='Name must be 3–80 characters and contain letters or permitted punctuation.';
if (!filter_var($email,FILTER_VALIDATE_EMAIL)) $errors[]='Enter a valid email address.';
if (!preg_match('/^[0-9]{9}$/',$phone)) $errors[]='Telephone must contain exactly 9 digits.';
$d=DateTime::createFromFormat('Y-m-d',$dob);
if (!$d || $d->format('Y-m-d')!==$dob || $dob>=date('Y-m-d')) $errors[]='Date of birth must be a valid date in the past.';
if (!in_array($programme,['Computer Science','Software Engineering','Information Technology','Networking'],true)) $errors[]='Choose a valid programme.';
if (!in_array($level,['Level 1','Level 2','Level 3','Level 4','Level 5'],true)) $errors[]='Choose a valid level.';
if (!in_array($gender,['Male','Female','Other'],true)) $errors[]='Select a gender.';
if (!isset($_POST['policy'])) $errors[]='You must accept the registration policy.';
if (!$errors) {
 $stmt=$pdo->prepare('INSERT INTO students(full_name,email,telephone,date_of_birth,programme,study_level,gender) VALUES(?,?,?,?,?,?,?)');
 $stmt->execute([$name,$email,$phone,$dob,$programme,$level,$gender]);
 $success='Student registration saved successfully. Record ID: '.$pdo->lastInsertId();
 $_POST=[];
}

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

<div class="field"><label for="full_name">Full name</label><input id="full_name" name="full_name" value="<?= old('full_name') ?>" required></div>
<div class="field"><label for="email">Email address</label><input type="email" id="email" name="email" value="<?= old('email') ?>" required></div>
<div class="field"><label for="telephone">Telephone (9 digits, local format)</label><input id="telephone" name="telephone" inputmode="numeric" value="<?= old('telephone') ?>" required></div>
<div class="field"><label for="dob">Date of birth</label><input type="date" id="dob" name="dob" value="<?= old('dob') ?>" required></div>
<div class="field"><label for="programme">Programme</label><select id="programme" name="programme" required><option value="">Choose an option</option><?php foreach(['Computer Science','Software Engineering','Information Technology','Networking'] as $v): ?><option <?= (($_POST['programme']??'')===$v?'selected':'') ?>><?= e($v) ?></option><?php endforeach; ?></select></div>
<div class="field"><label for="level">Level</label><select id="level" name="level" required><option value="">Choose an option</option><?php foreach(['Level 1','Level 2','Level 3','Level 4','Level 5'] as $v): ?><option <?= (($_POST['level']??'')===$v?'selected':'') ?>><?= e($v) ?></option><?php endforeach; ?></select></div>
<div class="field inline"><strong>Gender</strong><br><?php foreach(['Male','Female','Other'] as $v): ?><label><input type="radio" name="gender" value="<?= e($v) ?>" <?= (($_POST['gender']??'')===$v?'checked':'') ?>><?= e($v) ?></label><?php endforeach; ?></div>
<div class="field"><label class="check"><input type="checkbox" name="policy" value="1" <?= isset($_POST['policy'])?'checked':'' ?>> I accept the registration policy</label></div>

<div class="field"><button type="submit">Submit Form</button> <button type="reset" style="background:#667085">Reset</button></div>
</form>

</main></body></html>
