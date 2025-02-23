import { Injectable } from '@angular/core';
import {Observable} from "rxjs";
import {HttpClient} from "@angular/common/http";
import {Category} from "../interfaces/category";

@Injectable({
  providedIn: 'root'
})
export class CategoryService {
  private apiUrl = 'http://13.60.53.129:8000/';

  constructor(private http: HttpClient) { }

  getCategory(): Observable<Category[]>{
    return this.http.get<Category[]>(this.apiUrl+'api/categories', { withCredentials: true });
  }
}
