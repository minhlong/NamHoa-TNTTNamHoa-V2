import { Injectable } from '@angular/core';
import { Effect, Actions, ofType } from '@ngrx/effects';
import { Action } from '@ngrx/store';
import { Router } from '@angular/router';
import { Observable, of } from 'rxjs';
import { switchMap, map, catchError, tap } from 'rxjs/operators';

import { AuthService, ApiService } from '../../services';
import * as AuthActions from './../actions/auth.action';

@Injectable()
export class AuthEffect {
  constructor(
    private actions$: Actions,
    private router: Router,
    private http: ApiService,
    private authSer: AuthService,
  ) { }

  @Effect()
  auth$ = this.actions$.pipe(
    ofType(AuthActions.AUTH),
    switchMap((payload: any) => {
      return this.authSer.authenticate(payload.id, payload.password).pipe(
        map((data: any) => {
          this.router.navigate(['/home']);
          return new AuthActions.AuthCompleted(data);
        }),
        catchError((err) => {
          return of(new AuthActions.AuthFailed(err));
        })
      );
    })
  );

  @Effect()
  validateToken$ = this.actions$.pipe(
    ofType(AuthActions.VALIDATE_TOKEN),
    map(() => {
      const data = this.authSer.isAuthenticated();
      if (data) {
        return new AuthActions.AuthCompleted(data);
      }
      return new AuthActions.Logout();
    }),
    catchError((err) => {
      return of(new AuthActions.AuthFailed(err));
    })
  );

  @Effect()
  authCompleted$ = this.actions$.pipe(
    ofType(AuthActions.AUTH_COMPLETED),
    map((res: any) => res.payload),
    switchMap((data: any) => {
      return this.http.get('/khoa-hoc/' + data.khoa_hoc_hien_tai_id).pipe(
        map((res: any) => new AuthActions.GetKhoaHoc(res.data)),
        catchError((err) => {
          return of(new AuthActions.AuthFailed(err));
        })
      );
    })
  );

  @Effect()
  logout$ = this.actions$.pipe(
    ofType(AuthActions.LOGOUT),
    switchMap((data: any) => {
      return this.http.post('/auth/logout').pipe(
        map((res: any) => {
          localStorage.clear();
          return new AuthActions.LogoutSuccess();
        }),
        catchError((err) => {
          return of(new AuthActions.AuthFailed(err));
        })
      );
    })
  );

  @Effect({ dispatch: false })
  logoutSuccess$: Observable<Action> = this.actions$.pipe(
    ofType(AuthActions.LOGOUT_SUCCESS),
    tap(() => {
      this.router.navigate(['/dang-nhap']);
    }),
    catchError((err) => {
      return of(new AuthActions.AuthFailed(err));
    })
  );
}
