import { Component } from '@angular/core';
import {MatIconModule} from '@angular/material/icon'
import {FormBuilder, FormGroup, Validators} from "@angular/forms";
import {RecipesService} from "../services/recipes.service";
import {Recipes} from "../interfaces/recipes";
import {Preferences} from "../interfaces/preferences";
import {Router} from "@angular/router";
import {CategoryService} from "../services/category.service";
import {Category} from "../interfaces/category";
import {MatDialog} from "@angular/material/dialog";
import {RecipeListComponent} from "../recipe-list/recipe-list.component";
@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrl: './home.component.scss'
})
export class HomeComponent {
  recipeListForm: FormGroup;
  errorMessage: string | null = null;
  dietList: string[] = ["Végétarien", "Vegan", "Omnivore"];
  allergyList: string[] = ["Aucune", "Gluten", "Lactose"];

  constructor(private fb: FormBuilder, private recipesService: RecipesService, private router: Router, public dialog: MatDialog) {
    this.recipeListForm = this.fb.group({
      diet: ['', Validators.required],
      allergy: ['', [Validators.required]],
      mealQuantity: [0, [Validators.required, Validators.pattern('^[0-9]+$')]]
    });
  }
  increment() {
    let value = this.recipeListForm.get('mealQuantity')?.value || 0;
    this.recipeListForm.patchValue({ mealQuantity: value + 1 });
  }

  decrement() {
    let value = this.recipeListForm.get('mealQuantity')?.value || 0;
    if (value > 0) {
      this.recipeListForm.patchValue({ mealQuantity: value - 1 });
    }
  }

  onSubmit(): void {
    if (this.recipeListForm.valid) {
      const preferences: Preferences = this.recipeListForm.value;

      this.recipesService.generateList(preferences).subscribe({
        next: (response) => {
          if (response && response.id !== undefined) {
            this.dialog.open(RecipeListComponent, {
              data: { idList: response.id },
            });
          } else {
            console.warn("La réponse ne contient pas d'ID.");
          }
        },
        error: (error) => {
          this.errorMessage = error.error?.error || "Une erreur s'est produite.";
          console.error('Error : ',error);
        }
      });
    } else {
      this.errorMessage = 'Please fill out the form correctly before submitting.';
      console.log('Invalid form');
    }
  }


}
