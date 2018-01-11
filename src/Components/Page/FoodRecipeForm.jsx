import React, { Component } from 'react'
import { connect} from 'react-redux'
import MessageAlert from '../MessageAlert'
import queryString from 'qs' // or query-string

class FoodRecipeForm extends Component {
	constructor()
	{
		super();
		this.state={ disabled : false };
	}

	handleChangeInput(e)
	{
		this.props.dispatch({
			type:'setForm',value:{name:e.target.value}
		});
	}
	sendPost(data)
	{
		fetch(window.helmi.api + 'master-resep-makanan2/insert_update_delete',{
			method: 'POST',
    	headers: {'Content-Type':'application/x-www-form-urlencoded'},
    	body: queryString.stringify(data)
		}).then( result => result.json() ).then( data => {
				this.props.dispatch( dispatch => {
					if(data.success){
						if(this.props.formRecipe.mode === 'Add'){
							dispatch({type:'updateAlert',value:{texts:'Data Berhasil Ditambahkan',className:'alert alert-success',type:'Added'} })
						}else{ 
							dispatch({type:'updateAlert',value:{texts:'Data Berhasil Diperbaharui',className:'alert alert-info',type:'Updated'} })
						}
						this.setState({disabled:true});  dispatch({type:'setFetchDefault'})
						setTimeout( () => {
							dispatch({type:'updateAlert',value:{texts:false } });
							this.props.history.push('/');
						}, 1000)

					}else if(data.error){
						this.props.dispatch({
							type:'updateAlert',value:{texts:data.error,className:'alert alert-danger',type:'Error'}
						});
					}
					
				});
			}).catch( error => console.log(error) );
	}
	handleSubmit(e)
	{
		e.preventDefault();
		if(this.refs.recipe_name.value){
			this.props.dispatch( dispatch => {
				if(this.props.formRecipe.mode === 'Add'){
					this.sendPost({ recipe_name:this.refs.recipe_name.value }); 
				}else{
					this.sendPost({ recipe_name:this.refs.recipe_name.value,id_recipe:this.refs.id_recipe.value,mode:'update'}); 
				} 
				
			});
		}else{
			this.props.dispatch({
				type:'updateAlert',value:{texts:'Nama Resep Makanan Tidak Boleh Kosong',className:'alert alert-warning',type:'Warning'}
			});
		}
		
	}
	handleBack()
	{
		let {history} =  this.props;
		history.push('/');
	}

	render(){

		return(
			<div className="col-lg-12">
		        <div className="row">
		          <div className="col-lg-12">
		            <ol className="breadcrumb">
		              <li><a >Home</a></li>
		              <li className="active"><span>Master Data </span></li>
		            </ol>
		          <h1>{this.props.formRecipe.mode === 'Add' ? 'Tambah' : 'Update'} Resep Makanan </h1>
		          </div>
		        </div>
		        <div className="row">
		          <div className="col-lg-12 main-box" style={{minHeight:'150px',paddingTop:'27px'}}> 
		             
		             <form data-tombol="form" onSubmit={this.handleSubmit.bind(this)} >
		              <div className="input-group">
		                <label className="input-group-addon">Nama Resep
		                        <span className="required"> * </span>
		                    </label>
		                    <input type="text" className="form-control" onChange={this.handleChangeInput.bind(this)} value={this.props.formRecipe.name} ref="recipe_name" disabled={this.state.disabled} />
		                    <input type="hidden" ref="id_recipe" value={this.props.formRecipe.id} />
		                    <input type="hidden" ref="mode"  />
		              </div>
		              <div style={{marginTop:'27px'}}>
		              <button className="btn btn-danger" disabled={this.state.disabled} > {this.props.formRecipe.mode} </button>
		              <button className="btn btn-default pull-right" onClick={this.handleBack.bind(this)} >Kembali</button>
		              </div>		              
		            </form>

		            <MessageAlert />

		          </div>
		        </div>
		    </div>
		);
	}
}

export default connect(state => state )(FoodRecipeForm)