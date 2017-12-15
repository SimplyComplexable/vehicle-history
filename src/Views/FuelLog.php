<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='utf-8'>
    <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css'>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
    <script src="https://npmcdn.com/preact@8.2.6/dist/preact.js"></script>
    <title>Fuel Log | VSHTS</title>
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
        .bolder {
            font-weight: 700;
            color: #007bff;
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
    <a href='../../' class='display-4 navbar-brand ml-2' style='font-size: 1.7em;'>Vehicle Service History Tracking System</a>
    <div class='float-right text-right'>
        <a class="btn btn-primary mx-2 my-sm-0" href="..?token=<?php echo $token ?>">Vehicles</a>
        <a class="btn btn-primary mx-2 my-sm-0" href="javascript:logout()">Logout</a>
<!--        <a class="btn btn-primary mr-2 my-sm-0" href="./history">Service History</a>-->
<!--        <a class="btn btn-primary mr-2 my-sm-0 active" href="./fuel">Fuel Log</a>-->
<!--        <a class="btn btn-primary mr-2 my-sm-0" href="./parts">Parts</a>-->
    </div>
</nav>
<div id="fuels"></div>
<script>
    'use strict';


    const { Component, h, render } = window.preact;

    const spreadObject = obj => Object.keys(obj).reduce((prev, key) => Object.assign(prev, { [key]: obj[key] }), {});

    const url = new URL(window.location);
    const apiURI = url.pathname.replace('/vehicles', '/api/vehicles');

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
        return fetch(uri, headerConfig);
    };

    const getFuels = () => {
        return fetchWithToken(apiURI)
            .then(data => data.json());
    };

    const updateFuel = (id, data) => {
        return fetchWithToken(`${apiURI}/${id}`, {
            method: 'PATCH',
            body: JSON.stringify(data)
        })
            .then(data => data.json());
    };

    const deleteFuel = id => {
        return fetchWithToken(`${apiURI}/${id}`, {
            method: 'DELETE'
        })
            .then(data => data.json());
    };

    const addFuel = (data) => {
        return fetchWithToken(`${apiURI}`, {
            method: 'POST',
            body: JSON.stringify(data)
        })
            .then(data => data.json());
    };


    /** Example classful component */
    class Fuels extends Component {
        static getDefaultValue() {
            return {
                fuel_id: '',
                date: '',
                odometer: '',
                distance: '',
                volume: '',
                location: '',
                cost: '',
                mpg: '',
                editing: true
            };
        };

        constructor() {
            super();
            this.state = {
                fuels: [],
                newFuel: null,
                filter: _ => true
            };
        }

        handleSaveFuel(fuel, newFuel) {
            updateFuel(fuel.fuel_id, newFuel)
                .then(data => {
                    if (!data.success) {
                        return false;
                    }
                    this.setState(prevState => {
                        const fuels = prevState.fuels.map(f => {
                            if (f.fuel_id === newFuel.fuel_id) {
                                return newFuel;
                            }
                            return f;
                        });
                        return { fuels };
                    });
                })
                .catch(err => console.error(err));
        }

        handleDeleteFuel(id) {
            deleteFuel(id)
                .then(data => {
                    if (!data.success) {
                        return false;
                    }
                    this.setState(prevState => {
                        const fuels = prevState.fuels.filter(fuel => fuel.fuel_id !== id);
                        return {
                            fuels
                        };
                    })
                })
                .catch(err => console.error(err));
        }

        handleToggleNewFuel() {
            this.setState(prevState => {
                if (prevState.newFuel == null) {
                    return {
                        newFuel: Fuels.getDefaultValue()
                    };
                }
                return {
                    newFuel: null
                };
            });
        }

        handleAddFuel(fuel, data) {
            data['vehicle_id'] = <?php echo $vehicle_id ?>;
            addFuel(data)
                .then(response => {
                    if (response.success) {
                        this.setState(prevState => {
                            const fuels = [...prevState.fuels];
                            const newFuel = spreadObject(data);
                            newFuel.editing = false;
                            newFuel.fuel_id = response.id;
                            fuels.push(newFuel);
                            return { fuels, newFuel: null };
                        });
                    }
                })
                .catch(err => console.error(err));
        }

        handleUpdateFilter(filter) {
            this.setState({
                filter
            });
        }

        componentDidMount() {
            getFuels()
                .then(fuels => {
                    this.setState({
                        fuels
                    });
                })
                .catch(err => {
                    console.error(err);
                });
        }


        render(props, { newFuel, filter }) {
            const fuelList = this.state.fuels
                .filter(filter)
                .map(fuel => h(Fuel, {
                    fuel,
                    handleSaveFuel: this.handleSaveFuel.bind(this),
                    handleDeleteFuel: this.handleDeleteFuel.bind(this)
                }));
            const newFuelComponent = newFuel !== null ? h(Fuel, {
                fuel: newFuel,
                handleSaveFuel: this.handleAddFuel.bind(this),
                handleDeleteFuel: this.handleToggleNewFuel.bind(this)
            }) : null;

            return (
                h('div', { class: 'container container-fluid mx-auto mt-5 mb-5 px-4 py-4'},[
                    h('div', { class: 'float-right mr-2 mt-2'},
                        h('button', {
                            type: 'button',
                            class: 'btn btn-success add',
                            onClick: this.handleToggleNewFuel.bind(this)
                        }, newFuel === null ? 'Add Fuel' : 'Cancel')
                    ),
                    h('h1', { style: { fontWeight: 400 }}, 'Fuel Logs for your '),
                    h('h2', { style: { fontWeight: 600 }}, '<?php echo $vehicle_title ?>'),
                    h('hr'),
                    h('div', { class: '' },
                        h('h4', null, 'Filter by: ')
                    ),
                    h(Filter, {
                        handleUpdateFilter: this.handleUpdateFilter.bind(this),
                        filter
                    }),
                    h('hr'),
                    newFuelComponent,
                    h('div', {class: 'mt-4 mx-auto'}, fuelList)
                ])
            );
        }
    }

    const Filter = ({ handleUpdateFilter, filter: defaultFilter }) => {
        const filterByYear = e => {
            const value = e.target.value;
            handleUpdateFilter(fuel => {
                if (value === '') {
                    return defaultFilter
                }
                const date = new Date(fuel.date);
                return date.getFullYear().toString().substr(0, value.length).toLowerCase() === value.toLowerCase();
            })
        };
        return (
            h('div', { class: 'row' },
                h('div', { class: 'col-md-4'}, [
                    h('div', null, 'Year'),
                    h('input', { type: 'text', class: 'form-control', onKeyUp: filterByYear })
                ])
            )
        );
    };

    const Fuel = ({ fuel, handleSaveFuel, handleDeleteFuel }) => {
        return (
            h('div', { class: 'vehicle' }, [
                h(FuelLink, { fuel }),
                h(FuelPanel, { fuel, handleSaveFuel, handleDeleteFuel})
            ])
        );
    };

    const FuelLink = ({ fuel }) => {
        const {
            fuel_id,
            date,
            location,
            volume,
        } = fuel;
        const formattedDate = formatDate(new Date(date));
        return (
            h('a', { id: 'col', href: `#ser-${fuel_id}`, 'data-toggle': 'collapse' },
                h('div', { class: 'list-group-item fntbgr'}, !volume ? null : [
                    h('span', { class: 'bolder' }, `${volume} gallons`),
                    ' at ',
                    h('span', { class: 'bolder' }, `${location}`),
                    h('div', { class: 'float-right' }, formattedDate)
                ])
            )
        );
    };

    class FuelPanel extends Component {
        constructor() {
            super();
            this.state = {
                errorMessages: {
                    fuel_id: '',
                    date: '',
                    odometer: '',
                    location: '',
                    distance: '',
                    volume: '',
                    cost: '',
                    mpg: ''
                },
                edits: null,
                editing: false,
                isValid: true,
                newFuel: false
            };
        }

        componentDidMount() {
            const newFuel = this.props.fuel.editing === true;
            const editing = newFuel;
            const isValid = !newFuel;
            const edits = editing ? spreadObject(this.props.fuel ) : null;
            this.setState({
                edits,
                editing,
                isValid,
                newFuel
            });
        }

        handleEditFuel() {
            this.setState({
                edits: spreadObject(this.props.fuel),
                editing: true
            });
        }

        handleCancelFuel() {
            if (this.state.newFuel) {
                return this.props.handleDeleteFuel();
            }
            this.setState({ edits: null, editing: false });
        }

        onKeyUp(field, input) {
            this.setState(prevState => {
                const edits = spreadObject(prevState.edits);
                edits[field] = input.value;
                edits['mpg'] = edits['distance'] / edits['volume'];
                return {
                    edits
                };
            });
        }

        onChange(field, input) {
            this.setState(prevState => {
                const errorMessages = spreadObject(prevState.errorMessages );
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

        handleSaveFuel() {
            if (this.state.isValid) {
                this.props.handleSaveFuel(this.props.fuel, this.state.edits);
                this.setState({
                    editing: false
                });
            }
        }


        render({ fuel, handleDeleteFuel },
               { errorMessages, isValid, newFuel, editing, edits }) {
            const {
                fuel_id,
                date,
                odometer,
                location,
                distance,
                volume,
                cost,
                mpg,
            } = editing ? edits : fuel;

            return (
                h('div', {
                    class: `panel-collapse collapse ${newFuel ? 'show' : ''}`,
                    id: `ser-${fuel_id}`,
                }, [
                    h('ul', {class: 'list-group'}, [
                        h(FuelDetail, {
                            title: 'Date',
                            field: 'date',
                            value: date,
                            editing,
                            type: 'date',
                            errorMessage: errorMessages['date'],
                            onChange: this.onChange.bind(this)
                        }),
                        h(FuelDetail, {
                            title: 'Volume',
                            field: 'volume',
                            value: volume,
                            editing,
                            type: 'number',
                            errorMessage: errorMessages['fuel'],
                            onKeyUp: this.onKeyUp.bind(this),
                            onChange: this.onChange.bind(this)
                        }),
                        h(FuelDetail, {
                            title: 'Odometer',
                            field: 'odometer',
                            value: odometer,
                            editing,
                            type: 'number',
                            min: 0,
                            step: 1,
                            max: 1000000,
                            errorMessage: errorMessages['odometer'],
                            onChange: this.onChange.bind(this)
                        }),
                        h(FuelDetail, {
                            title: 'Cost',
                            field: 'cost',
                            value: cost,
                            editing,
                            type: 'money',
                            min: '0.01',
                            step: '0.01',
                            max: '10000',
                            errorMessage: errorMessages['cost'],
                            onChange: this.onChange.bind(this)
                        }),
                        h(FuelDetail, {
                            title: 'Location',
                            field: 'location',
                            value: location,
                            editing,
                            type: 'text',
                            errorMessage: errorMessages['location'],
                            onChange: this.onChange.bind(this)
                        }),
                        h(FuelDetail, {
                            title: 'Distance',
                            field: 'distance',
                            value: distance,
                            editing,
                            min: 0,
                            step: 0.1,
                            max: 10000,
                            type: 'number',
                            onKeyUp: this.onKeyUp.bind(this),
                            errorMessage: errorMessages['location'],
                            onChange: this.onChange.bind(this)
                        }),
                        h(FuelDetail, {
                            title: 'MPG',
                            field: 'mpg',
                            value: mpg,
                            editing,
                            disabled: 'disabled',
                            type: 'number',
                            errorMessage: errorMessages['location'],
                            onChange: this.onChange.bind(this)
                        })
                    ]),
                    h(ButtonContainer, {
                        fuel_id,
                        editing,
                        isValid,
                        handleEditFuel: this.handleEditFuel.bind(this),
                        handleSaveFuel: this.handleSaveFuel.bind(this),
                        handleDeleteFuel,
                        handleCancelFuel: this.handleCancelFuel.bind(this)
                    })
                ])
            )
        }
    }

    const formatDate = (date = null) => {
        if (date === null) {
            date = new Date();
        } else if (!(date instanceof Date)) {
            date = new Date(date);
        }
        const print = number => number > 9 ? number : '0' + number;
        return `${print(date.getMonth() + 1)}/${print(date.getDate() + 1)}/${date.getFullYear()}`;
    };

    const formatNumber = number => {
        const parts = number.toString().split('.');
        return parts[0].split('').reverse().reduce((next, n, i) => {
            if (i !== 0 && i % 3 === 0) {
                return n + ',' + next;
            }
            return n + next;
        }, '') + '.' + (parts[1] !== undefined ? parts[1] : '00');
    };

    const FuelDetail = ({ title, field, value, editing, type, min, max, step, disabled, onChange, onKeyUp, errorMessage }) => {
        const errorSpan = errorMessage ? (
            h('span', { class: 'error' }, errorMessage)
        ) : null;

        let formattedValue = value;

        switch (type) {
            case 'date':
                formattedValue = formatDate(value);
                if (!value) {
                    const date = new Date();
                    const print = number => number > 9 ? number : '0' + number;
                    value = `${date.getFullYear()}-${print(date.getMonth() + 1)}-${print(date.getDate() + 1)}`;
                }
                break;
            case 'money':
                formattedValue = `$${value}`;
                type = 'number';
                break;
            case 'number':
                formattedValue = formatNumber(value);
                break;
        }

        let content;
        if (!editing) {
            content = h('span', { class: 'bold' }, formattedValue);
        } else {
            content = h('input', {
                class: 'form-control bold',
                value,
                type,
                min,
                step,
                max,
                disabled,
                required: 'required',
                onChange: e => onChange(field, e.target),
                onKeyUp: onKeyUp ? e => onKeyUp(field, e.target) : null
            });
        }
        return (
            h('li', { class: 'list-group-item fntbgr blk' }, [
                `${title}: `,
                errorSpan,
                content
            ])
        )
    };

    const ButtonContainer = ({ fuel_id, editing, isValid, handleEditFuel, handleSaveFuel, handleDeleteFuel, handleCancelFuel }) => {
        if (editing) {
            return (
                h('div', {class: 'toggle'}, [
                    h('button', { class: 'btn btn-success ml-4 mt-2 mb-3', disabled: !isValid, onClick: handleSaveFuel }, 'Save'),
                    h('button', { class: 'btn btn-warning ml-2 mt-2 mb-3', onClick: handleCancelFuel }, 'Cancel'),
                ])
            );
        } else {
            return (
                h('div', null, [
                    h('button', { type: 'button', class: 'btn btn-success mb-3 mt-2 ml-4', onClick: () => handleEditFuel(fuel_id) }, 'Edit'),
                    h('button', { class: 'btn btn-danger mr-3 mt-2 mb-3 float-right', onClick: () => handleDeleteFuel(fuel_id) }, 'Delete')
                ])
            )
        }
    }

    render(h(Fuels), document.getElementById('fuels'));
</script>
</body>
</html>