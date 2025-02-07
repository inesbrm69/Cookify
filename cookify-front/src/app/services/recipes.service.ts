import { Injectable } from '@angular/core';
import {Recipes} from "../interfaces/recipes";
import {HttpClient} from "@angular/common/http";
import {Observable} from "rxjs";

@Injectable({
  providedIn: 'root'
})
export class RecipesService {
  private jsonRecipes = 'assets/fakedata/Recipes.json';
  constructor(private http: HttpClient) { }

  getRecipes(): Observable<Recipes[]>{
    return this.http.get<Recipes[]>(this.jsonRecipes);
  }
}
