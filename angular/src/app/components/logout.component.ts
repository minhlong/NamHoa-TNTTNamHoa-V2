import { Component } from '@angular/core';
import { Store } from '@ngrx/store';

import { AppState } from '../store/reducers/index';
import { consoleLog } from '../_helpers';
import * as AuthAction from '../store/actions/auth.action';

@Component({
  selector: 'app-logout',
  template: ''
})
export class LogoutComponent {
  constructor(
    private store: Store<AppState>
  ) {
    consoleLog('LogoutComponent: constructor');
    this.store.dispatch(new AuthAction.Logout());
  }
}
