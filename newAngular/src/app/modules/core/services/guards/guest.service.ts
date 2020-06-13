import { Injectable } from '@angular/core';
import { Router, CanActivate, CanActivateChild } from '@angular/router';
import { JwtHelperService } from '@auth0/angular-jwt';

@Injectable()
export class GuestGuard implements CanActivate, CanActivateChild {

  constructor(
    private router: Router,
    public jwtHelper: JwtHelperService
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

    if (!isExpired) {
      this.router.navigate(['/home']);
      return false;
    }

    return true;
  }
}
