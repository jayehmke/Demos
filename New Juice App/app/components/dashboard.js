var React = require('react');


var Juice = React.createClass({
            render: function() {
                return (
                    <li className="list-group-item">

                    {this.props.title} | {this.props.sku}

        <span className="badge">{this.props.needs}mL</span>
<span className="badge">{this.props.needs}mL</span>
</li>
);
}
});

var JuiceList = React.createClass({
    render: function() {

    var juiceNodes = this.props.data.map(function (juice, i) {
        return <Juice key={i} sku={juice.sku} needs={juice.needed} title={juice.name}>{juice.name} {juice.needed}</Juice>;
});
return (

    <div className="row">
    <div className="col-md-3"></div>
    <div className="col-md-6">
    <h2>Juice Needs for Current Pending Orders</h2>
{this.props.items}
<ul className="list-group">
    {juiceNodes}
    </ul>
    </div>
    <div className="col-md-3"></div>
    </div>
);
}
});

var JuiceBox = React.createClass({
    getInitialState: function() {
    return {data: [], items: 0};
},
componentWillMount: function() {

},
loadJuiceFromServer: function(){
    var xhr = new XMLHttpRequest();
    xhr.open('get', this.props.url, true);
    xhr.onload = function() {
        var data = JSON.parse(xhr.responseText);
        this.setState({ data: data });
        this.setState({items: data.length});
    }.bind(this);
    xhr.send();
},
componentDidMount: function(){
    this.loadJuiceFromServer();
    setInterval(this.loadJuiceFromServer, this.props.interval);
},
render: function() {
    return (
        <div className="juiceBox">


        <JuiceList items={this.state.items} data={this.state.data} />

</div>
);
}
});

var route = window.location.pathname.substring(1);

if (route == "dashboard"){
    React.render(
        <JuiceBox url="/flavors/getneeds" interval={2000} />,
        document.getElementById('needs')
    );
}
