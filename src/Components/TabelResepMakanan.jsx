import React, { Component } from 'react';

import $ from 'jquery' ;
$.DataTable = require('datatables.net');

export default class TabelResepMakanan extends Component {
  constructor()
  {
    super();
    this.state={
      permission:{}
    };
    window.helmi.that = this;
  } 

  componentDidMount() {
    //Load DataTable
    this.setDataTable(); this.setModal();
    //Set Click Event
    $(document).on('click','[data-tombol]',function(e){
      e.preventDefault();
      var {that} = window.helmi ;

      switch($(this).attr('data-tombol')){
        case 'tambah':
            that.toggleModal();
            if(!that.state.permission.create){
              $('button[data-tombol="simpan"]').attr('disabled','disabled');
              $('.modal form input').attr('readonly','readonly');
            }
          break;
        case 'hapus':
          if(typeof $(this).attr('data-id') !== 'undefined'){
              that.toggleModal();
              $('.modal-body .row').prepend('<h4>Anda Yakin ingin Menghapus Resep ini ? </h4>');
              $('.modal form [name="mode"]').val('delete');
              $('.modal form [name="id_resep"]').val($(this).attr('data-id'));
              $('.modal-title h4').text('Delete '+ $(this).attr('data-id') );
              $('.modal form input').attr('readonly','readonly');
              $('button[data-tombol="simpan"]').text('DELETE');            
          }
          break;
        case 'update':
          if(typeof $(this).attr('data-id') !== 'undefined'){
            that.toggleModal();
            $('.modal form [name="mode"]').val('update');
            $('.modal form input').attr('readonly','readonly');
            $.ajax({
              url: window.helmi.api + 'master-resep-makanan',
              data:{id_resep:$(this).attr('data-id'),accesstoken:that.props.accesstoken},
              type:'POST',dataType:'json',
              success:function(resp){
              try{
                if(resp.data){
                  let {data} = resp;
                  $('.modal form input').removeAttr('readonly');
                  for( var k in data[0]){
                    $('.modal form [name="'+k+'"]').val(data[0][k]);
                  }
                }
              }catch(e){}
                
              }
            });
            
            $('.modal-title h4').text('Update '+ $(this).attr('data-id') );
            $('button[data-tombol="simpan"]').text('UPDATE').removeClass('btn-danger').addClass('btn-info');
            
          }
          break;
        case 'simpan': $('form[data-tombol="form"]').trigger('submit'); break;
        default:break;
      }
    });
    $(document).on('submit','form[data-tombol="form"]',function(e){
          let data ={
            nama_resep:$('.modal form [name="nama_resep"]').val() ,
            id_resep:$('.modal form [name="id_resep"]').val() ,
            mode:$('.modal form [name="mode"]').val() ,
          };
          var {that} = window.helmi ;
          data = {...data,accesstoken:that.props.accesstoken};
          $.ajax({
            url: window.helmi.api + 'master-resep-makanan/insert_update_delete',
            data,
            type:'POST',dataType:'json',
            success:(resp) => {
              if(resp.success && resp.message ){
                if(resp.total){
                  that.totalTabel += parseInt(resp.total,10) ;
                }
                $('.modal #pesan-error').html(`
                    <div class="alert alert-success"><strong>Success : </strong>${resp.message}</div>\
                  `);
                  setTimeout(() => {
                    //that.props.dispatch({type:'TOGGLE_MODAL',value:'false'});
                    that.toggleModal();
                    that.dataTable.ajax.reload(null, false);
                    //that.setDataTable();
                  },1000);
              }
            },
            error:function(xhr){
              if(typeof xhr.responseJSON !== 'undefined'){
                  if(xhr.responseJSON.error){
                    $('.modal #pesan-error').html(`
                      <div class="alert alert-danger"><strong>Error : </strong>${xhr.responseJSON.error}</div>
                    `);
                  }
                }
            },beforeSend:function(){
              $('.modal [data-tombol]').attr('disabled','disabled');
            },complete:function(){
              $('.modal [data-tombol]').removeAttr('disabled');
            }
          });
          e.preventDefault();
          
       });
  }

  setModal()
  {
    if(typeof this.props.setModalContent === 'function')
      this.props.setModalContent({
          body:`<div class="row"><form data-tombol="form">
              <div class="input-group">
                <label class="input-group-addon">Nama Resep
                        <span class="required"> * </span>
                    </label>
                    <input type="text" class="form-control" data-value="nama_resep" name="nama_resep"  />
                    <input type="hidden" data-value="id_resep" name="id_resep"  />
                    <input type="hidden" name="mode"  />
              </div>
              </form></div><div style="margin-top:15px;" id="pesan-error"></div>`,
          header:'<h4>Tambah Resep Makanan</h4>',
          footer:'<button class="btn btn-danger pull-left" data-tombol="simpan">Kirim</button>',
        });
  }
  setPesanError(data)
  {
    if(typeof this.props.pesan.setPesanError === 'function')
      this.props.pesan.setPesanError(data);
  }

  toggleModal()
  {
    if(typeof this.props.closeClick === 'function')
      this.props.closeClick();
  }

  setDataTable(){
      var {that} = window.helmi ;
      this.dataTable = $('#tempat-table-crud table').DataTable({
           destroy: true,
             processing: true,
             serverSide: true,
             searching: false,
             ajax: {
              url: window.helmi.api + 'master-resep-makanan' ,
              data:function(d){
                d.accesstoken = that.props.accesstoken;
                d.totalrow = that.totalTabel;
                d.cari = that.searchQuery;
              },
              type: "POST",
              complete:function(){
                $('#loading-image').hide();
              },
              error:function(xhr){
                let errorMsg = {error:'Error Load Data',code:503}
                if(typeof xhr.responseJSON !== 'undefined'){
                  if(xhr.responseJSON.error)errorMsg.error = xhr.responseJSON.error;
                  if(typeof xhr.responseJSON.accesstoken !== 'undefined'){
                   /* that.props.dispatch({
                      type:'CHANGE_ACCESSTOKEN',value:xhr.responseJSON.accesstoken
                    });*/
                  }

                }
                that.setPesanError({teks:errorMsg.error});
              },
              dataSrc:(json) => {
                if(json.recordsTotal){
                  that.totalTabel = json.recordsTotal;
                }
                if(json.permission){
                  let permission = Object.assign({},that.state.permission,json.permission);
                  that.setState({permission});
                  console.log(permission);
                }

                var baru = [];
                const {data} = json;
                for ( var i=0; i < data.length ; i++ ) {
                  var no=0; baru[i]=[];
                  for(var k in data[i] ){
                    baru[i][no] = data[i][k]; 
                    no++;
                  }
                  baru[i][no]='';
                  if(that.state.permission.delete){
                    baru[i][no] +=`<button class="btn btn-action btn-danger" data-id="${data[i].id_resep}" data-tombol="hapus"><i class="fa fa-times"></i></button> `;
                  }
                  if(that.state.permission.update){
                    baru[i][no] += `<button class="btn btn-action btn-info" data-id="${data[i].id_resep}" data-tombol="update"><i class="fa fa-gear"></i></button>`;
                  }
            }
            return baru;
          }
             },
             columns:[
              {name: "id_resep",searchable: false,  className: "text-center", width: "5%"},
              {name: "nama_resep",orderable:true},
              {name: "action",orderable: false,searchable: false, className: "text-center", width: "15%"}
             ],
             bStateSave:true,
             //pagingType:'bootstrap_extended'
        });
    }
  shouldComponentUpdate() {
        return false;
  }
  // unbind
  componentWillUnmount(){
     $('#tempat-table-crud table').DataTable().destroy(true);
     $(document).off('click','[data-tombol]');
     $(document).off('submit','form[data-tombol="form"]');
  }

  render() {

    return (
      <div > 
            <header className="main-box-header clearfix">
              <h2 className="pull-left">Resep </h2>
              <div className="filter-block pull-right">
              <a className="btn btn-primary pull-right" data-tombol="tambah">
                <i className="fa fa-plus-circle fa-lg"></i> Tambah Resep Makanan
              </a>
              </div>
            </header>
            <div id="tempat-total-table" className="main-box-body clearfix"></div>
            <div className="main-box-body">
              <div id="tempat-table-crud">
                <table className="table table-striped table-bordered table-hover table-checkable order-column" >
                  <thead>
                    <tr>
                      <th> No </th>
                      <th> Nama Resep Makanan </th>
                      <th> Action </th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
              <div id="loading-image" style={{textAlign:'center', minHeight:'150px'}}>
                <img src="/external/img/loading.gif" alt="Loading...." />
              </div>
              </div>
            </div>
        </div> 
    );
  }
}