export interface Recipes {
  Id : number,
  Name : string
  CookingTime : number
  Description? : string,
  Calories : number,
  Quantity : number,
  PreparationTime : number,
  Instructions : string,
  Difficulty : string,
  IsPublic : boolean
}
