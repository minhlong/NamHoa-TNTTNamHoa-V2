import { Injectable } from '@angular/core';
import { Router, CanActivate, CanActivateChild } from '@angular/router';
import { Store } from '@ngrx/store';

import { AppState } from '../../store/reducers';
import { AuthService } from '../auth-service.service';
import * as AuthAction from '../../store/actions/auth.action';
import { JwtHelperService } from '@auth0/angular-jwt';

@Injectable()
export class AuthGuard implements CanActivate, CanActivateChild {
  private jwtHelper = new JwtHelperService();

  constructor(
    private router: Router,
    private store: Store<AppState>,
    private authCognito: AuthService,
  ) {
  }

  canActivate() {
    return this.checkGuard();
  }

  canActivateChild() {
    return this.checkGuard();
  }

  private checkGuard() {
    const token: string = localStorage.getItem('token');
    if (this.jwtHelper.isTokenExpired(token)) {
      this.store.dispatch(new AuthAction.Logout());
      return false;
    }

    return true;
  }
}
