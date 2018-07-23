<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>React Select Test</title>
  </head>
  <body>

    <!-- We will put our React component inside this div. -->
    <div id="select_container"></div>

    <!-- Load React. -->
<!--
<script src="https://cdnjs.cloudflare.com/ajax/libs/react/16.4.0/cjs/react.production.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/react-dom/16.4.1/umd/react-dom.production.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/react-select/1.2.1/react-select.min.js"></script>
-->

<script src="https://unpkg.com/react@15.6.1/dist/react.js"></script>
<script src="https://unpkg.com/react-dom@15.6.1/dist/react-dom.js"></script>
<script src="https://unpkg.com/prop-types@15.5.10/prop-types.js"></script>
<script src="https://unpkg.com/classnames@2.2.5/index.js"></script>
<script src="https://unpkg.com/react-input-autosize@2.0.0/dist/react-input-autosize.js"></script>
<script src="https://unpkg.com/react-select/dist/react-select.js"></script>
 
<link rel="stylesheet" href="https://unpkg.com/react-select/dist/react-select.css">

<script>
class App extends React.Component {
//  state = {
//    selectedOption: '',
//  }
  handleChange = (selectedOption) => {
    this.setState({ selectedOption });
    console.log(`Selected: ${selectedOption.label}`);
  }
  render() {
  	const { selectedOption } = this.state;
  	const value = selectedOption && selectedOption.value;
 
    return (
      <Select
        name="form-field-name"
        value={value}
        onChange={this.handleChange}
        options={[
          { value: 'one', label: 'One' },
          { value: 'two', label: 'Two' },
        ]}
      />
    );
  }
}
</script>

<h1>Ta Da</h1>

  </body>
</html>
