import { Injectable } from '@angular/core';
import { Actions, ofType, Effect } from '@ngrx/effects';
import { Router } from '@angular/router';
import { HttpClient } from '@angular/common/http';
import { switchMap, map, catchError, tap } from 'rxjs/operators';
import { of } from 'rxjs';

import * as AuthAction from '../actions/auth.action';
import { AuthService } from '../../services/auth-service.service';
import { environment } from 'src/environments/environment';

@Injectable()
export class AuthEffect {

  constructor(
    private actions$: Actions,
    private router: Router,
    private http: HttpClient,
    private authService: AuthService,
  ) { }
  urlAPI = environment.apiURL;

  @Effect()
  auth$ = this.actions$.pipe(
    ofType(AuthAction.AUTH),
    switchMap((payload: any) => {
      return this.authService.authenticate(payload.id, payload.password).pipe(
        map((data: any) => {
          this.router.navigate(['/home']);
          return new AuthAction.AuthCompleted(data);
        }),
        catchError((err) => of(new AuthAction.AuthFailed(err)))
      );
    })
  );

  @Effect()
  authCompleted$ = this.actions$.pipe(
    ofType(AuthAction.AUTH_COMPLETED),
    map((action: any) => action.payload),
    switchMap((payload: any) => {
      return this.http.get(this.urlAPI + '/khoa-hoc/' + payload.khoa_hoc_hien_tai_id).pipe(
        map((res: any) => new AuthAction.GetKhoaHoc(res.data)),
        catchError((err) => of(new AuthAction.AuthFailed(err))),
      );
    })
  );

  @Effect()
  logout$ = this.actions$.pipe(
    ofType(AuthAction.LOGOUT),
    map(() => {
      this.authService.logout();
      return new AuthAction.LogoutSuccess();
    }),
    catchError((err) => of(new AuthAction.AuthFailed(err))),
  );

  @Effect({ dispatch: false })
  logoutSuccess$ = this.actions$.pipe(
    ofType(AuthAction.LOGOUT_SUCCESS),
    tap(() => {
      this.router.navigate(['/dang-nhap']);
    }),
    catchError((err) => of(new AuthAction.AuthFailed(err))),
  );

  @Effect()
  validateToken$ = this.actions$.pipe(
    ofType(AuthAction.VALIDATE_TOKEN),
    map(() => {
      const data = this.authService.isAuthenticated();
      if (data) {
        return new AuthAction.AuthCompleted(data);
      }
      return new AuthAction.Logout();
    }),
    catchError((err) => of(new AuthAction.AuthFailed(err))),
  );
}
