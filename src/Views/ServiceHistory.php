<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='utf-8'>
    <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css'>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
    <script src="https://npmcdn.com/preact@8.2.6/dist/preact.js"></script>
    <title>Service History | VSHTS</title>
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
<!--        <a class="btn btn-primary mr-2 my-sm-0 active" href="./history">Service History</a>-->
<!--        <a class="btn btn-primary mr-2 my-sm-0" href="./fuel">Fuel Log</a>-->
<!--        <a class="btn btn-primary mr-2 my-sm-0" href="./parts">Parts</a>-->
    </div>
</nav>
<div id="services"></div>
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

    const getServices = () => {
        return fetchWithToken(apiURI)
            .then(data => data.json());
    };

    const updateService = (id, data) => {
        return fetchWithToken(`${apiURI}/${id}`, {
            method: 'PATCH',
            body: JSON.stringify(data)
        })
            .then(data => data.json());
    };

    const deleteService = id => {
        return fetchWithToken(`${apiURI}/${id}`, {
            method: 'DELETE'
        })
            .then(data => data.json());
    };

    const addService = (data) => {
        return fetchWithToken(`${apiURI}`, {
            method: 'POST',
            body: JSON.stringify(data)
        })
            .then(data => data.json());
    };


    /** Example classful component */
    class Services extends Component {
        static getDefaultValue() {
            return {
                service_id: '',
                date: '',
                service: '',
                odometer: '',
                cost: '',
                location: '',
                editing: true
            };
        };

        constructor() {
            super();
            this.state = {
                services: [],
                newService: null,
                filter: _ => true
            };
        }

        handleSaveService(service, newService) {
            updateService(service.service_id, newService)
                .then(data => {
                    if (!data.success) {
                        return false;
                    }
                    this.setState(prevState => {
                        const services = prevState.services.map(s => {
                            if (s.service_id === newService.service_id) {
                                console.log(newService);
                                return newService;
                            }
                            return s;
                        });
                        return { services };
                    });
                })
                .catch(err => console.error(err));
        }

        handleDeleteService(id) {
            deleteService(id)
                .then(data => {
                    if (!data.success) {
                        return false;
                    }
                    this.setState(prevState => {
                        const services = prevState.services.filter(service => service.service_id !== id);
                        return {
                            services
                        };
                    })
                })
                .catch(err => console.error(err));
        }

        handleToggleNewService() {
            this.setState(prevState => {
                if (prevState.newService == null) {
                    return {
                        newService: Services.getDefaultValue()
                    };
                }
                return {
                    newService: null
                };
            });
        }

        handleAddService(service, data) {
            data['vehicle_id'] = <?php echo $vehicle_id ?>;
            addService(data)
                .then(response => {
                    if (response.success) {
                        this.setState(prevState => {
                            const services = [...prevState.services];
                            const newService = spreadObject(data);
                            newService.editing = false;
                            newService.vehicle_id = response.id;
                            services.push(newService);
                            return { services, newService: null };
                        });
                    }
                })
                .catch(err => console.error(err));;
        }

        handleUpdateFilter(filter) {
            this.setState({
                filter
            });
        }

        componentDidMount() {
            getServices()
                .then(services => {
                    this.setState({
                        services
                    });
                })
                .catch(err => {
                    console.error(err);
                });
        }


        render(props, { newService, filter }) {
            const serviceList = this.state.services
                .filter(filter)
                .map(service => h(Service, {
                    service,
                    handleSaveService: this.handleSaveService.bind(this),
                    handleDeleteService: this.handleDeleteService.bind(this)
                }));
            const newServiceComponent = newService !== null ? h(Service, {
                service: newService,
                handleSaveService: this.handleAddService.bind(this),
                handleDeleteService: this.handleToggleNewService.bind(this)
            }) : null;

            return (
                h('div', { class: 'container container-fluid mx-auto mt-5 mb-5 px-4 py-4'},[
                    h('div', { class: 'float-right mr-2 mt-2'},
                        h('button', {
                            type: 'button',
                            class: 'btn btn-success add',
                            onClick: this.handleToggleNewService.bind(this)
                        }, newService === null ? 'Add Service' : 'Cancel')
                    ),
                    h('h1', { style: { fontWeight: 400 }}, 'Service History for your'),
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
                    newServiceComponent,
                    h('div', {class: 'mt-4 mx-auto'}, serviceList)
                ])
            );
        }
    }

    const Filter = ({ handleUpdateFilter, filter: defaultFilter }) => {

        const filterByName = e => {
            const value = e.target.value;
            handleUpdateFilter(service => {
                if (value === '') {
                    return defaultFilter
                }
                return service.service.substr(0, value.length).toLowerCase() === value.toLowerCase();
            })
        };
        const filterByYear = e => {
            const value = e.target.value;
            handleUpdateFilter(service => {
                if (value === '') {
                    return defaultFilter
                }
                const date = new Date(service.date);
                return date.getFullYear().toString().substr(0, value.length).toLowerCase() === value.toLowerCase();
            })
        };
        const filterByLocation = e => {
            const value = e.target.value;
            handleUpdateFilter(service => {
                if (value === '') {
                    return defaultFilter
                }
                return service.location.substr(0, value.length).toLowerCase() === value.toLowerCase();
            })
        };
        return (
            h('div', { class: 'row' }, [
                h('div', { class: 'col-md-4'}, [
                    h('div', null, 'Service'),
                    h('input', { type: 'text', class: 'form-control', onKeyUp: filterByName })
                ]),
                h('div', { class: 'col-md-4'}, [
                    h('div', null, 'Year'),
                    h('input', { type: 'text', class: 'form-control', onKeyUp: filterByYear })
                ]),
                h('div', { class: 'col-md-4'}, [
                    h('div', null, 'Location'),
                    h('input', { type: 'text', class: 'form-control', onKeyUp: filterByLocation })
                ]),
            ])
        );
    };

    const Service = ({ service, handleSaveService, handleDeleteService }) => {
        return (
            h('div', { class: 'vehicle' }, [
                h(ServiceLink, { service }),
                h(ServicePanel, { service, handleSaveService, handleDeleteService})
            ])
        );
    };

    const ServiceLink = ({ service }) => {
        const {
            service_id,
            date,
            location,
            service: service_title,
        } = service;
        const formattedDate = formatDate(new Date(date));
        const title = (!service_title) ? "" : `${service_title} at ${location} on ${formattedDate}`;
        return (
            h('a', { id: 'col', href: `#ser-${service_id}`, 'data-toggle': 'collapse' },
                h('div', { class: 'list-group-item fntbgr'}, !service_title ? null :  [
                    h('span', { class: 'bolder' }, `${service_title}`),
                    ' at ',
                    h('span', { class: 'bolder' }, `${location}`),
                    h('div', { class: 'float-right' }, formattedDate)
                ])
            )
        );
    };

    class ServicePanel extends Component {
        constructor() {
            super();
            this.state = {
                errorMessages: {
                    service_id: '',
                    date: '',
                    service: '',
                    odometer: '',
                    cost: '',
                    location: '',
                    vin: ''
                },
                edits: null,
                editing: false,
                isValid: true,
                newService: false
            };
        }

        componentDidMount() {
            const newService = this.props.service.editing === true;
            const editing = newService;
            const isValid = !newService;
            const edits = editing ? spreadObject(this.props.service ) : null;
            this.setState({
                edits,
                editing,
                isValid,
                newService
            });
        }

        handleEditService() {
            this.setState({
                edits: spreadObject(this.props.service),
                editing: true
            });
        }

        handleCancelService() {
            if (this.state.newService) {
                return this.props.handleDeleteService();
            }
            this.setState({ edits: null, editing: false });
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
                console.log(edits);

                return {
                    errorMessages,
                    isValid,
                    edits
                };
            });
        }

        handleSaveService() {
            if (this.state.isValid) {
                this.props.handleSaveService(this.props.service, this.state.edits);
                this.setState({
                    editing: false
                });
            }
        }


        render({ service, handleDeleteService },
               { errorMessages, isValid, newService, editing, edits }) {
            const {
                service_id,
                date,
                service: service_title,
                odometer,
                cost,
                location
            } = editing ? edits : service;
            if (editing) {
                console.log(edits);
            }

            return (
                h('div', {
                    class: `panel-collapse collapse ${newService ? 'show' : ''}`,
                    id: `ser-${service_id}`,
                }, [
                    h('ul', {class: 'list-group'}, [
                        h(ServiceDetail, {
                            title: 'Service Date',
                            field: 'date',
                            value: date,
                            editing,
                            type: 'date',
                            errorMessage: errorMessages['date'],
                            onChange: this.onChange.bind(this)
                        }),
                        h(ServiceDetail, {
                            title: 'Service',
                            field: 'service',
                            value: service_title,
                            editing,
                            type: 'text',
                            errorMessage: errorMessages['service'],
                            onChange: this.onChange.bind(this)
                        }),
                        h(ServiceDetail, {
                            title: 'Odometer',
                            field: 'odometer',
                            value: odometer,
                            editing,
                            type: 'number',
                            min: 0,
                            max: 1000000,
                            errorMessage: errorMessages['odometer'],
                            onChange: this.onChange.bind(this)
                        }),
                        h(ServiceDetail, {
                            title: 'Cost',
                            field: 'cost',
                            value: cost,
                            editing,
                            type: 'money',
                            min: '0.00',
                            step: '0.01',
                            max: '100000',
                            errorMessage: errorMessages['cost'],
                            onChange: this.onChange.bind(this)
                        }),
                        h(ServiceDetail, {
                            title: 'Location',
                            field: 'location',
                            value: location,
                            editing,
                            type: 'text',
                            errorMessage: errorMessages['location'],
                            onChange: this.onChange.bind(this)
                        })
                    ]),
                    h(ButtonContainer, {
                        service_id,
                        editing,
                        isValid,
                        handleEditService: this.handleEditService.bind(this),
                        handleSaveService: this.handleSaveService.bind(this),
                        handleDeleteService,
                        handleCancelService: this.handleCancelService.bind(this)
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

    const ServiceDetail = ({ title, field, value, editing, type, min, max, step, onChange, errorMessage }) => {
        const errorSpan = errorMessage ? (
            h('span', { class: 'error' }, errorMessage)
        ) : null;

        let formattedValue = value;

        switch (type) {
            case 'date':
                formattedValue = formatDate(value);
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

    const ButtonContainer = ({ service_id, editing, isValid, handleEditService, handleSaveService, handleDeleteService, handleCancelService }) => {
        if (editing) {
            return (
                h('div', {class: 'toggle'}, [
                    h('button', { class: 'btn btn-success ml-4 mt-2 mb-3', disabled: !isValid, onClick: handleSaveService }, 'Save'),
                    h('button', { class: 'btn btn-warning ml-2 mt-2 mb-3', onClick: handleCancelService }, 'Cancel'),
                ])
            );
        } else {
            return (
                h('div', null, [
                    h('button', { type: 'button', class: 'btn btn-success mb-3 mt-2 ml-4', onClick: () => handleEditService(service_id) }, 'Edit'),
                    h('button', { class: 'btn btn-danger mr-3 mt-2 mb-3 float-right', onClick: () => handleDeleteService(service_id) }, 'Delete')
                ])
            )
        }
    }

    render(h(Services), document.getElementById('services'));
</script>
</body>
</html>