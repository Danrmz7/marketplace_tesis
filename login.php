<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <!-- css -->
    <link rel="stylesheet" href="_assets/css/login.css">
</head>
<body class="login-page">

    <form method="post" role="form" action="./">
			<!-- <input type="text" class="form-control form-control-lg input-lg" name="nombre" placeholder="Nombre De Usuario" required> -->
			<!-- <input type="password" class="form-control form-control-lg input-lg" name="contrasena" placeholder="Contraseña" required> -->
			<!-- <p style="color:darkred;"><?php echo ($_GET['error']?$_GET['error']:''); ?></p> -->
			<!-- <input name="remember_me" type="checkbox" value="Remember Me" checked>
			<label for="remember-me"> Remember Me</label> -->
        <!-- <button type="submit" class="btn btn-success btn-lg btn-block"><i class="icon-unlock2"></i> Iniciar Sesión</button> -->
	</form>
    <div class="form">
        <!-- <img src="_assets/img/logo.png" width="50">
        <form class="register-form">
        <input type="text" placeholder="name"/>
        <input type="password" placeholder="password"/>
        <input type="text" placeholder="email address"/>
        <button>create</button>
        <p class="message">Already registered? <a href="#">Sign In</a></p> -->
    </form>
    <form class="login-form" method="post" role="form" action="./">
        <img src="_assets/img/logo.png" width="50">
        <input type="text" name="correo_comprador" placeholder="username"/>
        <input type="password" name="contrasena_comprador" placeholder="password"/>
        <button>login</button>
        <label for="remember-me"> Remember Me</label>
        <p style="color:darkred;"><?php echo ($_GET['error']?$_GET['error']:''); ?></p>
        <p class="message">Not registered? <a href="#">Create an account</a></p>
    </form>
  </div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    $('.message a').click(function(){
   $('form').animate({height: "toggle", opacity: "toggle"}, "slow");
});
</script>
</body>
</html>