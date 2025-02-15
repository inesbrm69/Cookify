import {Recipes} from "./recipes";
//Categorie
export interface Category {
  Id : number,
  name : string,
  recipes : Recipes[]
}
