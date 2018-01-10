import React, { Component } from 'react';
import {Router } from 'react-router';
import createBrowserHistory from 'history/createBrowserHistory';
import MyRoute from './Actions/MyRoute'

const customHistory = createBrowserHistory()



class App extends Component {
  render() {
    return (
      <Router history={customHistory} >
        <div className="App">
           <MyRoute />
        </div>
      </Router>      
    );
  }
}

export default App;
