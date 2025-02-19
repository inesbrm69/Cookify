import {Component, inject, OnInit} from '@angular/core';
import {Recipes} from "../interfaces/recipes";
import {MatDialogRef} from "@angular/material/dialog";
import {FormArray, FormBuilder, FormGroup, Validators} from "@angular/forms";
import {RecipesService} from "../services/recipes.service";
import {Router} from "@angular/router";
import {CategoryService} from "../services/category.service";
import {Category} from "../interfaces/category";

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
  categoriesList: Category[] = [];
  difficultyList: string[] = ["Facile","Moyen","Difficile"];

  readonly dialogRef = inject(MatDialogRef<RecipeFormComponent>);
  constructor(private fb: FormBuilder, private recipesService: RecipesService, private router: Router, private categoryService: CategoryService) {
    this.categoryService.getCategory().subscribe({
      next: (data) => {this.categoriesList = data;},
      error: (error) => {
          console.error('Error while getting categories:', error);
      }
    });

    this.newRecipeForm = this.fb.group({
      name: ['', Validators.required],
      cookingTime: ['', [Validators.required]],
      description: [''],
      calories: ['', [Validators.required, Validators.pattern('^[0-9]+$')]],
      quantity: ['', [Validators.required, Validators.pattern('^[0-9]+$')]],
      preparationTime: ['', [Validators.required]],
      instructions: ['', Validators.required],
      difficulty: ['', Validators.required],
      isPublic: [false, Validators.required],
      categories: [[], Validators.required],
      image: [null, Validators.required],
      ingredients: this.fb.array([])
    });
    this.addInput();
  }

  ngOnInit(): void {
  }

  // Soumettre le formulaire
  onSubmit(): void {
    this.isLoading = true;
    if (this.newRecipeForm.valid) {
      const newRecipe: Recipes = this.newRecipeForm.value;
      if(this.selectedFile){
        newRecipe.image = this.selectedFile;
      }

      this.recipesService.new(newRecipe).subscribe({
        next: (response) => {
          this.isLoading = false;
          this.dialogRef.close();
          if(this.router.url === '/explorer'){
            window.location.reload();
          }else{
            this.router.navigateByUrl('/explorer');
          }
        },
        error: (error) => {
          this.isLoading = false;
          this.errorMessage = 'An unexpected error occurred. Please try again later.';
          console.error('Error : ',error);
        }
      });
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

      const reader = new FileReader();
      reader.onload = () => {
        this.imagePreview = reader.result as string;
      };
      reader.readAsDataURL(file);
    }
  }

  get inputs(): FormArray {
    return this.newRecipeForm.get('ingredients') as FormArray;
  }

  addInput(): void {
    this.inputs.push(this.fb.group({
        name: ['', Validators.required],
        unity: ['', Validators.required],
        quantity: ['', [Validators.required, Validators.pattern('^[0-9]+$')]]
    })
    );
  }

  removeInput(index: number): void {
    this.inputs.removeAt(index);
  }


}
