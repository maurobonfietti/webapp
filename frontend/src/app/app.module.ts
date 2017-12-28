import {BrowserModule} from '@angular/platform-browser';
import {HttpModule} from '@angular/http';
import {NgModule} from '@angular/core';

import {FormsModule, ReactiveFormsModule} from '@angular/forms';

import {routing, appRoutingProviders} from './app.routing';

import {AppComponent} from './app.component';
import {LoginComponent} from './components/login.component';
import {RegisterComponent} from './components/register.component';
import {DefaultComponent} from './components/default.component';
import {UserEditComponent} from './components/user.edit.component';
import {TaskNewComponent} from './components/task.new.component';
import {TaskEditComponent} from './components/task.edit.component';
import {GenerateDatePipe} from './pipes/generate.date.pipe';

@NgModule({
    declarations: [
        AppComponent,
        LoginComponent,
        RegisterComponent,
        DefaultComponent,
        UserEditComponent,
        TaskNewComponent,
        TaskEditComponent,
        GenerateDatePipe,
    ],
    imports: [
        routing,
        BrowserModule,
        HttpModule,
        FormsModule,
        ReactiveFormsModule
    ],
    providers: [
        appRoutingProviders
    ],
    bootstrap: [AppComponent]
})
export class AppModule {}
