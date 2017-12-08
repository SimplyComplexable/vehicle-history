<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='utf-8'>
    <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css'>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
    <title>Fuel Log | VSHTS</title>
    <style>
        .btn{
            cursor: pointer;
            font-size: large;
        }
    </style>
</head>
<body>
    <nav class='navbar navbar-expand-xl navbar-dark bg-primary sticky-top'>
        <a href='./' class='display-4 navbar-brand ml-2' style='font-size: 1.7em;'>Vehicle Service History Tracking System</a>
        <div class='float-right text-right'>
            <a class="btn btn-primary mx-2 my-sm-0" href="./vehicles">Vehicles</a>
            <a class="btn btn-primary mr-2 my-sm-0" href="./history">Service History</a>
            <a class="btn btn-primary mr-2 my-sm-0 active" href="#">Fuel Log</a>
            <a class="btn btn-primary mr-2 my-sm-0" href="./parts">Parts</a>
        </div>
    </nav>
    <div class='container container-fluid mx-auto mt-4'>
        <h1 style="font-weight: 400;">Fuel Expenses for your</h1>
        <h2 style="font-size: 2em; font-weight: bold">{Selected Vehicle Here}</h2>
        <hr>
        <div class='mt-4 mx-auto'>
            <?php


            ?>
        </div=
    </div>
</body>
</html>