import React, { Component } from 'react'
import { Route, Switch } from 'react-router'

import FoodRecipe from '../Components/Page/FoodRecipe'
import FoodRecipeForm from '../Components/Page/FoodRecipeForm'
import ErrorPage from '../Components/ErrorPage'

export default class MyRoute extends Component{
	render(){
		return (
			<Switch>
				<Route exact path="/" component={FoodRecipe} > </Route>
				<Route exact path="/form" component={FoodRecipeForm} > </Route>
				<Route component={ErrorPage} > </Route>
			</Switch>
		);
	}
}