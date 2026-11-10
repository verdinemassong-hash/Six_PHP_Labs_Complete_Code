<?php
require_once __DIR__ . '/../config.php';
$errors = [];
$success = '';

$pageTitle = 'Event Registration';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        
$name=post_value('participant_name');$email=post_value('email');$phone=post_value('phone');$workshop=post_value('workshop');$mode=post_value('attendance_mode');$seats=post_value('seats');$special=post_value('special_requirements');
if(!valid_length($name,2,100))$errors[]='Name must be 2–100 characters.';
if(!filter_var($email,FILTER_VALIDATE_EMAIL))$errors[]='Enter a valid email.';
if(!preg_match('/^[0-9+() -]{7,20}$/',$phone))$errors[]='Enter a valid phone.';
if(!in_array($workshop,['Web Development','Database Management','Cybersecurity','Graphic Design'],true))$errors[]='Choose a workshop.';
if(!in_array($mode,['In-person','Online'],true))$errors[]='Choose attendance mode.';
if(!ctype_digit($seats)||(int)$seats<1||(int)$seats>3)$errors[]='Seats must be 1 to 3.';
if(!valid_length($special,0,300))$errors[]='Special requirements maximum is 300 characters.';
if(!isset($_POST['terms']))$errors[]='Accept the event terms.';
if(!$errors){$q=$pdo->prepare('INSERT INTO event_registrations(participant_name,email,phone,workshop,attendance_mode,seats,special_requirements) VALUES(?,?,?,?,?,?,?)');$q->execute([$name,$email,$phone,$workshop,$mode,(int)$seats,$special?:null]);$success='Registration saved. Reference: '.$pdo->lastInsertId();}
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

<div class="field"><label>Participant name</label><input name="participant_name" value="<?=old('participant_name')?>" required></div>
<div class="field"><label>Email</label><input type="email" name="email" value="<?=old('email')?>" required></div>
<div class="field"><label>Phone</label><input name="phone" value="<?=old('phone')?>" required></div>
<div class="field"><label>Workshop</label><select name="workshop" required><option value="">Choose</option><?php foreach(['Web Development','Database Management','Cybersecurity','Graphic Design'] as $v):?><option><?=e($v)?></option><?php endforeach;?></select></div>
<div class="field inline"><strong>Attendance mode</strong><br><label><input type="radio" name="attendance_mode" value="In-person">In-person</label><label><input type="radio" name="attendance_mode" value="Online">Online</label></div>
<div class="field"><label>Number of seats (1–3)</label><input type="number" min="1" max="3" name="seats" required></div>
<div class="field"><label>Special requirements (maximum 300 characters)</label><textarea name="special_requirements"><?=old('special_requirements')?></textarea></div>
<div class="field"><label class="check"><input type="checkbox" name="terms" value="1"> I accept the event terms</label></div>
<div class="field"><button type="submit">Submit Form</button> <button type="reset" style="background:#667085">Reset</button></div>
</form>
<h2>Registration totals per workshop</h2><?php $rows=$pdo->query("SELECT workshop,COUNT(*) total,SUM(seats) seats FROM event_registrations GROUP BY workshop")->fetchAll();?><ul><?php foreach($rows as $r):?><li><?=e($r['workshop'])?>: <?=e($r['total'])?> registrations, <?=e($r['seats'])?> seats</li><?php endforeach;?></ul>
</main></body></html>
