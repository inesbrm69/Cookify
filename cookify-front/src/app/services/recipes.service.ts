import { Injectable } from '@angular/core';
import {Recipes} from "../interfaces/recipes";
import {HttpClient} from "@angular/common/http";
import {Observable} from "rxjs";
import {Preferences} from "../interfaces/preferences";

@Injectable({
  providedIn: 'root'
})
export class RecipesService {
  private apiUrl = 'http://13.60.53.129:8000/';

  constructor(private http: HttpClient) { }

  getRecipes(): Observable<Recipes[]>{
    return this.http.get<Recipes[]>(this.apiUrl+'api/recipes', { withCredentials: true });
  }

  getRecipe(idRecipe: number): Observable<Recipes>{
    return this.http.get<Recipes>(this.apiUrl+'api/recipes/'+idRecipe, { withCredentials: true });
  }

  new(recipe: Recipes): Observable<Recipes> {
    const requestData = JSON.stringify(recipe);
    const formData = new FormData();
    formData.append('data', requestData);
    formData.append('image', recipe.image);
    console.log('FormData contenu :', formData.get('data'));
    return this.http.post<Recipes>(this.apiUrl+'api/recipes/create', formData, { withCredentials: true });
  }

  generateList(preferences: Preferences): Observable<any>{
    return this.http.post<any>(this.apiUrl+'api/recipelist/generate/'+preferences.mealQuantity, {preferences:preferences}, { withCredentials: true });
  }

  getRecipeList(idList: number): Observable<any>{
    return this.http.get<any>(this.apiUrl+'api/recipelist/'+idList, { withCredentials: true });
  }

  replaceRecipeInList(idList: number, idOldRecipe: number, idNewRecipe: number): Observable<any>{
    return this.http.put<any>(this.apiUrl+'api/recipelist/'+idList+'/update/'+idOldRecipe+'/with/'+idNewRecipe, {}, { withCredentials: true });
  }

  deleteRecipeInList(idList: number, idRecipe: number): Observable<any>{
    return this.http.delete<any>(this.apiUrl+'api/recipelist/'+idList+'/remove/'+idRecipe, { withCredentials: true });
  }
}
