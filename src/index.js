import React from 'react';
import ReactDOM from 'react-dom';
import PropTypes from 'prop-types';
import { withStyles } from '@material-ui/core/styles';
import Button from '@material-ui/core/Button';
import Navigation from './components/Navigation';


const title = 'Still working? Seems so. What?';
const styles = theme => ({
    button: {
        margin: theme.spacing.unit,
    },
    input: {
        display: 'none'
    }
});

class Title extends React.Component {
    render() {
        return (
            <div><Navigation /></div>
        );
    }
}

class Layout extends React.Component {
    render() {
        return (
            <div>
                <Button variant="raised">
                    We have a button
                </Button>
            </div>
        );
    }
}

// ReactDOM.render(<Title/><Layout/>,
ReactDOM.render(<Title/>,
  document.getElementById('app')
);

module.hot.accept();