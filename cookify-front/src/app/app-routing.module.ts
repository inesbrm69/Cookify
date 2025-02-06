import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import {HomeComponent} from "./home/home.component";
import {ExploreComponent} from "./explore/explore.component";
import {ProfileComponent} from "./profile/profile.component";

const routes: Routes = [
  {
    path: '', redirectTo: '/accueil', pathMatch: 'full'
  },
  {
    path: 'accueil', component: HomeComponent
  },
  {
    path: 'explorer', component: ExploreComponent
  },
  {
    path: 'profil', component: ProfileComponent
  },
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
