<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='utf-8'>
    <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css'>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
    <script src="https://npmcdn.com/preact@8.2.6/dist/preact.js"></script>
    <title>Parts | VSHTS</title>
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
    <a href='./' class='display-4 navbar-brand ml-2' style='font-size: 1.7em;'>Vehicle Service History Tracking System</a>
    <div class='float-right text-right'>
        <a class="btn btn-primary mx-2 my-sm-0" href="..">Vehicles</a>
        <a class="btn btn-primary mr-2 my-sm-0" href="./history">Service History</a>
        <a class="btn btn-primary mr-2 my-sm-0" href="./fuel">Fuel Log</a>
        <a class="btn btn-primary mr-2 my-sm-0 active" href="./parts">Parts</a>
    </div>
</nav>
<div id="parts"></div>
<script>
    'use strict';

    const { Component, h, render } = window.preact;

    const spreadObject = obj => Object.keys(obj).reduce((prev, key) => Object.assign(prev, { [key]: obj[key] }), {});

    const url = new URL(window.location);
    const apiURI = url.pathname.replace('/vehicles', '/api/vehicles');

    sessionStorage.setItem('token', '<?php echo $token ?>');

    let token;

    const getToken = () => {
        if (token === undefined) {
            token = sessionStorage.getItem('token');
        }
        return token;
    };

    const fetchWithToken = uri => {
        const token = getToken();
        const config = {
            headers: { 'Authorization': `Bearer: ${token}` }
        };
        return fetch(uri, config);
    };

    const getParts = () => {
        return fetch(apiURI)
            .then(data => data.json());
    };

    const updatePart = (id, data) => {
        return fetch(`${apiURI}/${id}`, {
            method: 'PATCH',
            body: JSON.stringify(data)
        })
            .then(data => data.json());
    };

    const deletePart = id => {
        return fetch(`${apiURI}/${id}`, {
            method: 'DELETE'
        })
            .then(data => data.json());
    };

    const addPart = (data) => {
        return fetch(`${apiURI}`, {
            method: 'POST',
            body: JSON.stringify(data)
        })
            .then(data => data.json());
    };


    /** Example classful component */
    class Parts extends Component {
        static getDefaultValue() {
            return {
                part_id: '',
                part_name: '',
                price: '',
                manufacturer: '',
                vendor: '',
                notes: '',
                editing: true
            };
        };

        constructor() {
            super();
            this.state = {
                parts: [],
                newPart: null,
                filter: _ => true
            };
        }

        handleSavePart(part, newPart) {
            updatePart(part.part_id, newPart)
                .then(data => {
                    if (!data.success) {
                        return false;
                    }
                    this.setState(prevState => {
                        const parts = prevState.parts.map(f => {
                            if (f.part_id === newPart.part_id) {
                                return newPart;
                            }
                            return f;
                        });
                        return { parts };
                    });
                })
                .catch(err => console.error(err));
        }

        handleDeletePart(id) {
            deletePart(id)
                .then(data => {
                    if (!data.success) {
                        return false;
                    }
                    this.setState(prevState => {
                        const parts = prevState.parts.filter(part => part.part_id !== id);
                        return {
                            parts
                        };
                    })
                })
                .catch(err => console.error(err));
        }

        handleToggleNewPart() {
            this.setState(prevState => {
                if (prevState.newPart == null) {
                    return {
                        newPart: Parts.getDefaultValue()
                    };
                }
                return {
                    newPart: null
                };
            });
        }

        handleAddPart(part, data) {
            data['vehicle_id'] = <?php echo $vehicle_id ?>;
            addPart(data)
                .then(response => {
                    if (response.success) {
                        this.setState(prevState => {
                            const parts = [...prevState.parts];
                            const newPart = spreadObject(data);
                            newPart.editing = false;
                            newPart.part_id = response.id;
                            parts.push(newPart);
                            return { parts, newPart: null };
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
            getParts()
                .then(parts => {
                    this.setState({
                        parts
                    });
                })
                .catch(err => {
                    console.error(err);
                });
        }


        render(props, { newPart, filter }) {
            const partList = this.state.parts
                .filter(filter)
                .map(part => h(Part, {
                    part,
                    handleSavePart: this.handleSavePart.bind(this),
                    handleDeletePart: this.handleDeletePart.bind(this)
                }));
            const newPartComponent = newPart !== null ? h(Part, {
                part: newPart,
                handleSavePart: this.handleAddPart.bind(this),
                handleDeletePart: this.handleToggleNewPart.bind(this)
            }) : null;

            return (
                h('div', { class: 'container container-fluid mx-auto mt-5 mb-5 px-4 py-4'},[
                    h('div', { class: 'float-right mr-2 mt-2'},
                        h('button', {
                            type: 'button',
                            class: 'btn btn-success add',
                            onClick: this.handleToggleNewPart.bind(this)
                        }, newPart === null ? 'Add Part' : 'Cancel')
                    ),
                    h('h1', { style: { fontWeight: 400 }}, 'Parts used on your '),
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
                    newPartComponent,
                    h('div', {class: 'mt-4 mx-auto'}, partList)
                ])
            );
        }
    }

    const Filter = ({ handleUpdateFilter, filter: defaultFilter }) => {
        const filterByYear = e => {
            const value = e.target.value;
            handleUpdateFilter(part => {
                if (value === '') {
                    return defaultFilter
                }
                const date = new Date(part.date);
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

    const Part = ({ part, handleSavePart, handleDeletePart }) => {
        return (
            h('div', { class: 'vehicle' }, [
                h(PartLink, { part }),
                h(PartPanel, { part, handleSavePart, handleDeletePart})
            ])
        );
    };

    const PartLink = ({ part }) => {
        const {
            part_id,
            part_name,
            manufacturer,
            vendor
        } = part;
        // const formattedDate = formatDate(new Date(date));
        return (
            h('a', { id: 'col', href: `#ser-${part_id}`, 'data-toggle': 'collapse' },
                h('div', { class: 'list-group-item fntbgr'}, [
                    h('span', { class: 'bolder' }, `${manufacturer} ${part_name}`),
                    ' from ',
                    h('span', { class: 'bolder' }, `${vendor}`),
                    // h('div', { class: 'float-right' }, formattedDate)
                ])
            )
        );
    };

    class PartPanel extends Component {
        constructor() {
            super();
            this.state = {
                errorMessages: {
                    part_id: '',
                    part_name: '',
                    price: '',
                    manufacturer: '',
                    vendor: '',
                    notes: '',
                },
                edits: null,
                editing: false,
                isValid: true,
                newPart: false
            };
        }

        componentDidMount() {
            const newPart = this.props.part.editing === true;
            const editing = newPart;
            const isValid = !newPart;
            const edits = editing ? spreadObject(this.props.part ) : null;
            this.setState({
                edits,
                editing,
                isValid,
                newPart
            });
        }

        handleEditPart() {
            this.setState({
                edits: spreadObject(this.props.part),
                editing: true
            });
        }

        handleCancelPart() {
            if (this.state.newPart) {
                return this.props.handleDeletePart();
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

        handleSavePart() {
            if (this.state.isValid) {
                this.props.handleSavePart(this.props.part, this.state.edits);
                this.setState({
                    editing: false
                });
            }
        }


        render({ part, handleDeletePart },
               { errorMessages, isValid, newPart, editing, edits }) {
            const {
                part_id,
                part_name,
                price,
                manufacturer,
                vendor,
                notes,
            } = editing ? edits : part;

            return (
                h('div', {
                    class: `panel-collapse collapse ${newPart ? 'show' : ''}`,
                    id: `ser-${part_id}`,
                }, [
                    h('ul', {class: 'list-group'}, [
                        h(PartDetail, {
                            title: 'Name',
                            field: 'part_name',
                            value: part_name,
                            editing,
                            type: 'text',
                            errorMessage: errorMessages['part_name'],
                            onChange: this.onChange.bind(this)
                        }),
                        h(PartDetail, {
                            title: 'Cost',
                            field: 'price',
                            value: price,
                            editing,
                            type: 'number',
                            min: 0,
                            max: 100000,
                            step: 0.01,
                            errorMessage: errorMessages['price'],
                            onChange: this.onChange.bind(this)
                        }),
                        h(PartDetail, {
                            title: 'Manufacturer',
                            field: 'manufacturer',
                            value: manufacturer,
                            editing,
                            type: 'text',
                            errorMessage: errorMessages['manufacturer'],
                            onChange: this.onChange.bind(this)
                        }),
                        h(PartDetail, {
                            title: 'Vendor',
                            field: 'vendor',
                            value: vendor,
                            editing,
                            type: 'text',
                            errorMessage: errorMessages['vendor'],
                            onChange: this.onChange.bind(this)
                        }),
                        h(PartDetail, {
                            title: 'Notes',
                            field: 'notes',
                            value: notes,
                            editing,
                            type: 'text',
                            errorMessage: errorMessages['notes'],
                            onChange: this.onChange.bind(this)
                        }),
                        h(ButtonContainer, {
                            part_id,
                            editing,
                            isValid,
                            handleEditPart: this.handleEditPart.bind(this),
                            handleSavePart: this.handleSavePart.bind(this),
                            handleDeletePart,
                            handleCancelPart: this.handleCancelPart.bind(this)
                        })
                    ])
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
        return number.split('').reverse().reduce((next, n, i) => {
            if (i !== 0 && i % 3 === 0) {
                return n + ',' + next;
            }
            return n + next;
        }, '')
    };

    const PartDetail = ({ title, field, value, editing, type, min, max, step, onChange, errorMessage }) => {
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

    const ButtonContainer = ({ part_id, editing, isValid, handleEditPart, handleSavePart, handleDeletePart, handleCancelPart }) => {
        if (editing) {
            return (
                h('div', {class: 'toggle'}, [
                    h('button', { class: 'btn btn-success ml-4 mt-2 mb-3', disabled: !isValid, onClick: handleSavePart }, 'Save'),
                    h('button', { class: 'btn btn-warning ml-2 mt-2 mb-3', onClick: handleCancelPart }, 'Cancel'),
                ])
            );
        } else {
            return (
                h('div', null, [
                    h('button', { type: 'button', class: 'btn btn-success mb-3 mt-2 ml-4', onClick: () => handleEditPart(part_id) }, 'Edit'),
                    h('button', { class: 'btn btn-danger ml-2 mt-2 mb-3', onClick: () => handleDeletePart(part_id) }, 'Delete')
                ])
            )
        }
    }

    render(h(Parts), document.getElementById('parts'));
</script>
</body>
</html>