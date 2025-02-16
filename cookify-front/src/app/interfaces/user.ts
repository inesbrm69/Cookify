import {Preferences} from "./preferences";

export interface User {
  email? : string,
  name? : string,
  lastName? : string,
  userName? : string,
  preferences? : Preferences[]
}
