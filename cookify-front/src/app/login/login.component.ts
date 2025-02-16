import { Component } from '@angular/core';
import {User} from "../interfaces/user";
import {FormBuilder, FormGroup, Validators} from "@angular/forms";
import {Router} from "@angular/router";
import {UserService} from "../services/user.service";

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrl: './login.component.scss'
})
export class LoginComponent {
  loginForm:FormGroup;
  errorMessage: string | null = null;
  isLoading: boolean = false;
  constructor(private fb:FormBuilder,
              private userService: UserService,
              private router: Router) {

    this.loginForm = this.fb.group({
      username: ['',Validators.required],
      password: ['',Validators.required]
    });
  }

  ngOnInit(): void {
    this.loginForm = this.fb.group({
      username: ['', ],
      password: ['', ]
    });

  }

  onSubmit() {
    this.isLoading = true;
    if (this.loginForm.valid) {
      const loggedUser: User = this.loginForm.value;
      this.userService.login(loggedUser)
        .subscribe({
            next: (response) => {
              this.isLoading = false;
              this.router.navigateByUrl('/accueil');
            },
            error: (error) => {
              this.isLoading = false;
              if(error.status == 401){
                this.errorMessage = 'Invalid username or password.';
              }else {
                this.errorMessage = 'An unexpected error occurred. Please try again later.';
                console.error('Error while login', error);
              }
            }
          }
        );
    }else {
      this.isLoading = false;
      this.errorMessage = 'Please fill out the form correctly before submitting.';
      console.log('Invalid form');
    }
  }
}
