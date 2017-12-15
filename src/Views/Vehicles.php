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
            color: #28a745;
        }
        input.error {
            box-shadow: 0 0 3px 2px rgba(255,0,0,.6);
        }
        span.error {
            color: red;
            font-size: 16px;
            font-weight: bold;
            font-style: italic;
        }
    </style>
</head>
<body>
    <nav class='navbar navbar-expand-xl navbar-dark bg-primary sticky-top'>
        <a href='..' class='display-4 navbar-brand ml-2' style='font-size: 1.7em;'>Vehicle Service History Tracking System</a>
        <div class='float-right text-right'>
            <a class="btn btn-primary mx-2 my-sm-0 active" href="#">Vehicles</a>
            <a class="btn btn-primary mx-2 my-sm-0" href="javascript:logout()">Logout</a>
<!--            <a class="btn btn-primary mr-2 my-sm-0" href="./history">Service History</a>-->
<!--            <a class="btn btn-primary mr-2 my-sm-0" href="./fuel">Fuel Log</a>-->
<!--            <a class="btn btn-primary mr-2 my-sm-0" href="./parts">Parts</a>-->
        </div>
    </nav>
    <div id="vehicles"></div>
<script>
    'use strict';

    const { Component, h, render } = window.preact;

    const spreadObject = obj => Object.keys(obj).reduce((prev, key) => Object.assign(prev, { [key]: obj[key] }), {});

    const url = new URL(window.location);
    const baseURI = url.pathname.endsWith('/') ? url.pathname.substr(0, url.pathname.length - 1) : url.pathname;
    const apiURI = baseURI.replace('/vehicles', '/api/vehicles');

    const logout = () => {
        sessionStorage.removeItem('token');
        window.location = '../';
    };

    sessionStorage.setItem('token', '<?php echo $token ?>');

    let token;

    const getToken = () => {
        if (token === undefined) {
            token = sessionStorage.getItem('token');
        }
        return token;
    };

    const fetchWithToken = (uri, config = null) => {
        const token = getToken();
        const headerConfig = Object.assign({}, config || {}, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        console.log(uri);
        return fetch(uri, headerConfig);
    };

    const getVehicles = () => {
        return fetchWithToken(apiURI)
            .then(data => data.json());
    };

    const updateVehicle = (id, data) => {
        console.log(apiURI);
        return fetchWithToken(`${apiURI}/${id}`, {
            method: 'PATCH',
            body: JSON.stringify(data)
        })
            .then(data => data.json());
    };

    const deleteVehicle = id => {
        return fetchWithToken(`${apiURI}/${id}`, {
            method: 'DELETE'
        })
            .then(data => data.json());
    };

    const addVehicle = (data) => {
        return fetchWithToken(`${apiURI}`, {
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
                newVehicle: null
            };
        }

        componentDidMount() {
            getVehicles()
                .then(vehicles => {
                    this.setState({
                        vehicles
                    });
                })
                .catch(err => {
                    console.error(err);
                });
        }

        handleSaveVehicle(vehicle, newVehicle) {
            updateVehicle(vehicle.vehicle_id, newVehicle)
                .then(data => {
                    if (!data.success) {
                        return false;
                    }
                    this.setState(prevState => {
                        const vehicles = prevState.vehicles.map(v => {
                            if (v.vehicle_id === newVehicle.vehicle_id) {
                                return newVehicle;
                            }
                            return v;
                        });
                        return { vehicles };
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

        handleAddVehicle(vehicle, data) {
            addVehicle(data)
                .then(response => {
                    if (response.success) {
                        this.setState(prevState => {
                            const vehicles = [...prevState.vehicles];
                            const newVehicle = spreadObject(data);
                            newVehicle.editing = false;
                            newVehicle.vehicle_id = response.id;
                            vehicles.push(newVehicle);
                            return { vehicles, newVehicle: null };
                        });
                    }
                })
        }

        render(props, { vehicles, newVehicle }) {
            const vehicleList = vehicles.map(vehicle => h(Vehicle, {
                vehicle,
                handleSaveVehicle: this.handleSaveVehicle.bind(this),
                handleDeleteVehicle: this.handleDeleteVehicle.bind(this)
            }));
            const newVehicleComponent = newVehicle !== null ? h(Vehicle, {
                vehicle: newVehicle,
                handleSaveVehicle: this.handleAddVehicle.bind(this),
                handleDeleteVehicle: this.handleToggleNewVehicle.bind(this)
            }) : null;

            return (
                h('div', { class: 'container container-fluid mx-auto mt-5 mb-5 px-4 py-4'},[
                    h('div', { class: 'float-right mr-2 mt-2'},
                        h('button', { type: 'button',
                            class: 'btn btn-success add',
                            onClick: this.handleToggleNewVehicle.bind(this)
                        }, newVehicle === null ? 'Add Vehicle' : 'Cancel')
                    ),
                    h('h1', { style: { fontWeight: '400' }}, 'Your Vehicles'),
                    h('hr'),
                    newVehicleComponent,
                    h('div', {class: 'mt-4 mx-auto'}, vehicleList)
                ])
            );
        }
    }

    const Vehicle = ({ vehicle, handleSaveVehicle, handleDeleteVehicle }) => {
        return (
            h('div', { class: 'vehicle' }, [
                h(VehicleLink, { vehicle }),
                h(VehiclePanel, { vehicle, handleSaveVehicle, handleDeleteVehicle})
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

    class VehiclePanel extends Component {
        constructor() {
            super();
            this.state = {
                errorMessages: {
                    model_year: '',
                    make: '',
                    model: '',
                    color: '',
                    license_plate_number: '',
                    vin: ''
                },
                edits: null,
                editing: false,
                isValid: true,
                newVehicle: false
            };
        }

        componentDidMount() {
            const newVehicle = this.props.vehicle.editing == true;
            const editing = newVehicle;
            const isValid = newVehicle ? false : true;
            const edits = editing ? spreadObject(this.props.vehicle): null;
            this.setState({
                edits,
                editing,
                isValid,
                newVehicle
            });
        }

        handleEditVehicle() {
            this.setState({
                edits: spreadObject(this.props.vehicle),
                editing: true
            });
        }

        handleCancelVehicle() {
            if (this.state.newVehicle) {
                return this.props.handleDeleteVehicle();
            }
            this.setState({ edits: null, editing: false });
        }

        onChange(field, input) {
            this.setState(prevState => {
                const errorMessages = spreadObject(prevState.errorMessages);
                const edits = spreadObject(prevState.edits);

                const isValid = input.validity.valid;
                if (isValid) {
                    input.classList.remove('error');
                } else {
                    input.classList.add('error');
                }
                errorMessages[field] = input.validationMessage;
                edits[field] = input.value;

                return {
                    errorMessages,
                    isValid,
                    edits
                };
            });
        }

        saveVehicle() {
            if (this.state.isValid) {
                this.props.handleSaveVehicle(this.props.vehicle, this.state.edits);
                this.setState({
                    editing: false
                });
            }
        }


        render({ vehicle, handleDeleteVehicle },
               { errorMessages, isValid, newVehicle, editing, edits }) {
            const {
                vehicle_id,
                model_year,
                make,
                model,
                color,
                license_plate_number,
                vin
            } = editing ? edits : vehicle;

            return (
                h('div', {
                    class: `panel-collapse collapse ${newVehicle ? 'show' : ''}`,
                    id: `veh-${vehicle_id}`,
                    'data-id': vehicle_id
                }, [
                    h('ul', {class: 'list-group'}, [
                        h(VehicleDetail, {
                            title: 'Year',
                            field: 'model_year',
                            value: model_year,
                            editing,
                            type: 'number',
                            min: 1900,
                            max: (new Date).getFullYear() + 2,
                            errorMessage: errorMessages['model_year'],
                            onChange: this.onChange.bind(this)
                        }),
                        h(VehicleDetail, {
                            title: 'Make',
                            field: 'make',
                            value: make,
                            editing,
                            type: 'text',
                            errorMessage: errorMessages['make'],
                            onChange: this.onChange.bind(this)
                        }),
                        h(VehicleDetail, {
                            title: 'Model',
                            field: 'model',
                            value: model,
                            editing,
                            type: 'text',
                            errorMessage: errorMessages['model'],
                            onChange: this.onChange.bind(this)
                        }),
                        h(VehicleDetail, {
                            title: 'Color',
                            field: 'color',
                            value: color,
                            editing,
                            type: 'text',
                            errorMessage: errorMessages['color'],
                            onChange: this.onChange.bind(this)
                        }),
                        h(VehicleDetail, {
                            title: 'License Plate',
                            field: 'license_plate_number',
                            value: license_plate_number,
                            editing,
                            type: 'text',
                            errorMessage: errorMessages['license_plate'],
                            onChange: this.onChange.bind(this)
                        }),
                        h(VehicleDetail, {
                            title: 'VIN',
                            field: 'vin',
                            value: vin,
                            editing,
                            type: 'text',
                            onChange: this.onChange.bind(this)
                        }),
                    ]),
                    h(ButtonContainer, {
                        vehicle_id,
                        editing,
                        isValid,
                        handleEditVehicle: this.handleEditVehicle.bind(this),
                        saveVehicle: this.saveVehicle.bind(this),
                        handleDeleteVehicle,
                        handleCancelVehicle: this.handleCancelVehicle.bind(this)
                    })
                ])
            )
        }
    }


    const VehicleDetail = ({ title, field, value, editing, type, min, max, onChange, errorMessage }) => {
        const errorSpan = errorMessage ? (
            h('span', { class: 'error' }, errorMessage)
        ) : null;

        let content;
        if (!editing) {
            content = h('span', { class: 'bold' }, value);
        } else {
            content = h('input', {
                class: 'form-control bold',
                value,
                type,
                min,
                max,
                required: 'required',
                onChange: e => onChange(field, e.target),});
        }
        return (
            h('li', { class: 'list-group-item fntbgr blk' }, [
                `${title}: `,
                errorSpan,
                content
            ])
        )
    };

    const ButtonContainer = ({ vehicle_id, editing, isValid, handleEditVehicle, saveVehicle, handleDeleteVehicle, handleCancelVehicle }) => {
        if (editing) {
            return (
                h('div', {class: 'toggle'}, [
                    h('button', { class: 'btn btn-success ml-4 mt-2 mb-3', onClick: () => saveVehicle() }, 'Save'),
                    h('button', { class: 'btn btn-warning ml-2 mt-2 mb-3', onClick: () => handleCancelVehicle(vehicle_id) }, 'Cancel'),
                ])
            );
        } else {
            return (
                h('div', null, [
                    h('button', { type: 'button', class: 'btn btn-success mb-3 mt-2 ml-4', onClick: () => handleEditVehicle(vehicle_id) }, 'Edit'),
                    h('button', { class: 'btn btn-danger mt-2 mr-3 float-right', onClick: () => handleDeleteVehicle(vehicle_id) }, 'Delete'),
                    h('a', { class: 'btn btn-primary mt-2 mb-3 ml-2', href: `${baseURI}/${vehicle_id}/history?token=${getToken()}` }, 'Service History'),
                    h('a', { class: 'btn btn-info mt-2 mb-3 ml-2', href: `${baseURI}/${vehicle_id}/fuel?token=${getToken()}` }, 'Fuel Log'),
                    h('a', { class: 'btn btn-dark mt-2 mb-3 ml-2', href: `${baseURI}/${vehicle_id}/parts?token=${getToken()}` }, 'Parts'),
                ])
            )
        }
    }

    render(h(Vehicles), document.getElementById('vehicles'));
</script>
</body>
</html>