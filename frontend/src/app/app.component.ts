import { Component, OnInit } from '@angular/core';
import { UserService } from './services/user.service';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css'],
  providers: [UserService]
})
export class AppComponent {
  title = 'app';

  constructor(
    private _userService: UserService
  ) {

  }

  ngOnInit() {
    console.log(this._userService.getIdentity());
    console.log(this._userService.getToken());
  }
}
