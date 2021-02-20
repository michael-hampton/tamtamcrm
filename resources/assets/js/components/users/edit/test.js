class NestedCheckboxTree extends React.Component {
    constructor(props) {
        super(props);
        this.onGroupChange = this.onGroupChange.bind(this);
        this.onChildChange = this.onChildChange.bind(this);

        this.state = _.transform(this.props.list, (acc, children, groupName) => {
            acc[groupName] = {
                name: groupName,
                checked: false,
                children: _.transform(children, (acc, child) => acc[child] = { name: child, checked: false }, {})
            };
        }, {});
    }

    onGroupChange(groupName) {
        let newState = {...this.state}

        let group = newState[groupName];
        group.checked = !group.checked;
        
        group.children.forEach(c => {
	    c.checked = group.checked
        })

        this.setState(newState);
    }

    onChildChange(groupName, childName) {
        let newState = {...this.state}

        var group = newState[groupName];
        group.children[childName].checked = !group.children[childName].checked;
        group.checked = _.every(group.children, "checked");

        this.setState(newState);
    }

    render() {
        return (
            <div>
                {_.map(this.state, (item) =>
                    <CheckboxGroup key={item.name} onGroupChange={this.onGroupChange} onItemChange={this.onChildChange} {...item} />
                )}
            </div>
        );
    }
}

function CheckboxGroup(props) {
    return (
        <div>
            <label>
                <input type="checkbox" checked={props.checked} onChange={props.onGroupChange.bind(null, props.name)} /> <strong>{props.name}</strong>
            </label>

            <div style={{marginLeft: 20}}>
                {_.map(props.children, function (childItem) {
                    return (
                        <Checkbox key={childItem.name} onChange={props.onItemChange.bind(null, props.name)} {...childItem} />
                    );
                }.bind(this))}
            </div>
        </div>
    );
}

function Checkbox(props) {
    return (
        <div>
            <label>
                <input type="checkbox" checked={props.checked} onChange={props.onChange.bind(null, props.name)} />
                {' '}
                {props.name}
            </label>
        </div>
    );
}

var itemList = {
    Fruits: ['Apple', 'Orange', 'Pineapple', 'Mango'],
    Vegetables: ['Tomatoes', 'Cucumbers', 'Carrots', 'Avocados']
};

ReactDOM.render(<NestedCheckboxTree list={itemList} />, document.getElementById('component'));
