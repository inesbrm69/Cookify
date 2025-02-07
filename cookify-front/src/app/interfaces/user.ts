import {Preferences} from "./preferences";

export interface User {
  Id : number,
  Email : string,
  Roles : string[],
  Name : string,
  LastName : string,
  UserName : string,
  Preferences : Preferences[]
}
