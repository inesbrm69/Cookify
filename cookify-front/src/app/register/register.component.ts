import {Component, OnInit} from '@angular/core';
import {FormBuilder, FormGroup, Validators} from "@angular/forms";
import {UserService} from "../services/user.service";
import {Router} from "@angular/router";
import {User} from "../interfaces/user";

@Component({
  selector: 'app-register',
  templateUrl: './register.component.html',
  styleUrl: './register.component.scss'
})
export class RegisterComponent implements OnInit {
  registerForm: FormGroup;
  errorMessage: string | null = null;
  isLoading: boolean = false;
  constructor(private fb: FormBuilder, private userService: UserService, private router: Router) {
    this.registerForm = this.fb.group({
      username: ['', Validators.required],
      name: ['', Validators.required],
      lastName: ['', Validators.required],
      password: ['', Validators.required]
    });
  }

  ngOnInit(): void {
    this.registerForm = this.fb.group({
      username: ['', ],
      name: ['', ],
      lastName: ['', ],
      password: ['', ]
    });

  }

  // Soumettre le formulaire
  onSubmit(): void {
    this.isLoading = true;
    if (this.registerForm.valid) {
      const newUser: User = this.registerForm.value;
      this.userService.register(newUser).subscribe({
        next: (response) => {
          this.isLoading = false;
          this.router.navigateByUrl('/login');
        },
        error: (error) => {
          this.isLoading = false;
          this.errorMessage = 'An unexpected error occurred. Please try again later.';
          console.error('Error while registering',error);
        }
      });
    } else {
      this.isLoading = false;
      this.errorMessage = 'Please fill out the form correctly before submitting.';
      console.log('Invalid form');
    }
  }
}
