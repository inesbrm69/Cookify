import {Component, inject, OnInit} from '@angular/core';
import {Recipes} from "../interfaces/recipes";
import {MAT_DIALOG_DATA} from "@angular/material/dialog";
import {FormBuilder, FormGroup, Validators} from "@angular/forms";
import {RecipesService} from "../services/recipes.service";
import {Router} from "@angular/router";

@Component({
  selector: 'app-recipe-form',
  templateUrl: './recipe-form.component.html',
  styleUrl: './recipe-form.component.scss'
})
export class RecipeFormComponent implements OnInit {
  newRecipeForm: FormGroup;
  errorMessage: string | null = null;
  isLoading: boolean = false;
  selectedFile: File | null = null;
  imagePreview: string | null = null;


  constructor(private fb: FormBuilder, private recipesService: RecipesService, private router: Router) {
    this.newRecipeForm = this.fb.group({
      name: ['', Validators.required],
      cookingTime: ['', [Validators.required, Validators.pattern('^[0-9]+$')]],
      description: [''],
      calories: ['', [Validators.required, Validators.pattern('^[0-9]+$')]],
      quantity: ['', [Validators.required, Validators.pattern('^[0-9]+$')]],
      preparationTime: ['', [Validators.required, Validators.pattern('^[0-9]+$')]],
      instructions: ['', Validators.required],
      difficulty: ['', Validators.required],
      isPublic: [false, Validators.required],
      image: [null, Validators.required]
    });
  }

  ngOnInit(): void {

  }

  // Soumettre le formulaire
  onSubmit(): void {
    this.isLoading = true;
    if (this.newRecipeForm.valid) {
      const newRecipe: Recipes = this.newRecipeForm.value;
      console.log(newRecipe);
      /*
      this.recipesService.new(newRecipe).subscribe({
        next: (response) => {
          this.isLoading = false;
          //this.router.navigateByUrl('/login');
          //
        },
        error: (error) => {
          this.isLoading = false;
          this.errorMessage = 'An unexpected error occurred. Please try again later.';
          console.error('Error while registering',error);
        }
      });
       */
    } else {
      this.isLoading = false;
      this.errorMessage = 'Please fill out the form correctly before submitting.';
      console.log('Invalid form');
    }
  }

  onFileSelected(event: Event): void {
    const file = (event.target as HTMLInputElement).files?.[0];
    if (file) {
      this.selectedFile = file;

      // Générer un aperçu de l'image sélectionnée
      const reader = new FileReader();
      reader.onload = () => {
        this.imagePreview = reader.result as string;
      };
      reader.readAsDataURL(file);
    }
  }
}
