import {Component, OnInit} from '@angular/core';
import {Router, ActivatedRoute, Params} from '@angular/router';
import {UserService} from '../services/user.service';

import {BrowserModule} from '@angular/platform-browser';
import {HttpModule} from '@angular/http';
import {NgModule} from '@angular/core';
import {MatButtonModule, MatCheckboxModule} from '@angular/material';

@Component({
    selector: 'login',
    templateUrl: '../views/login.html',
    providers: [UserService]
})

@NgModule({
  imports: [MatButtonModule, MatCheckboxModule],
  exports: [MatButtonModule, MatCheckboxModule],
})

export class LoginComponent implements OnInit {
    public title: string;
    public user;
    public identity;
    public token;

    constructor(
        private _route: ActivatedRoute,
        private _router: Router,
        private _userService: UserService
    ) {
        this.title = 'Inicia sesión';
        this.user = {
            "email": "",
            "password": "",
            "getData": true
        };
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
                if (this.identity.lenght <= 1) {
                    console.log('Server Error...');
                } {
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
            }
        );
    }
}
