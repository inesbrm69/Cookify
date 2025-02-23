import { Injectable } from '@angular/core';
import {Observable} from "rxjs";
import {HttpClient} from "@angular/common/http";
import {User} from "../interfaces/user";

@Injectable({
  providedIn: 'root'
})
export class UserService {
  private apiUrl = 'http://13.60.53.129:8000/';

  constructor(private http: HttpClient) { }

  getUser(): Observable<any>{
    return this.http.get<any>(this.apiUrl+'user', { withCredentials: true });
  }
  login(user: User): Observable<any> {
    return this.http.post<any>(
      this.apiUrl+'login',
      user,
      { withCredentials: true }
    );
  }

  logout(): Observable<any> {
    return this.http.post<any>(
      this.apiUrl+'logout',
      {},
      { withCredentials: true }
    );
  }

  register(user: User): Observable<User> {
    return this.http.post<User>(this.apiUrl+'register', user, { withCredentials: true });
  }
}
