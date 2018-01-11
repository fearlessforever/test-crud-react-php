import { createStore , combineReducers , applyMiddleware} from 'redux'
import thunk from 'redux-thunk'

const formRecipe = (state={ name:'',id:false ,mode:'Add'} , action) => {
	switch(action.type){
		case 'setForm':
			state = Object.assign({},state,action.value);
			break;
		default:break;
	}
	
	return state;
}
const fetchData = ( state = {
	fetching:false , loadedData:false , getdata:false
} , action) => {
	switch(action.type){
		case 'setFetch':
			state = Object.assign({},state,action.value);
			break;
		case 'setFetchDefault':
			state = {fetching:false , loadedData:false , getdata:false};
			break;
		default:break;
	}
	return state;
}
const permission = (state={}, action) => {

	return state;
}
const messageAlert = (state= { texts:false,type:false,className:false }, action) => {
	switch(action.type){
		case 'updateAlert':
			state = Object.assign({},state,action.value);
			break;
		default:break;
	}
	return state;
}
const dataRecipe = (state=[], action) => {
	let newData=[];
	switch(action.type){
		case 'updateRecipe':
			state[ action.value.id ]= action.value.name
			break;
		case 'addRecipe':
			state.push(action.value);
			break;
		case 'remRecipe':
			let index = state.findIndex( i => i.id_recipe === action.value );
			if(typeof state[index] !== 'undefined'){
				state.splice(index , 1); 
				state.map( v => {
					return newData.push(v);
				});
				return newData;
			}else{
				break;
			}
		case 'setRecipe':
			state = action.value.length > 0 ? action.value : state ;
			break;
		default:break;
	}
	return state;
}

const middleware = applyMiddleware(thunk);

const reducers = combineReducers({
	formRecipe,permission,messageAlert,dataRecipe,fetchData
});
const store = createStore(reducers,middleware);
export default store;