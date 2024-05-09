<?php
session_start();

if (isset($_SESSION["email"])) {
    if ($_SESSION["sudo"]) {
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
                <h1 class="text-5xl font-bold">Login</h1>
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

    <div class="dropdown dropdown-top dropdown-end dropdown-hover absolute bottom-4 right-4">
        <div tabindex="0" role="button" class="btn m-1">Temi</div>
        <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
            <li><input type="radio" name="theme-dropdown" class="theme-controller btn btn-sm btn-block btn-ghost justify-start" aria-label="Dark" value="dark" /></li>
            <li><input type="radio" name="theme-dropdown" class="theme-controller btn btn-sm btn-block btn-ghost justify-start" aria-label="Business" value="business" /></li>
            <li><input type="radio" name="theme-dropdown" class="theme-controller btn btn-sm btn-block btn-ghost justify-start" aria-label="Night" value="night" /></li>
            <li><input type="radio" name="theme-dropdown" class="theme-controller btn btn-sm btn-block btn-ghost justify-start" aria-label="Light" value="light" /></li>
            <li><input type="radio" name="theme-dropdown" class="theme-controller btn btn-sm btn-block btn-ghost justify-start" aria-label="Nord" value="nord" /></li>
            <li><input type="radio" name="theme-dropdown" class="theme-controller btn btn-sm btn-block btn-ghost justify-start" aria-label="Wireframe" value="wireframe" /></li>
        </ul>
    </div>

    <script>
        function setThemeLocalStorage() {
            localStorage.setItem("theme", this.value);
        }

        if (localStorage.getItem("theme")) {
            // find the corresponding input radio and check it
            document
                .querySelectorAll("input[name='theme']")
                .forEach((theme) => (theme.checked = false));
            document.querySelector(
                `input[value='${localStorage.getItem("theme")}']`
            ).checked = true;
        }

        document.querySelectorAll(".theme-controller").forEach((theme) => {
            theme.addEventListener("click", setThemeLocalStorage);
        });
    </script>
</body>

</html>