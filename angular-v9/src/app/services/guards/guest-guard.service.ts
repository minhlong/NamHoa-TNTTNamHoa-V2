import { Injectable } from '@angular/core';
import { Router, CanActivate, CanActivateChild } from '@angular/router';
import { Store } from '@ngrx/store';

import { AppState } from '../../store/reducers';
import { JwtHelperService } from '@auth0/angular-jwt';

@Injectable()
export class GuestGuard implements CanActivate, CanActivateChild {
  private jwtHelper = new JwtHelperService();

  constructor(
    private router: Router,
    private store: Store<AppState>
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
    if (!this.jwtHelper.isTokenExpired(token)) {
      this.router.navigate(['/home']);
      return false;
    }

    return true;
  }
}
