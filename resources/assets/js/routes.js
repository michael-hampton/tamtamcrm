import React from 'react'
import { Route } from 'react-router'
import Kanban from './components/Kanban'
// import About from './components/about';
const IndexPage = () => {
    return <div>Welcome to Scrum Master<br/><a href="/story/1">Homepage</a></div>
}
const NotFoundPage = () => {
    return <div><h2>Not Found</h2><br/><a href="/story/1">Homepage</a></div>
}

export default (
    <Route>
        <Route path='/dashboard' component={Kanban}/>
        {/* <Route path="/dashboard" component={IndexPage} /> */}
        <Route path='*' exact component={NotFoundPage}/>
    </Route>
)
