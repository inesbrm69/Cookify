import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { HomeComponent } from './home/home.component';
import { provideAnimationsAsync } from '@angular/platform-browser/animations/async';
import { LoginComponent } from './login/login.component';
import { RegisterComponent } from './register/register.component';
import {MatAnchor, MatButton, MatIconAnchor, MatIconButton} from "@angular/material/button";
import {MatIcon, MatIconModule} from "@angular/material/icon";
import { ExploreComponent } from './explore/explore.component';
import { NavBarComponent } from './nav-bar/nav-bar.component';
import { ProfileComponent } from './profile/profile.component';
import {MatProgressSpinner} from "@angular/material/progress-spinner";
import {MatCard, MatCardActions, MatCardHeader, MatCardImage} from "@angular/material/card";

@NgModule({
  declarations: [
    AppComponent,
    HomeComponent,
    LoginComponent,
    RegisterComponent,
    ExploreComponent,
    NavBarComponent,
    ProfileComponent
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    MatButton,
    MatIcon,
    MatIconModule,
    MatIconButton,
    MatAnchor,
    MatIconAnchor,
    MatProgressSpinner,
    MatCard,
    MatCardImage,
    MatCardHeader,
    MatCardActions
  ],
  providers: [
    provideAnimationsAsync()
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }
