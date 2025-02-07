import {Recipes} from "./recipes";
//Commentaire
export interface Notice {
  Id : number,
  Comment? : string,
  Rating : number,
  Recipes : Recipes
}
