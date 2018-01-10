import React, { Component } from 'react'
import { connect} from 'react-redux'
import MessageAlert from '../MessageAlert'

class FoodRecipeForm extends Component {
	constructor()
	{
		super();
		this.state={ disabled : false };
	}

	handleChangeInput(e)
	{
		this.props.dispatch({
			type:'setForm',value:{nama:e.target.value}
		});
	}

	handleSubmit(e)
	{
		e.preventDefault();
		if(this.refs.recipe_name.value){
			this.props.dispatch( dispatch => {
				if(this.props.formRecipe.mode === 'Add'){
					dispatch({type:'addRecipe',value:this.refs.recipe_name.value});
					dispatch({type:'updateAlert',value:{teks:'Data Berhasil Ditambahkan',kelas:'alert alert-success',tipe:'Added'} })
				}else{
					dispatch({type:'updateRecipe',value:{
						nama:this.refs.recipe_name.value,
						id:this.refs.id_recipe.value
					} });
					dispatch({type:'updateAlert',value:{teks:'Data Berhasil Diperbaharui',kelas:'alert alert-info',tipe:'Updated'} })
				}
				
				this.setState({disabled:true});
				setTimeout( () => {
					dispatch({type:'updateAlert',value:{teks:false } });
					this.props.history.push('/');
				}, 1000)
			});
		}else{
			this.props.dispatch({
				type:'updateAlert',value:{teks:'Data Gagal Ditambahkan',kelas:'alert alert-danger',tipe:'Failed'}
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
		                    <input type="text" className="form-control" onChange={this.handleChangeInput.bind(this)} value={this.props.formRecipe.nama} ref="recipe_name" disabled={this.state.disabled} />
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