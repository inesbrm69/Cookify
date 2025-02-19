import {Component, inject} from '@angular/core';
import {FormBuilder, Validators} from "@angular/forms";
import {RecipesService} from "../services/recipes.service";
import {Router} from "@angular/router";
import {CategoryService} from "../services/category.service";
import {Category} from "../interfaces/category";
import {MAT_DIALOG_DATA, MatDialog, MatDialogRef} from "@angular/material/dialog";
import {Recipes} from "../interfaces/recipes";
import {RecipeComponent} from "../recipe/recipe.component";
import {ExploreComponent} from "../explore/explore.component";

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

  replace(recipeId?: number){

    const dialogRef = this.dialog.open(ExploreComponent, {
      data: {
        isRecipeListMode: true,
        idList: this.data.idList,
        idOldRecipe: recipeId
      },
      height: '70%'
    });

    dialogRef.afterClosed().subscribe(result => {
      this.recipesService.getRecipeList(this.data.idList).subscribe({
        next: (data) => {
          this.recipes = data.recipes;
        },
        error: (error) => {
          console.error('Error while getting recipe list:', error);
        }
      });
    });
  }

  delete(idRecipe?: number){
    if(idRecipe != null) {
      this.recipesService.deleteRecipeInList(this.data.idList, idRecipe).subscribe({
        next: (data) => {
          this.recipesService.getRecipeList(this.data.idList).subscribe({
            next: (data) => {
              this.recipes = data.recipes;
            },
            error: (error) => {
              console.error('Error while getting recipe list:', error);
            }
          });
        },
        error: (error) => {
          console.error('Error while deleting recipe:', error);
        }
      });
    }
  }
}
