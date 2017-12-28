import {Component, OnInit} from '@angular/core';
import {User} from '../models/user';
import {UserService} from '../services/user.service';

@Component({
    selector: 'register',
    templateUrl: '../views/register.html',
    providers: [UserService]
})

export class RegisterComponent implements OnInit {
    public title: string;
    public user: User;
    public status;

    constructor(
        private _userService: UserService
    ) {
        this.title = 'Registrate';
        this.user = new User(1, "user", "", "", "", "");
    }

    ngOnInit() {
        console.log('register.component [OK]');
    }

    onSubmit() {
        this._userService.register(this.user).subscribe(
            response => {
                this.status = response.status;
                if (response.status != 'success') {
                    this.status = 'error';
                } else {
                    this.user = new User(1, "user", "", "", "", "");
                }
            },
            error => {
                console.log(<any> error);
            }
        );
    }
}
