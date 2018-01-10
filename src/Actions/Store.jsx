import { createStore , combineReducers , applyMiddleware} from 'redux'
import thunk from 'redux-thunk'

const formRecipe = (state={ nama:'',id:false ,mode:'Add'} , action) => {
	switch(action.type){
		case 'setForm':
			state = Object.assign({},state,action.value);
			break;
		default:break;
	}
	
	return state;
}
const permission = (state={}, action) => {

	return state;
}
const messageAlert = (state= { teks:false,tipe:false,kelas:false }, action) => {
	switch(action.type){
		case 'updateAlert':
			state = Object.assign({},state,action.value);
			break;
		default:break;
	}
	return state;
}
const dataRecipe = (state=[], action) => {
	let baru=[];
	switch(action.type){
		case 'updateRecipe':
			state[ action.value.id ]= action.value.nama
			break;
		case 'addRecipe':
			state.push(action.value);
			break;
		case 'remRecipe':
			if(typeof state[action.value] !== 'undefined'){
				state.splice(action.value , 1); 
				state.map( v => {
					return baru.push(v);
				});
				return baru;
			}else{
				break;
			}			
		default:break;
	}
	return state;
}

const middleware = applyMiddleware(thunk);

const reducers = combineReducers({
	formRecipe,permission,messageAlert,dataRecipe
});
const store = createStore(reducers,middleware);
export default store;