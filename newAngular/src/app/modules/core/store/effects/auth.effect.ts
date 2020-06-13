import { switchMap, map, catchError, tap } from 'rxjs/operators';
import { Injectable } from '@angular/core';
import { Effect, Actions, ofType } from '@ngrx/effects';
import { Action } from '@ngrx/store';
import { Observable, of } from 'rxjs';
import { Router } from '@angular/router';

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
        catchError((err) => of(new AuthActions.AuthFailed(err)))
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
    catchError((err) => of(new AuthActions.AuthFailed(err)))
  );

  @Effect()
  authCompleted$ = this.actions$.pipe(
    ofType(AuthActions.AUTH_COMPLETED),
    map((res: any) => res.payload),
    switchMap((data: any) => {
      return this.http.get('/khoa-hoc/' + data.khoa_hoc_hien_tai_id).pipe(
        map((res: any) => res.json()),
        map((res: any) => new AuthActions.GetKhoaHoc(res.data)),
        catchError((err) => of(new AuthActions.AuthFailed(err)))
      );
    })
  );

  @Effect()
  logout$ = this.actions$.pipe(
    ofType(AuthActions.LOGOUT),
    map(() => {
      this.authSer.logout();
      return new AuthActions.LogoutSuccess();
    }),
    catchError((err) => of(new AuthActions.AuthFailed(err)))
  );

  @Effect({ dispatch: false })
  logoutSuccess$: Observable<Action> = this.actions$.pipe(
    ofType(AuthActions.LOGOUT_SUCCESS),
    tap(() => {
      this.router.navigate(['/dang-nhap']);
    }),
    catchError((err) => of(new AuthActions.AuthFailed(err)))
  );
}
