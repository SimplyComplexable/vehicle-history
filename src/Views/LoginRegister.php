<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='utf-8'>
    <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css'>
    <title>Register/Login | VSHTS</title>
    <style>
        .border-rnd{
            border: 1px solid #d3d3d3;
            border-radius: 6px;
        }
        h1{
            font-weight: 400;
        }
        .h3{
            margin-top: 1.2em;
            font-weight: 400;
        }
    </style>
</head>
<body>
    <nav class='navbar navbar-expand-xl navbar-dark bg-primary sticky-top'>
        <a href='./' class='display-4 navbar-brand ml-2' style='font-size: 1.7em;'>Vehicle Service History Tracking System</a>
        <div class='float-right text-right'>
            <a class="btn btn-primary mr-2 my-sm-0 active" href="#">Login</a>
        </div>
    </nav>
    <div class='container container-fluid mx-auto mt-4 mb-4'>
        <div class="row justify-content-md-center mb-4">
            <div class='mt-4 col-md-5 border-rnd mr-4 px-5 py-5'>
                <h1 class="mb-4">Register</h1>
                <form method="post" action="./register">
                    <div class="form-group">
                        <input type="text" class="form-control form-control-lg mb-2" placeholder="Username" name="username">
                        <input type="password" class="form-control form-control-lg mb-2" placeholder="Password" name="password">
                        <input type="password" class="form-control form-control-lg mb-2" placeholder="Confirm Password" name="confirmpassword">
                        <div class="ml-2 col-md-13">
                            <p>* Passwords must be 8 characters long, contain one uppercase, one lowercase letter and one number.</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <input type="submit" class="btn btn-lg btn-success mt-3" value="Register">
                    </div>
                </form>
            </div>
            <div class="col-md-auto mt-5">
                <h1 class="h3">or</h1>
            </div>
            <div class='mt-4 col-md-5 border-rnd ml-4 px-5 py-5'>
                <h1 class="mb-4">Login</h1>
                <form method="post" action="./login">
                    <div class="form-group">
                        <input type="text" class="form-control form-control-lg mb-2" placeholder="Username" name="username">
                        <input type="password" class="form-control form-control-lg mb-2" placeholder="Password" name="password">
                    </div>
                    <div class="form-group">
                        <input type="submit" class="btn btn-lg btn-success mt-3" value="Login">
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>";