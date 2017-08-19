import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute, Params } from '@angular/router';
import { UserService } from '../services/user.service';

@Component({
    selector: 'login',
    templateUrl: '../views/login.html',
    providers: [UserService]
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
        this.title = 'Ingresar:';
        this.user = {
            "email": "",
            "password": "",
            "getHash": "true"
        };
    }

    ngOnInit() {
        console.log('El componente login.component ha sido cargado.');
        console.log(JSON.parse(localStorage.getItem('identity')));
        console.log(JSON.parse(localStorage.getItem('token')));
    }

    onSubmit() {
        console.log(this.user);

        this._userService.signUp(this.user).subscribe(
            response => {
                this.identity = response;
                if (this.identity.lenght <= 1) {
                    console.log('Error en el servidor.');
                } {
                    if (!this.identity.status) {
                        localStorage.setItem('identity', JSON.stringify(this.identity));

                        // Get Token.
                        this.user.getHash = null;
                        this._userService.signUp(this.user).subscribe(
                            response => {
                                this.token = response;
                                if (this.identity.lenght <= 1) {
                                    console.log('Error en el servidor.');
                                } {
                                    if (!this.identity.status) {
                                        localStorage.setItem('token', JSON.stringify(this.token));
                                    }
                                }
                            },
                            error => {
                                console.log(<any>error);
                            }
                        );
                    }
                }
            },
            error => {
                console.log(<any>error);
            }
        );
    }
}
