<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='utf-8'>
    <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css'>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
    <script src="https://npmcdn.com/preact@8.2.6/dist/preact.js"></script>
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
    <div id="vehicles"></div>
<script>
    'use strict';

    const { Component, h, render } = window.preact;

    const apiURI = 'api/vehicles';

    const getVehicles = () => {
        return fetch(apiURI)
            .then(data => data.json());
    };

    const updateVehicle = (id, data) => {
        return fetch(`${apiURI}/${id}`, {
            method: 'PATCH',
            body: JSON.stringify(data)
        })
            .then(data => data.json());
    };

    const deleteVehicle = id => {
        return fetch(`${apiURI}/${id}`, {
            method: 'DELETE'
        })
            .then(data => data.json());
    };

    const addVehicle = (data) => {
        return fetch(`${apiURI}`, {
            method: 'POST',
            body: JSON.stringify(data)
        })
            .then(data => data.json());
    };


    /** Example classful component */
    class Vehicles extends Component {
        static getDefaultValue() {
            return {
                vehicle_id: '',
                model_year: '',
                make: '',
                model: '',
                color: '',
                license_plate_number: '',
                vin: '',
                editing: true
            }
        };

        constructor() {
            super();
            this.state = {
                vehicles: [],
                edits: null,
                newVehicle: null
            };
        }

        handleEditVehicle(id) {
            this.setState(prevState => {
                const vehicles = prevState.vehicles.map(vehicle => {
                    if (vehicle.vehicle_id === id.toString()) {
                        vehicle['editing'] = true;
                    }
                    return vehicle;
                });
                const edits = prevState.vehicles.find(vehicle => vehicle.vehicle_id == id.toString());
                return { vehicles, edits };
            });
        }

        handleCancelVehicle(id) {
            this.setState(prevState => {
                const vehicles = prevState.vehicles.map(vehicle => {
                    if (vehicle.vehicle_id === id.toString()) {
                        vehicle['editing'] = false;
                    }
                    return vehicle;
                });
                return { vehicles };
            });
        }

        handleSaveVehicle() {
            updateVehicle(this.state.edits.vehicle_id, this.state.edits)
                .then(data => {
                    if (!data.success) {
                        return false;
                    }
                    this.setState(prevState => {
                        const vehicles = prevState.vehicles.map(vehicle => {
                            if (vehicle.vehicle_id === prevState.edits.vehicle_id) {
                                return Object.assign({}, prevState.edits, { editing: false });
                            }
                            return vehicle;
                        });
                        return { vehicles, edits: null };
                    });
                });
        }

        handleDeleteVehicle(id) {
            deleteVehicle(id)
                .then(data => {
                    if (!data.success) {
                        return false;
                    }
                    this.setState(prevState => {
                        const vehicles = prevState.vehicles.filter(vehicle => vehicle.vehicle_id !== id);
                        return {
                            vehicles
                        };
                    })
                });
        }

        handleUpdateEdits(field, value) {
            this.setState(prevState => {
                const edits = {...prevState.edits};
                edits[field] = value;
                return {
                    edits
                };
            })
        }

        handleToggleNewVehicle() {
            this.setState(prevState => {
                if (prevState.newVehicle == null) {
                    return {
                        newVehicle: Vehicles.getDefaultValue()
                    };
                }
                return {
                    newVehicle: null
                };
            });
        }

        handleAddVehicle() {
            addVehicle(this.state.newVehicle)
                .then(response => {
                    if (response.success) {
                        this.setState(prevState => {
                            const vehicles = [...prevState.vehicles];
                            const newVehicle = {...prevState.newVehicle};
                            newVehicle.editing = false;
                            newVehicle.vehicle_id = response.id;
                            vehicles.push(newVehicle);
                            return { vehicles, newVehicle: null };
                        });
                    }
                })
        }

        handleUpdateNewEdits(field, value) {
            this.setState(prevState => {
                const newVehicle = { ...prevState.newVehicle };
                newVehicle[field] = value;
                return {
                    newVehicle
                };
            });
        }

        componentDidMount() {
            getVehicles()
                .then(vehicles => {
                    this.setState({
                        vehicles: vehicles.map(vehicle => Object.assign({}, { editing: false }, vehicle))
                    })
                })
                .catch(err => {
                    console.error(err);
                });
        }


        render(props, state) {
            const vehicleList = this.state.vehicles.map(vehicle => h(Vehicle, {
                vehicle,
                edits: this.state.edits,
                handleEditVehicle: this.handleEditVehicle.bind(this),
                handleSaveVehicle: this.handleSaveVehicle.bind(this),
                handleDeleteVehicle: this.handleDeleteVehicle.bind(this),
                handleCancelVehicle: this.handleCancelVehicle.bind(this),
                handleUpdateEdits: this.handleUpdateEdits.bind(this),
            }));
            const newVehicle = state.newVehicle !== null ? h(Vehicle, {
                vehicle: state.newVehicle,
                edits: this.state.edits,
                handleEditVehicle: this.handleEditVehicle.bind(this),
                handleSaveVehicle: this.handleAddVehicle.bind(this),
                handleDeleteVehicle: this.handleToggleNewVehicle.bind(this),
                handleCancelVehicle: this.handleToggleNewVehicle.bind(this),
                handleUpdateEdits: this.handleUpdateNewEdits.bind(this),
            }) : null;

            return (
                h('div', { class: 'container container-fluid mx-auto mt-5 mb-5 px-4 py-4'},[
                    h('div', { class: 'float-right mr-2 mt-2'},
                        h('button', { type: 'button',
                            class: 'btn btn-success add',
                            onClick: this.handleToggleNewVehicle.bind(this)
                        }, state.newVehicle === null ? 'Add Vehicle' : 'Cancel')
                    ),
                    h('h1', { style: 'font-weight: 400px;'}, 'Your Vehicles'),
                    h('hr'),
                    newVehicle,
                    h('div', {class: 'mt-4 mx-auto'}, vehicleList)
                ])
            );
        }
    }

    const Vehicle = ({ vehicle, edits, handleEditVehicle, handleSaveVehicle, handleDeleteVehicle, handleCancelVehicle, handleUpdateEdits }) => {
        return (
            h('div', { class: 'vehicle' }, [
                h(VehicleLink, { vehicle }),
                h(VehiclePanel, { vehicle, edits, handleEditVehicle, handleSaveVehicle, handleDeleteVehicle, handleCancelVehicle, handleUpdateEdits })
            ])
        );
    };

    const VehicleLink = ({ vehicle }) => {
        const { vehicle_id, model_year: year, make, model } = vehicle;
        const title = `${year} ${make} ${model}`;
        return (
            h('a', { id: 'col', href: `#veh-${vehicle_id}`, 'data-toggle': 'collapse' },
                h('div', { class: 'list-group-item fntbgr'}, title)
            )
        );
    };

    const VehiclePanel = ({ vehicle, edits, handleEditVehicle, handleSaveVehicle, handleDeleteVehicle, handleCancelVehicle, handleUpdateEdits }) => {
        const {
            vehicle_id,
            model_year,
            make,
            model,
            color,
            license_plate_number,
            vin,
            editing
        } = edits && edits.vehicle_id === vehicle.vehicle_id ? edits : vehicle;
        const
        const saveVehicle = () => {
            if (validateInputs()) {
                handleSaveVehicle(vehicle_id, edits);
            }
        };
        return (
            h('div', { class: `panel-collapse collapse ${ editing ? 'show': ''}`, id: `veh-${vehicle_id}`, 'data-id': vehicle_id }, [
                h('ul', { class: 'list-group' }, [
                    h(VehicleDetail, { title: 'Year', field: 'model_year', value: model_year, editing, handleUpdateEdits, }),
                    h(VehicleDetail, { title: 'Make', field: 'make', value: make, editing, handleUpdateEdits, }),
                    h(VehicleDetail, { title: 'Model', field: 'model', value: model, editing, handleUpdateEdits, }),
                    h(VehicleDetail, { title: 'Color', field: 'color', value: color, editing, handleUpdateEdits, }),
                    h(VehicleDetail, { title: 'License Plate', field: 'license_plate_number', value: license_plate_number, editing, handleUpdateEdits }),
                    h(VehicleDetail, { title: 'VIN', field: 'vin', value: vin, editing, handleUpdateEdits }),
                ]),
                h(ButtonContainer, { vehicle_id, editing,  handleEditVehicle, saveVehicle, handleDeleteVehicle, handleCancelVehicle }, )
            ])
        )
    };

    // const validators = {
    //     required: value => !!value,
    //     number: value => !num
    // };

    const VehicleDetail = ({ title, field, value, editing, handleUpdateEdits }) => {
        let content;
        if (!editing) {
            content = h('span', {class: 'bold', 'data-field': field}, value);
        } else {
            content = h('input', { class: 'form-control bold', 'data-field': field, value, onChange: e => handleUpdateEdits(field, e.target.value) });
        }
        return (
            h('li', { class: 'list-group-item fntbgr blk' }, [
                `${title}: `,
                content
            ])
        )
    };

    const ButtonContainer = ({ vehicle_id, editing, handleEditVehicle, saveVehicle, handleDeleteVehicle, handleCancelVehicle }) => {
        const classList = 'btn btn-success ml-4 mt-2 mb-3';
        if (editing) {
            return (
                h('div', {class: 'toggle'}, [
                    h('button', { class: 'btn btn-success ml-4 mt-2 mb-3', onClick: () => saveVehicle() }, 'Save'),
                    h('button', { class: 'btn btn-warning ml-2 mt-2 mb-3', onClick: () => handleCancelVehicle(vehicle_id) }, 'Cancel'),
                    h('button', { class: 'btn btn-danger ml-2 mt-2 mb-3', onClick: () => handleDeleteVehicle(vehicle_id) }, 'Delete'),
                ])
            );
        } else {
            return (
                h('div', null, [
                    h('button', { type: 'button', class: 'btn btn-success mb-3 mt-2 ml-4', onClick: () => handleEditVehicle(vehicle_id) }, 'Edit'),
                    h('button', { type: 'button', class: 'btn btn-primary mt-2 mb-3 ml-2' }, 'Service History'),
                    h('button', { type: 'button', class: 'btn btn-info mt-2 mb-3 ml-2' }, 'Fuel Log'),
                    h('button', { type: 'button', class: 'btn btn-dark mt-2 mb-3 ml-2' }, 'Parts'),
                ])
            )
        }
    }

    render(h(Vehicles), document.getElementById('vehicles'));
</script>
</body>
</html>