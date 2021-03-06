import React, { Component } from 'react';
import {Modal, Button , OverlayTrigger , Popover , Tooltip } from 'react-bootstrap';

export default class ModalPage extends Component {
	closeModal(){
		if(typeof this.props.closeClick === 'function'){
			this.props.closeClick();
		}
	}
	render(){
		const popover = (
	      <Popover id="modal-popover" title="popover">
	        very popover. such engagement
	      </Popover>
	    );
	    const tooltip = (
	      <Tooltip id="modal-tooltip">
	        wow.
	      </Tooltip>
	    );
	    let modal ={body:'',header:'Modal heading',footer:''};
	    modal.body = (
	    	<div>
	    			<h4>Text in a modal</h4>
		            <p>Duis mollis, est non commodo luctus, nisi erat porttitor ligula.</p>

		            <h4>Popover in a modal</h4>
		            <p>there is a <OverlayTrigger overlay={popover}><a href="#">popover</a></OverlayTrigger> here</p>

		            <h4>Tooltips in a modal</h4>
		            <p>there is a <OverlayTrigger overlay={tooltip}><a href="#">tooltip</a></OverlayTrigger> here</p>

		            <hr />

		            <h4>Overflowing text to show scroll behavior</h4>
		            <p>Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.</p>
		            <p>Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.</p>
		            <p>Aenean lacinia bibendum nulla sed consectetur. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Donec sed odio dui. Donec ullamcorper nulla non metus auctor fringilla.</p>
		            <p>Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.</p>
		            <p>Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.</p>
		            <p>Aenean lacinia bibendum nulla sed consectetur. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Donec sed odio dui. Donec ullamcorper nulla non metus auctor fringilla.</p>
		            <p>Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.</p>
		            <p>Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.</p>
		            <p>Aenean lacinia bibendum nulla sed consectetur. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Donec sed odio dui. Donec ullamcorper nulla non metus auctor fringilla.</p>
	    	</div>
	    );
	    let {contentBody} = this.props ; 
	    if( contentBody.body){
	    	modal.body = <div dangerouslySetInnerHTML={{__html:contentBody.body}} />;
	    }
	    if(contentBody.footer){
	    	modal.footer = <div dangerouslySetInnerHTML={{__html:contentBody.footer}} />;
	    }
	    if(contentBody.header){
	    	modal.header = <div dangerouslySetInnerHTML={{__html:contentBody.header}} />;
	    }

		return(
			<div className="container">
				<Modal bsSize={this.props.modalSize} show={this.props.open} onHide={this.closeModal.bind(this)}>
		          <Modal.Header closeButton>
		            <Modal.Title> {modal.header} </Modal.Title>
		          </Modal.Header>
		          <Modal.Body>
		            {modal.body}
		          </Modal.Body>
		          <Modal.Footer>
		          	{modal.footer}
		            <Button onClick={this.closeModal.bind(this)}>Close</Button>
		          </Modal.Footer>
		        </Modal>
			</div>
		);
	}
}