import React from "react";
import PropTypes from 'prop-types';
import { withStyles } from '@material-ui/core/styles';
import AppBar from '@material-ui/core/AppBar';
import Toolbar from '@material-ui/core/Toolbar';
import Typography from '@material-ui/core/Typography';
import Button from '@material-ui/core/Button';
import IconButton from '@material-ui/core/IconButton';
import MenuIcon from '@material-ui/icons/Menu';
import Menu from '@material-ui/core/Menu';
import MenuItem from '@material-ui/core/MenuItem';

const styles = {
    root: {
        flexGrow: 1
    }
}

export default class Navigation extends React.Component {
    state = {
        anchorEl: null
    };

    handleClick = event => {
        this.setState({ anchorEl: event.currentTarget });
    };

    handleClose = () => {
        this.setState({ anchorEl: null })
    };

    render() {

        const { anchorEl } = this.state;

        return (
            <div>
                <AppBar position="static" color="default">
                    <Toolbar>
                        <IconButton color='inherit' aria-label="Menu" onClick={this.handleClick}>
                            <MenuIcon />
                        </IconButton>
                        <Menu id="simple-menu" anchorEl={anchorEl} open={Boolean(anchorEl)} onClose={this.handleClose}>
                            <MenuItem onClick={this.handleClose}>Issues</MenuItem>
                            <MenuItem onClick={this.handleClose}>Score</MenuItem>
                            <MenuItem onClick={this.handleClose}>Game</MenuItem>
                            <MenuItem onClick={this.handleClose}>Management</MenuItem>
                            <MenuItem onClick={this.handleClose}>Teams</MenuItem>
                            <MenuItem onClick={this.handleClose}>Cards</MenuItem>
                            <MenuItem onClick={this.handleClose}>Users</MenuItem>
                        </Menu>
                        <Typography variant="title" color="inherit">
                            Title of things
                        </Typography>
                    </Toolbar>
                </AppBar>
            </div>
        );
    }
}