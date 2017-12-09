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
        <button type="button" class="btn btn-success add">Add</button>
    </div>
    <h1 style="font-weight: 400;">Your Vehicles</h1>
    <hr>
    <div class='mt-4 mx-auto'>
        <!-- This is just example data until we pull stuff from the db -->
        <div class="vehicle">
            <div class="list-group list-group-hover ">
                <div class="panel-collapse collapse add-container" style="display:none;">
                    <ul class="list-group">
                        <li class="list-group-item fntbgr blk">
                            Year: <span class="bold form-control" data-field="model_year" contenteditable="true"></span>
                        </li>
                        <li class="list-group-item fntbgr blk">
                            Make: <span class="bold form-control" data-field="make" contenteditable="true"></span>
                        </li>
                        <li class="list-group-item fntbgr blk">
                            Model: <span class="bold form-control" data-field="model" contenteditable="true"></span>
                        </li>
                        <li class="list-group-item fntbgr blk">
                            Color: <span class="bold form-control" data-field="color" contenteditable="true"></span>
                        </li>
                        <li class="list-group-item fntbgr blk">
                            License Plate: <span class="bold form-control" data-field="license_plate_number" contenteditable="true"></span>
                        </li>
                        <li class="list-group-item fntbgr blk">
                            VIN: <span class="bold form-control" data-field="vin" contenteditable="true"></span>
                        </li>
                    </ul>
                    <div class="float-right">
                        <button type="button" class="btn btn-success create">Create</button>
                        <button type="button" class="btn btn-success cancel-add">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
            <?php foreach ($vehicles as $key => $vehicle) { ?>
                <div class="vehicle">
                    <a data-toggle="collapse" id="col" href="#veh<?php echo $key ?>">
                        <div class="list-group-item fntbgr">
                            <?php echo $vehicle['model_year'] . ' ' . $vehicle['make'] . ' ' . $vehicle['model'] ?>
                        </div>
                    </a>
                    <div class="panel-collapse collapse" id="veh<?php echo $key ?>" data-id="<?php echo $vehicle['vehicle_id'] ?>">
                        <ul class="list-group">
                            <li class="list-group-item fntbgr blk">
                                Year: <span class="bold" data-field="model_year"><?php echo $vehicle['model_year'] ?></span>
                            </li>
                            <li class="list-group-item fntbgr blk">
                                Make: <span class="bold" data-field="make"><?php echo $vehicle['make'] ?></span>
                            </li>
                            <li class="list-group-item fntbgr blk">
                                Model: <span class="bold" data-field="model"><?php echo $vehicle['model'] ?></span>
                            </li>
                            <li class="list-group-item fntbgr blk">
                                Color: <span class="bold" data-field="color"><?php echo $vehicle['color'] ?></span>
                            </li>
                            <li class="list-group-item fntbgr blk">
                                License Plate: <span class="bold" data-field="license_plate_number"><?php echo $vehicle['license_plate_number'] ?></span>
                            </li>
                            <li class="list-group-item fntbgr blk">
                                VIN: <span class="bold" data-field="vin"><?php echo $vehicle['vin'] ?></span>
                            </li>
                        </ul>
                        <div class="toggle">
                            <input type="button" class="btn btn-success edit" value="Edit"/>
                            <input type="button" onclick="window.location.href='_blank'" class="btn btn-primary" value="Service History"/>
                            <input type="button" onclick="window.location.href='_blank'" class="btn btn-info" value="Fuel Log"/>
                            <input type="button" onclick="window.location.href='_blank'" class="btn btn-dark" value="Parts"/>
                        </div>
                        <div class="toggle" style="display:none;">
                            <button type="button" class="btn btn-success ml-4 mt-2 mb-3 save" data-id="<?php echo $vehicle['vehicle_id'] ?>">
                                Save
                            </button>
                            <button type="button" class="btn btn-warning ml-2 mt-2 mb-3 cancel" data-id="<?php echo $vehicle['vehicle_id'] ?>">
                                Cancel
                            </button>
                            <button type="button" class="btn btn-danger ml-2 mt-2 mb-3 delete" data-id="<?php echo $vehicle['vehicle_id'] ?>">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<script>
    (function () {

        const toggleInputs = $panel => {
            $panel.find('span').each(function (i, el) {
                console.log();
                el.setAttribute('contenteditable', $(el).prop('contenteditable') !== 'true');
                el.classList.toggle("form-control");
            });
            $panel.find('.toggle').toggle();
        }

        const updateVehicle = e => {
            const target = e.target;
            const $parent = $(target).parents('.vehicle');
            const data = {};
            $parent.find('span[data-field]').each((i, el) => {
                data[el.dataset.field] = el.innerText;
            });

            fetch(`vehicles/${target.dataset.id}`, {
                method: 'PATCH',
                body: JSON.stringify(data)
            })
                .then(data => data.json())
                .then(data => {
                    toggleInputs($parent);
                })
                .catch(err => {
                    console.error(err);
                });
        };

        const deleteVehicle = e => {
            const target = e.target;
            const $parent = $(target).parents('.vehicle');

            fetch(`vehicles/${target.dataset.id}`, {
                method: 'DELETE'
            })
                .then(data => data.json())
                .then(data => {
                    if (data.success) {
                        $parent.remove();
                    }
                })

        };

        const toggleAddSection = e => {
            $('.add-container').toggle();
            $('.add').html($('.add').html() === 'Cancel' ? 'Add' : 'Cancel');
        };

        const createVehicle = e => {
            const target = e.target;
            const $parent = $(target).parents('.vehicle');
            const data = {};
            $parent.find('span[data-field]').each((i, el) => {
                data[el.dataset.field] = el.innerText;
            });

            console.log(data);

            fetch(`vehicles`, {
                method: 'POST',
                body: JSON.stringify(data)
            })
                .then(data => data.json())
                .then(data => {
                    toggleInputs($parent);
                })
                .catch(err => {
                    console.error(err);
                });
        }

        $('.create').on('click', createVehicle);

        $('.add').on('click', toggleAddSection);
        $('.cancel-add').on('click', toggleAddSection);

        $('.save').on('click', updateVehicle);
        $('.delete').on('click', deleteVehicle);

        $('.edit').on("click", function (e) {
            const $parent = $(e.target).parents('.vehicle');
            toggleInputs($parent);
        });
    })();
</script>
</body>
</html>