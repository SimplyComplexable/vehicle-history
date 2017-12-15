<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='utf-8'>
    <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css'>
    <title style=''>Home | VSHTS</title>
</head>
<body>
<nav class='navbar navbar-expand-xl navbar-dark bg-primary sticky-top'>
    <a href='./' class='display-4 navbar-brand ml-2' style='font-size: 1.7em;'>Vehicle Service History Tracking System</a>
    <div class='float-right text-right'>
        <a class="text-light btn btn-primary mr-2 my-sm-0" href="./login">Login</a>
        <a class="text-light btn btn-primary mr-2 my-sm-0" href="./register">Register</a>
    </div>
</nav>
<div class='container-fluid px-0'>
    <div class='mt-4 mx-auto alert alert-success px-4 py-4 ml-4' style='width: 72%'><h2 class='display-4 alert-heading pb-2'>Welcome!</h2>
        <h3 class='lead mb-4' style="font-size: x-large">This website allows you to track service history/maintenance records for your vehicle(s). You can add vehicles to your account, input maintenance records (i.e. oil changed, tires rotated, etc.), input fuel receipts, input parts, and view all the maintenance records, fuel expenses, and parts.</h3>
        <h3 class='lead mb-4' style="font-size: x-large">To begin, simply create an account by clicking register. Then fill in the fields and you can begin adding vehicles and tracking info about them.</h3>
        <h3 class='lead' style="font-size: x-large">If you already have an account, just login.</h3>
    </div>
    </div>
</body>

<script>
    const token = sessionStorage.getItem('token');
    if (token) {
        const nav = document.querySelector('.navbar .float-right');
        nav.innerHTML = `
            <div class='float-right text-right'>
                <a class="text-light btn btn-primary mr-2 my-sm-0" href="./vehicles/?token=${token}">Vehicles</a>
            </div>
        `;
    }
</script>
</html>