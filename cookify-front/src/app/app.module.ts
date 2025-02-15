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
import {MatCard, MatCardActions, MatCardContent, MatCardHeader, MatCardImage} from "@angular/material/card";
import {HttpClientModule} from "@angular/common/http";
import { RecipeComponent } from './recipe/recipe.component';
import {
  MatDialogActions,
  MatDialogClose,
  MatDialogContainer,
  MatDialogContent,
  MatDialogTitle
} from "@angular/material/dialog";
import { RecipeFormComponent } from './recipe-form/recipe-form.component';
import {ReactiveFormsModule} from "@angular/forms";
import {MatError, MatFormField, MatLabel} from "@angular/material/form-field";
import {MatInput} from "@angular/material/input";
import {MatSlideToggle} from "@angular/material/slide-toggle";
import { MaterialFileInputModule } from 'ngx-material-file-input';
import {MatOption, MatSelect, MatSelectTrigger} from "@angular/material/select";
import {MatChipGrid, MatChipInput, MatChipRow, MatChipsModule} from "@angular/material/chips";
import {MatGridList} from "@angular/material/grid-list";
import {MatRow} from "@angular/material/table";
import {MatDatepicker, MatDatepickerInput, MatDatepickerToggle} from "@angular/material/datepicker";
import {DateAdapter, MAT_DATE_FORMATS, NativeDateAdapter} from "@angular/material/core";
import {MatSlider, MatSliderThumb} from "@angular/material/slider";

@NgModule({
  declarations: [
    AppComponent,
    HomeComponent,
    LoginComponent,
    RegisterComponent,
    ExploreComponent,
    NavBarComponent,
    ProfileComponent,
    RecipeComponent,
    RecipeFormComponent
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
    MatCardActions,
    HttpClientModule,
    MatCardContent,
    MatDialogContent,
    MatDialogTitle,
    MatDialogContainer,
    MatDialogActions,
    MatDialogClose,
    ReactiveFormsModule,
    MatFormField,
    MatInput,
    MatSlideToggle,
    MatError,
    MatLabel,
    MaterialFileInputModule,
    MatSelect,
    MatSelectTrigger,
    MatOption,
    MatChipGrid,
    MatChipRow,
    MatChipInput,
    MatChipsModule,
    MatGridList,
    MatRow,
    MatDatepickerInput,
    MatDatepickerToggle,
    MatDatepicker,
    MatSlider,
    MatSliderThumb
  ],
  providers: [
    provideAnimationsAsync(),
    { provide: DateAdapter, useClass: NativeDateAdapter },
    { provide: MAT_DATE_FORMATS, useValue: { parse: { dateInput: 'YYYY-MM-DD' }, display: { dateInput: 'YYYY-MM-DD' } } }
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }
