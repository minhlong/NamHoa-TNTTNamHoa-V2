import { Injectable } from '@angular/core';
import { Effect, Actions, toPayload } from '@ngrx/effects';
import { Action } from '@ngrx/store';
import { Observable } from 'rxjs/Rx';
import { Router } from '@angular/router';
import 'rxjs/Rx';

import * as AuthAction from '../actions/auth.action';
import { AuthService } from '../../services/auth-service.service';

@Injectable()
export class AuthEffect {

  constructor(
    private actions$: Actions,
    private router: Router,
    private authService: AuthService,
  ) { }

  @Effect() auth$ = this.actions$.ofType(AuthAction.AUTH)
    .switchMap((payload: any) => {
      return this.authService.authenticate(payload.id, payload.password)
        .map((_data: any) => {
          this.router.navigate(['/home']);
          return new AuthAction.AuthCompleted(_data);
        })
        .catch((err) => Observable.of(new AuthAction.AuthFailed(err)))
    });

  @Effect() validateToken$ = this.actions$.ofType(AuthAction.VALIDATE_TOKEN)
    .map(() => {
      const __data = this.authService.isAuthenticated()
      if (__data) {
        return new AuthAction.AuthCompleted(__data);
      }
      return new AuthAction.Logout()
    }).catch((err) => Observable.of(new AuthAction.AuthFailed(err)))

  @Effect() logout$ = this.actions$.ofType(AuthAction.LOGOUT)
    .map(() => {
      this.authService.logout();
      return new AuthAction.LogoutSuccess()
    }).catch((err) => Observable.of(new AuthAction.AuthFailed(err)));

  @Effect({ dispatch: false }) logoutSuccess$: Observable<Action> = this.actions$.ofType(AuthAction.LOGOUT_SUCCESS)
    .do(() => {
      this.router.navigate(['/dang-nhap']);
    }).catch((err) => Observable.of(new AuthAction.AuthFailed(err)));
}
