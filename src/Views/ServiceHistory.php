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
    <a href='./' class='display-4 navbar-brand ml-2' style='font-size: 1.7em;'>Vehicle Service History Tracking System</a>
    <div class='float-right text-right'>
        <a class="btn btn-primary mx-2 my-sm-0" href="#">Vehicles</a>
        <a class="btn btn-primary mr-2 my-sm-0 active" href="./history">Service History</a>
        <a class="btn btn-primary mr-2 my-sm-0" href="./fuel">Fuel Log</a>
        <a class="btn btn-primary mr-2 my-sm-0" href="./parts">Parts</a>
    </div>
</nav>
<div id="services"></div>
<script>
    'use strict';

    const { Component, h, render } = window.preact;

    const url = new URL(window.location);
    const apiURI = url.pathname.replace('/vehicles', '/api/vehicles');

    const getServices = () => {
        return fetch(apiURI)
            .then(data => data.json());
    };

    const updateService = (id, data) => {
        return fetch(`${apiURI}/${id}`, {
            method: 'PATCH',
            body: JSON.stringify(data)
        })
            .then(data => data.json());
    };

    const deleteService = id => {
        return fetch(`${apiURI}/${id}`, {
            method: 'DELETE'
        })
            .then(data => data.json());
    };

    const addService = (data) => {
        console.log(data);
        return fetch(`${apiURI}`, {
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
            }
        };

        constructor() {
            super();
            this.state = {
                services: [],
                newService: null
            };
        }

        handleSaveService(service, newService) {
            updateService(service.vehicle_id, newService)
                .then(data => {
                    if (!data.success) {
                        return false;
                    }
                    this.setState(prevState => {
                        const services = prevState.services.map(s => {
                            if (s.vehicle_id === newService.vehicle_id) {
                                return newService;
                            }
                            return s;
                        });
                        return { services };
                    });
                });
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
                });
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
            addService(data)
                .then(response => {
                    if (response.success) {
                        this.setState(prevState => {
                            const services = [...prevState.services];
                            const newService = {...data};
                            newService.editing = false;
                            newService.vehicle_id = response.id;
                            services.push(newService);
                            return { services, newService: null };
                        });
                    }
                })
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


        render(props, state) {
            const serviceList = this.state.services.map(service => h(Service, {
                service,
                handleSaveService: this.handleSaveService.bind(this),
                handleDeleteService: this.handleDeleteService.bind(this)
            }));
            const newService = state.newService !== null ? h(Service, {
                service: state.newService,
                handleSaveService: this.handleAddService.bind(this),
                handleDeleteService: this.handleToggleNewService.bind(this)
            }) : null;

            return (
                h('div', { class: 'container container-fluid mx-auto mt-5 mb-5 px-4 py-4'},[
                    h('div', { class: 'float-right mr-2 mt-2'},
                        h('button', { type: 'button',
                            class: 'btn btn-success add',
                            onClick: this.handleToggleNewService.bind(this)
                        }, state.newService === null ? 'Add Service' : 'Cancel')
                    ),
                    h('h1', { style: 'font-weight: 400px;'}, 'Your Services'),
                    h('hr'),
                    newService,
                    h('div', {class: 'mt-4 mx-auto'}, serviceList)
                ])
            );
        }
    }

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
            service: service_title,
        } = service;
        return (
            h('a', { id: 'col', href: `#veh-${service_id}`, 'data-toggle': 'collapse' },
                h('div', { class: 'list-group-item fntbgr'}, service_title)
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
            const newService = this.props.service.editing == true;
            const editing = newService;
            const isValid = newService ? false : true;
            const edits = editing ? { ...this.props.service } : null;
            this.setState({
                edits,
                editing,
                isValid,
                newService
            });
        }

        handleEditService() {
            this.setState({
                edits: {...this.props.service},
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
                const errorMessages = { ...prevState.errorMessages };
                const edits = {...prevState.edits};

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

            return (
                h('div', {
                    class: `panel-collapse collapse ${newService ? 'show' : ''}`,
                    id: `veh-${service_id}`,
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
                            type: 'text',
                            errorMessage: errorMessages['odometer'],
                            onChange: this.onChange.bind(this)
                        }),
                        h(ServiceDetail, {
                            title: 'Cost',
                            field: 'cost',
                            value: cost,
                            editing,
                            type: 'text',
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


    const ServiceDetail = ({ title, field, value, editing, type, min, max, onChange, errorMessage }) => {
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

    const ButtonContainer = ({ service_id, editing, isValid, handleEditService, handleSaveService, handleDeleteService, handleCancelService }) => {
        if (editing) {
            return (
                h('div', {class: 'toggle'}, [
                    h('button', { class: 'btn btn-success ml-4 mt-2 mb-3', disabled: !isValid, onClick: handleSaveService }, 'Save'),
                    h('button', { class: 'btn btn-warning ml-2 mt-2 mb-3', onClick: handleCancelService }, 'Cancel'),
                    h('button', { class: 'btn btn-danger ml-2 mt-2 mb-3', onClick: () => handleDeleteService(service_id) }, 'Delete'),
                ])
            );
        } else {
            return (
                h('div', null, [
                    h('button', { type: 'button', class: 'btn btn-success mb-3 mt-2 ml-4', onClick: () => handleEditService(service_id) }, 'Edit'),
                    h('button', { type: 'button', class: 'btn btn-primary mt-2 mb-3 ml-2' }, 'Service History'),
                    h('button', { type: 'button', class: 'btn btn-info mt-2 mb-3 ml-2' }, 'Fuel Log'),
                    h('button', { type: 'button', class: 'btn btn-dark mt-2 mb-3 ml-2' }, 'Parts'),
                ])
            )
        }
    }

    render(h(Services), document.getElementById('services'));
</script>
</body>
</html>