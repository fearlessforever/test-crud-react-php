import React, { Component } from 'react'
import { connect } from 'react-redux'

class FoodRecipe extends Component {

	handleAddButton()
	{
		let {history} = this.props;
		this.props.dispatch( dispatch => {
			dispatch({type:'setForm',value:{nama:'',id:false ,mode:'Add'} });
			history.push('/form');
		});
		//console.log(this.props);
	}
	handleEditButton(e)
	{
		//let index = this.props.dataRecipe.findIndex( i => i === e.target.getAttribute('data-id') );
		let {dataRecipe} = this.props;
		let id = e.target.getAttribute('data-id');
		if(typeof dataRecipe[id] !== 'undefined' ){
			let {history} = this.props;
			this.props.dispatch( dispatch => {
				dispatch({type:'setForm',value:{nama:dataRecipe[id],id:id ,mode:'Update'} });
				history.push('/form');
			});
		}else{

		}
	}

	handleRemButton(e)
	{
		this.props.dispatch({
			type:'remRecipe',value:e.target.getAttribute('data-id')
		});
	}
	/*componentWillReceiveProps(nextProps) {
    	console.log(nextProps);
	} */

	render(){
		let tables = this.props.dataRecipe;
		let TABLE = ( tables.length > 0 ) ? tables.map((v,k) => {
			return <tr key={k}><td>{k}</td><td>{v}</td><td><button data-id={k} className="btn btn-danger" onClick={this.handleRemButton.bind(this)} ><i className="fa fa-times"></i></button> <button onClick={this.handleEditButton.bind(this)} className="btn btn-info" data-id={k} ><i className="fa fa-gear"></i></button></td></tr>
		}) : false ;

		return(
			<div className="col-lg-12">
		        <div className="row">
		          <div className="col-lg-12">
		            <ol className="breadcrumb">
		              <li><a >Home</a></li>
		              <li className="active"><span>Master Data </span></li>
		            </ol>
		          <h1>Daftar Resep Makanan </h1>
		          </div>
		        </div>
		        <div className="row">
		          <div className="col-lg-12 main-box"> 
		            <header className="main-box-header clearfix">
		              <h2 className="pull-left">Resep </h2>
		              <div className="filter-block pull-right">
		              <a className="btn btn-primary pull-right" onClick={this.handleAddButton.bind(this) } >
		                <i className="fa fa-plus-circle fa-lg"></i> Tambah Resep Makanan
		              </a>
		              </div>
		            </header>

		            <div id="tempat-total-table" className="main-box-body clearfix"></div>
		            <div className="main-box-body">
		              <div id="table-crud">
		                <table className="table table-striped table-bordered table-hover table-checkable order-column" >
		                  <thead>
		                    <tr>
		                      <th> No </th>
		                      <th> Nama Resep Makanan </th>
		                      <th> Action </th>
		                    </tr>
		                  </thead>
		                  <tbody>{ TABLE ? TABLE : <tr><td colSpan="3" ><h4>Data Tidak Ditemukan</h4></td></tr> }</tbody>
		                </table>

		              </div>
		            </div>

		          </div>
		        </div>
		    </div>
		);
	}
}

export default connect( store => {
	return {dataRecipe:store.dataRecipe}
} )(FoodRecipe)