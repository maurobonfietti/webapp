import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute, Params } from '@angular/router';
import { User } from '../models/user';
import { UserService } from '../services/user.service';

@Component({
    selector: 'user-edit',
    templateUrl: '../views/user.edit.html',
    providers: [UserService]
})

export class UserEditComponent implements OnInit {
    public title: string;
    public user: User;
    public status;
    public identity;
    public token;

    constructor (
        private _route: ActivatedRoute,
        private _router: Router,
        private _userService: UserService
    ) {
        this.title = 'Editar mis datos';
        this.identity = this._userService.getIdentity();
        this.token = this._userService.getToken();
    }

    ngOnInit() {
        if (this.identity == null) {
            this._router.navigate(['/login']);
        } else {
            this.user = new User(
                this.identity.sub,
                this.identity.role,
                this.identity.name,
                this.identity.surname,
                this.identity.email,
                this.identity.password
            );
        }
    }
}
