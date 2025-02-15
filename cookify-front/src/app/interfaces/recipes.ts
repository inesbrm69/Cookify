import {Images} from "./images";
import {Category} from "./category";
import {QuantityFood} from "./quantity-food";

export interface Recipes {
  //Id : number,
  name : string
  cookingTime : number
  description? : string,
  calories : number,
  quantity : number,
  preparationTime : number,
  instructions : string,
  difficulty : string,
  isPublic : boolean,
  categories : Category[],
  ingredients : QuantityFood[],
  images : Images,
  image : File
}
