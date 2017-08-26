import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute, Params } from '@angular/router';
import { UserService } from '../services/user.service';
import { TaskService } from '../services/task.service';
import { Task } from '../models/task';

@Component({
    selector: 'default',
    templateUrl: '../views/default.html',
    providers: [UserService, TaskService]
})

export class DefaultComponent implements OnInit {
    public title: string;
    public identity;
    public token;
    public tasks: Array<Task>;
    public status_task;

    constructor (
        private _route: ActivatedRoute,
        private _router: Router,
        private _userService: UserService,
        private _taskService: TaskService
    ) {
        this.title = 'Home Page';
        this.identity = this._userService.getIdentity();
        this.token = this._userService.getToken();
    }

    ngOnInit() {
        console.log('El componente default.component ha sido cargado.');
        this.getAllTasks();
    }

    getAllTasks() {
        this._route.params.forEach((params: Params) => {
            let page = +params['page'];

            if (!page) {
                page = 1;
            }

            this._taskService.getTasks(this.token, page).subscribe(
                response => {
                    this.status_task = response.status;
                    //console.log(response);
                    if (this.status_task == 'success') {
                        this.tasks = response.tasks;
                        console.log(this.tasks);
                        //console.log('aaa');
                        //console.log(response);
                        //console.log(response.tasks);
                    } else {
                        //console.log('bbb');
                    }
                },
                error => {
                    console.log(<any>error);
                }
            );
        });
    }
}
