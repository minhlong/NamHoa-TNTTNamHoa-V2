import { Injectable } from '@angular/core';
import { Http, Request, Response, RequestOptionsArgs } from '@angular/http';
import { AuthHttp, AuthConfig } from 'angular2-jwt';
import { Observable } from 'rxjs';
import { Store } from '@ngrx/store';

import { AppState } from '../store/reducers';
import * as AuthAction from '../store/actions/auth.action';

@Injectable()
export class JwtAuthHttp extends AuthHttp {

  constructor(
    options: AuthConfig,
    http: Http,
    public store: Store<AppState>
  ) {
    super(options, http);
  }

  /** Kiểm tra xem kết nối đã được xác thực chưa (Authorize) */
  private isUnauthorized(status: number): boolean {
    return status === 401;
  }

  /** Tạo các kết nối đến server */
  public request(url: string | Request, options?: RequestOptionsArgs): Observable<Response> {
    const response = super.request(url, options).catch(error => {
      // Kiểm tra kết nối được xác thực chưa
      if (this.isUnauthorized(error.status)) {
        this.store.dispatch(new AuthAction.Logout());
      }
      return Observable.throw(error.json().error);
    });

    return response;
  }
}

export function authFactory(http: Http, store: Store<any>) {
  return new JwtAuthHttp(
    new AuthConfig({
      headerPrefix: 'Bearer',
      noTokenScheme: true,
      noJwtError: true
    }), http, store);
}
