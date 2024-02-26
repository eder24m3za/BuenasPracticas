<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sms Verify</title>
</head>
<body>
    <form method="POST" action="/verify/<?php echo e($user_id); ?>/sms">
        <?php echo csrf_field(); ?>
        <input type="hidden" id="request_id" value="<?php echo e($request_id); ?>" name="request_id">
        <div class="mb-3">
            <label for="exampleInputSms" class="form-label">Sms Verify</label>
            <input type="number" id="code" class="form-control" id="exampleInputSms" aria-describedby="smsVerify" require name="code">
            <div id="smsVerify" class="form-text">Enter the code we send you in your phone</div>
        </div>
        <button type="submit" class="btn btn-primary">Verify</button>
    </form>
</body>
</html><?php /**PATH C:\Users\egmr9\OneDrive\Documentos\UTT\ING\8\Igmar\practica\resources\views//sms.blade.php ENDPATH**/ ?>