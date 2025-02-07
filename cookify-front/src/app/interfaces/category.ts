import {Recipes} from "./recipes";
//Categorie
export interface Category {
  Id : number,
  Name : string,
  Recipes : Recipes[]
}
