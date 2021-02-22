import React from 'react'
import { translations } from '../../utils/_translations'

export default class NestedCheckboxTree extends React.Component {
    constructor (props) {
        super(props)
        this.onGroupChange = this.onGroupChange.bind(this)
        this.onChildChange = this.onChildChange.bind(this)
        this.onSectionChange = this.onSectionChange.bind(this)
        this.rebuildPermissions = this.rebuildPermissions.bind(this)

        this.allowed_permissions = JSON.parse(localStorage.getItem('allowed_permissions'))

        const data = this.props.list

        this.state = {
            roles: this.props.selected_roles,
            customize: this.props.has_custom_permissions || false,
            first_load: false
        }

        this.state.permissions = Object.keys(data).reduce((acc, key) => {
            acc[key] = {
                name: key,
                checked: false,
                children: this.reduceChildren(data[key], key)
            }
            return acc
        }, {})

        this.state.sections = {
            store: {
                checked: false
            },
            destroy: {
                checked: false
            },
            update: {
                checked: false
            },
            show: {
                checked: false
            }
        }
    }

    static getDerivedStateFromProps (props, state) {
        if (props.selected_roles && props.selected_roles !== state.roles) {
            return {
                roles: props.selected_roles
            }
        }

        return null
    }

    componentDidMount () {
        if (this.props.has_custom_permissions === true) {
            this.rebuildPermissions()
        }
    }

    componentDidUpdate (prevProps, prevState) {
        if (this.props.selected_roles && this.props.selected_roles !== prevProps.selected_roles) {
            this.props.setPermissions(this.state.permissions, true)
        }
    }

    rebuildPermissions () {
        const permissions = Object.keys(this.props.list).reduce((acc, key) => {
            acc[key] = {
                name: key,
                checked: false,
                children: this.reduceChildren(this.props.list[key], key)
            }
            return acc
        }, {})

        this.setState({ permissions: permissions, first_load: true }, () => {
            this.props.setPermissions(permissions, this.state.customize)
        })
    }

    reduceChildren (children, group) {
        return children.reduce((acc, key) => {
            const value = `${group}controller.${key}`

            let checked = false

            if (this.state.roles && this.state.roles.length) {
                this.state.roles.forEach((role) => {
                    if (this.allowed_permissions[role] && this.allowed_permissions[role][value] && (this.allowed_permissions[role][value] === 1 || this.allowed_permissions[role][value] === true)) {
                        checked = true
                    }
                })
            }

            acc[key] = {
                name: key,
                value: value,
                checked: checked
            }
            return acc
        }, {})
    }

    onSectionChange (name) {
        const newState = { ...this.state.permissions }
        const sections = { ...this.state.sections }
        sections[name].checked = !this.state.sections[name].checked

        Object.keys(newState).forEach((group) => {
            Object.keys(newState[group].children).forEach((key) => {
                if (newState[group].children[key].name.includes(name)) {
                    newState[group].children[key].checked = this.state.sections[name].checked
                }
            })
        })

        this.setState({ permissions: newState, sections: sections }, () => {
            if (this.state.customize === true) {
                this.props.setPermissions(newState, this.state.customize)
            }
        })
    }

    onGroupChange (groupName) {
        const newState = { ...this.state.permissions }

        const group = newState[groupName]
        group.checked = !group.checked

        Object.keys(group.children).forEach((key) => {
	        group.children[key].checked = group.checked
        })

        this.setState({ permissions: newState }, () => {
            if (this.state.customize === true) {
                this.props.setPermissions(newState, this.state.customize)
            }
        })
    }

    onChildChange (groupName, childName) {
        const newState = { ...this.state.permissions }

        const group = newState[groupName]
        group.children[childName].checked = !group.children[childName].checked
        // group.checked = _.every(group.children, "checked");

        this.setState({ permissions: newState }, () => {
            if (this.state.customize === true) {
                this.props.setPermissions(newState, this.state.customize)
            }
        })
    }

    render () {
        const sections = ['store', 'update', 'destroy', 'show']
        return (
            <React.Fragment>
                <div className="row">
                    <div className="d-flex justify-content-between col-12">
                        <label>
                            <input type="checkbox" checked={this.state.customize} onClick={(e) => {
                                this.setState({ customize: !this.state.customize }, () => {
                                    if (!this.state.first_load) {
                                        this.rebuildPermissions()
                                    }
                                })
                            }}/> {translations.customize}
                        </label>

                        <h3>{translations.permissions}</h3>
                    </div>
                </div>

                {this.state.customize &&
                <div className="row">
                    <div className="col-12">
                        <div className="col-md-4" />
                        {sections.map((section) => (
                            <CheckboxSection customize={this.state.customize} name={section} key={section} onSectionChange={this.onSectionChange} />
                        ))}
                    </div>
                </div>
                }

                <div className="row">
                    <div className="col-md-12">
                        {Object.keys(this.state.permissions).map((item) => (
                            <CheckboxGroup customize={this.state.customize} key={this.state.permissions[item].name} onGroupChange={this.onGroupChange} onItemChange={this.onChildChange} {...this.state.permissions[item]} />
                        ))}
                    </div>
                </div>
            </React.Fragment>

        )
    }
}

function CheckboxSection (props) {
    let input = <input type="checkbox" checked={props.checked} onChange={props.onSectionChange.bind(null, props.name)} />

    if (!props.customize) {
        input = props.checked ? <span className="fa fa-check" style={{ fontSize: 20 }} /> : <span className="fa fa-times" style={{ fontSize: 20 }} />
    }

    return (
        <div className="col-md-2">
            <label>
                {input} <strong>{props.name}</strong>
            </label>
        </div>
    )
}

function CheckboxGroup (props) {
    let input = <input type="checkbox" checked={props.checked} onChange={props.onGroupChange.bind(null, props.name)} />

    if (!props.customize) {
        input = props.checked ? <span className="fa fa-check" style={{ fontSize: 20 }} /> : <span className="fa fa-times" style={{ fontSize: 20 }} />
    }

    return (
        <div>
            <div className="col-md-4">
                <label>
                    {input}
                    <strong>{translations[props.name]}</strong>
                </label>
            </div>

            {Object.keys(props.children).map((key) => {
                return (
                    <div className="col-md-2">
                        <Checkbox customize={props.customize} key={props.children[key].name} group={props.name} onChange={props.onItemChange.bind(null, props.name)} {...props.children[key]} />
                    </div>
                )
            })}
        </div>
    )
}

function Checkbox (props, group) {
    const labels = { store: 'create', update: 'update', destroy: 'delete', show: 'view' }
    const name = labels[props.name]
    const value = props.group + 'controller.' + props.name

    let input = <input type="checkbox" checked={props.checked} onChange={props.onChange.bind(null, props.name)} />

    if (!props.customize) {
        input = props.checked ? <span className="fa fa-check" style={{ fontSize: 20 }} /> : <span className="fa fa-times" style={{ fontSize: 20 }} />
    }

    return (
        <div>
            <label>
                {input}
                {' '}
                {name}
            </label>
        </div>
    )
}
