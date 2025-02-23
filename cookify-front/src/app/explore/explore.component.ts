import {Component, inject, Injector, OnInit} from '@angular/core';
import {Recipes} from "../interfaces/recipes";
import {RecipesService} from "../services/recipes.service";
import {Router} from "@angular/router";
import {MAT_DIALOG_DATA, MatDialog, MatDialogRef} from "@angular/material/dialog";
import {RecipeComponent} from "../recipe/recipe.component";

@Component({
  selector: 'app-explore',
  templateUrl: './explore.component.html',
  styleUrl: './explore.component.scss'
})
export class ExploreComponent implements OnInit {
  isLoading: boolean = false;
  recipes: Recipes[] = [];
  apiUrlPublic: string = "http://13.60.53.129/uploads/images/";
  isRecipeListMode: boolean = false;
  private dialogRef? : null  |  MatDialogRef<ExploreComponent> = null;
  private  dialogData;
  constructor(private recipeService: RecipesService, private router: Router, private injector: Injector) {
    this.dialogRef = this.injector.get(MatDialogRef, null);
    this.dialogData = this.injector.get(MAT_DIALOG_DATA, null);

    if(this.dialogRef != null && this.dialogData !=  null){
      this.isRecipeListMode = true;
    }
  }

  ngOnInit(): void {
    this.recipeService.getRecipes().subscribe({
      next: (data) => {
        this.recipes = data.filter(recipe=> {
                return recipe.isPublic
              });
        },
      error: (error) => {
        if(error.status == 401){
          this.router.navigateByUrl('/login');
        }else{
          console.error('Error while getting recipes:', error);
        }
      }
    });
  }

  dialog = inject(MatDialog);

  openDialog(recipe: Recipes) {
    this.dialog.open(RecipeComponent, {
      data: {
        recipe: recipe,
      },
    });
  }

  replace(newRecipeId?: number){
    if(this.dialogRef != null && this.dialogData !=  null && newRecipeId != null){
      let idList = this.dialogData.idList;
      let idOldRecipe = this.dialogData.idOldRecipe;

      this.recipeService.replaceRecipeInList(idList, idOldRecipe, newRecipeId).subscribe({
        next: (data) => {
          this.dialogRef?.close();

        },
        error: (error) => {
            console.error('Error while replacing recipe:', error);
        }
      });
    }
  }
}
