import React, { Component } from 'react';

export default class MessageAlert extends Component {
	//<div dangerouslySetInnerHTML={{__html: this.props.pesan_error}} className={this.showHide()} />
	kelasAlert(){
		let pesan = this.props.pesan.teks ;
		return pesan ? (this.props.pesan.kelas ? this.props.pesan.kelas : 'alert alert-danger' ) : 'hidden';
	}
	tipeAlert(){
		return this.props.pesan.tipe ? this.props.pesan.tipe : 'Error';
	}

	handleClick()
	{
		if(typeof this.props.pesan.setPesanError === 'function')
		this.props.pesan.setPesanError({ teks:false });
	}
	render() {
    	return (	
    		<div className={this.kelasAlert() }>
    			<strong>{this.tipeAlert()} : </strong> {this.props.pesan.teks} <button onClick={this.handleClick.bind(this)} className="close" data-dismiss="alert">&times;</button>
    		</div>
    	);
  	}
}