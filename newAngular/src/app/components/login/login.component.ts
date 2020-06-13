import { Component } from '@angular/core';
import { Store } from '@ngrx/store';
import { Observable } from 'rxjs';

import { AppState, Auth } from './../../modules/core/store';

@Component({
  selector: 'app-login-component',
  templateUrl: 'login.template.html'
})
export class LoginComponent {
  credential: { id?: string, password?: string } = {};

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
  onLogin() {
    this.store.dispatch(new Auth(
      this.credential.id,
      this.credential.password
    ));
  }
}
