import React from "react";
import PropTypes from 'prop-types';
import { withStyles } from '@material-ui/core/styles';
import AppBar from '@material-ui/core/AppBar';
import Toolbar from '@material-ui/core/Toolbar';
import Typography from '@material-ui/core/Typography';
import Button from '@material-ui/core/Button';
import IconButton from '@material-ui/core/IconButton';
import MenuIcon from '@material-ui/icons/Menu';
import Drawer from '@material-ui/core/Drawer';
import List from '@material-ui/core/List';
import ListItem from '@material-ui/core/ListItem';
import ListItemText from '@material-ui/core/ListItemText';

const styles = {
    list: {
        width: '100%',
        maxWidth: 500
    }
}

class Navigation extends React.Component {

    state = {
        left: false
    };

    toggleDrawer = (side, open) => () => {
        this.setState({
            [side]: open
        });
    };

    render() {

        const { classes } = this.props;

        const sideList = (
            <div>
                <List>
                    <ListItem button>
                        <ListItemText primary="Issues" />
                    </ListItem>
                    <ListItem button>
                        <ListItemText primary="Scores" />
                    </ListItem>
                </List>
            </div>
        );

        return (
            <div>
                <AppBar position="static" color="default">
                    <Toolbar>
                        <IconButton color='inherit' aria-label="Menu" onClick={this.toggleDrawer('left', true)}>
                            <MenuIcon />
                        </IconButton>
                        <Drawer open={this.state.left} onClose={this.toggleDrawer('left', false)}>
                            <div>
                                {sideList}
                            </div>
                        </Drawer>
                        <Typography variant="title" color="inherit">
                            Title of things
                        </Typography>
                    </Toolbar>
                </AppBar>
            </div>
        );
    }
}

Navigation.propTypes = {
    classes: PropTypes.object.isRequired
};

export default withStyles(styles)(Navigation);