import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute, Params } from '@angular/router';
import { UserService } from '../services/user.service';
import { TaskService } from '../services/task.service';
import { Task } from '../models/task';

@Component({
    selector: 'task-detail',
    templateUrl: '../views/task.detail.html',
    providers: [UserService, TaskService]
})

export class TaskDetailComponent implements OnInit {
    //public page_title: string;
    public identity;
    public token;
    public task: Task;
    public status_task;

    constructor (
        private _route: ActivatedRoute,
        private _router: Router,
        private _userService: UserService,
        private _taskService: TaskService
    ) {
        //this.page_title = 'Editar tarea';
        this.identity = this._userService.getIdentity();
        this.token = this._userService.getToken();
    }

    ngOnInit() {
        if (this.identity && this.identity.sub) {
            this.getTask();
        } else {
            this._router.navigate(['/login']);
        }
    }

    getTask() {
        this._route.params.forEach((params: Params) => {
            let id = +params['id'];
    
            this._taskService.getTask(this.token, id).subscribe(
                response => {
                    //this.task = response.data;
                    this.status_task = response.status;
                    if (response.status == 'success') {
                        if (response.task.user.id == this.identity.sub) {
                            this.task = response.task;
                            console.log(this.task);
                        } else {
                            this._router.navigate(['/']);
                        }
                    } else {
                        this._router.navigate(['/login']);
                    }
                    //console.log(response);
                },
                error => {
                    console.log(<any>error);
                }
            );
        });
    }
/*
    onSubmit() {
        console.log(this.task);
        this._taskService.create(this.token, this.task).subscribe(
            response => {
                this.status_task = response.status;
                if (this.status_task != "success") {
                    this.status_task = 'error';
                } else {
                    this.task = response.data;
                    //this._router.navigate(['/task', this.task.id]);
                    this._router.navigate(['/']);
                }
            },
            error => {
                console.log(<any>error);
            }
        );
    }
*/
}
