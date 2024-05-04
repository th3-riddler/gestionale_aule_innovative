<?php
session_start();

if(isset($_SESSION["email"])) {
    if($_SESSION["sudo"]) {
        header("Location: tecnici/index.php");
        exit();
    } else {
        header("Location: docenti/index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="it" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.10.2/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Login</title>
</head>
<body>
    <div class="hero min-h-screen bg-base-200">
        <div class="hero-content flex-col lg:flex-row-reverse justify-between w-10/12">
            <div class="text-center lg:text-left">
                <h1 class="text-5xl font-bold">Pagina di Login</h1>
                <p class="py-6">Benvenuto nel Gestionale per Aule Innovative, IIS N.Copernico A.Carpeggiani.</p>
            </div>
            <div class="card shrink-0 w-full max-w-sm shadow-2xl bg-base-100">
            <form method="POST" action="API/auth.php" class="card-body">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Email</span>
                    </label>
                    <input name="email" type="email" placeholder="email" class="input input-bordered" required />
                    </div>
                    <div class="form-control">
                    <label class="label">
                        <span class="label-text">Password</span>
                    </label>
                    <input name="password" type="password" placeholder="password" class="input input-bordered" required />
                    </div>
                    <div class="form-control mt-6">
                    <button class="btn btn-primary">Login</button>
                </div>
            </form>
            </div>
        </div>
    </div>
</body>
</html>