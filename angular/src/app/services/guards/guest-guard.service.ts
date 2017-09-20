import { Injectable } from '@angular/core';
import { Router, CanActivate, CanActivateChild } from '@angular/router';
import { tokenNotExpired } from 'angular2-jwt';
import { Store } from '@ngrx/store';

import { AppState } from '../../store/reducers/index';

@Injectable()
export class GuestGuard implements CanActivate, CanActivateChild {

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
    const notAuth = !tokenNotExpired();

    if (!notAuth) {
      this.router.navigate(['/home']);
    }

    return notAuth;
  }
}
