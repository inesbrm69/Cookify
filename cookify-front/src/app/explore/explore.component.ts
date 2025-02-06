import { Component } from '@angular/core';

@Component({
  selector: 'app-explore',
  templateUrl: './explore.component.html',
  styleUrl: './explore.component.scss'
})
export class ExploreComponent {
  isLoading: boolean = false;
  reciepes: Reciepe[] = [];
}
export interface Reciepe {
  Name : string
  CookingTime : number
  Desc : string
  Calories : number
}
