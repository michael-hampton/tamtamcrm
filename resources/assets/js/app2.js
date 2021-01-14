import React from 'react'
import ReactDOM from 'react-dom'
import Kanban from './components/Kanban'

import { Router, browserHistory } from 'react-router'
import routes from './routes'

const parentClass = ReactDOM.render(<Kanban project_id="2"/>, document.getElementById('app'))
