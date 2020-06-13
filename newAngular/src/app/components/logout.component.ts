import { Component } from '@angular/core';
import { Store } from '@ngrx/store';
import { AppState, Logout } from '../modules/core/store';


@Component({
  selector: 'app-logout',
  template: ''
})
export class LogoutComponent {
  constructor(
    private store: Store<AppState>
  ) {
    this.store.dispatch(new Logout());
  }
}
