import { Injectable } from '@angular/core';
import { Router, CanActivate, CanActivateChild } from '@angular/router';
// import { tokenNotExpired } from 'angular2-jwt';
// import { Store } from '@ngrx/store';

// import { AppState } from '../../store/reducers';
// import { AuthService } from '../auth-service.service';
// import * as AuthAction from '../../store/actions/auth.action';

@Injectable()
export class AuthGuard implements CanActivate, CanActivateChild {

  constructor(
    private router: Router,
    // private store: Store<AppState>,
    // private authCognito: AuthService,
  ) {
  }

  canActivate() {
    return this.checkGuard();
  }

  canActivateChild() {
    return this.checkGuard();
  }

  private checkGuard() {
    // if (!tokenNotExpired()) {
    //   this.store.dispatch(new AuthAction.Logout());
      // return false;
    // }

    return true;
  }
}
