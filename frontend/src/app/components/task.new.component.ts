import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute, Params } from '@angular/router';
import { UserService } from '../services/user.service';

@Component({
    selector: 'task-new',
    templateUrl: '../views/task.new.html',
    providers: [UserService]
})

export class TaskNewComponent implements OnInit {
    public title: string;
    //public user: User;
    //public status;
    public identity;
    //public token;

    constructor (
        private _route: ActivatedRoute,
        private _router: Router,
        private _userService: UserService
    ) {
        this.title = 'Crear nueva tarea';
        this.identity = this._userService.getIdentity();
        //this.token = this._userService.getToken();
    }

    ngOnInit() {
        if (this.identity == null) {
            //this._router.navigate(['/login']);
        } else {

        }
    }

    onSubmit() {
        console.log('Cargado: task.new.component');
    }
}
