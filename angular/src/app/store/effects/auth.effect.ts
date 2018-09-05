import { Injectable } from '@angular/core';
import { Effect, Actions, toPayload } from '@ngrx/effects';
import { Action } from '@ngrx/store';
import { Observable } from 'rxjs';
import { Router } from '@angular/router';
import 'rxjs/add/operator/switchMap';
import 'rxjs/add/operator/map';
import 'rxjs/add/operator/catch';
import 'rxjs/add/operator/do';

import * as AuthAction from '../actions/auth.action';
import { AuthService } from '../../services/auth-service.service';
import { JwtAuthHttp } from '../../services/http-auth.service';
import { environment } from 'environments/environment';

@Injectable()
export class AuthEffect {

  constructor(
    private actions$: Actions,
    private router: Router,
    private _http: JwtAuthHttp,
    private authService: AuthService,
  ) { }
  urlAPI = environment.apiURL;

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
      const _data = this.authService.isAuthenticated()
      if (_data) {
        return new AuthAction.AuthCompleted(_data);
      }
      return new AuthAction.Logout()
    }).catch((err) => Observable.of(new AuthAction.AuthFailed(err)))

  @Effect() authCompleted$ = this.actions$.ofType(AuthAction.AUTH_COMPLETED)
    .map<Action, any>(toPayload)
    .switchMap((_data: any) => {
      return this._http.get(this.urlAPI + '/khoa-hoc/' + _data.khoa_hoc_hien_tai_id)
        .map(res => res.json())
        .map(res => new AuthAction.GetKhoaHoc(res.data))
        .catch((err) => Observable.of(new AuthAction.AuthFailed(err)));
    });

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
