import React, { Component } from 'react';

import ErrorPage from './Components/ErrorPage';
import ResepMakanan from './Components/ResepMakanan';
import LS from './Actions/LocalStorage'; 

class Core extends Component {
  constructor(){
      super();
      this.state ={
        loaded:false,error:false,
      };
  }
  componentDidMount()
  {
    LS.sendAjax({
      error : (xhr) => {
        let error = false;
        if(xhr.responseJSON){
          error = xhr.responseJSON;
        }
        this.setState({loaded:true,error});
      },
      success:(data) => {
        this.setState({loaded:true,error:false,data:data});
      }, 
    });
  } 

  render() {
    return (
      <div className="App">
        { this.state.loaded ? (this.state.error ? <ErrorPage obj={this.state.error } /> : <ResepMakanan /> ) : <Loading /> }
      </div>
    );
  }
}

export default Core;

class Loading extends Component {
  render() {
  const css ={
    margin: 15 +'% auto'
  };

    return (
      <div className="text-center" style={css}>
        <h1>Loading ...... </h1>
      </div>
      
    );
  }
}