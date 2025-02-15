import {Component, inject, OnInit} from '@angular/core';
import {Recipes} from "../interfaces/recipes";
import {RecipesService} from "../services/recipes.service";
import {Router} from "@angular/router";
import {MatDialog} from "@angular/material/dialog";
import {RecipeComponent} from "../recipe/recipe.component";

@Component({
  selector: 'app-explore',
  templateUrl: './explore.component.html',
  styleUrl: './explore.component.scss'
})
export class ExploreComponent implements OnInit {
  isLoading: boolean = false;
  recipes: Recipes[] = [];
  apiUrlPublic: string = "http://localhost:8000/uploads/images/";

  constructor(private recipeService: RecipesService, private router: Router) {
  }

  ngOnInit(): void {
    this.recipeService.getRecipes().subscribe({
      next: (data) => {this.recipes = data;},
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
}
