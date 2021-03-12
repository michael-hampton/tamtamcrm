import React, { Component } from 'react'
import BottomNavigation from '@material-ui/core/BottomNavigation'
import BottomNavigationAction from '@material-ui/core/BottomNavigationAction'

export default function BottomNavigationButtons (props) {
    const text_color = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true')
        ? 'text-light' : 'text-dark'
    const bg_color = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true')
        ? 'bg-dark' : 'bg-light'

    return (
        <React.Fragment>
            <BottomNavigation showLabels className={`${bg_color} ${text_color}`}>
                <BottomNavigationAction style={{ fontSize: '14px !important' }} className={text_color}
                    onClick={props.button1_click} label={props.button1.label}
                    value={props.button1.label}/>
                <BottomNavigationAction style={{ fontSize: '14px !important' }} className={text_color}
                    onClick={props.button2_click} label={props.button2.label}
                    value={props.button2.label}/>
            </BottomNavigation>
        </React.Fragment>
    )
}
