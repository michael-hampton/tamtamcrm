import { AppBar, Tab, Tabs, Box } from '@material-ui/core'

export default function AppTabs (props) {
    return (
        <Tabs TabIndicatorProps={{ style: { backgroundColor: '#0062cc', height: '4px' } }} variant={props.fullWidth ? 'fullWidth' : 'scrollable'} scrollButtons="auto" value={props.value} onChange={props.handleChange}>
            {props.tabs.map((value, i) => (
                <Tab style={{ textTransform: 'none' }} label={value.label}/>
            ))}
        </Tabs>

    )
}
