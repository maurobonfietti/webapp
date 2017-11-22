import {Component, OnInit} from '@angular/core';
import {Router, ActivatedRoute, Params} from '@angular/router';
import {UserService} from '../services/user.service';
import {TaskService} from '../services/task.service';
import {Task} from '../models/task';

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
    public pages;
    public pagesPrev;
    public pagesNext;
    public loading;

    constructor(
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
        console.log('default.component [OK]');
        this.getAllTasks();
    }

    getAllTasks() {
        this._route.params.forEach((params: Params) => {
            let page = +params['page'];

            if (!page) {
                page = 1;
            }

            this.loading = 'show';
            this._taskService.getTasks(this.token, page).subscribe(
                response => {
                    this.status_task = response.status;
                    console.log(response);
                    if (this.status_task == 'success') {
                        this.tasks = response.tasks;
                        this.loading = 'hide';
                        this.pages = [];
                        for (let i = 0; i < response.totalPages; i++) {
                            this.pages.push(i);
                        }
                        if (page >= 2) {
                            this.pagesPrev = (page - 1);
                        } else {
                            this.pagesPrev = page;
                        }
                        if (page < response.totalPages || page == 1) {
                            this.pagesNext = (page + 1);
                        } else {
                            this.pagesNext = page;
                        }
                    }
                },
                error => {
                    console.log(<any> error);
                }
            );
        });
    }

    public filter = 0;
    public order = 0;
    public searchString;

    search() {
        console.log(this.filter);
        console.log(this.order);
        console.log(this.searchString);

        this.loading == 'show';

        if (!this.searchString || this.searchString.trim().length == 0) {
            this.searchString = null;
        }

        this._taskService.search(this.token, this.searchString, this.filter, this.order).subscribe(
            response => {
                if (response.status == 'success') {
                    this.tasks = response.data;
                    this.loading == 'hide';
                } else {
                    this._router.navigate(['/index']);
                }
            },
            error => {
                console.log(<any> error);
            }
        );
    }
}
