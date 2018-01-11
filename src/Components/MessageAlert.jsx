import React, { Component } from 'react';
import { connect } from 'react-redux'

class MessageAlert extends Component {
	//<div dangerouslySetInnerHTML={{__html: this.props.message}} className={this.showHide()} />
	getClassAlert(){
		let message = this.props.message.texts ;
		return message ? (this.props.message.className ? this.props.message.className : 'alert alert-danger' ) : 'hidden';
	}
	getAlertType(){
		return this.props.message.type ? this.props.message.type : 'Error';
	}

	handleClick()
	{
		this.props.dispatch({
			type:'updateAlert',value:{texts:false}
		});
	}
	render() {
    	return (	
    		<div className={this.getClassAlert() }>
    			<strong>{this.getAlertType()} : </strong> {this.props.message.texts} <button onClick={this.handleClick.bind(this)} className="close" data-dismiss="alert">&times;</button>
    		</div>
    	);
  	}
}

export default connect( store => {
	return { message : store.messageAlert }
} )(MessageAlert)