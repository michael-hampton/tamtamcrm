import React, { Component } from 'react'
import RestoreModal from '../common/RestoreModal'
import DeleteModal from '../common/DeleteModal'
import ActionsMenu from '../common/ActionsMenu'
import axios from 'axios'
import { Input, ListGroupItem } from 'reactstrap'
import EditPlan from './edit/EditPlan'
import PlanPresenter from '../presenters/PlanPresenter'

export default class PlanItem extends Component {
    constructor (props) {
        super(props)

        this.state = {
            width: window.innerWidth
        }

        this.deletePlan = this.deletePlan.bind(this)
        this.handleWindowSizeChange = this.handleWindowSizeChange.bind(this)
    }

    componentWillMount () {
        window.addEventListener('resize', this.handleWindowSizeChange)
    }

    componentWillUnmount () {
        window.removeEventListener('resize', this.handleWindowSizeChange)
    }

    handleWindowSizeChange () {
        this.setState({ width: window.innerWidth })
    }

    deletePlan (id, archive = false) {
        const url = archive === true ? `/api/plans/archive/${id}` : `/api/plans/${id}`
        const self = this
        axios.delete(url)
            .then(function (response) {
                const arrPlans = [...self.props.entities]
                const index = arrPlans.findIndex(plan => plan.id === id)
                arrPlans[index].is_deleted = archive !== true
                arrPlans[index].deleted_at = new Date()
                self.props.addUserToState(arrPlans, true)
            })
            .catch(function (error) {
                self.setState(
                    {
                        error: error.response.data
                    }
                )
            })
    }

    render () {
        const { plans, ignoredColumns, entities } = this.props
        if (plans && plans.length) {
            return plans.map((plan, index) => {
                const restoreButton = plan.deleted_at
                    ? <RestoreModal id={plan.id} entities={entities} updateState={this.props.addUserToState}
                        url={`/api/plans/restore/${plan.id}`}/> : null

                const deleteButton = !plan.deleted_at
                    ? <DeleteModal archive={false} deleteFunction={this.deletePlan} id={plan.id}/> : null

                const archiveButton = !plan.deleted_at
                    ? <DeleteModal archive={true} deleteFunction={this.deletePlan} id={plan.id}/> : null

                const editButton = !plan.deleted_at
                    ? <EditPlan plan_types={this.props.plan_types} plan={plan} plans={entities} action={this.props.addUserToState}/>
                    : null

                const columnList = Object.keys(plan).filter(key => {
                    return ignoredColumns.includes(key)
                }).map(key => {
                    return <td key={key}
                        onClick={() => this.props.toggleViewedEntity(plan, plan.name, editButton)}
                        data-label={key}><PlanPresenter toggleViewedEntity={this.props.toggleViewedEntity}
                            field={key} entity={plan} edit={editButton}/></td>
                })

                const checkboxClass = this.props.showCheckboxes === true ? '' : 'd-none'
                const isChecked = this.props.bulk.includes(plan.id)
                const selectedRow = this.props.viewId === plan.id ? 'table-row-selected' : ''
                const actionMenu = this.props.showCheckboxes !== true
                    ? <ActionsMenu show_list={this.props.show_list} edit={editButton} delete={deleteButton}
                        archive={archiveButton}
                        restore={restoreButton}/> : null

                const is_mobile = this.state.width <= 768
                const list_class = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true')
                    ? 'list-group-item-dark' : ''

                if (!this.props.show_list) {
                    return <tr className={selectedRow} key={plan.id}>
                        <td>
                            <Input checked={isChecked} className={checkboxClass} value={plan.id} type="checkbox"
                                onChange={this.props.onChangeBulk}/>
                            {actionMenu}
                        </td>
                        {columnList}
                    </tr>
                }

                return is_mobile || this.props.force_mobile ? <div className={`d-flex d-inline ${list_class}`}>
                    <div className="list-action">
                        {!!this.props.onChangeBulk &&
                        <Input checked={isChecked} className={checkboxClass} value={plan.id} type="checkbox"
                            onChange={this.props.onChangeBulk}/>
                        }
                        {actionMenu}
                    </div>

                    <ListGroupItem onClick={() => this.props.toggleViewedEntity(plan, plan.name, editButton)}
                        key={index}
                        className={`border-top-0 list-group-item-action flex-column align-items-start ${list_class}`}>
                        <div className="d-flex w-100 justify-content-between">
                            <h5 className="mb-1">{<PlanPresenter field="name"
                                entity={plan}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/>}
                            {<PlanPresenter field="price"
                                entity={plan}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/>}
                            {<PlanPresenter field="trial_period"
                                entity={plan}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/>}
                            </h5>
                        </div>
                    </ListGroupItem>
                </div> : <div className={`d-flex d-inline ${list_class}`}>
                    <div className="list-action">
                        {!!this.props.onChangeBulk &&
                        <Input checked={isChecked} className={checkboxClass} value={plan.id} type="checkbox"
                            onChange={this.props.onChangeBulk}/>
                        }
                        {actionMenu}
                    </div>

                    <ListGroupItem onClick={() => this.props.toggleViewedEntity(plan, plan.name, editButton)}
                        key={index}
                        className={`border-top-0 list-group-item-action flex-column align-items-start ${list_class}`}>
                        <div className="d-flex w-100 justify-content-between">
                            <h5 className="mb-1">{<PlanPresenter field="name"
                                entity={plan}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/>} .
                            {<PlanPresenter field="price"
                                entity={plan}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/>}
                            {<PlanPresenter field="trial_period"
                                entity={plan}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/>}
                            </h5>
                        </div>
                    </ListGroupItem>
                </div>
            })
        } else {
            return <tr>
                <td className="text-center">No Records Found.</td>
            </tr>
        }
    }
}
