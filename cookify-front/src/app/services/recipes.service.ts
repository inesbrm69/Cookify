import { Injectable } from '@angular/core';
import {Recipes} from "../interfaces/recipes";
import {HttpClient} from "@angular/common/http";
import {Observable} from "rxjs";

@Injectable({
  providedIn: 'root'
})
export class RecipesService {
  private apiUrl = 'http://localhost:8000/';

  constructor(private http: HttpClient) { }

  getRecipes(): Observable<Recipes[]>{
    return this.http.get<Recipes[]>(this.apiUrl+'api/recipes', { withCredentials: true });
  }

  new(recipe: Recipes): Observable<Recipes> {
    const requestData = JSON.stringify(recipe);
    const formData = new FormData();
    formData.append('data', requestData);
    formData.append('image', recipe.image);
    console.log('FormData contenu :', formData.get('data'));
    return this.http.post<Recipes>(this.apiUrl+'api/recipes/create', formData, { withCredentials: true });
  }
}
