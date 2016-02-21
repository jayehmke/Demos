var optionIds = [];
var optionIdValues = [];
var optionNames = [];
var optionRequired = [];
var selectedJuice = {};
var custGroup = document.getElementById('custGroupId').value;

var History = React.createClass({

    render: function() {
        var i = 1;
        return (
            <div className="history grid12-4">
                <h3>History</h3>
                <div>
                    {this.props.historyItems.map(function(item){
                        i++;
                        return (
                            <div key={i} className="historyItem">
                                {item}
                            </div>
                        )
                    })
                    }
                </div>

            </div>
        )
    }
});

var ExtraDropDown = React.createClass({
    getInitialState: function() {
        return {
            selectedOption: null,
        };
    },

    onDropDownChange(e){
        var index = e.nativeEvent.target.selectedIndex;
        selectedJuice.sizeStrength = e.nativeEvent.target[index].text;
        selectedJuice.required = e.nativeEvent.target[index].id;
        //console.log(e.nativeEvent.target[index].id);
        //console.log("Selected Size/Strength/Label/Whatever: " + selectedJuice.sizeStrength);
        this.props.onOptionDropDownChange(e.target.value, this.props.optionKey, selectedJuice.sizeStrength, selectedJuice.required);
    },

    render: function() {
        //console.log("option Key " + this.props.optionKey);
        var theOptions = this.props.options || [];
        return (
            <div className="mainselection">
                <select disabled={this.props.disabled} onChange={this.onDropDownChange}>
                    <option id="please-select" value="Select Juice First">{this.props.title}</option>
                    {theOptions.map(function(option){
                        return <option key={option.optionTypeId} value={option.optionTypeId} id={option.optionTypeId} >{option.name}</option>
                    })}
                </select>
            </div>
        );
    }
});

var ExtraOptions = React.createClass({
    getInitialState: function(){
        return {
            nic: [],
            options: [],
            disabled: this.props.disabled
        }
    },


    componentWillMount: function() {
        this.getOptions();
    },

    componentWillUpdate: function(){

        if (this.state.disabled){
            this.getOptions();
        }
    },

    getOptions: function(){

        var juiceId = this.props.selectedJuice;
        var optionId = this.props.option;
        //console.log("http://test.wholesalevapingsupply.com/scripts/getOptionsByJuice.php?juice=" + juiceId + "&function=getOptions&optionId=" + this.props.option.id);

        jQuery.get("https://www.wholesalevapingsupply.com/scripts/getOptionsByJuice.php?juice=" + juiceId + "&function=getOptions&optionId=" + this.props.option.id, function(result) {
                var collection = JSON.parse(result);
            //console.log(this.props.option.id);
            if (this.isMounted()) {
                this.setState({
                    options: collection,
                    disabled:false,
                });
                //console.log(collection);
            }
        }.bind(this));
    },

    render: function(){
        return(
                <ExtraDropDown optionKey={this.props.optionKey} onOptionDropDownChange={this.props.onOptionDropDownChange} title={this.props.option.title} options={this.state.options} disabled={this.state.disabled} />
        )
    }
});

var JuiceNames = React.createClass({
    getInitialState: function() {
        return {
            selectedJuice: null,
            qty: 1,
            nic: null,
            disabled:true,
            optionTypes: null,
            myOptions: [],
            addNew: false,
            added: false,
            addUrl: "",
            optionIds: "",
            optionIdValues: "",
            historyName: '',
        };
    },

    onOptionDropDownChange(optionId, optionKey, optionName, required){

        optionIdValues[optionKey] = optionId;
        optionNames[optionKey] = optionName;
        optionRequired[optionKey] = required;
        this.setState({optionIdValues: optionIdValues});
    },

    getOptionTypes: function(juiceId) {
        //console.log("getting options");
        jQuery.get("https://www.wholesalevapingsupply.com/scripts/getOptionsByJuice.php?juice="+juiceId+"&function=getTypes&custgroup=" + custGroup, function(result) {
            var collection = JSON.parse(result);
            if (this.isMounted()) {
                this.setState({
                    optionTypes: collection.option,
                    optionCount: collection.count,
                    disabled: false,
                    myOptions: this.setAllOptions(collection.count, collection.option)
                });
            }
        }.bind(this));
    },

    onDropDownChange: function(event){
        //this.getOptionTypes(event.target.value);

        var index = event.nativeEvent.target.selectedIndex;
        selectedJuice.name = event.nativeEvent.target[index].text;

        this.setState({
            selectedJuice: event.target.value,
            disabled: true,
            myOptions: this.setAllOptions(),
        });
        this.getOptionTypes(event.target.value);
    },

    setAllOptions: function(rows, options){

        var optionRows = [];
        for (var i=0; i < rows; i++) {

            optionIds[i] = options[i].id;
            this.setState({optionIds: optionIds});

            optionRows.push(<ExtraOptions key={i} onOptionDropDownChange={this.onOptionDropDownChange} optionKey={i} option={options[i]} selectedJuice={this.state.selectedJuice} disabled={this.state.disabled} nicOptions={this.state.nic} />)
        }

        return optionRows
    },

    incrementRows: function(){
        this.props.incrementRows();
        this.setState({addNew: false})
    },

    onQtyChange: function(e){

        var keyPressed = e.target.value.slice(-1);
        if (keyPressed < 10 && keyPressed >= 0){
            this.setState({qty: e.target.value});
        }

    },

    getAddUrl: function(baseUrl){
        var addUrl = baseUrl + "&qty=" + this.state.qty;
        var historyName = selectedJuice.brand + ' ' + selectedJuice.name;
        for (var i=0; i < this.state.optionIds.length; i++) {
            console.log(optionNames[i]);
            if (!this.state.optionIdValues[i] || optionRequired[i] == 'please-select'){
                this.props.setMessage("Please fill out all options");
                return
            }
            else{
                addUrl = addUrl + "&options[" + this.state.optionIds[i] + "]=" + this.state.optionIdValues[i];

                historyName = historyName + ' ' + optionNames[i];
            }

        }
        this.props.confirmHistory(historyName);
        return addUrl
    },

    addToCart: function(){
        this.props.setMessage('');
        var baseUrl = "https://www.wholesalevapingsupply.com/checkout/cart/add?product=" + this.state.selectedJuice;
        this.setState({
            added:true
        });
        console.log(Object.keys(selectedJuice).length);
        if (Object.keys(selectedJuice).length > 1){
            jQuery.get(this.getAddUrl(baseUrl), function(result) {
                if (this.isMounted()) {
                    //console.log("options result: ");
                    //console.log(collection);
                }
            }.bind(this));
        }
        else {
            console.log("no juice here")
        }

    },

    render: function() {
        var theJuice = this.props.juice || [];

        return (
            <div>
                <div className="mainselection">
                    <select disabled={this.props.disableJuice} onChange={this.onDropDownChange}>
                        <option id="please-select" value="Please Select">Please Select</option>
                    {theJuice.map(function(juice){
                        return <option value={juice.id} id={juice.id} key={juice.id}>{juice.name}</option>
                    })}
                        </select>

                    {this.state.myOptions}
                </div>
                <input className="qtyBox" value={this.state.qty} onChange={this.onQtyChange} type="text" placeholder="QTY" />
                <button className="addButton" disabled={this.state.addNew} onClick={this.addToCart} >Add to Cart</button>
            </div>
        );
    }
});

var JuiceQuickOrder = React.createClass({

    getInitialState: function(){
      return {
          juice: [],
          rows: 1,
          disableJuice:true,
          message: '',
          historyBrand: '',
          historyName: '',
          historySizeStrength: '',
          historyItems: []
      }
    },

    setMessage: function(message){
      this.setState({message: message})
    },

    getJuice: function(source){
        jQuery.get(source, function(result) {
            var collection = JSON.parse(result);
            if (this.isMounted()) {
                this.setState({
                    juice: collection,
                    disableJuice: false,
                });
            }
        }.bind(this));
    },

    brandChange: function(e){
        this.setState({disableJuice: true});
        this.getJuice(e.target.value);
        var index = e.nativeEvent.target.selectedIndex;
        selectedJuice.brand = e.nativeEvent.target[index].text;
    },


    confirmHistory: function(item){
        var historyArray = this.state.historyItems;
        historyArray.push(item);
        this.setState({historyItems: historyArray});
    },


    addNewRow: function(){
        var rows = this.state.rows;
        var juiceRows = [];
        for (var i=0; i < rows; i++) {
            juiceRows.push(<JuiceNames confirmHistory={this.confirmHistory} setMessage={this.setMessage} disableJuice={this.state.disableJuice} key={i} juice={this.state.juice} />)
        }

        return juiceRows
    },

    render: function(){
        var message = this.state.message;
        var juiceBrands = [];
        juiceBrands.push({id: 1, name: "Top Vapor", source: "/scripts/getJuiceByBrand.php?brand=Ultimate%20Vapor%20e-Liquid"});
        juiceBrands.push({id: 2, name: "Lion Head", source: "/scripts/getJuiceByBrand.php?brand=Lion%20Head%20e-Liquid"});
        juiceBrands.push({id: 3, name: "Patriot Vapor", source: "/scripts/getJuiceByBrand.php?brand=Patriot%20Vapor"});

        return(<div>
                <div className="orderForm grid12-8">
                {message}
                    <div className="mainselection">
                        <select onChange={this.brandChange}>
                            <option value="Please Select">Please Select</option>
                        {juiceBrands.map(function(brand){
                            return <option key={brand.id} value={brand.source} >{brand.name}</option>
                        })}
                        </select>
                    </div>
                {this.addNewRow()}
                </div>
                <History currentBrand={this.state.historyBrand} currentName={this.state.historyName} currentSizeStrength={this.state.historySizeStrength} historyItems = {this.state.historyItems} />
            </div>
        )
    }
});

var cssId = 'myCss';
if (!document.getElementById(cssId))
{
    var head  = document.getElementsByTagName('head')[0];
    var link  = document.createElement('link');
    link.id   = cssId;
    link.rel  = 'stylesheet';
    link.type = 'text/css';
    link.href = '/scripts/css/juiceQuickOrder.css';
    link.media = 'all';
    head.appendChild(link);
}
ReactDOM.render(
    <JuiceQuickOrder />,
    document.getElementById('juice-quick-order')
);
