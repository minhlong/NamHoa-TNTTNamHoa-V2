import { Component } from '@angular/core';
import { NgForm } from '@angular/forms';
import { Store } from '@ngrx/store';
import { Observable } from 'rxjs/Rx';

import { AppState } from '../../store/reducers/index';
import * as AuthAction from '../../store/actions/auth.action';

@Component({
  selector: 'app-login-component',
  templateUrl: 'login.template.html'
})
export class LoginComponent {
  login: { username?: string, password?: string } = {};

  isLoading: Observable<any>;
  errorMessage: Observable<any>;

  constructor(
    private store: Store<AppState>
  ) {
    this.isLoading = this.store.select((state: AppState) => state.auth.loading);
    this.errorMessage = this.store.select((state: AppState) => state.auth.error);
  }

  /**
   * User submit login form
   */
  onLogin(form: NgForm) {
    if (form.valid) {
      this.store.dispatch(new AuthAction.Auth(
        this.login.username,
        this.login.password
      ));
    }
  }
}
