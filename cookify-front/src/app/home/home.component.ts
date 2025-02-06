import { Component } from '@angular/core';
import {MatIconModule} from '@angular/material/icon'
@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrl: './home.component.scss'
})
export class HomeComponent {
  counter = 0;

  increment() {
    this.counter++;
  }

  decrement() {
    if(this.counter > 0){
      this.counter--;
    }
  }
}
