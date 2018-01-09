import React, { Component } from 'react';

import ModalElement from './ModalElement';
import MessageAlert from './MessageAlert';
import TabelResepMakanan from './TabelResepMakanan';
import $ from 'jquery' ;
$.DataTable = require('datatables.net');

export default class ResepMakanan extends Component {
  constructor()
  {
    super();
    this.state = {
      modal:{
        open:false,closeClick:this.toggleModal.bind(this),
        setModalContent:this.setModalContent.bind(this),contentBody:{} ,modalSize:'sm'
      } ,
      pesanError:{
        tipe:'Error',teks:false,kelas:false,setPesanError:this.setPesanError.bind(this)
      }
    };
  }

  setPesanError(data)
  {
    let pesanError = {...this.state.pesanError };
    pesanError = Object.assign(pesanError ,data);
    /*if(typeof data.teks !== 'undefined')pesanError.teks = data.teks;
    if(data.kelas)pesanError.kelas = data.kelas;
    if(data.tipe)pesanError.tipe = data.tipe;*/

    this.setState({pesanError});
  }

  setModalContent(data)
  {
    let modal = {...this.state.modal,contentBody:data};
    this.setState({modal});
  }

  toggleModal()
  {
    let modal = {...this.state.modal ,open:!this.state.modal.open};
    this.setState({modal});
    //this.setState({modal:{open:}});
  }

  /*componentDidMount() {
    setTimeout(() => {
      alert('gaga');
      this.toggleModal();
    },3000);
  } */

  render() {
    let {...stateModal} = this.state.modal;

    return (
      <div className="col-lg-12">
        <div className="row">
          <div className="col-lg-12">
            <ol className="breadcrumb">
              <li><a >Home</a></li>
              <li className="active"><span>Master Data </span></li>
            </ol>
          <h1>Resep Makanan </h1>
          </div>
        </div>
        <div className="row">
          <div className="col-lg-12 main-box">
            <TabelResepMakanan {...stateModal} pesan={this.state.pesanError} />
            <MessageAlert pesan={this.state.pesanError} />
          </div>
        </div>
        
        <ModalElement {...stateModal} />
      </div>
    );
  }
}