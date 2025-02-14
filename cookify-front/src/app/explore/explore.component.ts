import { Component } from '@angular/core';
import {Recipes} from "../interfaces/recipes";

@Component({
  selector: 'app-explore',
  templateUrl: './explore.component.html',
  styleUrl: './explore.component.scss'
})
export class ExploreComponent {
  isLoading: boolean = false;
  recipes: Recipes[] = [];
}
