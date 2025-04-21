<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Login - Silandik</title>
    <link href="/css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="bg-primary">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-5">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header bg-primary text-white">
                                    <h3 class="text-center font-weight-light my-2">Login</h3>
                                </div>
                                <div class="card-body">
                                    <?php if(isset($error)): ?>
                                        <div class="alert alert-danger text-center"><?php echo $error; ?></div>
                                    <?php endif; ?>
                                    <form method="POST" action="login.php">
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="inputEmail" name="email" type="email" placeholder="name@example.com" required />
                                            <label for="inputEmail">Email address</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="inputPassword" name="password" type="password" placeholder="Password" required />
                                            <label for="inputPassword">Password</label>
                                        </div>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" id="inputRememberPassword" type="checkbox" />
                                            <label class="form-check-label" for="inputRememberPassword">Show Password</label>
                                        </div>
                                        <script>
                                            document.getElementById('inputRememberPassword').addEventListener('change', function () {
                                                const passwordInput = document.getElementById('inputPassword');
                                                passwordInput.type = this.checked ? 'text' : 'password';
                                            });
                                        </script>
                                    </form>
                                </div>
                                <div class="card-footer text-center py-3">
                                    <div class="small"> Apakah Anda ingin mendaftar menjadi Admin? Hubungi operator Anda!</a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <div id="layoutAuthentication_footer">
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <a href="https://instagram.com/sultan.studiooo">© 2025 Sultan Studio</a>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="/js/scripts.js"></script>
</body>

</html>
