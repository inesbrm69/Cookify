import {Component, inject, OnInit} from '@angular/core';
import {MatDialog} from "@angular/material/dialog";
import {RecipeFormComponent} from "../recipe-form/recipe-form.component";
import {Router} from "@angular/router";
import {UserService} from "../services/user.service";

@Component({
  selector: 'app-nav-bar',
  templateUrl: './nav-bar.component.html',
  styleUrl: './nav-bar.component.scss'
})
export class NavBarComponent implements OnInit {

  readonly dialog = inject(MatDialog);

  constructor(private userService: UserService, private router: Router) {
  }

  ngOnInit(): void {
    this.userService.getUser().subscribe({
      next: (data) => {},
      error: (error) => {

        if(error.status == 401){
          this.router.navigateByUrl('/login');
        }else{
          console.error('Error while getting user:', error);
        }
      }
    });
  }

  openDialog(): void {
    const dialogRef = this.dialog.open(RecipeFormComponent, {
      maxWidth: '75vw',
      maxHeight: '75vh',
      height: '100%',
      width: '100%'
    });

    dialogRef.afterClosed().subscribe(result => {

    });
  }

  logout(): void {
    const confirmed = window.confirm('Êtes-vous sûr de vouloir vous déconnecter ?');
    if (confirmed) {
      this.userService.logout().subscribe({
        next: () => {
          console.log('Déconnecté avec succès');
          this.router.navigateByUrl('/login');
        },
        error: (err) => {
          if (err.status === 404) {
            console.log('Session déjà supprimée ou erreur de déconnexion');
            this.router.navigateByUrl('/login');
          } else {
            console.error('Erreur inattendue :', err);
          }
        },
      });
    }
  }
}
