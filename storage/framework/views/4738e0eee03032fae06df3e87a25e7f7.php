<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email</title>
</head>
<body>
  <form method="POST" action="<?php echo e($signedUrl); ?>">
      <?php echo csrf_field(); ?>
    <div class="form-group">
        <h2>Click to verify the mail</h2>
    </div>
    <button type="submit" class="btn btn-primary">Verify Email</button>
  </form>
</body>
</html><?php /**PATH C:\Users\egmr9\OneDrive\Documentos\UTT\ING\8\Igmar\practica\resources\views/emailVerify.blade.php ENDPATH**/ ?>