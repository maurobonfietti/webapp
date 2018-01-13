import {Component, OnInit} from '@angular/core';
import {Router, ActivatedRoute, Params} from '@angular/router';
import {UserService} from '../services/user.service';

import {FormControl, FormGroupDirective, NgForm, Validators} from '@angular/forms';
import {ErrorStateMatcher} from '@angular/material/core';
import {MatSnackBar} from '@angular/material';

export class MyErrorStateMatcher implements ErrorStateMatcher {
  isErrorState(control: FormControl | null, form: FormGroupDirective | NgForm | null): boolean {
    const isSubmitted = form && form.submitted;
    return !!(control && control.invalid && (control.dirty || control.touched || isSubmitted));
  }
}

@Component({
  selector: 'snack-bar-component-example',
  templateUrl: '../views/login.ok.html',
})
export class SnackBarComponentExample {
  constructor(public snackBar: MatSnackBar) {}
}

@Component({
  selector: 'snack-bar-component-example',
  templateUrl: '../views/login.error.html',
})
export class SnackBarComponentExampleError {
  constructor(public snackBar: MatSnackBar) {}
}

@Component({
    selector: 'login',
    templateUrl: '../views/login.html',
    providers: [UserService]
})

export class LoginComponent implements OnInit {

    emailFormControl = new FormControl('', [
      Validators.required,
      Validators.email,
    ]);

    matcher = new MyErrorStateMatcher();

    public title: string;
    public user;
    public identity;
    public token;

    constructor(
        private _route: ActivatedRoute,
        private _router: Router,
        private _userService: UserService,
        public snackBar: MatSnackBar
    ) {
        this.title = 'Iniciar sesión';
        this.user = {
            "email": "",
            "password": "",
            "getData": true
        };
    }

    openSnackBar() {
        this.snackBar.openFromComponent(SnackBarComponentExample, {
          duration: 3000,
//          verticalPosition: 'top',
        });
    }

    openSnackBarError() {
        this.snackBar.openFromComponent(SnackBarComponentExampleError, {
          duration: 3000,
        });
    }

    ngOnInit() {
        console.log('login.component [OK]');
        this.logout();
        this.redirectIfIdentity();
    }

    logout() {
        this._route.params.forEach((params: Params) => {
            let logout = +params['id'];
            if (logout == 1) {
                localStorage.removeItem('identity');
                localStorage.removeItem('token');
                this.identity = null;
                this.token = null;
                window.location.href = '/login';
            }
        });
    }

    redirectIfIdentity() {
        let identity = this._userService.getIdentity();
        if (identity != null && identity.sub) {
            this._router.navigate(["/index/1"]);
        }
    }

    onSubmit() {
        this._userService.signUp(this.user).subscribe(
            response => {
                this.identity = response;
//                console.log(this.identity);
                if (this.identity.lenght <= 1) {
                    console.log('Server Error...');
                } {
                    console.log(this.identity);
                    if (!this.identity.status) {
                        localStorage.setItem('identity', JSON.stringify(this.identity));
                        // Get Token.
                        this.user.getData = false;
                        this._userService.signUp(this.user).subscribe(
                            response => {
                                this.token = response;
                                if (this.token.lenght <= 1) {
                                    console.log('Server Error...');
                                } {
                                    if (!this.token.status) {
                                        localStorage.setItem('token', JSON.stringify(this.token));
                                        this.openSnackBar();
                                        window.location.href = '/index/1';
                                    }
                                }
                            },
                            error => {
                                console.log(<any> error);
                            }
                        );
                    }
                }
            },
            error => {
                console.log(<any> error);
                this.openSnackBarError();
            }
        );
    }
}
