import { Injectable } from '@angular/core';
import { CanActivate, CanActivateChild } from '@angular/router';
import { JwtHelperService } from '@auth0/angular-jwt';
import { Store } from '@ngrx/store';

import { AppState, Logout } from '../../store';

@Injectable()
export class AuthGuard implements CanActivate, CanActivateChild {

  constructor(
    private jwtHelper: JwtHelperService,
    private store: Store<AppState>,
  ) {
  }

  canActivate() {
    return this.checkGuard();
  }

  canActivateChild() {
    return this.checkGuard();
  }

  private checkGuard() {
    const isExpired = this.jwtHelper.isTokenExpired();

    console.log(isExpired, 2);

    if (isExpired) {
      this.store.dispatch(new Logout());
      return false;
    }

    return true;
  }
}
