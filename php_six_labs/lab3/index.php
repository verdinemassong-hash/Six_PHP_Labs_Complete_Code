<?php
require_once __DIR__ . '/../config.php';
$errors = [];
$success = '';

$pageTitle = 'Clinic Appointment Booking';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        
$name=post_value('patient_name');$email=post_value('email');$phone=post_value('phone');$dept=post_value('department');$doctor=post_value('doctor');$date=post_value('appointment_date');$slot=post_value('time_slot');$reason=post_value('reason');
if(!valid_length($name,2,100))$errors[]='Name must be 2–100 characters.';
if($email!==''&&!filter_var($email,FILTER_VALIDATE_EMAIL))$errors[]='Email is invalid.';
if(!preg_match('/^[0-9+() -]{7,20}$/',$phone))$errors[]='Enter a valid phone number.';
if(!in_array($dept,['General Medicine','Dental','Eye Clinic','Paediatrics','Maternity'],true))$errors[]='Choose a valid department.';
if(!in_array($doctor,['Dr. Amina','Dr. John','Dr. Peter','Dr. Grace'],true))$errors[]='Choose a valid doctor.';
if($date<=date('Y-m-d'))$errors[]='Appointment must be on a future date.';
if(!in_array($slot,['09:00','10:00','11:00','12:00','14:00','15:00','16:00'],true))$errors[]='Choose a valid time slot.';
if(!valid_length($reason,10,500))$errors[]='Reason must be 10–500 characters.';
if(!isset($_POST['consent']))$errors[]='Consent is required.';
if(!$errors){$q=$pdo->prepare("INSERT INTO appointments(patient_name,email,phone,department,doctor,appointment_date,time_slot,reason,status) VALUES(?,?,?,?,?,?,?,?, 'Pending')");$q->execute([$name,$email?:null,$phone,$dept,$doctor,$date,$slot,$reason]);$success='Appointment saved as Pending. Reference: '.$pdo->lastInsertId();}
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

<div class="field"><label>Patient name</label><input name="patient_name" value="<?=old('patient_name')?>" required></div>
<div class="field"><label>Email (optional)</label><input type="email" name="email" value="<?=old('email')?>"></div>
<div class="field"><label>Phone</label><input name="phone" value="<?=old('phone')?>" required></div>
<div class="field"><label>Department</label><select name="department" required><option value="">Choose</option><?php foreach(['General Medicine','Dental','Eye Clinic','Paediatrics','Maternity'] as $v):?><option><?=e($v)?></option><?php endforeach;?></select></div>
<div class="field"><label>Preferred doctor</label><select name="doctor" required><option value="">Choose</option><?php foreach(['Dr. Amina','Dr. John','Dr. Peter','Dr. Grace'] as $v):?><option><?=e($v)?></option><?php endforeach;?></select></div>
<div class="field"><label>Appointment date</label><input type="date" name="appointment_date" value="<?=old('appointment_date')?>" required></div>
<div class="field"><label>Time slot</label><select name="time_slot" required><option value="">Choose</option><?php foreach(['09:00','10:00','11:00','12:00','14:00','15:00','16:00'] as $v):?><option><?=e($v)?></option><?php endforeach;?></select></div>
<div class="field"><label>Symptoms or reason (10–500 characters)</label><textarea name="reason" required><?=old('reason')?></textarea></div>
<div class="field"><label class="check"><input type="checkbox" name="consent" value="1"> I consent to data use</label></div>
<div class="field"><button type="submit">Submit Form</button> <button type="reset" style="background:#667085">Reset</button></div>
</form>
<h2>Appointment schedule</h2><?php $rows=$pdo->query("SELECT doctor,appointment_date,time_slot,status FROM appointments ORDER BY appointment_date,time_slot")->fetchAll();?><ul><?php foreach($rows as $r):?><li><?=e($r['doctor'])?> — <?=e($r['appointment_date'])?> at <?=e($r['time_slot'])?> (<?=e($r['status'])?>)</li><?php endforeach;?></ul>
</main></body></html>
