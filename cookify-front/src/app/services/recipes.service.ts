import { Injectable } from '@angular/core';
import {Recipes} from "../interfaces/recipes";
import {HttpClient} from "@angular/common/http";
import {Observable} from "rxjs";

@Injectable({
  providedIn: 'root'
})
export class RecipesService {
  private jsonRecipes = 'assets/fakedata/Recipes.json';
  private apiUrl = 'http://localhost:8000/';

  constructor(private http: HttpClient) { }

  getRecipes(): Observable<Recipes[]>{
    return this.http.get<Recipes[]>(this.jsonRecipes);
  }
//Todo : voir si on utilise le jwt
  new(recipe: Recipes): Observable<Recipes> {
    return this.http.post<Recipes>(this.apiUrl+'create/recipe', recipe, { withCredentials: true });
  }
}
