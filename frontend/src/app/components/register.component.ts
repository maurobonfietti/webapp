import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute, Params } from '@angular/router';
import { User } from '../models/user';

@Component({
    selector: 'register',
    templateUrl: '../views/register.html'
})

export class RegisterComponent implements OnInit {
    public title: string;
    public user: User;

    constructor (
        private _route: ActivatedRoute,
        private _router: Router
    ) {
        this.title = 'Registrate';
        this.user = new User(1, "user", "", "", "", "");
    }

    ngOnInit() {
        console.log('El componente register.component ha sido cargado.');
    }

    onSubmit() {
        console.log(this.user);
    }
}
