<!-- registro.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body>
<?php if($errors->any()): ?>
    <div class="alert alert-danger">
        <ul>
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
<?php endif; ?>
<div class="container" style="margin:100px 85px 85px 85px">
    <form action="/createUser" method="POST" id="formRegister">
        <?php echo csrf_field(); ?>
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="Name" required maxlength="50">
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required maxlength="60">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" id="password" class="form-control" name="password" aria-describedby="passwordHelpBlock" required minlength="8">
            <div id="passwordHelpBlock" class="form-text">
                Your password must be 8-20 characters long, contain letters and numbers, and must not contain spaces, special characters.
            </div>
        </div>
        <div class="mb-3">
            <label for="c_password" class="form-label">Confirm Password</label>
            <input type="password" id="c_password" class="form-control" name="c_password" required minlength="8">
        </div>
        <div id="errorMessage" class="mt-3 text-danger" style="display: none;">Las contraseñas no coinciden.</div>
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        <div class="mb-3">
            <label for="phoneNumber" class="form-label">Phone Number</label>
            <input type="text" class="form-control" id="phoneNumber" name="phoneNumber" placeholder="1234567890" required>
        </div>
        <div class="g-recaptcha" data-sitekey="6Lf0u14pAAAAAJg-Qm3wZvCXRazx4dNjxWylVpEl"></div> <br>
        <button type="submit" class="btn btn-primary">Register</button>
        <a href="/" class="btn btn-link" role="button" aria-pressed="true">Login</a>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  
<script>
    $(document).ready(function(){
        $('#formRegister').submit(function(e){
            e.preventDefault(); // Prevenir el envío del formulario

            // Obtener los valores de los inputs
            var password1 = $('#password').val();
            var password2 = $('#c_password').val();
            var phoneNumber = $('#phoneNumber').val().replace(/\D/g, '').substring(0, 10);

            // Validar si las contraseñas son iguales
            if(password1 !== password2){
                $('#errorMessage').show(); // Mostrar el mensaje de error de contraseñas
            } else {
                $('#errorMessage').hide(); // Ocultar el mensaje de error de contraseñas si coinciden
                // Actualizar el valor del campo de teléfono con el número telefónico limitado a 10 dígitos
                $('#phoneNumber').val(phoneNumber);
                // Aquí puedes enviar el formulario si es necesario
                $(this).off('submit').submit();
            }
        });

        // Agregar el event listener para limitar la longitud del número telefónico
        $('#phoneNumber').on('input', function() {
            var phoneNumber = $(this).val().replace(/\D/g, '').substring(0, 10);
            $(this).val(phoneNumber);
        });
    });
</script>
</body>
</html>
<?php /**PATH C:\Users\egmr9\OneDrive\Documentos\UTT\ING\8\Igmar\practica\resources\views/register.blade.php ENDPATH**/ ?>