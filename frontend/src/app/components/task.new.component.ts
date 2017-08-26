import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute, Params } from '@angular/router';
import { UserService } from '../services/user.service';
import { TaskService } from '../services/task.service';
import { Task } from '../models/task';

@Component({
    selector: 'task-new',
    templateUrl: '../views/task.new.html',
    providers: [UserService, TaskService]
})

export class TaskNewComponent implements OnInit {
    public page_title: string;
    public identity;
    public task: Task;

    constructor (
        private _route: ActivatedRoute,
        private _router: Router,
        private _userService: UserService,
        private _taskService: TaskService
    ) {
        this.page_title = 'Crear nueva tarea';
        this.identity = this._userService.getIdentity();
    }

    ngOnInit() {
        if (this.identity == null && !this.identity.sub) {
            this._router.navigate(['/login']);
        } else {
            this.task = new Task(1, '', '', 'new', 'null', 'null');
        }

        console.log(this._taskService.create());
    }

    onSubmit() {
        console.log('asd');
        console.log(this.task);
    }
}
