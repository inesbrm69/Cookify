import {Recipes} from "./recipes";
import {Food} from "./food";

export interface QuantityFood {
  Id : number,
  Quantity : number,
  Unity : string,
  Recipes : Recipes,
  Food : Food
}
