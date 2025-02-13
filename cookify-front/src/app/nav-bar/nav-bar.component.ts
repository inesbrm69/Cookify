import {Component, inject} from '@angular/core';
import {MatDialog} from "@angular/material/dialog";
import {RecipeFormComponent} from "../recipe-form/recipe-form.component";

@Component({
  selector: 'app-nav-bar',
  templateUrl: './nav-bar.component.html',
  styleUrl: './nav-bar.component.scss'
})
export class NavBarComponent {

  readonly dialog = inject(MatDialog);

  openDialog(): void {
    const dialogRef = this.dialog.open(RecipeFormComponent, {
      maxWidth: '75vw',
      maxHeight: '75vh',
      height: '100%',
      width: '100%'
    });

    dialogRef.afterClosed().subscribe(result => {
      console.log('The dialog was closed');
      //refresh?
    });
  }
}
