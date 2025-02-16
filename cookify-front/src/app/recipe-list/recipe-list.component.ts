import {Component, inject} from '@angular/core';
import {FormBuilder, Validators} from "@angular/forms";
import {RecipesService} from "../services/recipes.service";
import {Router} from "@angular/router";
import {CategoryService} from "../services/category.service";
import {Category} from "../interfaces/category";
import {MAT_DIALOG_DATA, MatDialog} from "@angular/material/dialog";
import {Recipes} from "../interfaces/recipes";
import {RecipeComponent} from "../recipe/recipe.component";

@Component({
  selector: 'app-recipe-list',
  templateUrl: './recipe-list.component.html',
  styleUrl: './recipe-list.component.scss'
})
export class RecipeListComponent {
  recipes: Recipes[] = [];
  data = inject(MAT_DIALOG_DATA);
  apiUrlPublic: string = "http://localhost:8000/uploads/images/";
  constructor(private recipesService: RecipesService, private router: Router) {
    this.recipesService.getRecipeList(this.data.idList).subscribe({
      next: (data) => {
        this.recipes = data.recipes;
        },
      error: (error) => {
        console.error('Error while getting recipe list:', error);
      }
    });
  }

  dialog = inject(MatDialog);

  openDialog(recipeId?: number) {
    if(recipeId){
      this.recipesService.getRecipe(recipeId).subscribe({
        next: (data) => {
          this.dialog.open(RecipeComponent, {
            data: {
              recipe: data,
            },
          });
        },
        error: (error) => {
          console.error('Error while getting recipe list:', error);
        }
      });
    }
  }
}
