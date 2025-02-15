import {Component, inject} from '@angular/core';
import {MAT_DIALOG_DATA, MatDialogContent, MatDialogTitle} from "@angular/material/dialog";
import {Recipes} from "../interfaces/recipes";

@Component({
  selector: 'app-recipe',
  templateUrl: './recipe.component.html',
  styleUrl: './recipe.component.scss',
})
export class RecipeComponent {

  data = inject(MAT_DIALOG_DATA);
  recipe: Recipes;
  apiUrlPublic: string = "http://localhost:8000/uploads/images/";

  constructor() {
    this.recipe = this.data.recipe;
  }
}
