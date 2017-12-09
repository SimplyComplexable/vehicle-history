<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='utf-8'>
    <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css'>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
    <title>Vehicles | VSHTS</title>
    <style>
        .container{
            background-color: #fafafb;
            border: 1px solid #d3d3d3;
            border-radius: 6px;
        }
        .fntbgr{
            font-size: x-large;
            font-weight: 400;
        }
        .btn{
            cursor: pointer;
            font-size: large;
        }
        .list-group-item{
            color: #007bff;
        }
        .list-group-hover .list-group-item:hover {
            background-color: #d8ebff;
        }
        #col:hover{
            text-decoration: none;
        }
        ul{
            padding: 10px 14px 4px !important;
        }
        .blk{
            color: black;
        }
        .bold{
            font-weight: 500;
            color: #007bff;
        }
    </style>
</head>
<body>
<nav class='navbar navbar-expand-xl navbar-dark bg-primary sticky-top'>
    <a href='./' class='display-4 navbar-brand ml-2' style='font-size: 1.7em;'>Vehicle Service History Tracking System</a>
    <div class='float-right text-right'>
        <a class="btn btn-primary mx-2 my-sm-0 active" href="#">Vehicles</a>
        <a class="btn btn-primary mr-2 my-sm-0" href="./history">Service History</a>
        <a class="btn btn-primary mr-2 my-sm-0" href="./fuel">Fuel Log</a>
        <a class="btn btn-primary mr-2 my-sm-0" href="./parts">Parts</a>
    </div>
</nav>
<div class='container container-fluid mx-auto mt-5 mb-5 px-4 py-4'>
    <div class="float-right mr-2 mt-2">
        <input type="button" onclick="window.location.href='_blank'" class="btn btn-primary" value="Service History"/>
        <input type="button" onclick="window.location.href='_blank'" class="btn btn-info" value="Fuel Log"/>
        <input type="button" onclick="window.location.href='_blank'" class="btn btn-dark" value="Parts"/>
        <input type="button" id="edit" class="btn btn-success" value="Edit"/>
    </div>
    <h1 style="font-weight: 400;">Your Vehicles</h1>
    <hr>
    <div class='mt-4 mx-auto'>
        <!-- This is just example data until we pull stuff from the db -->
        <div class="list-group list-group-hover ">
            <?php foreach ($vehicles as $vehicle) { ?>
                <a data-toggle="collapse" id="col" href="#veh1">
                    <div class="list-group-item fntbgr">
                        <?php echo $vehicle['model_year'] . ' ' . $vehicle['make'] . ' ' . $vehicle['model'] ?>
                    </div>
                </a>
                <div class="panel-collapse collapse" id="veh1">
                    <ul class="list-group">
                        <li class="list-group-item fntbgr blk">
                            Year: <span class="bold"><?php echo $vehicle['model_year'] ?></span>
                        </li>
                        <li class="list-group-item fntbgr blk">
                            Make: <span class="bold"><?php echo $vehicle['make'] ?></span>
                        </li>
                        <li class="list-group-item fntbgr blk">
                            Model: <span class="bold"><?php echo $vehicle['model'] ?></span>
                        </li>
                        <li class="list-group-item fntbgr blk">
                            Color: <span class="bold"><?php echo $vehicle['color'] ?></span>
                        </li>
                        <li class="list-group-item fntbgr blk">
                            License Plate: <span class="bold"><?php echo $vehicle['license_plate_number'] ?></span>
                        </li>
                        <li class="list-group-item fntbgr blk">
                            VIN: <span class="bold"><?php echo $vehicle['vin'] ?></span>
                        </li>
                    </ul>
                    <input type="button" id="save" class="btn btn-success ml-4 mt-2 mb-3 invisible" value="Save"/>
                    <input type="button" id="cancel" class="btn btn-warning ml-2 mt-2 mb-3 invisible" value="Cancel"/>
                    <input type="button" id="delete" class="btn btn-danger ml-2 mt-2 mb-3 invisible" value="Delete"/>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<script>
    window.onload = function (ev) {
        document.getElementById("edit").addEventListener("click", function(){
            var allSpans = document.getElementsByTagName('span');
            Array.from(allSpans).forEach(function (el) {
                el.setAttribute('contenteditable', 'true');
                el.classList.add("form-control");
            });

            var saveBtns = document.getElementsByClassName('invisible');
            Array.from(saveBtns).forEach(function (el) {
                el.classList.remove('invisible');
                el.classList.add('visible');
            });
        });

        var invisibleBtns = document.getElementsByClassName("invisible");
        Array.from(invisibleBtns).forEach(function (el) {
            el.addEventListener("click", function() {
                var allSpans = document.getElementsByTagName('span');
                Array.from(allSpans).forEach(function (el) {
                    el.setAttribute('contenteditable', 'false');
                    el.classList.remove("form-control");
                });

                var saveBtns = document.getElementsByClassName('visible');
                Array.from(saveBtns).forEach(function (el) {
                    el.classList.remove('visible');
                    el.classList.add('invisible');
                });
            });
        })
    }
</script>
</body>
</html>