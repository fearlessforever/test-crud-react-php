import React, { Component } from 'react'
import { connect } from 'react-redux'
import queryString from 'qs'

class FoodRecipe extends Component {

	handleAddButton()
	{
		let {history} = this.props;
		this.props.dispatch( dispatch => {
			dispatch({type:'setForm',value:{name:'',id:false ,mode:'Add'} });
			history.push('/form');
		});
		//console.log(this.props);
	}
	handleEditButton(e)
	{
		let index = this.props.dataRecipe.findIndex( i => i.id_recipe === e.target.getAttribute('data-id') );
		let {dataRecipe} = this.props;

		if(typeof dataRecipe[index] !== 'undefined' ){
			let {history} = this.props;
			this.props.dispatch( dispatch => {
				dispatch({type:'setForm',value:{name:dataRecipe[index].recipe_name,id:dataRecipe[index].id_recipe ,mode:'Update'} });
				history.push('/form');
				dispatch({type:'setFetchDefault' });
			});
		}else{

		}
	}

	handleRemButton(e)
	{
		let id = e.target.getAttribute('data-id')
		fetch(window.helmi.api + 'master-resep-makanan2/insert_update_delete',{
			method: 'POST',
    	headers: {'Content-Type':'application/x-www-form-urlencoded'},
    	body: queryString.stringify({mode:'delete',id_recipe: id})
		}).then( result => result.json() ).then( data => {
			if(data.success)
			this.props.dispatch( dispatch => {
				dispatch({type:'remRecipe',value: id });
			});
		});
		
		

	}
	componentDidMount()
	{
		this.getFetchedData();
	}
	getFetchedData( )
	{
		fetch(window.helmi.api + 'master-resep-makanan2')
		.then( result => result.json() ).then( data => {
			this.props.dispatch( dispatch => {
				dispatch({type:'setRecipe',value:data.data});
				dispatch({type:'setFetch',value:{loadedData:true} })
			});
		});
	}
	/*componentWillReceiveProps(nextProps) {
    	console.log(nextProps);
	} */

	render(){
		let tables, TABLE;
		if(this.props.fetchData.loadedData){
			tables = this.props.dataRecipe;
			TABLE = ( tables.length > 0 ) ? tables.map((v ) => {
				return <tr key={v.id_recipe}><td>{v.id_recipe}</td><td>{v.recipe_name}</td><td><button data-id={v.id_recipe} className="btn btn-danger" onClick={this.handleRemButton.bind(this)} ><i className="fa fa-times"></i></button> <button onClick={this.handleEditButton.bind(this)} className="btn btn-info" data-id={v.id_recipe} ><i className="fa fa-gear"></i></button></td></tr>
			}) : <tr><td colSpan="3" ><h4>Data Tidak Ditemukan</h4></td></tr> ;
		}else{
			TABLE =
				<tr><td colSpan="3"><div id="loading-image" style={{textAlign:'center', marginTop:'27px', minHeight:'100px'}}>
	          <img src="/external/img/loading.gif" alt="Loading...." />
	        </div>
	      </td></tr>
			 
		}

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
		                  <tbody>{ TABLE }</tbody>
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
	return {dataRecipe:store.dataRecipe,fetchData:store.fetchData}
} )(FoodRecipe)